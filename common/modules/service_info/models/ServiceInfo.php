<?php

namespace common\modules\service_info\models;

use Yii;

/**
 * This is the model class for table "service_info".
 *
 * @property string $id
 * @property string $name
 * @property string $content
 */
class ServiceInfo extends \yii\db\ActiveRecord
{
    /* @var  */
    public static $serviceInfoCache;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'content'], 'required'],
            [['content'], 'string'],
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
            'name' => 'Название',
            'content' => 'Содержимое',
        ];
    }

    public static function get($slug)
    {
        if (is_null(static::$serviceInfoCache)) {
            static::$serviceInfoCache = ServiceInfo::find()->select(['slug', 'content'])->indexBy('slug')->asArray()->all();
        }

        return static::$serviceInfoCache[$slug]['content'];
    }
}
