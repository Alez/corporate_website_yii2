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
 *                  'class' => MultiImageHandler::className(),
 *                  'attributes' => [
 *                      'gallery_images_id' => 'gallery_images_file',
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
class MultiImageHandler extends AbstractImageHandler
{
    /**
     * Сохранит поле со множественными картинками в БД.
     * Создаст запись в табличе Files, заберёт оттуда новый ID и поместит в поле таблицы указаное в attributes
     *
     * @param $event
     */
    public function beforeSave($event)
    {
        foreach ($this->attributes as $propertyName) {
            if (!property_exists($event->sender, $propertyName)) {
                throw new \Exception('В модели "' . $event->sender->className() . '" необходимо создать свойство с именем "' . $propertyName . '"');
            }
        }

        foreach ($this->attributes as $fieldName => $propertyName) {
            if ($event->sender->$propertyName && $event->sender->{$propertyName}[0] !== '') {
                $newFilesIdArray = [];
                foreach ($event->sender->$propertyName as $file) {
                    $newFilesIdArray[] = (new Files())->addImage($file, $event->sender->className(), null, null);
                }

                if ($event->sender->getAttribute($fieldName)) {
                    $filesIdArray = unserialize($event->sender->getAttribute($fieldName));
                } else {
                    $filesIdArray = [];
                }
                $filesIdArray = ArrayHelper::merge($filesIdArray, $newFilesIdArray);
                $event->sender->setAttribute($fieldName, serialize($filesIdArray));
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
            $event->sender->$propertyName = UploadedFile::getInstances($event->sender, $propertyName);
        }
    }

    /**
     * Вернёт все файлы продукта в виде массива объектов
     *
     * @param string $propertyName Название свойства где лежат id файлов
     *
     * @return Files[]|null
     */
    public function getFiles($propertyName)
    {
        $filesId = unserialize($this->owner->getAttribute($propertyName));
        if (is_array($filesId) && count($filesId) > 0) {
            return Files::findAll($filesId);
        }
        return null;
    }

    /**
     * Вернёт все файлы продукта в виде массива объектов
     *
     * @param string $propertyName Название свойства где лежат id файлов
     * @param string $index Порядковый номер файла
     *
     * @return Files|null
     */
    public function getFile($propertyName, $index = 0)
    {
        $filesId = unserialize($this->owner->getAttribute($propertyName));
        if (isset($filesId[$index])) {
            return Files::findOne($filesId[$index]);
        }
        return null;
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
            if ($filesId = unserialize($this->getAttribute($fieldName))) {
                foreach ($filesId as $fileId) {
                    if ($file = Files::findOne(['id' => $fileId])) {
                        $file->delete();
                    }
                }
            }
        }
    }
}