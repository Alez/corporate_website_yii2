<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\news\models\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">
    <p>
        <?= Html::a('Добавить Новость', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <? Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'announce:html',
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
    <? Pjax::end(); ?>

</div>
