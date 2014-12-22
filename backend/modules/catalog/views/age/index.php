<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\catalog\models\AgeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Возрасты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="age-index">
    <p>
        <?= Html::a('Добавить возраст', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
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

</div>
