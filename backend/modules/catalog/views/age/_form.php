<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\widgets\transliterateInput\TransliterateInput;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Age */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="age-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'slug')->widget(TransliterateInput::className()) ?>

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
