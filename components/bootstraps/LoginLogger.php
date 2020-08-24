<?php

namespace app\components\bootstraps;

use app\models\User;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\web\User as YiiWebUser;
use yii\web\UserEvent;

/**
 * Сохраняет в лог факт логина пользователя на сайте
 */
class LoginLogger implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        Event::on(
            YiiWebUser::class,
            YiiWebUser::EVENT_AFTER_LOGIN,
            [$this, 'afterLogin']
        );
    }

    /**
     * Сохраняет данные о входе в лог
     *
     * @param UserEvent $event
     *
     * @throws InvalidConfigException
     */
    public function afterLogin(UserEvent $event): void
    {
        $user = User::findIdentity($event->identity->getId());

        //Обход проблемы, когда nginx в роли прокси
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
        }

        $data = [
            Yii::$app->formatter->asDatetime(time(), 'yyyy-MM-dd HH:mm:ss'),
            Yii::$app->request->getUserIP(),
            $user->username,
        ];

        file_put_contents(Yii::getAlias('@runtime/logs/login.log'), implode("\t", $data) . "\n", FILE_APPEND | LOCK_EX);
    }
}
