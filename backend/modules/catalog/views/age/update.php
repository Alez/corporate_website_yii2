<?php

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Age */

$this->title = 'Изменить Возраст: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Возрасты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="age-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
