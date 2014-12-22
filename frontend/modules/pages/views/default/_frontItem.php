<?php

/* @var $this yii\web\View */
/* @var common\modules\catalog\models\Category $category */
?>
<div class="small-4 columns">
    <div class="item">
        <a href="<?= $category->getUrl() ?>">
            <div class="thumb">
                <? if ($image = $category->image): ?>
                <img src="<?= $image->getThumbSrc(278, 257) ?>" alt="<?= $image->getAttribute('alt') ?>">
                <? endif ?>
            </div>
            <div class="caption">
                <p><?= $category->getAttribute('name') ?></p>
            </div>
        </a>
    </div>
</div>