<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);
$I->see('Ping Logger');
$I->seeLink('Войти');
$I->click('Войти');
$I->see('Пожалуйста, заполните следующие поля для входа');
