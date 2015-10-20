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
        'rowOptions' => function (Object $model, $key, $index, $grid) {
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
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (Object $data) {
                    $res = [];

                    if ($data->name != '') {
                        $res[] = Html::tag('strong', Html::encode($data->name));
                    }

                    if ($data->address != '') {
                        $res[] = Html::encode($data->address);
                    }

                    if ($data->note != '') {
                        $res[] = Html::encode($data->note);
                    }

                    return implode('<br />', $res);
                },
            ],
            'ip',
            [
                'attribute' => 'port',
                'format' => 'text',
                'headerOptions' => ['width' => 60]
            ],
            [
                'attribute' => 'port_udp',
                'format' => 'text',
                'headerOptions' => ['width' => 60]
            ],
            [
                'attribute' => 'group_id',
                'format' => 'text',
                'value' => function (Object $data) {
                    return $data->group['name'];
                },
                'filter' => Group::getAllList(),
            ],
            [
                'attribute' => 'status',
                'format' => 'text',
                'filter' => Object::$statuses,
                'value' => function (Object $model, $key, $index, $widget) {
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
