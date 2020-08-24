<?php

namespace app\commands;

use app\models\Group;
use app\models\Log;
use app\models\PingObject;
use Yii;
use yii\console\Controller;

class PingController extends Controller
{
    public const AVG_RTT_COUNT_PACKAGES = 50;
    public const AVG_RTT_DELAY_TCP_AND_UDP = 0.1;
    public const AVG_RTT_DELAY_ICMP = 0.1;

    /**
     * Пингует все объекты
     */
    public function actionIndex()
    {
        //Удаляем зависшие группы
        Group::unLockOld();

        //Вынимаем все группы, чтобы на каждую группу создать по отдельному потоку
        /** @var Group[] $groups */
        $groups = Group::find()
            ->select(['id'])
            ->where(['is_disable' => false, 'lock_at' => null])
            ->indexBy('id')
            ->all();

        if (count($groups) == 0) {
            return;
        }

        //Ставим отметку о блокировке для всех найденных записей
        Group::lock(array_keys($groups));

        //shell_exec($cmd . " > /dev/null &");
        //https://florian.ec/articles/running-background-processes-in-php/

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['file', Yii::getAlias('@runtime/shel_errors.txt') . '', 'a'],
        ];

        $process = [];
        $pipes = [];

        $procLastOutput = [];

        //Запускаем по процессу на каждую группу
        foreach ($groups as $group) {
            if (!isset($process[$group->id]) || !is_resource($process[$group->id])) {
                $process[$group->id] = proc_open('exec ' . Yii::getAlias('@app') . '/yii ping/ping-group ' . $group->id, $descriptorSpec, $pipes[$group->id]);
                $procLastOutput[$group->id] = time();

                if (is_resource($process[$group->id])) {
                    stream_set_blocking($pipes[$group->id][0], 0);
                    stream_set_blocking($pipes[$group->id][1], 0);
                }
            }
        }

        //Ожидаем завершение всех процессов
        while (count($process) > 0) {
            foreach ($process as $processKey => $processRes) {
                if (is_resource($processRes)) {
                    $status = proc_get_status($processRes);
                    if (!$status['running'] || (time() - $procLastOutput[$processKey] > 1000)) {
                        fclose($pipes[$processKey][0]);
                        fclose($pipes[$processKey][1]);
                        proc_close($processRes);
                        Group::unLock($processKey);
                        unset($process[$processKey]);
                        break;
                    }
                    //else {
                    //    //INFO: Блокирует проверку других процессов
                    //    if (stream_get_contents($pipes[$processKey][1])) {
                    //        $procLastOutput[$processKey] = time();
                    //    }
                    //}
                } else {
                    Group::unLock($processKey);
                    unset($process[$processKey]);
                    break;
                }
            }

            usleep(1000000);
        }
    }

    public function actionPingGroup(int $groupId)
    {
        //Первыми идут объекты которые в прошлырй раз пинговались без ошибки
        /** @var PingObject[] $objects */
        $objects = PingObject::find()
            ->where(['group_id' => $groupId, 'is_disable' => false])
            ->orderBy(['status' => SORT_DESC])
            ->all();
        //->select(['id', 'ip', 'port', 'port_udp', 'status', 'avg_rtt', 'updated_at']) Не сохраняет запись если так выбирать

        //Перебераем объекты внутри группы
        foreach ($objects as $object) {
            //Пингуем каждый объект
            $resStatus = Log::EVENT_ERROR;
            $avgRtt = 0;

            //tcp
            if ($resStatus == Log::EVENT_ERROR && $object->port > 0) {
                if ($this->pingByNPing($object->ip, $object->port, false) !== null) {
                    $resStatus = Log::EVENT_GOOD;
                    //Посылаем self::COUNT_AVG_RTT_PACKAGES пакетов, чтоб узнать среднее время отклика
                    $avgRttTmp = $this->pingByNPing($object->ip, $object->port, false, self::AVG_RTT_COUNT_PACKAGES, self::AVG_RTT_DELAY_TCP_AND_UDP);
                    if ($avgRttTmp > 0) {
                        $avgRtt = $avgRttTmp;
                    }
                }
            }

            //udp
            if ($resStatus == Log::EVENT_ERROR && $object->port_udp > 0) {
                if ($this->pingByNPing($object->ip, $object->port, false) !== null) {
                    $resStatus = Log::EVENT_GOOD;
                    //Посылаем self::COUNT_AVG_RTT_PACKAGES пакетов, чтоб узнать среднее время отклика
                    $avgRttTmp = $this->pingByNPing($object->ip, $object->port, true, self::AVG_RTT_COUNT_PACKAGES, self::AVG_RTT_DELAY_TCP_AND_UDP);
                    if ($avgRttTmp > 0) {
                        $avgRtt = $avgRttTmp;
                    }
                }
            }

            //icmp
            if ($resStatus == Log::EVENT_ERROR && $object->port == 0 && $object->port_udp == 0) {
                if ($this->pingByPing($object->ip) !== null) {
                    $resStatus = Log::EVENT_GOOD;
                    //Посылаем self::COUNT_AVG_RTT_PACKAGES пакетов, чтоб узнать среднее время отклика
                    $avgRttTmp = $this->pingByPing($object->ip, self::AVG_RTT_COUNT_PACKAGES, self::AVG_RTT_DELAY_ICMP);
                    if ($avgRttTmp > 0) {
                        $avgRtt = $avgRttTmp;
                    }
                }
            }

            if ($object->status != $resStatus || $object->avg_rtt != $avgRtt) {
                //Обновляем время пинга
                $object->avg_rtt = $avgRtt;

                //Если статус изменился, создаем лог
                if ($object->status != $resStatus) {
                    $log = new Log();
                    $log->object_id = $object->id;
                    $log->event_num = $resStatus;
                    $log->save();

                    $object->status = $resStatus;
                }

                $object->save();
            } else {
                $object->touch('updated_at');
            }
        }
    }

    protected function pingByPing(string $ip, int $count = 1, float $delay = 0.0): ?float
    {
        if ($ip == '') {
            return null;
        }

        $cmd = 'ping';
        if ($count > 0) {
            $cmd .= ' -c ' . $count;
        }
        if ($delay >= 0.1) {
            $cmd .= ' -i ' . $delay;
        }
        $cmd .= ' ' . $ip;

        $response = shell_exec($cmd);

        if (strstr($response, '0 packets received') === false && preg_match('/= \d+\.\d+\/(\d+\.\d+)\/\d+\.\d+\/\d+\.\d+ ms/i', $response, $matches)) {
            return floatval($matches[1]);
        }

        return null;
    }

    protected function pingByNPing(string $ip, string $port, bool $isUdp = false, int $count = 1, float $delay = 0.0): ?float
    {
        if ($ip == '' || $port <= 0) {
            return null;
        }

        $cmd = 'nping';
        if ($isUdp) {
            $cmd .= ' --udp';
        } else {
            $cmd .= ' --tcp-connect';
        }
        $cmd .= ' ' . $ip;
        $cmd .= ' -p ' . $port;
        if ($count > 0) {
            $cmd .= ' -c ' . $count;
        }
        if ($delay > 0) {
            $cmd .= ' --delay ' . $delay;
        }

        $response = shell_exec($cmd);
        if (preg_match('/Avg rtt: (\d+\.\d+)ms/i', $response, $matches)) {
            return floatval($matches[1]);
        }

        return null;
    }
}
