<?php
use yii\helpers\Url;

/* @var $image common\modules\files\models\Files */
/* @var $model mixed */
/* @var $fieldName string */
/* @var $isMultiple bool Это множественный инпут? */
?>
<li data-id="<?= $image->getAttribute('id') ?>" data-product-id="<?= $model->getAttribute('id') ?>">
    <img src="<?= $image->getSrc() ?>"
         alt="<?= $image->getAttribute('alt') ?>"
         data-edit-url="<?= Url::toRoute('/files/image/imageeditpopup') ?>">
    <i class="fileDelete-js"
        data-url="<?= Url::to('/files/image/deletefile') ?>"
        data-id="<?= $model->getAttribute('id') ?>"
        data-model="<?= $model->className() ?>"
        <? if ($isMultiple): ?>
            data-fileid="<?= $image->getAttribute('id') ?>"
        <? endif ?>
        <? if ($fieldName): ?>
            data-field="<?= $fieldName ?>"
        <? endif ?>></i>
</li>