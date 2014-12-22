<?php

namespace common\modules\pages\models;

use Yii;
use common\modules\pages\models\PagesTemplates;
use common\modules\files\models\Files;
use common\components\helpers\SerializeHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "pages_params".
 *
 * @property string $id
 * @property string $page_id
 * @property string $value
 * @property string $type
 *
 * @property PagesTemplatesParams $pagesTemplatesParams
 * @property Pages $page
 */
class MultifileParams extends PagesParams implements PagesParamsInterface
{
    const TYPE = 'multifile';

    /* @var \yii\web\UploadedFile */
    public $uploadFile;

    public function init()
    {
        $this->setAttribute('type', self::TYPE);
        parent::init();
    }

    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['uploadFile'], 'safe'],
            ]);
    }

    public function beforeDelete()
    {
        // Перед удалением атрибута страницы, нужно удалить связанные с ним файлы, если они есть
        if ($filesArray = SerializeHelper::decode($this->getAttribute('value'))) {
            foreach (SerializeHelper::decode($this->getAttribute('value')) as $delFileId) {
                Files::find()->where(['id' => $delFileId])->one()->delete();
            }
        }

        return parent::beforeDelete();
    }

    /**
     * Вернёт все файлы продукта в виде массива объектов
     *
     * @return Files[]
     */
    public function getFiles()
    {
        return Files::find()->where(['IN', 'id', SerializeHelper::decode($this->getAttribute('value'))])->all();
    }

    /**
     * Удалит файл из сериализованного поля с id файлов
     *
     * @return bool
     */
    public function deleteFile($deletingId)
    {
        $filesId = $this->getFilesId();
        foreach ($filesId as $key => $fileId) {
            if ((int)$fileId === $deletingId) {
                Files::findOne($deletingId)->delete();
                unset($filesId[$key]);
            }
        }

        // Проверим, остались ли там еще файлы
        if (count($filesId) === 0) {
            $this->setAttribute('value', null);
        } else {
            $this->setAttribute('value', SerializeHelper::encode($filesId));
        }

        if ($this->updateAttributes(['value'])) {
            return true;
        }

        return false;
    }


    /**
     * Вернёт все файлы продукта в виде массива объектов
     *
     * @return Files[]
     */
    public function getFilesId()
    {
        return ArrayHelper::getColumn($this->getFiles(), 'id');
    }

    /**
     * Проверить является ли тип загрузкой файла
     *
     * @return bool
     */
    public function isFileType()
    {
        return true;
    }

    /**
     * Проверить является ли тип множественной загрузкой файла
     *
     * @return bool
     */
    public function isMultifileType()
    {
        return true;
    }

    /**
     * Проверить является ли тип загрузкой изображения
     *
     * @return bool
     */
    public function isImageType()
    {
        return false;
    }
}
