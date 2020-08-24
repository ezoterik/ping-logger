<?php
/**
 * @var View $this
 * @var Group $model
 * @var ActiveForm $form
 */

use app\models\Group;
use kartik\icons\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

$form = ActiveForm::begin();
echo $form->field($model, 'name')->textInput(['maxlength' => true]);
echo $form->field($model, 'is_disable')->checkbox();
?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Icon::show('check') . Yii::t('app', 'Create') : Icon::show('save', ['framework' => Icon::FAR]) . Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php
ActiveForm::end();
