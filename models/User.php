<?php

namespace app\models;

use Yii;
use yii\base\BaseObject;
use yii\web\IdentityInterface;

class User extends BaseObject implements IdentityInterface
{
    public int $id;

    public string $username;

    public string $password;

    public string $authKey;

    public string $accessToken;

    /**
     * @inheritdoc
     *
     * @return IdentityInterface|User|null
     */
    public static function findIdentity($id)
    {
        return isset(Yii::$app->params['users'][$id]) ? new static(Yii::$app->params['users'][$id]) : null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (Yii::$app->params['users'] as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    public static function findByUsername(string $username): ?User
    {
        foreach (Yii::$app->params['users'] as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword(string $password): bool
    {
        return ($this->password === $password);
    }
}
