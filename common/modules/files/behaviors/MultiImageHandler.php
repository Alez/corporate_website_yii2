<?php

namespace common\modules\files\behaviors;

use yii\web\UploadedFile;
use common\modules\files\models\ImageRecord;
use common\modules\files\models\FileRecord;
use Yii;
use yii\db\Query;

/**
 * Используется для реализации функционала удаление и добавления изображений по событиям
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
     * Создаст запись в таблице Files, заберёт оттуда новый ID и поместит в поле таблицы указаное в attributes
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
                if (!($groupId = $event->sender->getAttribute($fieldName))) {
                    $groupId = (new Query())->from(FileRecord::tableName())->max('group_id') ?? 0;
                    ++$groupId;
                }
                foreach ($event->sender->$propertyName as $file) {
                    ImageRecord::addImage(
                        $file,
                        $event->sender->className(),
                        null,
                        null,
                        ImageRecord::IMAGE_MAX_WIDTH,
                        ImageRecord::IMAGE_MAX_HEIGHT,
                        100,
                        ImageRecord::PROPORTIONAL,
                        $groupId
                    );
                }
                $event->sender->setAttribute($fieldName, $groupId);
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
     * Перед удалением записи удалить все связанные с ней файлы
     *
     * @param $event
     * @throws \Exception
     */
    public function beforeDelete($event)
    {
        foreach (array_keys($this->attributes) as $fieldName) {
            if ($groupId = $this->owner->getAttribute($fieldName)) {
                foreach (FileRecord::find()->where(['group_id' => $groupId])->all() as $file) {
                    $file->delete();
                }
            }
        }
    }
}