<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $masterModel mixed */
/* @var $title string */
/* @var $slug string */
/* @var $provider yii\data\ActiveDataProvider */
/* @var $columns array */
?>
    <label><?= $title?></label>
    <p>
        <?= Html::a('Добавить', ['/catalog/' . $slug . '/create' . '?material=' . $masterModel->getAttribute('id')], ['class' => 'btn btn-success']) ?>
    </p>

    <div id="tableMark<?= $slug ?>"></div>
<?
$options = [
    'dataProvider' => $provider,
    'columns' => array_merge($columns, [[
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'urlCreator' => function ($action, $model, $key, $index) use ($slug, $masterModel) {
                if ($action === 'update') {
                    return '@web/catalog/' . $slug . '/update' . '?id=' . $model->getAttribute('id');
                }
                if ($action === 'delete') {
                    return '@web/catalog/' . $slug . '/delete' . '?id=' . $model->getAttribute('id');
                }
                return $action;
            }
        ]]),
];
echo GridView::widget($options) ?>