<?php

namespace common\modules\catalog\models;

use Yii;
use common\modules\files\models\Files;
use common\components\helpers\SerializeHelper;

/**
 * This is the model class for table "material_audio".
 *
 * @property string $id
 * @property string $audio_id
 * @property string $description
 * @property string $material_id
 *
 * @property Material $material
 */
class MaterialAudio extends \yii\db\ActiveRecord
{
    public $audio_file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material_audio';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['audio_id', 'material_id'], 'integer'],
            [['description'], 'string'],
            [['audio_file'], 'safe'],
            [['audio_file'], 'file',
                'extensions' => 'mp3',
                'checkExtensionByMimeType' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'audio_id'    => 'Audio ID',
            'audio_file'  => 'Аудио файл',
            'description' => 'Описание',
            'material_id' => 'Material ID',
        ];
    }

    public function beforeSave($insert)
    {
        // Сохраним единичные файлы, если они были загружены
        foreach (
            [
                'audio_id' => 'audio_file',
            ] as $dbName => $fieldName
        ) {
            if ($this->{$fieldName}) {
                if ($fileId = $this->getOldAttribute($dbName)) {
                    $oldFile = Files::findOne($fileId);
                    if ($oldFile) {
                        $oldFile->delete();
                    }
                }
                $newFileId = (new Files())->addFile($this->{$fieldName}, self::className());
                $this->setAttribute($dbName, $newFileId);
            }
        }

        return parent::beforeSave($insert);
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
