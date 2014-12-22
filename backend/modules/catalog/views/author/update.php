<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Author */

$this->title = 'Изменить Автора: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
