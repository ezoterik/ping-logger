<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace app\assets;

use yii\web\AssetBundle;

class ReactAsset extends AssetBundle
{
    public $sourcePath = '@bower/react';

    public $css = [];

    public $js = [
        'JSXTransformer.js',
        'react-with-addons.js',
    ];

    public $depends = [];
}
