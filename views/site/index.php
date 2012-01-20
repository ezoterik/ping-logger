<?php
use app\helpers\Date;
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

        $content .= Html::a(
            $text,
            ['object/view', 'id' => $object->id],
            [
                'id' => 'o_' . $object->id,
                'class' => 'obj' . ($object->status > 0 ? ' good' : ' bad'),
                'title' => $object->ip . ':' . $object->port,
                'data-toggle' => 'tooltip',
            ]
        );

        if ($object->status <= 0) {
            $isAnyError = true;
        }
    }

    $itemOptions = [];

    if ($isAnyError) {
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
<?php } ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>