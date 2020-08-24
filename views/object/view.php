<?php
/**
 * @var View $this
 * @var PingObject $model
 * @var Log[] $logs
 */

use app\models\Log;
use app\models\PingObject;
use kartik\icons\Icon;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Objects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));
?>
<div class="row">
    <div class="col-md-5 col-sm-6">
        <h2><?= Yii::t('app', 'Information') ?></h2>
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
                    'value' => ($model->group_id ? $model->group->name : ''),
                ],
                [
                    'attribute' => 'status',
                    'value' => PingObject::$statuses[$model->status],
                ],
                [
                    'attribute' => 'avg_rtt',
                    'value' => $model->avg_rtt . ' ms',
                ],
                [
                    'attribute' => 'is_disable',
                    'format' => 'boolean',
                ],
                'updated_at:datetime',
            ],
        ]) ?>
        <p>
            <?= Html::a(Icon::show('edit', ['framework' => Icon::FAR]) . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Icon::show('trash-alt', ['framework' => Icon::FAR]) . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>
    <div class="col-md-5 col-sm-6">
        <?php
        $headerButton = '';
        if (count($logs) > 0) {
            $headerButton = ' ' . Html::a(Icon::show('trash-alt', ['framework' => Icon::FAR]) . Yii::t('app', 'Clear history'), ['delete-logs', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-xs',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to clear the history of this object?'),
                        'method' => 'post',
                    ],
                ]);
        }

        echo Html::tag('h2', Yii::t('app', 'Events') . $headerButton);

        echo GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $logs,
                'pagination' => false,
            ]),
            'rowOptions' => function (Log $model) {
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
                'created_at:datetime',
                [
                    'attribute' => 'event_num',
                    'value' => function (Log $data) {
                        return Log::$events[$data->event_num];
                    },
                    'headerOptions' => ['width' => 80],
                ],
            ],
        ]);
        ?>
    </div>
</div>
