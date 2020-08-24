<?php
/**
 * @var View $this
 * @var Group $model
 */

use app\models\Group;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('app', 'Creating a group');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo $this->render('_form', [
    'model' => $model,
]);
