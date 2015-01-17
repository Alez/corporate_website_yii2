<?php

namespace common\modules\pages\models;

use Yii;

/**
 * This is the model class for table "pages_templates".
 *
 * @property string $id
 * @property string $name
 *
 * @property Pages[] $pages
 * @property PagesTemplatesParams[] $pagesTemplatesParams
 */
class PagesTemplates extends \yii\db\ActiveRecord
{
    const TEMPLATES_PATH = '/../templates/';
    const EMPTY_TEMPLATE_SLUG = 'empty';
    const EMPTY_TEMPLATE_ID = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages_templates';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPages()
    {
        return $this->hasMany(Pages::className(), ['pages_template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagesTemplatesParams()
    {
        return $this->hasMany(PagesTemplatesParams::className(), ['pages_templates_id' => 'id']);
    }

    /**
     * Найдёт путь до шаблона по его слагу
     *
     * @param $slug string
     *
     * @return string|bool Путь до файла или false
     */
    public static function findTemplate($slug)
    {
        $filePath = __DIR__ . self::TEMPLATES_PATH . $slug . '.php';
        if (file_exists($filePath)) {
            return $filePath;
        }

        return false;
    }
}
