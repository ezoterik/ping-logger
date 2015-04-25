<?php

namespace app\commands;

use app\models\Group;
use app\models\Log;
use app\models\Object;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
use yii\log\Logger;

class PingController extends Controller
{
    /**
     * Пингует все объекты
     */
    public function actionIndex()
    {
        //Вынимаем все группы, чтобы на каждую группу создать по отдельному потоку
        /** @var Group[] $groups */
        $groups = Group::find()
            ->select(['id'])
            ->where([
                'is_disable' => false,
                'lock_date' => '0000-00-00 00:00:00',
            ])
            ->indexBy('id')
            ->all();

        if (count($groups) == 0) {
            return;
        }

        //Ставим отметку о блокировке для всех найденных записей
        Group::lock(array_keys($groups));

        //shell_exec($cmd . " > /dev/null &");
        //https://florian.ec/articles/running-background-processes-in-php/

        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["file", Yii::getAlias('@runtime') . '/shel_errors.txt', "a"]
        ];

        $process = [];
        $pipes = [];

        $procLastOutput = [];

        //Запускаем по процессу на каждую группу
        foreach ($groups as $group) {
            if (!isset($process[$group->id]) || !is_resource($process[$group->id])) {
                $process[$group->id] = proc_open('exec ' . Yii::getAlias('@app') . '/yii ping/ping-group ' . $group->id, $descriptorspec, $pipes[$group->id]);
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
                    } else {
                        //INFO: Блокирует проверку других процессов
                        //if (stream_get_contents($pipes[$processKey][1])) {
                        //    $procLastOutput[$processKey] = time();
                        //}
                    }
                } else {
                    Group::unLock($processKey);
                    unset($process[$processKey]);
                    break;
                }
            }

            usleep(1000000);
        };
    }

    public function actionPingGroup($groupId)
    {
        //Первыми идут объекты которые в прошлырй раз пинговались без ошибки
        /** @var \app\models\Object[] $objects */
        $objects = Object::find()
            ->select(['ip', 'port', 'port_udp', 'status', 'avg_rtt', 'updated'])
            ->where(['group_id' => $groupId, 'is_disable' => false])
            ->orderBy('status DESC')->all();

        //Перебераем объекты внутри группы
        foreach ($objects as $object) {
            //Пингуем каждый объект
            $resStatus = Log::EVENT_ERROR;
            $avgRtt = 0;

            //tcp
            if ($resStatus == Log::EVENT_ERROR && $object->port > 0) {
                if ($this->pingByNPing($object->ip, $object->port, false) !== false) {
                    $resStatus = Log::EVENT_GOOD;
                    //Посылаем 100 пакетов, чтоб узнать среднее время отклика
                    $avgRttTmp = $this->pingByNPing($object->ip, $object->port, false, 100, 0.01);
                    if ($avgRttTmp > 0) {
                        $avgRtt = $avgRttTmp;
                    }
                }
            }

            //udp
            if ($resStatus == Log::EVENT_ERROR && $object->port_udp > 0) {
                if ($this->pingByNPing($object->ip, $object->port, false) !== false) {
                    $resStatus = Log::EVENT_GOOD;
                    //Посылаем 100 пакетов, чтоб узнать среднее время отклика
                    $avgRttTmp = $this->pingByNPing($object->ip, $object->port, true, 100, 0.01);
                    if ($avgRttTmp > 0) {
                        $avgRtt = $avgRttTmp;
                    }
                }
            }

            //icmp
            if ($resStatus == Log::EVENT_ERROR) {
                if ($this->pingByPing($object->ip) !== false) {
                    $resStatus = Log::EVENT_GOOD;
                    //Посылаем 100 пакетов, чтоб узнать среднее время отклика
                    $avgRttTmp = $this->pingByPing($object->ip, 100, 0.1);
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
                $object->touch('updated');
            }
        }
    }

    /**
     * @param string $ip
     * @param int $count
     * @param float $delay
     * @return bool|float
     */
    protected function pingByPing($ip, $count = 1, $delay = 0.0)
    {
        if ($ip == '') {
            return false;
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
        if (
            strstr($response, '0 packets received') === false &&
            preg_match('/= \d+\.\d+\/(\d+\.\d+)\/\d+\.\d+\/\d+\.\d+ ms/i', $response, $matches)
        ) {
            return floatval($matches[1]);
        }

        return false;
    }

    protected function pingByNPing($ip, $port, $isUdp = false, $count = 1, $delay = 0.0)
    {
        if ($ip == '' || $port <= 0) {
            return false;
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

        return false;
    }
}
