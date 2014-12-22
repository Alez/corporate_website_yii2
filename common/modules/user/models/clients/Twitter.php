<?php

namespace common\modules\user\models\clients;

class Twitter extends \yii\authclient\clients\Twitter
{
    /**
     * Нормализуем атрибуты под нашу БД
     */
    public function getUserAttributes()
    {
        $attributes = parent::getUserAttributes();
        $newAttributes = [];
        if (isset($attributes['location'])) {
            $newAttributes['city'] = $attributes['location'];
        }

        if (isset($attributes['name'])) {
            $parts = explode(' ', $attributes['name']);
            // Тут возможно имя
            $newAttributes['first_name'] = $parts[0];
            // Тут берём всё остальное кроме того, что возможно является именем
            $newAttributes['last_name'] = str_replace($parts[0] . ' ', '', $attributes['name']);
        }

        if (isset($attributes['profile_image_url'])) {
            $newAttributes['photo'] = str_replace('_normal', '', $attributes['profile_image_url']);
        }

        if (isset($attributes['id'])) {
            $newAttributes['uid'] = $attributes['id'];
        }

        return $newAttributes;
    }
}
