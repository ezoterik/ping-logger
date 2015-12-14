<?php

namespace app\components\bootstraps;

use app\models\User;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\web\UserEvent;

/**
 * Сохраняет в лог факт логина пользователя на сайте
 */
class LoginLogger implements BootstrapInterface
{

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Event::on(
            \yii\web\User::className(),
            \yii\web\User::EVENT_AFTER_LOGIN,
            [$this, 'afterLogin']
        );
    }

    /**
     * Сохраняет данные о входе в лог
     *
     * @param UserEvent $event
     */
    public function afterLogin(UserEvent $event)
    {
        $user = User::findIdentity($event->identity->getId());

        $data = [
            Yii::$app->formatter->asDatetime(time(), 'yyyy-MM-dd HH:mm:ss'),
            (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0'),
            $user->username,
        ];

        file_put_contents(Yii::getAlias('@runtime/logs/login.log'), implode("\t", $data) . "\n", FILE_APPEND | LOCK_EX);
    }
}
