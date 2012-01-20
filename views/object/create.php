<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Object $model
 */

$this->title = 'Создание объекта';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Objects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
