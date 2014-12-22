<?php

namespace common\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "material_crosslinking".
 *
 * @property string $id
 * @property string $material_id
 * @property string $to
 * @property string $created_at
 * @property string $update_at
 *
 * @property Material $material
 */
class MaterialCrosslinking extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_crosslinking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['to'], 'required'],
            [['to', 'created_at', 'update_at', 'material_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'to' => 'Связь',
            'created_at' => 'Создана',
            'update_at' => 'Обновлена',
            'material_id' => 'Material Id',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(Material::className(), ['id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrosslinkedMaterial()
    {
        return $this->hasOne(Material::className(), ['id' => 'to']);
    }
}
