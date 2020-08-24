<?php
/**
 * @var View $this
 * @var PingObject $model
 */

use app\models\PingObject;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('app', 'Creating an object');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Objects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo $this->render('_form', [
    'model' => $model,
]);
