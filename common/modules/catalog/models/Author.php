<?php

namespace common\modules\catalog\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "author".
 *
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property string $description
 *
 * @property LinkMaterialAuthor[] $linkMaterialAuthors
 * @property Material[] $materials
 */
class Author extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'author';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['slug', 'name'], 'string', 'max' => 255],
            [['slug', 'name'], 'trim'],
            [['slug'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => 'Имя',
            'slug'        => 'ЧПУ',
            'created_at'  => 'Создано',
            'updated_at'  => 'Обновлено',
            'description' => 'Описание',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLinkMaterialAuthors()
    {
        return $this->hasMany(LinkMaterialAuthor::className(), ['author_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterials()
    {
        return $this->hasMany(Material::className(), ['id' => 'material_id'])->viaTable('link_material_author', ['author_id' => 'id']);
    }
}
