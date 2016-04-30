<?php
use yii\helpers\Url;

/* @var $file common\modules\files\models\FileRecord */
/* @var $model mixed */
/* @var $fieldName string */
/* @var $isMultiple bool Это множественный файл */
?>
<li data-id="<?= $file->getAttribute('id') ?>" data-product-id="<?= $model->getAttribute('id') ?>">
    <a href="<?= $file->getSrc() ?>">Файл: <?= $file->getAttribute('name') ?></a>
    <i class="fileDelete-js"
       data-delete-url="<?= Url::to('deletefile') ?>"
       data-delete-id="<?= $model->getAttribute('id') ?>"
        <? if ($isMultiple): ?>
            data-delete-fileid="<?= $image->getAttribute('id') ?>"
        <? endif ?>
        <? if ($fieldName): ?>
            data-delete-field-name="<?= $fieldName ?>"
        <? endif ?>></i>
</li>