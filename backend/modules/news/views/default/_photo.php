<?php
use yii\helpers\Url;

/* @var $image common\modules\files\models\Files */
/* @var $pageParam common\modules\news\models\News */
?>
<li data-id="<?= $image->getAttribute('id') ?>" data-product-id="<?= $pageParam->getAttribute('id') ?>">
    <img src="<?= $image->getSrc() ?>"
         alt="<?= $image->getAttribute('alt') ?>"
         data-edit-url="<?= Url::toRoute('/files/image/imageeditpopup') ?>">
    <i class="fileDelete-js"
        data-delete-url="<?= Url::to('deletefile') ?>"
        data-delete-id="<?= $pageParam->id ?>"
        data-delete-fileid="<?= $image->getAttribute('id') ?>"></i>
</li>