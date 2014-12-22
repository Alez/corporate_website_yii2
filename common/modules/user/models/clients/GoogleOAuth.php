<?php

namespace common\modules\user\models\clients;

class GoogleOAuth extends \yii\authclient\clients\GoogleOAuth
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

        if (isset($attributes['emails'][0]['value'])) {
            $newAttributes['email'] = $attributes['emails'][0]['value'];
        }

        if (isset($attributes['name']['familyName'])) {
            $newAttributes['last_name'] = $attributes['name']['familyName'];
        }

        if (isset($attributes['name']['givenName'])) {
            $newAttributes['first_name'] = $attributes['name']['givenName'];
        }

        if (isset($attributes['id'])) {
            $newAttributes['uid'] = $attributes['id'];
        }

        if (isset($attributes['placesLived'][0]['value'])) {
            $newAttributes['city'] = $attributes['placesLived'][0]['value'];
        }

        if (isset($attributes['image']['url']) && !$attributes['image']['isDefault']) {
            $newAttributes['photo'] = preg_replace('/\?sz=\d+/', '', $attributes['image']['url']);
        }

        return $newAttributes;
    }
}
