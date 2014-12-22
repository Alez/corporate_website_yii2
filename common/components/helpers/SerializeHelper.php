<?php
namespace common\components\helpers;

use yii;

class SerializeHelper
{
    /**
     * Сериализует, а потом прогонит через Base64
     *
     * @param $data
     *
     * @return string
     */
    public static function encode($data)
    {
        return base64_encode(serialize($data));
    }

    /**
     * Вытащит из Base64, а потом разсериализует
     *
     * @param $data
     *
     * @return mixed
     */
    public static function decode($data)
    {
        return unserialize(base64_decode($data));
    }
}