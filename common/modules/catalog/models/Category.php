<?php

namespace common\modules\catalog\models;

use Yii;
use common\modules\files\models\Files;
use yii\helpers\Html;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use common\components\helpers\SerializeHelper;

/**
 * This is the model class for table "category".
 *
 * @property string     $id
 * @property string     $name
 * @property integer    $order
 * @property string     $slug
 * @property integer    $parent_id
 * @property integer    $top_parent_id
 * @property string     $description
 *
 */
class Category extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            // Запись верхнего родителя
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'top_parent_id',
                    self::EVENT_BEFORE_UPDATE => 'top_parent_id',
                ],
                'value' => function ($event) {
                    if ($this->getOldAttribute('top_parent_id') === $this->getAttribute('top_parent_id')
                        && $event->name !== self::EVENT_BEFORE_INSERT
                    ) {
                        return $this->getAttribute('top_parent_id');
                    }

                    $parent = $this->getParentCategory();

                    // Родителя нет, это и есть верхняя категория
                    if (!$parent) {
                        return 0;
                    }

                    while ($parent) {
                        $previousParent = $parent;
                        $parent = $parent->getParentCategory();
                        if (!$parent) {
                            return $previousParent->id;
                        }
                    }

                    return 0;
                },
            ],
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'parent_id'], 'required'],
            [['slug'], 'unique'],
            [['name', 'slug'], 'trim'],
            [['order', 'parent_id', 'top_parent_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => 'Название',
            'order'       => 'Сортировка',
            'slug'        => 'ЧПУ',
            'parent_id'   => 'Родительская категория',
            'parent'      => 'Родительская категория',
            'created_at'  => 'Создана',
            'updated_at'  => 'Обновлена',
            'description' => 'Описание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterials()
    {
        return $this->hasMany(Material::className(), ['id' => 'material_id'])
            ->viaTable('link_material_category', ['category_id' => 'id'])->orderBy('order');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTopParent()
    {
        return $this->hasOne(self::className(), ['id' => 'top_parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirstChildren()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id'])
            ->from(self::tableName() . ' parent');
    }

    /**
     * Получить имя верхнего родителя
     *
     * @return string
     */
    public function getTopParentName()
    {
        if ((int)$this->getAttribute('top_parent_id') == 0) {
            return 'Главная';
        } else {
            return $this->topParent->name;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id'])->from(self::tableName() . ' parent');
    }

    /**
     * Создаст массив для употребления с виджетом меню
     *
     * @param $category Category
     *
     * @return array Массив меню
     */
    public static function getCategoryMenuItems($category)
    {
        if ($category->getAttribute('top_parent_id') != 0) {
            $topParent = $category->topParent;
        } else {
            $topParent = $category;
        }

        $categories = $topParent->firstChildren;

        $menuItems = [];

        foreach ($categories as $categoryItem) {
            $menuItems[] = [
                'label' => Html::encode($categoryItem->name),
                'url'   => [$categoryItem->getUrl()],
            ];
        }

        return $menuItems;
    }

    /**
     * Возвращает детей текущего объекта каталога в виде массива для меню
     *
     * @param Category $category Категория у которой нужно взять детей
     *
     * @return array
     */
    private static function getMenuChildren($category)
    {
        $children = [];

        foreach ($category->firstChildren as $childrenItem) {
            $children[] = [
                'label' => $childrenItem->name,
                'url'   => [$childrenItem->getUrl()],
            ];;
        }

        return $children;
    }

    /**
     * Создаст цепочку из родителей данной категории, НЕ ВКЛЮЧАЯ саму эту категорию
     *
     * @return array
     */
    public function getBreadcrumbs()
    {
        $parent = $this->getParentCategory();

        $parents = [];

        while ($parent) {
            $parents[] = ['label' => $parent->name, 'url' => $parent->getUrl()];
            $parent = $parent->getParentCategory();
        }

        return $parents;
    }

    /**
     * Находит родителя текущего пункта каталога, если его нет - возвращает false
     *
     * @return bool|Category
     */
    public function getParentCategory()
    {
        if ((int)$this->getAttribute('parent_id') != 0) {
            return self::findOne($this->parent_id);
        } else {
            return false;
        }
    }

    /**
     * Возвращает пункты меню для верхнего меню в виде массива для виджета меню
     * [
     *      ['label' => xxx, 'url' => xxx],
     *      ...
     * ]
     *
     * @return array Пункты меню
     */
    public static function getTopLevelItemsAsMenu()
    {
        $productMenu = [];

        foreach (self::find()->where(['parent_id' => 0])->orderBy('order')->all() as $menuItem) {
            $productMenu[] = ['label' => $menuItem->name, 'url' => $menuItem->getUrl()];
        }

        return $productMenu;
    }

    /**
     * Подготовит УРЛ для товара (фронтенд)
     *
     * @return string
     */
    public function getUrl()
    {
        return '/products/' . $this->getAttribute('slug');
    }

    /**
     * Удалит файл на основе входных параметров.
     * Если нет $fileId, то удаляем единичный файл, если есть, то удаляем множественный
     *
     * @param string $fieldName Название поля где хранятся Id файла/файлов
     * @param int|null $fileId Номер файла
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteFile($fieldName, $fileId = null)
    {
        if ($fileId) {
            if ($fieldData = $this->getAttribute($fieldName)) {
                $fileIds = SerializeHelper::decode($fieldData);
                $fileIds = array_flip($fileIds);
                unset($fileIds[$fileId]);
                $fileIds = array(array_keys($fileIds));
                $fileIds = SerializeHelper::encode($fileIds);
                if (count($fileIds)) {
                    $this->setAttribute($fieldName, $fileIds);
                } else {
                    $this->setAttribute($fieldName, null);
                }
                $this->updateAttributes([$fieldName]);
                if (Files::findOne($fileId)->delete()) {
                    return true;
                }
            }
        } else {
            $fileId = $this->getAttribute($fieldName);
            $this->setAttribute($fieldName, null);
            $this->updateAttributes([$fieldName]);
            if (Files::findOne($fileId)->delete()) {
                return true;
            }
        }

        return false;
    }
}
