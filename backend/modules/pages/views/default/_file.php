<?php
use yii\helpers\Url;

/* @var $file common\modules\files\models\Files */
/* @var $pageParam common\modules\pages\models\PagesParams */
?>
<div class="fileEditWrapper-js">
    <a href="<?= $pageParam->file->getSrc() ?>">Название: <?= $file->getAttribute('name') ?></a>
    <i class="fileDelete fileDelete-js fa fa-times"
       data-delete-url="<?= Url::to('deletefile') ?>"
       data-delete-id="<?= $pageParam->id ?>"
       data-delete-fileid="<?= $file->getAttribute('id') ?>"></i>
</div>