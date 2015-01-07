<?php

use frontend\modules\contact_form\models\ContactForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model ContactForm*/
?>
<? if (!(Yii::$app->request->isAjax || Yii::$app->request->isPost)): ?>
<div id="modal_1" class="reveal-modal tiny" data-reveal>
    <div class="modal_box">
        <a class="close-reveal-modal">&times;</a>
        <div class="modal_title">Заказать звонок</div>
<? endif ?>
<div class="modal_1-js">
    <?= Html::beginForm(Url::to('/contact_form/default/contact'), 'POST');
    if (!isset($model)) {
        $model = new ContactForm();
    } ?>
    <?= Html::activeTextInput($model, 'name', [
            'placeholder' => 'Ваше имя',
        ]) ?>
    <?= Html::error($model, 'name') ?>

    <?= Html::activeTextInput($model, 'phone', [
            'class' => 'phone_input',
            'placeholder' => 'Ваш номер телефона',
        ]) ?>
    <?= Html::error($model, 'phone') ?>

    <?= Html::activeHiddenInput($model, 'url') ?>
    <?= Html::activeHiddenInput($model, 'article') ?>

    <?= Html::button('Отправить заявку', [
            'type' => 'submit',
            'class' => 'btn_style',
        ])?>
    <?= Html::endForm() ?>
</div>
<? if (!(Yii::$app->request->isAjax || Yii::$app->request->isPost)): ?>
    </div>
</div>
<? endif ?>