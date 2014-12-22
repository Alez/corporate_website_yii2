<?php

namespace common\modules\catalog\models;

use Yii;

/**
 * This is the model class for table "link_material_age".
 *
 * @property string $material_id
 * @property string $age_id
 *
 * @property Age $age
 * @property Material $material
 */
class LinkMaterialAge extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'link_material_age';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_id', 'age_id'], 'required'],
            [['material_id', 'age_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'material_id' => 'Material ID',
            'age_id' => 'Age ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAge()
    {
        return $this->hasOne(Age::className(), ['id' => 'age_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(Material::className(), ['id' => 'material_id']);
    }
}
