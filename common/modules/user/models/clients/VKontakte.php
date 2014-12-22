<?php

namespace common\modules\user\models\clients;

class VKontakte extends \yii\authclient\clients\VKontakte
{
    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $attributes = $this->api('users.get.json', 'GET', [
            'fields' => implode(',', [
                'uid',
                'first_name',
                'last_name',
                'nickname',
                'screen_name',
                'sex',
                'bdate',
                'city',
                'country',
                'timezone',
                'photo',
                'photo_max_orig',
            ]),
        ]);
        return array_shift($attributes['response']);
    }

    /**
     * Нормализуем атрибуты под нашу БД
     */
    public function getUserAttributes()
    {
        $attributes = parent::getUserAttributes();
        $newAttributes = [];
        if (isset($attributes['sex'])) {
            if ((int)$attributes['sex'] === 2) {
                $newAttributes['gender'] = 1;
            } elseif ((int)$attributes['sex'] === 1) {
                $newAttributes['gender'] = 0;
            }
        }

        if (isset($attributes['id'])) {
            $newAttributes['uid'] = $attributes['uid'];
        }

        if (isset($attributes['first_name'])) {
            $newAttributes['first_name'] = $attributes['first_name'];
        }

        if (isset($attributes['last_name'])) {
            $newAttributes['last_name'] = $attributes['last_name'];
        }

        if (isset($attributes['photo_max_orig'])) {
            $newAttributes['photo'] = $attributes['photo_max_orig'];
        }

        if (isset($attributes['bdate'])) {
            $parts = explode('.', $attributes['bdate']);
            $day = $parts[0];
            $month = isset($parts[1]) ? $parts[1] : '';
            $year = isset($parts[2]) ? $parts[2] : '';
            $newAttributes['birth'] = $year . '-' . $month . '-' . $day;
        }

        if (isset($attributes['city'])) {
            $ch = curl_init('http://api.vk.com/method/database.getCitiesById?city_ids=' . $attributes['city']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            $raw = curl_exec($ch);
            curl_close ($ch);
            $citiesResp = json_decode($raw);
            try {
                $newAttributes['city'] = $citiesResp->response[0]->name;
            } catch (\Exception $e) {
            }

        }

        if (isset($attributes['country'])) {
            $ch = curl_init('http://api.vk.com/method/database.getCountriesById?country_ids=' . $attributes['country']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            $raw = curl_exec($ch);
            curl_close ($ch);
            $countriesResp = json_decode($raw);
            try {
                $newAttributes['country'] = $countriesResp->response[0]->name;
            } catch (\Exception $e) {
            }
        }

        return $newAttributes;
    }
}
