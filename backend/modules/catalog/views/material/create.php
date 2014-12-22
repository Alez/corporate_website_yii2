<?php

/* @var $this yii\web\View */
/* @var $model common\modules\catalog\models\Product */

$this->title = 'Добавить Материал';
$this->params['breadcrumbs'][] = ['label' => 'Материалы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
