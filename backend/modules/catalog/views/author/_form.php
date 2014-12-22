<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\widgets\transliterateInput\TransliterateInput;
use yii\redactor\widgets\Redactor;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Author */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="author-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['class' => 'translitFrom-js form-control']) ?>

    <?= $form->field($model, 'slug')->widget(TransliterateInput::className()) ?>

    <?= $form->field($model, 'description')->widget(Redactor::className(), [
            'clientOptions' => [
                'toolbarFixed' => false,
                'lang'         => 'ru',
                'buttonSource' => true,
                'buttons'      => [
                    'formatting',
                    'bold',
                    'italic',
                    'underline',
                    'deleted',
                    'unorderedlist',
                    'orderedlist',
                    'outdent',
                    'indent',
                    'table',
                    'link',
                    'alignment',
                    'horizontalrule',
                ],
                'plugins' => [
                    'fullscreen',
                    'video',
                    'table',
                    'applyButton',
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
            ],
        ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::submitButton('Применить', [
                'class' => 'btn btn-default',
                'value' => 'apply',
                'name' => 'submit',
            ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
