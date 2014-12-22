<?php
namespace common\components\helpers;

use yii;

class DateHelper
{
    public static function translateMonth($dateString)
    {
        $moths = [
            'January' => 'января',
            'February' => 'февраля',
            'March' => 'марта',
            'April' => 'апреля',
            'May' => 'мая',
            'June' => 'июня',
            'July' => 'июля',
            'August' => 'августа',
            'September' => 'сентября',
            'October' => 'октября',
            'November' => 'ноября',
            'December' => 'декабря',
        ];

        return strtr($dateString, $moths);
    }
}
