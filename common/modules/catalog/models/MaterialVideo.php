<?php

namespace common\modules\catalog\models;

use Yii;
use common\components\helpers\SerializeHelper;

/**
 * This is the model class for table "material_video".
 *
 * @property string $id
 * @property string $material_id
 * @property string $video
 * @property string $description
 *
 * @property Material $material
 */
class MaterialVideo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_id', 'video'], 'required'],
            [['material_id'], 'integer'],
            [['description'], 'string'],
            [['video'], 'string', 'max' => 255],
            [['video'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'material_id' => 'Material ID',
            'video' => 'Html-код видео для вставки',
            'description' => 'Описание',
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
     * Удалит файл на основе входных параметров.
     * Если нет $fileId, то удаляем единичный файл, если есть, то удаляем множественный
     *
     * @param string $fieldName Название поля где хранятся Id файла/файлов
     * @param int|null $fileId Номер файла
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteFile($fieldName, $fileId = null)
    {
        if ($fileId) {
            if ($fieldData = $this->getAttribute($fieldName)) {
                $fileIds = SerializeHelper::decode($fieldData);
                $fileIds = array_flip($fileIds);
                unset($fileIds[$fileId]);
                $fileIds = array_keys($fileIds);
                if (count($fileIds)) {
                    $fieldData = SerializeHelper::encode($fileIds);
                    $this->setAttribute($fieldName, $fieldData);
                } else {
                    $this->setAttribute($fieldName, null);
                }
                $this->updateAttributes([$fieldName]);
                if (Files::findOne($fileId)->delete()) {
                    return true;
                }
            }
        } else {
            $fileId = $this->getAttribute($fieldName);
            $this->setAttribute($fieldName, null);
            $this->updateAttributes([$fieldName]);
            if (Files::findOne($fileId)->delete()) {
                return true;
            }
        }

        return false;
    }
}
