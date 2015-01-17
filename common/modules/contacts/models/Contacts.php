<?php

namespace common\modules\contacts\models;

use Yii;

/**
 * This is the model class for table "contacts".
 *
 * @property string $id
 * @property string $name
 * @property string $value
 */
class Contacts extends \yii\db\ActiveRecord
{
    /* @var  */
    public static $contactsCache;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contacts';
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
        if (is_null(self::$contactsCache)) {
            self::$contactsCache = self::find()->select(['slug', 'value'])->indexBy('slug')->asArray()->all();
        }

        return self::$contactsCache[$slug]['value'];
    }
}
