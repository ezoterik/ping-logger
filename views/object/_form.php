<?php
/**
 * @var View $this
 * @var PingObject $model
 * @var ActiveForm $form
 */

use app\models\Group;
use app\models\PingObject;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

$form = ActiveForm::begin();

echo $form->field($model, 'name')->textInput(['maxlength' => true]);
echo $form->field($model, 'address')->textInput(['maxlength' => true]);
?>
    <div class="row">
        <div class="col-sm-4"><?= $form->field($model, 'ip')->textInput(['maxlength' => 15]) ?></div>
        <div class="col-sm-4"><?= $form->field($model, 'port')->input('number', ['min' => 1, 'max' => 65535]) ?></div>
        <div class="col-sm-4"><?= $form->field($model, 'port_udp')->input('number', ['min' => 0, 'max' => 65535]) ?></div>
    </div>
<?php

echo $form->field($model, 'note')->textInput(['maxlength' => true]);
echo $form->field($model, 'group_id')->dropDownList(Group::getAllList());
echo $form->field($model, 'is_disable')->checkbox();
?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Icon::show('check') . Yii::t('app', 'Create') : Icon::show('save', ['framework' => Icon::FAR]) . Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php
ActiveForm::end();
