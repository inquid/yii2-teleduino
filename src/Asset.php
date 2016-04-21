<?php

namespace madand\teleduino;

use yii\web\AssetBundle;

/**
 * Class Asset
 *
 * @package gogl92\teleduino\assets
 * @author Andriy Kmit' <dev@madand.net>
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@madand/teleduino/assets';

    public $js = [
        'js/jquery.tools.min.js',
        'js/teleduino.js',
    ];

    public $css = ['css/teleduino.css'];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}

