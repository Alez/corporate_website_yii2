<?php

namespace common\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "material_external_crosslinking".
 *
 * @property string $id
 * @property string $name
 * @property string $url
 * @property string $created_at
 * @property string $update_at
 * @property string $material_id
 *
 * @property Material $material
 */
class MaterialExternalCrosslinking extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_external_crosslinking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url', 'material_id'], 'required'],
            [['created_at', 'update_at', 'material_id'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Текст ссылки',
            'url' => 'Адрес',
            'created_at' => 'Создано',
            'update_at' => 'Обновлено',
            'material_id' => 'Material ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(Material::className(), ['id' => 'material_id']);
    }
}
