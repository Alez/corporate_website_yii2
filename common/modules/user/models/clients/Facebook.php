<?php

namespace common\modules\user\models\clients;

class Facebook extends \yii\authclient\clients\Facebook
{
    /**
     * Нормализуем атрибуты под нашу БД
     */
    public function getUserAttributes()
    {
        $attributes = parent::getUserAttributes();
        $newAttributes = [];
        if (isset($attributes['gender'])) {
            if ($attributes['gender'] === 'male') {
                $newAttributes['gender'] = 1;
            } elseif ($attributes['gender'] === 'female') {
                $newAttributes['gender'] = 0;
            }
        }

        if (isset($attributes['first_name'])) {
            $newAttributes['first_name'] = $attributes['first_name'];
        }

        if (isset($attributes['last_name'])) {
            $newAttributes['last_name'] = $attributes['last_name'];
        }

        if (isset($attributes['id'])) {
            $newAttributes['uid'] = $attributes['id'];
        }

        $newAttributes['photo'] = 'https://graph.facebook.com/' . $attributes['id'] .
            '/picture?type=large' .
            '&access_token=' . $this->getAccessToken()->token;

        if (isset($attributes['location']['name'])) {
            $parts = explode(', ', $attributes['location']['name']);
            $newAttributes['city'] = $parts[0];
            $newAttributes['country'] = isset($parts[1]) ? $parts[1] : null;
        }

        if (isset($attributes['birthday'])) {
            $parts = explode('/', $attributes['birthday']);
            $month = isset($parts[1]) ? $parts[1] : '';
            $day = $parts[0];
            $year = isset($parts[2]) ? $parts[2] : '';
            $newAttributes['birth'] = $year . '-' . $month . '-' . $day;
        }

        if (isset($attributes['email'])) {
            $newAttributes['email'] = $attributes['email'];
        }

        return $newAttributes;
    }
}
