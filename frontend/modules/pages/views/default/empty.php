<?php

use common\modules\pages\models\PagesParams;
use common\modules\pages\models\Pages;

/* @var $this yii\web\View */

$this->params['menu'] = Pages::get('slug');
$this->title = Pages::get('name');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="small-12 columns">
            <h2><?= $this->title ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="small-12 columns">
            <?= PagesParams::get('content') ?>
        </div>
        <div class="small-12 columns">
            <br>
            <a href="/">Вернуться на главную</a>
        </div>
    </div>
</div>