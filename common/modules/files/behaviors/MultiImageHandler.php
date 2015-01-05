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
        foreach ($this->attributes as $fieldName) {
            if (!property_exists($event->sender, $fieldName)) {
                throw new \Exception('В модели"' . $event->sender->className() . '" необходимо создать поле с именем "' . $fieldName . '"');
            }
        }

        foreach ($this->attributes as $dbName => $fieldName) {
            if ($event->sender->$fieldName && $event->sender->{$fieldName}[0] !== '') {
                $newFilesIdArray = [];
                foreach ($event->sender->$fieldName as $file) {
                    $newFilesIdArray[] = (new Files())->addImage($file, $event->sender->className(), null, null);
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
     * Добавит файл в общую валидацию
     *
     * @param $event
     * @throws \Exception
     */
    /**
     * Добавит файл в общую валидацию
     *
     * @param $event
     * @throws \Exception
     */
    public function beforeValidate($event)
    {
        foreach ($this->attributes as $dbName => $fieldName) {
            if (!property_exists($event->sender, $fieldName)) {
                throw new \Exception('В модели"' . $event->sender->className() . '" необходимо создать поле с именем "' . $fieldName . '"');
            }
            $event->sender->$fieldName = UploadedFile::getInstances($event->sender, $fieldName);
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
    public function beforeDelete($event)
    {
        foreach ($this->attributes as $fieldName) {
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