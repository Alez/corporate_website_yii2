<?php

namespace common\modules\settings\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property string $id
 * @property string $name
 * @property string $value
 */
class Settings extends \yii\db\ActiveRecord
{
    /* @var  */
    public static $settingsCache;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 100]
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
            'value' => 'Содержимое',
        ];
    }

    public static function get($slug)
    {
        if (is_null(self::$settingsCache)) {
            self::$settingsCache = self::find()->select(['slug', 'value'])->indexBy('slug')->asArray()->all();
        }

        return self::$settingsCache[$slug]['value'];
    }
}
