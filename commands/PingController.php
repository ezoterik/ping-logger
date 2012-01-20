<?php

namespace app\commands;

use app\models\Log;
use app\models\Object;
use Yii;
use yii\console\Controller;

class PingController extends Controller
{
    /**
     * Пингует все объекты
     *
     * @param int $timeoutStep Таймаут в секкундах для одного объекта
     */
    public function actionIndex($timeoutStep = 10)
    {
        //Защита от параллельного запуска
        if (!Yii::$app->mutex->acquire('ping_index')) {
            return;
        }

        //Узнаем список всех IP (объектов)
        /** @var \app\models\Object[] $objects */
        $objects = Object::find()->all();
        //Перебераем объекты
        foreach ($objects as $object) {
            //Пингуем каждый объект
            $resStatus = Log::EVENT_ERROR;
            $fp = @fsockopen($object->ip, $object->port, $errno, $errstr, $timeoutStep);
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