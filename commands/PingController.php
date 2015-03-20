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
        $groups = Group::find()->all();

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
        /** @var Object[] $objects */
        $objects = Object::find()
            ->where(['type_id' => $groupId])
            ->orderBy('status DESC')->all();

        //Перебераем объекты внутри группы
        foreach ($objects as $object) {
            //Пингуем каждый объект
            $resStatus = Log::EVENT_ERROR;
            $fp = @fsockopen($object->ip, $object->port, $errno, $errstr, 10);
            if ($fp) {
                $resStatus = Log::EVENT_GOOD;
                fclose($fp);
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
