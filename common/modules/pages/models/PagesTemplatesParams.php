<?php

namespace common\modules\pages\models;

use Yii;

/**
 * This is the model class for table "pages_templates_params".
 *
 * @property string $id
 * @property string $name
 * @property integer $type
 * @property string $pages_templates_id
 *
 * @property PagesParams[] $pagesParams
 * @property PagesTemplates $pagesTemplates
 */
class PagesTemplatesParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pages_templates_params';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'pages_templates_id'], 'required'],
            [['type', 'pages_templates_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'type' => 'Type',
            'pages_templates_id' => 'Pages Templates ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagesParams()
    {
        return $this->hasMany(PagesParams::className(), ['page_templates_params_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPagesTemplates()
    {
        return $this->hasOne(PagesTemplates::className(), ['id' => 'pages_templates_id']);
    }
}
