<?php
/**
 * @var View $this
 * @var PingObject $model
 */

use app\models\PingObject;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('app', 'Editing an object') . ': ' . $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Object'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Editing');

echo Html::tag('h1', Html::encode($this->title));

echo $this->render('_form', [
    'model' => $model,
]);
