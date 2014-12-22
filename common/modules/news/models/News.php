<?php

namespace common\modules\news\models;

use common\modules\files\models\Files;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "news".
 *
 * @property string $id
 * @property string $name
 * @property string $announce
 * @property string $content
 * @property integer $created_at
 * @property string $slug
 * @property integer $img_id
 *
 * @property Files $image
 */
class News extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'required'],
            [['slug'], 'unique'],
            [['name', 'slug'], 'trim'],
            [['announce', 'content'], 'string'],
            [['created_at', 'updated_at', 'img_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['file'], 'safe'],
            [['file'], 'file',
                'extensions' => 'jpg, jpeg, png, gif',
                'mimeTypes' => 'image/jpeg, image/png, image/gif',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Название',
            'slug'       => 'ЧПУ',
            'announce'   => 'Анонс',
            'content'    => 'Содержание',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
            'file'       => 'Изображение',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function beforeDelete()
    {
        // Перед удалением новости, нужно снести связанные с ней файлы, если они есть
        if ($fileId = $this->getAttribute('img_id')) {
            if ($file = Files::findOne(['id' => $fileId])) {
                $file->delete();
            }
        }

        return parent::beforeDelete();
    }

    public function beforeSave($insert)
    {
        if ($this->file) {
            if ($fileId = $this->getOldAttribute('img_id')) {
                $oldFile = Files::findOne($fileId);
                if ($oldFile) {
                    $oldFile->delete();
                }
            }

            $newId = (new Files())->addImage($this->file, self::className());

            $this->setAttribute('img_id', $newId);
        }

        return parent::beforeSave($insert);
    }

    public function getImage()
    {
        return $this->hasOne(Files::className(), ['id' => 'img_id']);
    }

    /**
     * Подготовит УРЛ для новости (фронтенд)
     *
     * @return string
     */
    public function getUrl()
    {
        return '/news/' . $this->getAttribute('slug');
    }
}
