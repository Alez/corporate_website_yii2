<?php

use frontend\modules\contact_form\models\ContactForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model ContactForm*/
/* @var $buttonText string|null Текст на кнопке*/
?>
<? if (!isset($model)) {
    $model = new ContactForm();
    $model->url = $model->url = $_SERVER['REQUEST_URI'];;
} ?>
<? $form = ActiveForm::begin([
    'action' => '/contact_form/default/contact',
    'method' => 'POST',
    'options' => [
        'class' => 'callback-js callbackForm',
    ],
]) ?>

<?
$field = $form->field($model, 'name')->textInput(['placeholder' => 'Ваше имя']);
$field->template ="{input}\n{hint}\n{error}";
echo $field;
?>

<?
$field = $form->field($model, 'phone')->textInput(['placeholder' => 'Ваш номер телефона']);
$field->template ="{input}\n{hint}\n{error}";
echo $field;
?>

<?= Html::activeHiddenInput($model, 'url') ?>

<?= Html::button(isset($buttonText) ? $buttonText : 'Заказать звонок', [
    'type' => 'submit',
]) ?>

<?= Html::img('/images/loader.gif', [
    'class' => 'loader-js loader',
    'style' => 'display: none'
]) ?>
<? ActiveForm::end() ?>