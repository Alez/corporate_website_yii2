<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\modules\news\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-form">

    <?php $form = ActiveForm::begin([
            'method' => 'POST',
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ]); ?>

    <?= $form->field($model, 'name')->textInput([
            'maxlength' => 255,
            'class' => 'translitFrom-js form-control',
        ]) ?>

    <?= Html::checkbox('', true, [
            'label' => 'Транслитерация',
            'class' => 'translitEnabled-js'
        ]) ?>

    <?= $form->field($model, 'slug')->textInput([
            'maxlength' => 255,
            'class' => 'translitTo-js form-control',
        ]) ?>

    <? if ($image = $model->image): ?>
    <div class="form-group">
        <label class="control-label" for="productImgInput">Изображение</label>
        <ul id="galleryPreviewList" class="galleryPreviewList galleryList-js">
            <?= $this->render('_photo.php', [
                    'image' => $image,
                    'pageParam' => $model,
                ]); ?>
        </ul>
    </div>
    <? endif ?>

    <?= $form->field($model, 'file')->fileInput([
            'class' => 'productImgInput-js',
        ])
        ->label('Сменить изображение') ?>

    <?= $form->field($model, 'announce')->widget(Redactor::className(), [
            'clientOptions' => [
                'lang' => 'ru',
                'buttons' => [
                    'formatting',  'bold', 'italic', 'underline', 'deleted',
                    'unorderedlist', 'orderedlist', 'outdent', 'indent',
                    'file', 'table', 'link', 'alignment', 'horizontalrule'
                ],
                'minHeight' => 150,
                'linebreaks' => true,
            ],
        ]) ?>

    <?= $form->field($model, 'content')->widget(Redactor::className(), [
            'clientOptions' => [
                'lang' => 'ru',
                'buttons' => [
                    'formatting',  'bold', 'italic', 'underline', 'deleted',
                    'unorderedlist', 'orderedlist', 'outdent', 'indent',
                    'file', 'table', 'link', 'alignment', 'horizontalrule'
                ],
                'minHeight' => 150,
                'linebreaks' => true,
            ],
        ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::submitButton('Применить', [
                'class' => 'btn btn-default',
                'value' => 'apply',
                'name'  => 'submit',
            ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<? Modal::begin([
        'id' => 'editPhotoPopup'
    ]) ?>
<? Modal::end() ?>