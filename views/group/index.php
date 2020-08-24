<?php
/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var GroupSearch $searchModel
 */

use app\models\Group;
use app\models\search\GroupSearch;
use kartik\icons\Icon;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('app', 'Groups');

$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));

echo Html::tag('p', Html::a(Icon::show('plus') . Yii::t('app', 'Create a group'), ['create'], ['class' => 'btn btn-success']));

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions' => function (Group $model) {
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
        'name',
        [
            'attribute' => 'is_disable',
            'format' => 'boolean',
            'filter' => [0 => 'Нет', 1 => 'Да'],
        ],
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
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item with all objects?'),
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
