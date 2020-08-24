<?php
/**
 * @var View $this
 */

use app\assets\MonitorBoxAsset;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

MonitorBoxAsset::register($this);

$this->title = 'Ping Logger';

echo Html::tag('div', Icon::show('sync', ['class' => 'fa-3x fa-fw fa-spin']), [
    'id' => 'monitor-box',
    'data' => [
        'url' => Url::to(['site/get-monitor-data']),
    ],
]);
