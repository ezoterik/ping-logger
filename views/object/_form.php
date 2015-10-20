<?php

use app\models\Group;
use app\models\Object;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var app\models\Object $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="object-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-sm-4"><?= $form->field($model, 'ip')->textInput(['maxlength' => 15]) ?></div>
        <div class="col-sm-4"><?= $form->field($model, 'port')->input('number', ['min' => 1, 'max' => 65535]) ?></div>
        <div class="col-sm-4"><?= $form->field($model, 'port_udp')->input('number', ['min' => 0, 'max' => 65535]) ?></div>
    </div>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group_id')->dropDownList(Group::getAllList()) ?>

    <?= $form->field($model, 'is_disable')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Icon::show('check') . Yii::t('app', 'Create') : Icon::show('save') . Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>