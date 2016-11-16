<?php

namespace common\modules\files\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use common\modules\files\models\ImageRecord;

abstract class AbstractImageHandler extends Behavior
{
    public $attributes;

    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            throw new \Exception('Необходимо указать атрибуты содержащие ID файлов');
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
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
            if ($fieldData = $this->owner->getAttribute($fieldName)) {
                $fileIds = unserialize($fieldData);
                $fileIds = array_flip($fileIds);
                // Если в массиве ключами записаны оригинальные картинки
                $originalFileId = $fileIds[$fileId] ? $fileIds[$fileId] : null;
                unset($fileIds[$fileId]);
                $fileIds = array_keys($fileIds);
                if (count($fileIds)) {
                    $fieldData = serialize($fileIds);
                    $this->owner->setAttribute($fieldName, $fieldData);
                } else {
                    $this->owner->setAttribute($fieldName, null);
                }
                $this->owner->updateAttributes([$fieldName]);
                if (ImageRecord::findOne($fileId)->delete()) {
                    if ($originalFile = ImageRecord::findOne($originalFileId)) {
                        $originalFile->delete();
                    }

                    return true;
                }
            }
        } else {
            $fileId = $this->owner->getAttribute($fieldName);
            $this->owner->setAttribute($fieldName, null);
            $this->owner->updateAttributes([$fieldName]);
            if (ImageRecord::findOne($fileId)->delete()) {
                return true;
            }
        }

        return false;
    }

    abstract public function beforeValidate($event);
    abstract public function afterSave($event);
    abstract public function beforeDelete($event);
}