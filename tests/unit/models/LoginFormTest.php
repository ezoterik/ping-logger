<?php

namespace tests\unit\models;

use app\models\form\LoginForm;
use Codeception\Test\Unit;
use Yii;

class LoginFormTest extends Unit
{
    protected function _after()
    {
        Yii::$app->user->logout();
    }

    public function testLoginNoUser()
    {
        $model = new LoginForm([
            'username' => 'not_existing_username',
            'password' => 'not_existing_password',
        ]);

        expect_not($model->login());
        expect_that(Yii::$app->user->isGuest);
    }

    public function testLoginWrongPassword()
    {
        $model = new LoginForm([
            'username' => 'demo',
            'password' => 'wrong_password',
        ]);

        expect_not($model->login());
        expect_that(Yii::$app->user->isGuest);
        expect($model->errors)->hasKey('password');
    }

    public function testLoginCorrect()
    {
        $model = new LoginForm([
            'username' => 'admin',
            'password' => 'admin',
        ]);

        expect_that($model->login());
        expect_not(Yii::$app->user->isGuest);
        expect($model->errors)->hasntKey('password');
    }
}
