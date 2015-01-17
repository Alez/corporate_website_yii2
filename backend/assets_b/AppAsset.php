<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets_b;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/assets_b';
    public $css = [
        'js/bootstrap-datepicker/css/datepicker3.css',
        'js/cropper/cropper.css',
        'css/site.css',
    ];
    public $js = [
        'js/bootstrap-datepicker/js/bootstrap-datepicker.js',
        'js/bootstrap-datepicker/js/locales/bootstrap-datepicker.ru.js',
        'js/cropper/cropper.js',
        'js/adminModule.js',
        'js/imageModule.js',
        'js/filesModule.js',
        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'backend\assets_b\AdminLTEAsset',
        'backend\assets_b\PagesAsset',
    ];
}
