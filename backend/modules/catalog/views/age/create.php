<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Age */

$this->title = 'Добавить Возраст';
$this->params['breadcrumbs'][] = ['label' => 'Возрасты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="age-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
