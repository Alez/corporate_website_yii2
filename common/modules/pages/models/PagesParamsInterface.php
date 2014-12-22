<?php

namespace common\modules\pages\models;

interface PagesParamsInterface
{
    /**
    * Проверить является ли тип загрузкой файла
    *
    * @return bool
    */
    public function isFileType();

    /**
     * Проверить является ли тип множественной загрузкой файла
     *
     * @return bool
     */
    public function isMultifileType();

    /**
     * Проверить является ли тип изображением
     *
     * @return bool
     */
    public function isImageType();
}
?>