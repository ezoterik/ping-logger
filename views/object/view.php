<?php

use app\models\Log;
use app\models\Object;
use kartik\icons\Icon;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var app\models\Object $model
 * @var app\models\Log[] $logs
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Objects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="object-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-5 col-sm-6">
            <h2>Информация</h2>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'ip',
                    'port',
                    'port_udp',
                    'name',
                    'address',
                    'note',
                    [
                        'attribute' => 'group_id',
                        'format' => 'text',
                        'value' => ($model->group_id ? $model->group->name : ''),
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => Object::$statuses[$model->status],
                    ],
                    [
                        'attribute' => 'avg_rtt',
                        'format' => 'text',
                        'value' => $model->avg_rtt . ' ms',
                    ],
                    [
                        'attribute' => 'is_disable',
                        'format' => 'boolean',
                    ],
                    'updated:datetime',
                ],
            ]) ?>
            <p>
                <?= Html::a(Icon::show('edit') . 'Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Icon::show('trash') . 'Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы точно хотите удалить этот эллемент?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        </div>
        <div class="col-md-5 col-sm-6">
            <?php
            $headerButton = '';
            if (count($logs) > 0) {
                $headerButton = ' ' . Html::a(Icon::show('trash') . 'Очистить историю', ['delete-logs', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-xs',
                    'data' => [
                        'confirm' => 'Вы точно хотите очистить историю этого объекта?',
                        'method' => 'post',
                    ],
                ]);
            }

            echo Html::tag('h2', 'События' . $headerButton);
            ?>
            <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $logs,
                    'pagination' => false,
                ]),
                'rowOptions' => function ($model, $key, $index, $grid) {
                    $res = [];

                    switch ($model->event_num) {
                        case Log::EVENT_GOOD:
                            $res['class'] = 'success';
                            break;
                        case Log::EVENT_ERROR:
                            $res['class'] = 'danger';
                            break;
                    }

                    return $res;
                },
                'columns' => [
                    'created:datetime',
                    [
                        'attribute' => 'event_num',
                        'format' => 'text',
                        'value' => function ($data) {
                            return Log::$events[$data->event_num];
                        },
                        'headerOptions' => ['width' => 80],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
