<?php
use app\assets\MonitorBoxAsset;
use kartik\icons\Icon;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 */

MonitorBoxAsset::register($this);

$this->title = 'Ping Logger';

echo Html::tag('div', Icon::show('refresh', ['class' => 'fa-3x fa-fw fa-spin']), ['id' => 'monitor-box']);
