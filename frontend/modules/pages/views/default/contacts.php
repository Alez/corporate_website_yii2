<?php

use common\modules\service_info\models\ServiceInfo;
use common\modules\pages\models\PagesParams;
use common\modules\pages\models\Pages;
use frontend\assets\GoogleMapAsset;

GoogleMapAsset::register($this);

/* @var $this yii\web\View */
/* @var $files common\modules\files\models\files[] */

//$this->params['menuRoute'] = '';
$this->title = Pages::get('name');
?>
<div class="wrap_fixed b-advantage our_contacts">
    <div class="row">
        <div class="small-6 columns">
            <div class="b-advantage__title text-center">
                <h2><?= Pages::get('name') ?></h2>
            </div>
        </div>
        <div class="small-6 columns">
            <div class="b-advantage__info">
                <div class="item"><span class="color1">Телефон:</span><?= ServiceInfo::get('cityCode') ?> <?= ServiceInfo::get('phone') ?></div>
                <div class="item"><span class="color1">Адрес:</span><?= ServiceInfo::get('address') ?></div>
                <div class="item"><span class="color1">E-mail:</span><?= ServiceInfo::get('email') ?></div>
                <div class="item"><span class="color1">Режим работы:</span>с <?= ServiceInfo::get('workingHours') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="bg_main">
    <div class="wrap_fixed b-advantage find_us">
        <div class="row">
            <div class="small-6 columns">
                <div class="b-advantage__title text-center">
                    <h2>Как нас найти</h2>
                </div>
            </div>
            <div class="small-6 columns">
                <div class="b-advantage__info">
                    <p><?= PagesParams::get('how_to_find') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="b-map">
    <script>
        function initialize() {
            var myLatlng = new google.maps.LatLng(<?= PagesParams::get('latitude') ?>,<?= PagesParams::get('longitude') ?>),
                mapOptions = {
                    zoom: 15,
                    center: myLatlng,
                    mapTypeControl: false,
                    scaleControl: false,
                    streetViewControl: false,
                    overviewMapControl: false,
                    scrollwheel: false,
                    disableDoubleClickZoom: true
                },
                map = new google.maps.Map(document.querySelector('.b-map'), mapOptions),
                marker = new google.maps.Marker({
                    position: myLatlng,
                    map: map,
                    title: 'Hello World!'
                });
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</div>