<?php

namespace common\modules\pages\models;

use Yii;

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
class RedactorParams extends PagesParams implements PagesParamsInterface
{
    const TYPE = 'redactor';

    public function init()
    {
        $this->setAttribute('type', self::TYPE);
        parent::init();
    }

    /**
     * Проверить является ли тип загрузкой файла
     *
     * @return bool
     */
    public function isFileType()
    {
        return false;
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
        return false;
    }
}
