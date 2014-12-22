<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\widgets\transliterateInput\TransliterateInput;
use common\modules\files\widgets\imageinput\ImageInput;
use yii\redactor\widgets\Redactor;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use common\modules\catalog\models\Category;
use common\modules\catalog\models\Author;
use common\modules\catalog\models\Age;
use yii\bootstrap\Modal;
use common\modules\catalog\widgets\redactorimageinput\RedactorImageInput;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Material */
/* @var $form yii\widgets\ActiveForm */
/* @var $videoDataProvider yii\data\ActiveDataProvider */
/* @var $audioDataProvider yii\data\ActiveDataProvider */
/* @var $backingtrackDataProvider yii\data\ActiveDataProvider */
/* @var $crosslinkingDataProvider yii\data\ActiveDataProvider */
/* @var $externalCrosslinkingDataProvider yii\data\ActiveDataProvider */
?>

<div class="material-form">
    <?php $form = ActiveForm::begin([
            'method' => 'post',
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ]); ?>
    <div role="tabpanel">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#article" aria-controls="article" role="tab" data-toggle="tab">Статья</a></li>
            <li role="presentation"><a href="#multimedia" aria-controls="multimedia" role="tab" data-toggle="tab">Мультимедия</a></li>
            <li role="presentation"><a href="#coloring" aria-controls="coloring" role="tab" data-toggle="tab">Раскраска</a></li>
            <li role="presentation"><a href="#crosslinking" aria-controls="crosslinking" role="tab" data-toggle="tab">Перелинковка</a></li>
            <li role="presentation"><a href="#seo" aria-controls="seo" role="tab" data-toggle="tab">SEO</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="article">
                <?= $form->field($model, 'name')->textInput() ?>

                <?= $form->field($model, 'slug')->widget(TransliterateInput::className()) ?>

                <?= $form->field($model, 'preview_file')->widget(ImageInput::className(), [
                        'fieldName'   => 'preview_id',
                        //'imageSource' => '',
                    ]) ?>

                <?= $form->field($model, 'categoryIds')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Category::find()->all(), 'id', 'name'),
                        'options' => [
                            'multiple' => true
                        ],
                        'language' => 'ru',
                    ]) ?>

                <?= $form->field($model, 'authorIds')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Author::find()->all(), 'id', 'name'),
                        'options' => [
                            'multiple' => true
                        ],
                        'language' => 'ru',
                    ]) ?>

                <?= $form->field($model, 'ageIds')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Age::find()->all(), 'id', 'name'),
                        'options' => [
                            'multiple' => true
                        ],
                        'language' => 'ru',
                    ]) ?>

                <?= $form->field($model, 'annotation')->widget(Redactor::className(), [
                        'clientOptions' => [
                            'toolbarFixed' => false,
                            'lang'       => 'ru',
                            'buttons'    => [
                                'formatting',
                                'bold',
                                'italic',
                                'underline',
                                'deleted',
                                'unorderedlist',
                                'orderedlist',
                                'outdent',
                                'indent',
                                /*'file',*/
                                'table',
                                'link',
                                'alignment',
                                'horizontalrule',
                                /*'image',*/
                            ],
                            'minHeight'  => 150,
                            'linebreaks' => true,
//                            'imageUpload' => '/files/file/uncategorized',
//                            'fileUpload' => '/files/file/uncategorized',
//                            'uploadFields' => [
//                                '_csrf' => Yii::$app->request->csrfToken,
//                            ],
                        ],
                    ]) ?>

                <? echo $form->field($model, 'body', [
                        'template' => "{label}\n<span class='postPreview postPreview-js'>Предпросмотр</span>\n{input}\n{hint}\n{error}"
                    ])->widget(Redactor::className(), [
                        'clientOptions' => [
                            'toolbarFixed' => false,
                            'lang'         => 'ru',
                            'buttons'      => [
                                'html',
                                'formatting',
                                'bold',
                                'italic',
                                'underline',
                                'deleted',
                                'unorderedlist',
                                'orderedlist',
                                'outdent',
                                'indent',
                                /*'file',*/
                                'table',
                                'link',
                                'alignment',
                                'horizontalrule',
                                /*'image',*/

                            ],
                            'plugins' => [
                                'fullscreen',
                                'video',
                                'table',
                                'applyButton',
                                'articleMenu',
                            ],
                            'formatting'    => ['p', 'blockquote', 'h2', 'h3', 'h4', 'h5'],
                            'formattingAdd' => [
                                [
                                    'tag'   => 'span',
                                    'title' => 'Выделить строку',
                                    'class' => 'highlightedRow',
                                ],
                            ],
                            'minHeight'    => 150,
                            'linebreaks'   => true,
                            'buttonSource' => true,
                        ],
                    ]);
                $redactorId = end($form->attributes)['id'];
                ?>

                <?= $form->field($model, 'post_images_file')->widget(RedactorImageInput::className(), [
                        'imageSource' => $model->getFiles('post_images_id'),
                        'fieldName'   => 'post_images_id',
                        'options' => [
                            'multiple' => '',
                        ],
                        'redactorId' => $redactorId,
                    ]) ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="multimedia">
                <? if ($model->isNewRecord): ?>
                    <p>Перед добавлением мультимедия-ресурсов нужно сохранить материал</p>
                <? endif ?>
                <? if (!$model->isNewRecord): ?>
                    <?= $this->render('_slaveTabularForm', [
                            'title'       => 'Видео материалы',
                            'provider'    => $videoDataProvider,
                            'slug'        => 'video',
                            'masterModel' => $model,
                            'columns'     => ['description'],
                        ]) ?>
                    <?= $this->render('_slaveTabularForm', [
                            'title'       => 'Аудио материалы',
                            'provider'    => $audioDataProvider,
                            'slug'        => 'audio',
                            'masterModel' => $model,
                            'columns'     => ['description'],
                        ]) ?>
                    <?= $this->render('_slaveTabularForm', [
                        'title'       => 'Минусовки',
                        'provider'    => $backingtrackDataProvider,
                        'slug'        => 'backingtrack',
                        'masterModel' => $model,
                        'columns'     => ['description'],
                    ]) ?>
                <? endif ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="coloring">
                <?= $form->field($model, 'coloring_image_file')->widget(ImageInput::className(), [
                        'fieldName'   => 'coloring_image_id',
                        //'imageSource' => '',
                    ]) ?>

                <?= $form->field($model, 'coloring_image_bw_file')->widget(ImageInput::className(), [
                        'fieldName'   => 'coloring_image_bw_id',
                        //'imageSource' => '',
                    ]) ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="crosslinking">
                <? if ($model->isNewRecord): ?>
                    <p>Перед добавлением мультимедия-ресурсов нужно сохранить материал</p>
                <? endif ?>
                <? if (!$model->isNewRecord): ?>
                    <?= $this->render('_slaveTabularForm', [
                            'title'       => 'Перелинковка',
                            'provider'    => $crosslinkingDataProvider,
                            'slug'        => 'crosslinking',
                            'masterModel' => $model,
                            'columns'     => ['crosslinkedMaterial.name'],
                        ]) ?>
                    <?= $this->render('_slaveTabularForm', [
                            'title'       => 'Внешняя перелинковка',
                            'provider'    => $externalCrosslinkingDataProvider,
                            'slug'        => 'externalcrosslinking',
                            'masterModel' => $model,
                            'columns'     => ['name', 'url'],
                        ]) ?>
                <? endif ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="seo">
                <?= $form->field($model, 'title')->textInput() ?>
                <?= $form->field($model, 'description')->textarea() ?>
            </div>
        </div>

    </div>

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
<?
Modal::begin(['id' => 'editPhotoPopup']);
Modal::end(); ?>
<?
Modal::begin([
    'id' => 'previewTextPopup',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4>Предпросмотр статьи</h4>',
]);
Modal::end(); ?>