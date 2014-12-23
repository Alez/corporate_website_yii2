<?php

namespace common\modules\files\behaviors;

use yii\base\Behavior;
use yii\base\Exception;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;
use common\modules\files\models\Files;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Используется для реализации функционала просмотра и сохранения изображений при помощи модуля Files
 * Пример использования:
 * ```php
 *      public function behaviors()
 *      {
 *          return [
 *              [
 *                  'class' => MultiImageHandler::className(),
 *                  'attributes' => [
 *                      'gallery_images_id' => 'gallery_images_file',
 *                  ]
 *              ],
 *          ];
 *      }
 * ```
 * В attributes в поле ключа попадает название таблицы хранящее сериализованный массив ID файлов,
 * в поле значения попадает название поля созданного в модели для загрузки файла.
 *
 * @package common\modules\files\behaviors
 */
class MultiImageHandler extends Behavior
{
    public $attributes;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'evaluateAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'evaluateAttributes',
        ];
    }

    /**
     * Сохранит поле со множественными картинками в БД.
     * Создаст запись в табличе Files, заберёт оттуда новый ID и поместит в поле таблицы указаное в attributes
     *
     * @param $event
     */
    public function evaluateAttributes($event)
    {
        if (empty($this->attributes)) {
            throw new Exception('Необходимо указать атрибуты содержащие ID файлов');
        }

        $event->sender->gallery_images_file = UploadedFile::getInstances($event->sender, 'gallery_images_file');

        foreach ($this->attributes as $dbName => $fieldName) {
            if ($event->sender->{$fieldName} && $event->sender->{$fieldName}[0] !== '') {
                $newFilesIdArray = [];
                foreach ($event->sender->{$fieldName} as $file) {
                    $newFileId = (new Files())->addImage($file, self::className(), null, null);
                    $newSmallFileId = Files::findOne($newFileId)->makeSmallCopy();
                    $newFilesIdArray[$newFileId] = $newSmallFileId;
                }

                if ($event->sender->getAttribute($dbName)) {
                    $filesIdArray = unserialize($event->sender->getAttribute($dbName));
                } else {
                    $filesIdArray = [];
                }
                $filesIdArray = ArrayHelper::merge($filesIdArray, $newFilesIdArray);
                $event->sender->setAttribute($dbName, serialize($filesIdArray));
            }
        }
    }

    /**
     * Вернёт все файлы продукта в виде массива объектов
     *
     * @param string $fieldName Название поля откуда брать id файлов
     *
     * @return Files[]
     */
    public function getFiles($fieldName)
    {
        return Files::find()->where(['IN', 'id', unserialize($this->owner->getAttribute($fieldName))])->all();
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
                if (Files::findOne($fileId)->delete()) {
                    if ($originalFile = Files::findOne($originalFileId)) {
                        $originalFile->delete();
                    }

                    return true;
                }
            }
        } else {
            $fileId = $this->owner->getAttribute($fieldName);
            $this->owner->setAttribute($fieldName, null);
            $this->owner->updateAttributes([$fieldName]);
            if (Files::findOne($fileId)->delete()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Вернёт все id файлов продукта в виде массива
     *
     * @param string $fieldName Название поля откуда брать id файлов
     *
     * @return array
     */
    public function getFilesId($fieldName)
    {
        return ArrayHelper::getColumn($this->owner->getFiles($fieldName), 'id');
    }
}