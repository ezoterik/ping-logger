<?php
/**
 * @var View $this
 * @var Group $model
 */

use app\models\Group;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('app', 'Editing a group') . ': ' . $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Editing');

echo Html::tag('h1', Html::encode($this->title));

echo $this->render('_form', [
    'model' => $model,
]);
