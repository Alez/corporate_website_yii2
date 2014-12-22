<?php

use common\modules\pages\models\PagesParams;
use common\modules\pages\models\Pages;

/* @var $this yii\web\View */
/* @var $files common\modules\files\models\files[] */

//$this->params['menuRoute'] = '';
$this->title = Pages::get('name');
?>
<div class="b-about">
    <div class="wrap_fixed">
        <div class="row">
            <div class="small-4 columns"><div class="thumb-quality"></div></div>
            <div class="small-8 columns">
                <div class="b-about__info">
                    <h2><?= Pages::get('name') ?></h2>
                    <p><?= PagesParams::get('about') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrap_fixed b-advantage">
    <div class="row">
        <div class="small-6 columns">
            <div class="b-advantage__title text-center">
                <h2>Наши преимущества</h2>
            </div>
        </div>
        <div class="small-6 columns">
            <div class="b-advantage__info">
                <p><?= PagesParams::get('our_strengths') ?></p>
            </div>
        </div>
    </div>
</div>

<div class="b-icons">
    <div class="wrap_fixed">
        <div class="row">
            <div class="small-3 columns">
                <div class="icon icon1"></div>
                <h4 class="text-center">удобно</h4>
                <p><?= PagesParams::get('convenient') ?></p>
            </div>
            <div class="small-3 columns">
                <div class="icon icon2"></div>
                <h4 class="text-center">Бесплатно</h4>
                <p><?= PagesParams::get('free') ?></p>
            </div>
            <div class="small-3 columns">
                <div class="icon icon3"></div>
                <h4 class="text-center">Выгодно</h4>
                <p><?= PagesParams::get('benefit') ?></p>
            </div>
            <div class="small-3 columns">
                <div class="icon icon4"></div>
                <h4 class="text-center">Стерильно</h4>
                <p><?= PagesParams::get('sterile') ?></p>
            </div>
        </div>
    </div>
</div>