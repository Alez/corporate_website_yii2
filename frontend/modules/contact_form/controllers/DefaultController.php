<?php

namespace frontend\modules\contact_form\controllers;

use Yii;
use yii\web\Controller;
use frontend\modules\contact_form\models\ContactForm;

class DefaultController extends Controller
{
    public function actionContact()
    {
        if (Yii::$app->request->isAjax || Yii::$app->request->isPost) {
            $contactForm = new ContactForm();
            $contactForm->load(Yii::$app->request->post());
            if ($contactForm->validate()) {
                if ($contactForm->send()) {
                    return $this->renderAjax('success');
                }
            } else {
                return $this->renderAjax('contactForm', [
                        'model' => $contactForm,
                    ]);
            }
        }

        return $this->renderAjax('fail');
    }
}