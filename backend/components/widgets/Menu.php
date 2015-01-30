<?php

namespace backend\components\widgets;

use common\modules\pages\models\Pages;

class Menu extends \yii\widgets\Menu
{
    /* @var bool $showAdd Показывать кнопку "добавить страницу" */
    public $showAddPage = true;

    public function init()
    {
        parent::init();

        $this->items = $this->getItems();
        $this->route = isset($this->view->params['menu']) ? $this->view->params['menu'] : null;
        $this->encodeLabels = false;
        $this->activateParents = true;
    }

    public function getItems()
    {
        return [
            [
                'label' => '<i class="fa fa-dashboard"></i> <span>Панель управления</span>',
                'url' => ['/'],
            ],
            [
                'label' => '<i class="fa fa-level-down"></i> <span>Вернуться на сайт</span>',
                'url' => '/',
            ],
            [
                'label' => '<i class="fa fa-cog"></i> <span>Настройки</span>',
                'url' => ['/settings/default/index'],
            ],
            [
                'label' => '<i class="fa fa-envelope"></i> <span>Контактная информация</span>',
                'url' => ['/contacts/default/index'],
            ],
            [
                'label' => '<i class="fa fa-table"></i> <span>Записи</span><i class="fa fa-angle-left pull-right"></i>',
                'url' => '#',
                'options' => [
                    'class' => 'treeview',
                ],
                'items' => [
                    [
                        'label' => '<i class="fa fa-angle-double-right"></i> Адреса',
                        'url' => ['/entities/address/index'],
                    ],
                    [
                        'label' => '<i class="fa fa-angle-double-right"></i> Категории',
                        'url' => ['/entities/category/index'],
                    ],
                    [
                        'label' => '<i class="fa fa-angle-double-right"></i> Сертификаты',
                        'url' => ['/entities/certificate/index'],
                    ],
                    [
                        'label' => '<i class="fa fa-angle-double-right"></i> Сотрудники',
                        'url' => ['/entities/employee/index'],
                    ],
                    [
                        'label' => '<i class="fa fa-angle-double-right"></i> Заказы',
                        'url' => ['/entities/order/index'],
                    ],
                    [
                        'label' => '<i class="fa fa-angle-double-right"></i> Товары',
                        'url' => ['/entities/product/index'],
                    ],
                    [
                        'label' => '<i class="fa fa-angle-double-right"></i> Акции',
                        'url' => ['/entities/promo/index'],
                    ],
                    [
                        'label' => '<i class="fa fa-angle-double-right"></i> Отзывы',
                        'url' => ['/entities/review/index'],
                    ],
                    [
                        'label' => '<i class="fa fa-angle-double-right"></i> Вызовы',
                        'url' => ['/entities/callback/index'],
                    ],
                ],
            ],
            [
                'label' => '<i class="fa fa-files-o"></i> <span>Страницы</span><i class="fa fa-angle-left pull-right"></i>',
                'url' => '#',
                'options' => [
                    'class' => 'treeview',
                ],
                'items' => $this->generatePageItems(),
            ]
        ];
    }

    /**
    * Сгенерирует пункты меню которые относятся к страницам
    */
    public function generatePageItems()
    {
        $items = [];
        /* @var $page Pages */
        foreach (Pages::find()->all() as $page) {
            $items[] = [
                'label' => '<i class="fa fa-angle-double-right"></i> ' . $page->getAttribute('name'),
                'url' => ['/pages/default/edit', 'slug' => $page->getAttribute('slug')],
            ];
        }

        if ($this->showAddPage) {
            $items[] = [
                'label' => '<i class="fa fa-plus"></i> Добавить',
                'url' => ['/pages/default/edit'],
            ];
        }

        return $items;
    }
}