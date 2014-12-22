<?php

use common\modules\pages\models\PagesParams;
use common\modules\pages\models\Pages;

/* @var $this yii\web\View */
?>
<div class="wrap_fixed">
    <div class="row">
        <div class="small-12 columns">
            <h2><?= Pages::get('name') ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="small-12 columns">
            <?= PagesParams::get('content') ?>
        </div>
    </div>
</div>