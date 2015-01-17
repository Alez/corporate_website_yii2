<?php

/* @var $this yii\web\View */
/* @var $model common\modules\contacts\models\Contacts[] */

$this->title = 'Служебная информация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
