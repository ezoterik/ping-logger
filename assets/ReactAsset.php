<?php

namespace app\assets;

use yii\web\AssetBundle;

class ReactAsset extends AssetBundle
{
    public $sourcePath = '@app/assets/react';

    public $css = [];

    public $js = [
        'js/react-with-addons.min.js',
        'js/JSXTransformer.js',
    ];

    public $depends = [];
}
