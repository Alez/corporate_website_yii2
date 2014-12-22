<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use common\modules\catalog\models\Material;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\MaterialAudio */

$this->title = $model->isNewRecord ? 'Добавить' : 'Изменить' . ' Перелинковку';
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

    <?= $form->field($model, 'to')->widget(Select2::className(), [
            'data' => ArrayHelper::map(Material::find()->all(), 'id', 'name'),
            'language' => 'ru',
        ]) ?>

    <?= Html::activeHiddenInput($model, 'material_id') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>