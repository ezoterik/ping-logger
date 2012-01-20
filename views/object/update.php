<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Object $model
 */

$this->title = 'Редактирование объекта: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Object'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="object-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
