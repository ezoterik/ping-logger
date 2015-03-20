<?php
use app\helpers\Date;
use app\models\Object;
use yii\helpers\Html;
use yii\bootstrap\Collapse;

/**
 * @var yii\web\View $this
 * @var app\models\Group[] $groups
 * @var array $lastErrorEventsDates
 */

$this->title = 'Ping Logger';

$itemsCollapse = [];

foreach ($groups as $group) {
    $content = '';
    $isAnyError = false;

    foreach ($group->objects as $object) {
        $text = Html::tag('b', Html::encode($object->name)) . '<br />';
        $text .= Html::encode($object->ip) . '<br />';
        $text .= Html::tag('i', Date::timeAgo($object->updated));

        if (isset($lastErrorEventsDates[$object->id])) {
            $text .= Html::tag('span', $lastErrorEventsDates[$object->id]['last_error_event']);
        } else {
            $text .= Html::tag('span', '');
        }

        $stateClass = '';
        if ($object->group->is_disable || $object->is_disable) {
            $stateClass = ' disable';
        } elseif ($object->status == Object::STATUS_ERROR) {
            $stateClass = ' bad';
        } else {
            $stateClass = ' good';
        }

        $content .= Html::a(
            $text,
            ['object/view', 'id' => $object->id],
            [
                'id' => 'o_' . $object->id,
                'class' => 'obj' . $stateClass,
                'title' => $object->ip . ':' . $object->port,
                'data-toggle' => 'tooltip',
            ]
        );

        if ($object->status <= 0) {
            $isAnyError = true;
        }
    }

    $itemOptions = [];

    if ($group->is_disable) {
        $itemOptions['class'] = 'panel panel-warning';
    } elseif ($isAnyError) {
        $itemOptions['class'] = 'panel panel-danger';
    } elseif (count($group->objects) > 0) {
        $itemOptions['class'] = 'panel panel-success';
    }

    $itemsCollapse[] = [
        'label' => $group->name,
        'content' => $content,
        'options' => $itemOptions,
    ];
}

if (count($itemsCollapse) > 0) {
    $countColumns = 3;

    $itemsCollapse = array_chunk($itemsCollapse, ceil(count($itemsCollapse) / $countColumns), true);
    ?>
    <div class="row">
        <?php
        foreach ($itemsCollapse as $column) {
            $colX = floor(12 / $countColumns);
            echo '<div class="col-md-' . $colX . ' col-sd-' . $colX . '">';

            echo Collapse::widget([
                'items' => $column
            ]);

            echo '</div>';
        }
        ?>
    </div>
    <?php
}
