<?php
/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var PingObjectSearch $searchModel
 */

use app\models\Group;
use app\models\PingObject;
use app\models\search\PingObjectSearch;
use kartik\icons\Icon;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('app', 'Objects');

$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo Html::tag('p', Html::a(Icon::show('plus') . Yii::t('app', 'Create a object'), ['create'], ['class' => 'btn btn-success']));

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions' => function (PingObject $model) {
        $res = [];

        if ($model->is_disable) {
            $res['class'] = 'warning';
        }

        return $res;
    },
    'columns' => [
        [
            'attribute' => 'id',
            'headerOptions' => ['width' => 50],
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function (PingObject $data) {
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
            'headerOptions' => ['width' => 60],
        ],
        [
            'attribute' => 'port_udp',
            'headerOptions' => ['width' => 60],
        ],
        [
            'attribute' => 'group_id',
            'value' => function (PingObject $data) {
                return $data->group['name'];
            },
            'filter' => Group::getAllList(),
        ],
        [
            'attribute' => 'status',
            'filter' => PingObject::$statuses,
            'value' => function (PingObject $model) {
                return PingObject::$statuses[$model->status];
            },
            'headerOptions' => ['width' => 80],
        ],
        [
            'attribute' => 'is_disable',
            'format' => 'boolean',
            'filter' => [0 => 'Нет', 1 => 'Да'],
        ],
        'updated_at:datetime',
        [
            'class' => ActionColumn::class,
            'contentOptions' => ['class' => 'action-column'],
            'buttons' => [
                'view' => function ($url) {
                    $options = [
                        'title' => Yii::t('app', 'View'),
                        'data' => [
                            'toggle' => 'tooltip',
                            'pjax' => '0',
                        ],
                    ];

                    return Html::a(Icon::show('eye', ['framework' => Icon::FAR, 'space' => false]), $url, $options);
                },
                'update' => function ($url) {
                    $options = [
                        'title' => Yii::t('app', 'Update'),
                        'data' => [
                            'toggle' => 'tooltip',
                            'pjax' => '0',
                        ],
                    ];

                    return Html::a(Icon::show('edit', ['framework' => Icon::FAR, 'space' => false]), $url, $options);
                },
                'delete' => function ($url) {
                    $options = [
                        'title' => Yii::t('app', 'Delete'),
                        'class' => 'delete',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                            'toggle' => 'tooltip',
                            'pjax' => '0',
                        ],
                    ];

                    return Html::a(Icon::show('trash-alt', ['framework' => Icon::FAR, 'space' => false]), $url, $options);
                },
            ],
            'headerOptions' => ['width' => 1],
        ],
    ],
]);
