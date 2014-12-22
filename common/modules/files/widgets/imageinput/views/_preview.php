<?php

/* @var $widget common\modules\files\widgets\imageinput\ImageInput */
?>
<div class="form-group">
    <label class="control-label" for="productImgInput">
        <?= $widget->name ?>
    </label>
    <div class="galleryEditWrapper">
        <ul id="galleryPreviewList" class="galleryPreviewList galleryList-js">

<?
foreach ($widget->imageSource as $image) {
    echo $widget->render('_photo.php', [
        'image'      => $image,
        'model'      => $widget->model,
        'fieldName'  => !is_null($widget->fieldName) ? $widget->fieldName : $widget->attribute,
        'isMultiple' => isset($widget->options['multiple']),
    ]);
} ?>
        </ul>
    </div>
</div>