<?php

use common\modules\pages\models\PagesParams;
use common\modules\pages\models\Pages;

/* @var $this yii\web\View */
/* @var $files common\modules\files\models\files[] */

$this->params['menu'] = Pages::get('slug');
$this->title = Pages::get('name');
?>
<!--<div class="banner"-->
<?// if ($value = PagesParams::get('jumbotron_image')): ?>
<!--     style="background: url(--><?//= $files[$value]->getThumbSrc(1920, 628) ?>/*)"*/
<?// endif ?><!-->-->
<!---->
<!--    <div class="wrap_fixed">-->
<!--        <div class="banner__title">-->
<!--            <h1>--><?//= PagesParams::get('jumbotron_text') ?><!--</h1>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!---->
<!--<div class="b-about">-->
<!--    <div class="wrap_fixed">-->
<!--        <div class="row">-->
<!--            <div class="small-4 columns"><div class="thumb-quality"></div></div>-->
<!--            <div class="small-8 columns">-->
<!--                <div class="b-about__info">-->
<!--                    <p>--><?//= PagesParams::get('about') ?><!--</p>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!---->
<!--<div class="wrap_fixed b-range">-->
<!--    <div class="row">-->
<!--        <div class="small-12 columns">-->
<!--            <h2 class="text-center">Наш ассортимент</h2>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <div class="b-range__items">-->
<!---->
<!--        <div class="row">-->
<!--            --><?//
//            $categories = Category::findAll(['parent_id' => 0]);
//            $per = 3;
//            $count = 0;
//            $total = count($categories);
//            foreach ($categories as $category): ?>
<!--            --><?//= $this->render('_frontItem', [
//                    'category' => $category,
//                ]) ?>
<!--            --><?// $count++;
//            if ($count % $per === 0 && $total !== $count): ?>
<!--        </div>-->
<!--        <div class="row">-->
<!--            --><?// endif ?>
<!--            --><?//
//            endforeach; ?>
<!--        </div>-->
<!---->
<!--    </div>-->
<!---->
<!--</div>-->
<!---->
<!--<div class="wrap_fixed b-advantage">-->
<!--    <div class="row">-->
<!--        <div class="small-6 columns">-->
<!--            <div class="b-advantage__title text-center">-->
<!--                <h2>Наши преимущества</h2>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="small-6 columns">-->
<!--            <div class="b-advantage__info">-->
<!--                <p>--><?//= PagesParams::get('our_strengths') ?><!--</p>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!---->
<!--<div class="b-icons">-->
<!--    <div class="wrap_fixed">-->
<!--        <div class="row">-->
<!--            <div class="small-3 columns">-->
<!--                <div class="icon icon1"></div>-->
<!--                <h4 class="text-center">удобно</h4>-->
<!--                <p>--><?//= PagesParams::get('convenient') ?><!--</p>-->
<!--            </div>-->
<!--            <div class="small-3 columns">-->
<!--                <div class="icon icon2"></div>-->
<!--                <h4 class="text-center">Бесплатно</h4>-->
<!--                <p>--><?//= PagesParams::get('free') ?><!--</p>-->
<!--            </div>-->
<!--            <div class="small-3 columns">-->
<!--                <div class="icon icon3"></div>-->
<!--                <h4 class="text-center">Выгодно</h4>-->
<!--                <p>--><?//= PagesParams::get('benefit') ?><!--</p>-->
<!--            </div>-->
<!--            <div class="small-3 columns">-->
<!--                <div class="icon icon4"></div>-->
<!--                <h4 class="text-center">Стерильно</h4>-->
<!--                <p>--><?//= PagesParams::get('sterile') ?><!--</p>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->