<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use common\modules\pages\models\PagesTemplates;
use kartik\widgets\Select2;
use yii\helpers\Url;
use common\modules\pages\models\TextParams;
use common\modules\pages\models\RedactorParams;
use common\modules\pages\models\FileParams;
use common\modules\pages\models\MultifileParams;
use common\modules\pages\models\ImageParams;
use common\modules\pages\models\MultiimageParams;
use common\modules\files\widgets\imageinput\ImageInput;
use common\modules\pages\models\TextareaParams;


/* @var $this yii\web\View */
/* @var $page common\modules\pages\models\Pages */
/* @var $params common\modules\pages\models\PagesParams[] */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pages-form">
    <?php $form = ActiveForm::begin([
            'method' => 'POST',
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ]); ?>
    <div class="row">
        <div class="col-xs-9">
            <?
            $i = 0;
            foreach ($params as $param) {
                $i++;
                switch ($param->type) {
                    // Текстовое поле
                    case TextParams::TYPE:
                        $name = $param->getAttribute('id') ? $param->id : 'template_' . $param->pages_templates_params_id;
                        echo $form
                            ->field($param, "[$name]value")
                            ->label($param->pagesTemplatesParams->getAttribute('name'), [
                                    'for' => 'input' . $i,
                                ])
                            ->textInput([
                                    'id' => 'input' . $i,
                                ]);
                        break;
                    case TextareaParams::TYPE:
                        $name = $param->getAttribute('id') ? $param->id : 'template_' . $param->pages_templates_params_id;
                        echo $form
                            ->field($param, "[$name]value")
                            ->label($param->pagesTemplatesParams->getAttribute('name'), [
                                'for' => 'input' . $i,
                            ])
                            ->textarea([
                                'id' => 'input' . $i,
                                'rows' => '6',
                            ]);
                        break;
                    // WYSIWYG
                    case RedactorParams::TYPE:
                        $name = $param->getAttribute('id') ? $param->id : 'template_' . $param->pages_templates_params_id;
                        echo $form
                            ->field($param, "[$name]value")
                            ->label($param->pagesTemplatesParams->getAttribute('name'), [
                                    'for' => 'input' . $i,
                                ])
                            ->widget(Redactor::className(), [
                                    'clientOptions' => [
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
                                            'file',
                                            'table',
                                            'link',
                                            'alignment',
                                            'horizontalrule',
                                            'video',
                                        ],
                                        'linebreaks' => true,
                                        'imageUpload' => '/files/file/uncategorized',
                                        'fileUpload' => '/files/file/uncategorized',
                                        'uploadFields' => [
                                            Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
                                        ],
                                    ],
                                    'options' => [
                                        'id' => 'input' . $i,
                                    ],
                                ]
                            );
                        break;
                    // Одиночный файл
                    case FileParams::TYPE:
                        $name = $param->getAttribute('id') ? $param->id : 'template_' . $param->pages_templates_params_id;
                        echo $form
                            ->field($param, "[$name]uploadFile")
                            ->label('Сменить - ' . $param->pagesTemplatesParams->getAttribute('name'), [
                                    'for' => 'input' . $i,
                                ])
                            ->fileInput();
                        break;
                    // Мультизагрузка файлов
                    case MultifileParams::TYPE:
                        $name = $param->getAttribute('id') ? $param->id : 'template_' . $param->pages_templates_params_id;
                        echo 'Поля нет';
                        break;
                    // Одиночная картинка
                    case ImageParams::TYPE:
                        $name = $param->getAttribute('id') ? $param->id : 'template_' . $param->pages_templates_params_id;
                        echo $form
                            ->field($param, "[$name]uploadFile")
                            ->label('Сменить - ' . $param->pagesTemplatesParams->getAttribute('name'), [
                                    'for' => 'input' . $i,
                                ])
                            ->widget(ImageInput::className(), [
                                    'imageSource' => $param->file,
                                    'fieldName' => '',
                                ]);
                        break;
                    // Мультизагрузка картинок
                    case MultiimageParams::TYPE:
                        $name = $param->getAttribute('id') ? $param->id : 'template_' . $param->pages_templates_params_id;
                        echo $form
                            ->field($param, "[$name]uploadFile")
                            ->label('Сменить - ' . $param->pagesTemplatesParams->getAttribute('name'), [
                                    'for' => 'input' . $i,
                                ])
                            ->widget(ImageInput::className(), [
                                    'imageSource' => $param->getFiles(),
                                    'fieldName' => '',
                                    'multiple' => '',
                                ]);
                        break;
                }
            } ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div> <!-- col-xs-9 -->
        <div class="col-xs-3">
            <? $url = $page->getAttribute('slug') ? "/backend/pages/default/edit?slug=$page->slug&template=" : '/backend/pages/default/edit?template=' ?>
            <?= $form->field($page, 'pages_template_id')->widget(Select2::className(), [
                    'language' => 'ru',
                    'data'     => ArrayHelper::map(PagesTemplates::find()->all(), 'id', 'name'),
                    'class'    => 'chooseTemplate-js',
                    'options'  => [
                        'class' => 'chooseTemplate-js',
                        'data-url' => Url::to($url),
                    ],
                ]) ?>
            <?= $form->field($page, 'name')->textInput() ?>
            <?= $form->field($page, 'slug')->textInput() ?>
            <? if ($page->getAttribute('id')) {
                echo Html::activeHiddenInput($page, 'id');
            } ?>

            <? // Если страница не новая, то её можно удалить
            if ($page->getAttribute('id')) {
                echo Html::a('Удалить страницу', Url::to('@web/pages/default/delete?id=' . $page->getAttribute('id')), [
                        'data-method'  => 'post',
                        'data-confirm' => 'Вы уверены, что хотите удалить эту страницу?',
                        'title'        => 'Удалить страницу',
                        'class'        => 'btn btn-warning pull-right',
                    ]);
            } ?>
        </div>
    </div><!-- row -->
</div>
<?php ActiveForm::end(); ?>

<? Modal::begin([
        'id' => 'editPhotoPopup'
    ]) ?>
<? Modal::end() ?>