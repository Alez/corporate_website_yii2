<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\modules\catalog\models\Category;
use kartik\widgets\Select2;
use common\components\widgets\transliterateInput\TransliterateInput;
use yii\redactor\widgets\Redactor;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'slug')->widget(TransliterateInput::className()) ?>

    <?= $form->field($model, 'parent_id')->widget(Select2::className(), [
            'data' => ArrayHelper::merge([0 => 'Главная'], ArrayHelper::map(Category::find()->all(), 'id', 'name')),
        ]) ?>

    <?= $form->field($model, 'order')->textInput() ?>

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