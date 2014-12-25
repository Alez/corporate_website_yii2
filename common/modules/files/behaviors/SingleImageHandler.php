<?php

namespace common\modules\files\behaviors;

use yii\web\UploadedFile;
use common\modules\files\models\Files;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * Используется для реализации функционала сохранения, добавления и просмотра изображений при помощи модуля Files
 * Пример использования:
 * ```php
 *      public function behaviors()
 *      {
 *          return [
 *              [
 *                  'class' => SingleImageHandler::className(),
 *                  'attributes' => [
 *                      'gallery_preview_id' => 'gallery_preview_file',
 *                  ]
 *              ],
 *          ];
 *      }
 * ```
 * В attributes в поле ключа нужно указать название таблицы хранящее сериализованный массив ID файлов,
 * в поле значения нужно указать название поля созданного в модели для загрузки файла.
 *
 * @package common\modules\files\behaviors
 */
class SingleImageHandler extends AbstractImageHandler
{
    /**
     * Сохранит поле со множественными картинками в БД.
     * Создаст запись в табличе Files, заберёт оттуда новый ID и поместит в поле таблицы указаное в attributes
     *
     * @param $event
     */
    public function evaluateAttributes($event)
    {
        foreach ($this->attributes as $dbName => $fieldName) {
            if (!property_exists($event->sender, $fieldName)) {
                throw new \Exception('В модели"' . $event->sender->className() . '" необходимо создать поле с именем "' . $fieldName . '"');
            }

            $event->sender->$fieldName = UploadedFile::getInstance($event->sender, $fieldName);

            if ($event->sender->$fieldName) {
                if ($fileId = $event->sender->getOldAttribute($dbName)) {
                    $oldFile = Files::findOne($fileId);
                    if ($oldFile) {
                        $oldFile->delete();
                    }
                }
                $newFileId = (new Files())->addFile($event->sender->$fieldName, $event->sender->className());
                $event->sender->setAttribute($dbName, $newFileId);
            }
        }
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

    /**
     * Перед удалением записи удалить все связанные с ней файлы
     *
     * @param $event
     * @throws \Exception
     */
    public function clearFiles($event)
    {
        foreach ($this->attributes as $fieldName) {
            if ($fileId = $event->sender->getAttribute($fieldName)) {
                if ($file = Files::findOne(['id' => $fileId])) {
                    $file->delete();
                }
            }
        }
    }
}