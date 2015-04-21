<?php

namespace app\commands;

use app\models\Group;
use app\models\Log;
use app\models\Object;
use Yii;
use yii\console\Controller;

class PingController extends Controller
{
    /**
     * Пингует все объекты
     */
    public function actionIndex()
    {
        //Защита от параллельного запуска
        if (!Yii::$app->mutex->acquire('ping_index')) {
            return;
        }

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

        //Вынимаем все группы, чтобы на каждую группу создать по отдельному потоку
        /** @var Group[] $groups */
        $groups = Group::find()
            ->where(['is_disable' => false])
            ->all();

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
                        unset($process[$processKey]);
                        break;
                    } else {
                        //INFO: Блокирует проверку других процессов
                        //if (stream_get_contents($pipes[$processKey][1])) {
                        //    $procLastOutput[$processKey] = time();
                        //}
                    }
                } else {
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
            ->where(['type_id' => $groupId, 'is_disable' => false])
            ->orderBy('status DESC')->all();

        //Перебераем объекты внутри группы
        foreach ($objects as $object) {
            //Пингуем каждый объект
            $resStatus = Log::EVENT_ERROR;

            //TODO: Жесткая валидация IP при сохранении (чтоб нельзя было выполнить в консоли левые команды)
            //icmp
            $response = shell_exec('ping -c 1 ' . $object->ip);
            if (
                strstr($response, '0 packets received') === false &&
                preg_match_all('/= \d+\.\d+\/(\d+\.\d+)\/\d+\.\d+\/\d+\.\d+ ms/i', $response, $matches)
            ) {
                //$avgRtt = floatval($matches[1]);
                $resStatus = Log::EVENT_GOOD;
            }

            //tcp
            if ($resStatus == Log::EVENT_ERROR && $object->port > 0) {
                $response = shell_exec('nping --tcp-connect ' . $object->ip . ' -p ' . $object->port . ' -c 1');
                if (preg_match_all('/Avg rtt: (\d+\.\d+)ms/i', $response, $matches)) {
                    //$avgRtt = floatval($matches[1]);
                    $resStatus = Log::EVENT_GOOD;
                }
            }

            //udp
            if ($resStatus == Log::EVENT_ERROR && $object->port_udp > 0) {
                $response = shell_exec('nping --udp ' . $object->ip . ' -p ' . $object->port_udp . ' -c 1');
                if (preg_match_all('/Avg rtt: (\d+\.\d+)ms/i', $response, $matches)) {
                    //$avgRtt = floatval($matches[1]); //14.778
                    $resStatus = Log::EVENT_GOOD;
                }
            }

            //Если статус изменился, создаем лог
            if ($object->status != $resStatus) {
                $log = new Log();
                $log->object_id = $object->id;
                $log->event_num = $resStatus;
                $log->save();

                $object->status = $resStatus;
                $object->save();
            } else {
                $object->touch('updated');
            }
        }
    }
}
