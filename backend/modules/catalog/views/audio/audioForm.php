<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use common\modules\files\widgets\fileinput\FileInput;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\MaterialAudio */

$this->title = $model->isNewRecord ? 'Добавить' : 'Изменить' . ' Аудио';
$this->params['breadcrumbs'][] = ['label' => 'Материалы', 'url' => ['/catalog/material/index']];
$this->params['breadcrumbs'][] = ['label' => 'Материал', 'url' => ['/catalog/material/update?id=' . $model->getAttribute('material_id')]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="video-create">
    <?php $form = ActiveForm::begin([
            'method' => 'post',
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ]); ?>

    <?= $form->field($model, 'audio_file')->widget(FileInput::className(), [
            'fieldName' => 'audio_id',
        ]) ?>

    <?= $form->field($model, 'description')->textarea([
            'maxlength' => 255,
        ]) ?>

    <?= Html::activeHiddenInput($model, 'material_id') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>