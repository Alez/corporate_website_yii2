<?php

namespace frontend\components\widgets;

use common\modules\entities\models\ServiceCategory;

class SideMenu extends \yii\widgets\Menu
{
    public function init()
    {
        parent::init();

        $this->items = self::getItems();
        $this->route = isset($this->view->params['menu']) ? $this->view->params['menu'] : null;
    }

    public static function getItems()
    {
        $categories = ServiceCategory::find()->all();
        $categoriesMenu = [];
        foreach ($categories as $key => $category) {
            if ((int)$category->parent_id === 0) {
                $categoriesMenu[$category->id] = [
                    'label' => $category->name,
                    'url' => [$category->getUrl()],
                ];
                unset($categories[$key]);
            }
        }

        foreach ($categories as $category) {
            $categoriesMenu[$category->parent_id]['items'][] = [
                'label' => $category->name,
                'url' => [$category->geturl()],
            ];
        }

        return $categoriesMenu;
    }
}