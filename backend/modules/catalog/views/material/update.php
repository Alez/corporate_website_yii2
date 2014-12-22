<?php

/* @var $this yii\web\View */
/* @var $videoDataProvider yii\data\ActiveDataProvider */
/* @var $audioDataProvider yii\data\ActiveDataProvider */
/* @var $backingtrackDataProvider yii\data\ActiveDataProvider */
/* @var $crosslinkingDataProvider yii\data\ActiveDataProvider */
/* @var $externalCrosslinkingDataProvider yii\data\ActiveDataProvider */

$this->title = 'Изменить Материал: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Материалы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="material-update">

    <?= $this->render('_form', [
            'model'             => $model,
            'videoDataProvider' => $videoDataProvider,
            'audioDataProvider' => $audioDataProvider,
            'backingtrackDataProvider' => $backingtrackDataProvider,
            'crosslinkingDataProvider' => $crosslinkingDataProvider,
            'externalCrosslinkingDataProvider' => $externalCrosslinkingDataProvider,
        ]) ?>

</div>
