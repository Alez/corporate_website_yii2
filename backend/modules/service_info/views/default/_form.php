<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\service_info\models\ServiceInfo[] */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service_info-form">

    <?php $form = ActiveForm::begin([
        'method' => 'POST',
//            'options' => [
//                'enctype' => 'multipart/form-data',
//            ],
    ]); ?>

    <? foreach ($model as $field): ?>

    <?= $form->field($field, "[$field->id]content")->label($field->name)->widget(Redactor::className(), [
        'clientOptions' => [
            'lang' => 'ru',
            'buttons' => [
                'formatting',  'bold', 'italic', 'deleted',
                'unorderedlist', 'orderedlist', 'outdent', 'indent',
                'file', 'table', 'link', 'alignment', 'horizontalrule'
            ],
            'linebreaks' => true,
            'minHeight' => 150,
        ],
    ]) ?>

    <? endforeach ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>