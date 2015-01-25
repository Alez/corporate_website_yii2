<?php

namespace common\modules\pages\models;

use Yii;

/**
 * This is the model class for table "pages_params".
 *
 * @property string $id
 * @property string $page_id
 * @property string $value
 *
 * @property PagesTemplatesParams $template
 * @property Pages $page
 */
class DatetimeParams extends PagesParams implements PagesParamsInterface
{
    const TYPE = 'datetime';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['value'], 'string'],
        ]);
    }

    public function setValue($value)
    {
        $this->setAttribute('value', strtotime($value));
    }

    public function getValue($format = 'd.m.y H:i')
    {
        return date($format, $this->value);
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
