<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $image common\modules\files\models\Files */
/* @var $this yii\web\View */
?>
<div class="modal-dialog modal-extra-lg">
    <div class="modal-content">
        <? $form = ActiveForm::begin(['id' => 'editImagePopupForm']); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
            <h4 class="modal-title">Редактирование изображения</h4>
        </div>
        <div class="modal-body editImagePopup">
            <div class="imageWrapper">
                <img class="cropper-js" src="<?= $image->getSrc() ?>" alt="<?= $image->alt ?>">
            </div>
            <div class="cropSettings">
                <p class="bg-info cropLabel">Обрезка изображения</p>
                <div class="cropper-run-wide-js cropper-run btn btn-info">[16:9]</div>
                <div class="cropper-run-square-js cropper-run btn btn-info">[5:4]</div>
                <div class="cropper-run-release-js cropper-run- btn btn-info">Без учёта пропорций</div>
                <div class="cropper-run-destroy-js cropper-run btn btn-danger pull-right" style="display: none">Отменить выделение</div>
            </div>

                <?= Html::hiddenInput('id', $image->id) ?>
                <?= Html::hiddenInput('cropData', '', ['id' => 'cropData']) ?>
                <?= $form->field($image, 'name')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($image, 'alt')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($image, 'title')->textInput(['maxlength' => 255]) ?>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
        <? $form->end()?>
    </div>
</div>