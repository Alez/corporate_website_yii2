<?php

use common\modules\pages\models\Pages;

/* @var $this yii\web\View */
/* @var $files common\modules\files\models\files[] */

//$this->params['menuRoute'] = '';
$this->title = Pages::get('name');
?>
<div class="wrap_fixed b-category">
    <div class="row">
        <div class="small-12 columns">
            <h2 class="text-center">Категории товаров</h2>
            <div id="skelet" class="text_ban">
                <div class="skelet_item" id="skull"><div class="dashed_circle"><span>&nbsp;</span></div></div>
                <div class="skelet_item" id="rib"><div class="dashed_circle"><span>&nbsp;</span></div></div>
                <div class="skelet_item" id="backbone"><div class="dashed_circle"><span>&nbsp;</span></div></div>
                <div class="skelet_item" id="ass"><div class="dashed_circle"><span>&nbsp;</span></div></div>
                <div class="skelet_item" id="thigh"><div class="dashed_circle"><span>&nbsp;</span></div></div>
                <div class="skelet_item" id="leg"><div class="dashed_circle"><span>&nbsp;</span></div></div>
                <div class="skelet_item" id="foot"><div class="dashed_circle"><span>&nbsp;</span></div></div>
                <div class="skelet_item" id="hand"><div class="dashed_circle"><span>&nbsp;</span></div></div>
                <div class="skelet_item" id="shoulder"><div class="dashed_circle"><span>&nbsp;</span></div></div>
                <div class="skelet_item" id="arm"><div class="dashed_circle"><span>&nbsp;</span></div></div>
            </div>
            <div class="skelet__catalog">
                <div id="scrollbar" class="scrollbar-inner">
                    <div class="skelet__catalog_media-info">
                        <div class="row">
                            <div class="small-3 columns"><div class="skelet__catalog_media"><img src="img/product/plastin.jpg" alt=""></div></div>
                            <div class="small-9 columns"><div class="skelet__catalog_info">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea consequuntur explicabo blanditiis repellendus, tenetur totam.</div></div>
                        </div>
                    </div>
                    <div class="skelet__catalog_media-info">
                        <div class="row">
                            <div class="small-3 columns"><div class="skelet__catalog_media"><img src="img/product/plastin.jpg" alt=""></div></div>
                            <div class="small-9 columns"><div class="skelet__catalog_info">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea consequuntur explicabo blanditiis repellendus, tenetur totam.</div></div>
                        </div>
                    </div>
                    <div class="skelet__catalog_media-info">
                        <div class="row">
                            <div class="small-3 columns"><div class="skelet__catalog_media"><img src="img/product/plastin.jpg" alt=""></div></div>
                            <div class="small-9 columns"><div class="skelet__catalog_info">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea consequuntur explicabo blanditiis repellendus, tenetur totam.</div></div>
                        </div>
                    </div>
                    <div class="skelet__catalog_media-info">
                        <div class="row">
                            <div class="small-3 columns"><div class="skelet__catalog_media"><img src="img/product/plastin.jpg" alt=""></div></div>
                            <div class="small-9 columns"><div class="skelet__catalog_info">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea consequuntur explicabo blanditiis repellendus, tenetur totam.</div></div>
                        </div>
                    </div>
                    <div class="skelet__catalog_media-info">
                        <div class="row">
                            <div class="small-3 columns"><div class="skelet__catalog_media"><img src="img/product/plastin.jpg" alt=""></div></div>
                            <div class="small-9 columns"><div class="skelet__catalog_info">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea consequuntur explicabo blanditiis repellendus, tenetur totam.</div></div>
                        </div>
                    </div>
                    <div class="skelet__catalog_media-info">
                        <div class="row">
                            <div class="small-3 columns"><div class="skelet__catalog_media"><img src="img/product/plastin.jpg" alt=""></div></div>
                            <div class="small-9 columns"><div class="skelet__catalog_info">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea consequuntur explicabo blanditiis repellendus, tenetur totam.</div></div>
                        </div>
                    </div>
                    <div class="skelet__catalog_media-info">
                        <div class="row">
                            <div class="small-3 columns"><div class="skelet__catalog_media"><img src="img/product/plastin.jpg" alt=""></div></div>
                            <div class="small-9 columns"><div class="skelet__catalog_info">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea consequuntur explicabo blanditiis repellendus, tenetur totam.</div></div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="/products"><button type="button" class="btn_style">Каталог товаров</button></a>
        </div>
    </div>
</div>