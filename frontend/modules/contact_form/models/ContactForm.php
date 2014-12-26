<?php

namespace frontend\modules\contact_form\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use common\modules\service_info\models\ServiceInfo;

class ContactForm extends Model
{
    public $name;
    public $phone;
    public $url;
    public $article;

    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            [['name', 'phone', 'url', 'article'], 'safe'],
            [['name', 'phone'], 'trim'],
            [['name', 'phone'], 'string', 'max' => 255],
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
        if ($email = ServiceInfo::findOne(['slug' => 'notificationEmail'])->content) {
            Yii::$app->mailer->compose('layouts/callback', [
                    'name'    => $this->name,
                    'phone'   => $this->phone,
                    'url'     => $this->url,
                    'article' => $this->article,
                ])
                ->setFrom('webmaster@' . $_SERVER['SERVER_NAME'])
                ->setTo($email)
                ->setSubject('Заявка на звонок с сайта')
                ->send();
        } else {
            throw new Exception('Значение для email в настройках не установлено');
        }

        return true;
    }
}
