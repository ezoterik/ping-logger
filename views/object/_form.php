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

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'ip')->textInput(['maxlength' => 15]) ?>

    <?= $form->field($model, 'port')->textInput() ?>

    <?= $form->field($model, 'type_id')->dropDownList(Group::getAllList()) ?>

    <?= $form->field($model, 'is_disable')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Icon::show('check') . Yii::t('app', 'Create') : Icon::show('save') . Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
