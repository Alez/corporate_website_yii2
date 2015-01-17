<?php

use frontend\modules\contact_form\models\ContactForm;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model ContactForm*/
?>
<div class="modal" id="modal_1" tabindex="-1" role="dialog" aria-labelledby="modal_1Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span>&times;</span></button>
            <h3 class="modal_title">Пожалуйста, заполните форму и мы свяжемся с вами</h3>
            <? if (!isset($model)) {
                $model = new ContactForm();
                $model->url = $_SERVER['REQUEST_URI'];
            } ?>
            <? $form = ActiveForm::begin([
                'action' => '/contact_form/default/contact',
                'method' => 'POST',
                'options' => [
                    'class' => 'callback-js callbackForm',
                    'data-redirect' => '/thanks',
                ],
            ]) ?>
                <div class="row">
                    <div class="hidden-xs col-sm-4 col-md-4 text-right">
                        <label for="">Как к вам обращаться:</label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8">
                        <?
                        $field = $form->field($model, 'name')->textInput([
                            'placeholder' => 'Фамилия, имя и отчество',
                            'class' => 'required',
                        ]);
                        $field->template = "{input}\n{hint}\n{error}";
                        echo $field;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="hidden-xs col-sm-4 col-md-4 text-right">
                        <label for="">Телефон:</label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8">
                        <?
                        $field = $form->field($model, 'phone')->textInput([
                            'placeholder' => '+7 ...',
                            'class' => 'phone_input required',
                        ]);
                        $field->template = "{input}\n{hint}\n{error}";
                        echo $field;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="hidden-xs col-sm-4 col-md-4 text-right">
                        <label for="">E-mail:</label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8">
                        <?
                        $field = $form->field($model, 'mail')->textInput([
                            'placeholder' => 'Заполните, если такой способ связи удобен для вас',
                        ]);
                        $field->template = "{input}\n{hint}\n{error}";
                        echo $field;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="hidden-xs col-sm-4 col-md-4 text-right">
                        <label for="">Комментарии:</label>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8">
                        <?
                        $field = $form->field($model, 'comment')->textarea([
                            'placeholder' => 'Например: «необходима консультация терапевта» или «острая пульсирующая боль в левой части головы»',
                        ]);
                        $field->template = "{input}\n{hint}\n{error}";
                        echo $field;
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-sm-offset-4 col-md-8 col-md-offset-4">
                        <div class="prompt">Консультация специалиста осуществляется на территории центра и оплачивается отдельно от лечения</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-sm-offset-4 col-md-8 col-md-offset-4">
                        <button type="submit" class="btn_style"><i class="fa fa-check-circle-o"></i>Отправить</button>
                    </div>
                </div>
            <?= Html::activeHiddenInput($model, 'url') ?>
            <? ActiveForm::end() ?>
        </div>
    </div>
</div>