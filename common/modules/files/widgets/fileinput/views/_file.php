<?php
use yii\helpers\Url;

/* @var $file common\modules\files\models\Files */
/* @var $model mixed */
/* @var $fieldName string */
/* @var $isMultiple bool Это множественный файл */
?>
<li data-id="<?= $file->getAttribute('id') ?>" data-product-id="<?= $model->getAttribute('id') ?>">
    <a href="<?= $file->getSrc() ?>">Файл: <?= $file->getAttribute('name') ?></a>
    <i class="fileDelete-js"
       data-url="<?= Url::to('/files/image/deletefile') ?>"
       data-id="<?= $model->getAttribute('id') ?>"
        <? if ($isMultiple): ?>
            data-fileid="<?= $image->getAttribute('id') ?>"
        <? endif ?>
        <? if ($fieldName): ?>
            data-field="<?= $fieldName ?>"
        <? endif ?>></i>
</li>