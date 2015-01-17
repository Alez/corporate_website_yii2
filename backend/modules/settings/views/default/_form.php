<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\settings\models\Settings[] */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service_info-form">

    <?php $form = ActiveForm::begin([
        'method' => 'POST',
    ]); ?>

    <? foreach ($model as $field): ?>

    <?= $form->field($field, "[$field->id]value")->label($field->name)->textInput() ?>

    <? endforeach ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>