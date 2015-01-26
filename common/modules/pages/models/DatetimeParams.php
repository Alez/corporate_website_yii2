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

    public function beforeSave($insert)
    {
        if ($this->value) {
            $this->setAttribute('value', strtotime($this->value));

        }

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();
        if ($this->value) {
            $this->value = date('y-m-d H:i', $this->value);
        }
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
