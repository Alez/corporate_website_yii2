<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\MaterialVideo */

$this->title = $model->isNewRecord ? 'Добавить' : 'Изменить' . ' Видео';
$this->params['breadcrumbs'][] = ['label' => 'Материалы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Материал', 'url' => ['/catalog/material/update?id=' . $model->getAttribute('material_id')]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="video-create">
    <?php $form = ActiveForm::begin([
            'method' => 'post',
        ]); ?>

    <?= $form->field($model, 'video')->textarea([
            'maxlength' => 255,
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