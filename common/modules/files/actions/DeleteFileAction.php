<?php

namespace common\modules\files\actions;

use yii\base\Action;
use Yii;
use yii\base\InvalidParamException;

class DeleteFileAction extends Action
{
    /** @var string Класс у которого будут удалены файлы */
    public $relatedClass;

    /**
     * @param $id int ID модели у которой происходит удаление файла
     * @param $fieldName string Поле в котором хранится ID файла или группы
     * @param $fileId int|null ID удаляемого файла
     * @return mixed
     */
    public function run($id, $fieldName, $fileId = null)
    {
        if (!class_exists($this->relatedClass)) {
            throw new InvalidParamException();
        }
        Yii::$app->response->format = 'json';

        $material = call_user_func([$this->relatedClass, 'findOne'], $id);

        if ($fileId) {
            return $material->deleteFile($fieldName, (int)$fileId);
        } else {
            return $material->deleteFile($fieldName);
        }
    }
}
