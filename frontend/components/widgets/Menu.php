<?php

namespace frontend\modules\menu\widgets;

class Menu extends \yii\widgets\Menu
{
    public function init()
    {
        parent::init();

        $this->items = self::getItems();
        $this->route = isset($this->view->params['menu']) ? $this->view->params['menu'] : null;
    }

    public static function getItems()
    {
        return [
            [
                'label' => 'О центре',
                'url' => ['/about']
            ],
            [
                'label' => 'Новости',
                'url' => ['/news'],
            ],
            [
                'label' => 'Услуги',
                'url' => ['/services']
            ],
            [
                'label' => 'Прайс-лист',
                'url' => ['/pricelist'],
            ],
            [
                'label' => 'Врачи',
                'url' => ['/doctors']
            ],
            [
                'label' => 'Отзывы',
                'url' => ['/reviews']
            ],
            [
                'label' => 'Видео',
                'url' => ['/video']
            ],
            [
                'label' => 'Акции',
                'url' => ['/promo']
            ],
        ];
    }
}