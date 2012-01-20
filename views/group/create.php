<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var app\models\Group $model
 */

$this->title = 'Создание группы';
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
