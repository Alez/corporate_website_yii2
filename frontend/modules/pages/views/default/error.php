<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="row">
        <div class="small-12 columns">
            <h2><?= $name ?></h2>
        </div>
    </div>
    <div class="row">
        <div class="small-12 columns">
            <?= nl2br(Html::encode($message)) ?>
        </div>
    </div>
</div>
