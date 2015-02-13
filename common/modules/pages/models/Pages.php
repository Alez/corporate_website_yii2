<?php

namespace common\modules\pages\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pages".
 *
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $pages_template_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PagesTemplates $pagesTemplate
 * @property PagesParams[] $pagesParams
 */
class Pages extends \yii\db\ActiveRecord
{
    /* @var Pages Страница, которая будет использована при выводе шаблона */
    public static $page;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'pages_template_id'], 'required'],
            [['pages_template_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 255],
            [['name', 'slug'], 'trim'],
            [['slug'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'slug' => 'ЧПУ',
            'pages_template_id' => 'Шаблон',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::className()],
        ];
    }

    public function beforeDelete()
    {
        foreach ($this->pagesParams as $oldParam) {
            $oldParam->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagesTemplate()
    {
        return $this->hasOne(PagesTemplates::className(), ['id' => 'pages_template_id'])->from(PagesTemplates::tableName(), 'template');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagesParams()
    {
        return $this->hasMany(PagesParams::className(), ['page_id' => 'id']);
    }

    /**
     * Устанавливает с какой страницы будут использованы параметры в этом шаблоне.
     * Подтянет параметры для выбранной страницы.
     *
     * @param $slug
     */
    public static function setPage($slug)
    {
        if (!$slug) {
            return false;
        }

        if (isset(self::$page)) {
            return false;
        }

        $page = Pages::find()
            ->where([Pages::tableName() . '.slug' => $slug])
            ->with('pagesTemplate')
            ->asArray()
            ->one();

        if ($page) {
            self::$page = $page;

            return true;
        } else {
            return false;
        }
    }

    public static function get($name)
    {
        if (isset(self::$page[$name])) {
            return self::$page[$name];
        }

        return false;
    }
}
