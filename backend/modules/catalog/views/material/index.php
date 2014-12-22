<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\catalog\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Материалы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">
    <p>
        <?= Html::a('Добавить Материал', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <? Pjax::begin() ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'baseCategory',
                'value' => 'baseCategory.name'
            ],
            [
                'format'             => ['date', 'php:d-m-Y H:i'],
                'attribute'          => 'created_at',
                'filterInputOptions' => [
                    'class' => 'datepicker-js form-control',
                ],
            ],

            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
    <? Pjax::end() ?>

</div>
