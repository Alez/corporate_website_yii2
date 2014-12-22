<?php

namespace common\modules\files\widgets\imageinput;

use common\modules\files\models\Files;
use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;

class ImageInput extends InputWidget
{
    /* @var \common\modules\files\models\Files|\common\modules\files\models\Files[] Откуда брать изображения для превью */
    public $imageSource;

    /* @var string Поле где хранится номер файла */
    public $fieldName;

    public function init()
    {
        if (!isset($this->imageSource) && $this->hasModel()) {
            $this->imageSource = Files::findOne($this->model->{$this->fieldName});
        }

        if (isset($this->options['class'])) {
            $this->options['class'] .= ' imageInput-js';
        } else {
            $this->options['class'] = 'imageInput-js';
        }

        // Магия, что бы одиночная картинка точно так же как и множественные была в массиве
        if (!is_array($this->imageSource)) {
            $this->imageSource = [$this->imageSource];
        }

        ImageInputAsset::register($this->getView());
    }

    public function run()
    {
        // Отрисовка уже загруженных картинок
        if ($this->hasModel() && isset($this->imageSource[0])) {
            echo $this->render('_preview', [
                   'widget' => $this,
                ]);
        }

        echo $this->renderInput(isset($this->options['multiple']));
    }

    public function renderInput($isMultiple)
    {
        if ($this->hasModel()) {
            $name = $isMultiple ? $this->attribute . '[]': $this->attribute;

            return Html::activeFileInput($this->model, $name, $this->options);
        } else {
            $name = $isMultiple ? $this->name . '[]': $this->name;

            return Html::fileInput($name, $this->value, $this->options);
        }
    }
}
