<?php

use app\models\Group;
use app\models\Object;
use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\ObjectSearch $searchModel
 */

$this->title = Yii::t('app', 'Objects');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create a object'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $res = [];

            if ($model->is_disable) {
                $res['class'] = 'warning';
            }

            return $res;
        },
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'text',
                'headerOptions' => ['width' => 50]
            ],
            'name',
            'ip',
            [
                'attribute' => 'port',
                'format' => 'text',
                'headerOptions' => ['width' => 60]
            ],
            [
                'attribute' => 'type_id',
                'format' => 'text',
                'value' => function ($data) {
                    return $data->group['name'];
                },
                'filter' => Group::getAllList(),
            ],
            [
                'attribute' => 'status',
                'format' => 'text',
                'filter' => Object::$statuses,
                'value' => function ($model, $key, $index, $widget) {
                    return Object::$statuses[$model->status];
                },
                'headerOptions' => ['width' => 80],
            ],
            [
                'attribute' => 'is_disable',
                'format' => 'boolean',
                'filter' => [0 => 'Нет', 1 => 'Да'],
            ],
            'updated',
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['width' => 80]
            ],
        ],
    ]); ?>

</div>
