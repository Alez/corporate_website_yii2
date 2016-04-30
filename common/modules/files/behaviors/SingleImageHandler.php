<?php

namespace common\modules\files\behaviors;

use yii\web\UploadedFile;
use common\modules\files\models\ImageRecord;
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
     * Сохранит поле со картинкой в БД.
     * Создаст запись в таблице Files, заберёт оттуда новый ID и поместит в поле таблицы указаное в attributes
     *
     * @param $event
     * @throws \Exception
     */
    public function beforeSave($event)
    {
        foreach ($this->attributes as $fieldName => $propertyName) {
            if (!property_exists($event->sender, $propertyName)) {
                throw new \Exception('В модели "' . $event->sender->className() . '" необходимо создать свойство с именем "' . $propertyName . '"');
            }


            if ($event->sender->$propertyName) {
                if ($fileId = $event->sender->getOldAttribute($fieldName)) {
                    $oldFile = ImageRecord::findOne($fileId);
                    if ($oldFile) {
                        $oldFile->delete();
                    }
                }

                if ($newFile = ImageRecord::addFile($event->sender->$propertyName, $event->sender->className())) {
                    $event->sender->setAttribute($fieldName, $newFile->id);
                }
            }
        }
    }

    /**
     * Добавит файл в общую валидацию
     *
     * @param $event
     * @throws \Exception
     */
    public function beforeValidate($event)
    {
        foreach ($this->attributes as $fieldName => $propertyName) {
            if (!property_exists($event->sender, $propertyName)) {
                throw new \Exception('В модели "' . $event->sender->className() . '" необходимо создать свойство с именем "' . $propertyName . '"');
            }

            $event->sender->$propertyName = UploadedFile::getInstance($event->sender, $propertyName);
        }
    }

    /**
     * Вернёт все id файлов продукта в виде массива
     *
     * @param string $propertyName Название поля откуда брать id файлов
     *
     * @return array
     */
    public function getFilesId($propertyName)
    {
        return ArrayHelper::getColumn($this->owner->getFiles($propertyName), 'id');
    }

    /**
     * Перед удалением записи удалить все связанные с ней файлы
     *
     * @param $event
     * @throws \Exception
     */
    public function beforeDelete($event)
    {
        foreach (array_keys($this->attributes) as $fieldName) {
            if ($fileId = $event->sender->getAttribute($fieldName)) {
                if ($file = ImageRecord::findOne(['id' => $fileId])) {
                    $file->delete();
                }
            }
        }
    }
}
