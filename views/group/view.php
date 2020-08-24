<?php
/**
 * @var View $this
 * @var Group $model
 * @var PingObject[] $objects
 */

use app\models\Group;
use app\models\Log;
use app\models\PingObject;
use kartik\icons\Icon;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Html::tag('h1', Html::encode($this->title));
?>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'name',
                [
                    'attribute' => 'is_disable',
                    'format' => 'boolean',
                ],
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
    <div class="col-md-6 col-sm-6">
        <h2><?= Yii::t('app', 'Objects') ?></h2>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $objects,
                'pagination' => false,
            ]),
            'rowOptions' => function (PingObject $model) {
                $res = [];

                switch ($model->status) {
                    case Log::EVENT_GOOD:
                        $res['class'] = 'danger';
                        break;
                    case Log::EVENT_ERROR:
                        $res['class'] = 'success';
                        break;
                }

                return $res;
            },
            'columns' => [
                'id',
                'name',
                'ip',
                'port',
            ],
        ]); ?>
    </div>
</div>
