<?php

namespace app\assets;

use yii\web\AssetBundle;

class MonitorBoxAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/monitor-box';

    public $css = [];

    public $js = [
        'js/monitor-box.js',
    ];

    public $jsOptions = [
        'type' => 'text/jsx'
    ];

    public $depends = [
        'omnilight\assets\MomentAsset',
        'app\assets\ReactAsset',
    ];
}
