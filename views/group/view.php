<?php

use app\models\Log;
use kartik\icons\Icon;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var app\models\Group $model
 * @var app\models\Object[] $objects
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6 col-sm-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
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
        <div class="col-md-6 col-sm-6">
            <h2>Объекты</h2>
            <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $objects,
                    'pagination' => false,
                ]),
                'rowOptions' => function ($model, $key, $index, $grid) {
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
</div>
