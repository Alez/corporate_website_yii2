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
 *
 * @property PagesTemplatesParams $pagesTemplatesParams
 * @property Pages $page
 */
class ImageParams extends PagesParams implements PagesParamsInterface
{
    const TYPE = 'image';

    /* @var \yii\web\UploadedFile */
    public $uploadFile;

    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['uploadFile'],
                    'file',
                    'extensions' => 'jpg, jpeg, png, gif',
                    'mimeTypes' => 'image/jpeg, image/png, image/gif',
                ],
                [['uploadFile'], 'safe'],
            ]);
    }

    public function beforeDelete()
    {
        // Перед удалением атрибута страницы, нужно снести связанные с ним файлы, если они есть
        if ($fileId = $this->getAttribute('value')) {
            if ($file = Files::findOne(['id' => $fileId])) {
                $file->delete();
            }
        }

        return parent::beforeDelete();
    }

    public function beforeSave($insert)
    {
        // Сохраним файлы, если они были загружены
        if ($this->uploadFile) {
            if ($fileId = $this->getOldAttribute('value')) {
                $oldFile = Files::findOne($fileId);
                if ($oldFile) {
                    $oldFile->delete();
                }
            }

            $newImageId = (new Files())->addImage($this->uploadFile, self::className());

            $this->setAttribute('value', $newImageId);
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(Files::className(), ['id' => 'value']);
    }

    /**
     * Удалить единичный файл, обновит записи в базе, удалит файл из ФС
     *
     * @return bool|int
     */
    public function deleteFile()
    {
        $fileId = $this->getAttribute('value');
        $this->setAttribute('value', null);
        if ($this->updateAttributes(['value'])) {
            return Files::findOne($fileId)->delete();
        }

        return false;
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
        return false;
    }

    /**
     * Проверить является ли тип загрузкой изображения
     *
     * @return bool
     */
    public function isImageType()
    {
        return true;
    }
}
