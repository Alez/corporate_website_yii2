<?php

namespace frontend\modules\contact_form\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use common\modules\settings\models\Settings;

class ContactForm extends Model
{
    public $name;
    public $phone;
    public $url;
    public $mail;
    public $comment;

    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            [['name', 'phone', 'mail'], 'trim'],
            [['name', 'phone', 'mail', 'url'], 'string', 'max' => 255],
            [['comment'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'  => 'Имя',
            'phone' => 'Телефон',
        ];
    }

    /**
     * Отослать на почту
     *
     * @return bool
     * @throws Exception
     */
    public function send()
    {
        if ($email = Settings::findOne(['slug' => 'notificationEmail'])->value) {
            Yii::$app->mailer->compose('layouts/callback', [
                    'name'    => $this->name,
                    'phone'   => $this->phone,
                    'mail'    => $this->mail,
                    'comment' => $this->comment,
                    'url'     => $this->url,
                ])
                ->setFrom('webmaster@' . $_SERVER['SERVER_NAME'])
                ->setTo($email)
                ->setSubject('Заявка с сайта')
                ->send();
        } else {
            throw new Exception('Значение для email в настройках не установлено');
        }

        return true;
    }
}
