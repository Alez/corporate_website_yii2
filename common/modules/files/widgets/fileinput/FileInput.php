<?php

namespace common\modules\files\widgets\fileinput;

use common\modules\files\models\Files;
use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;

class FileInput extends InputWidget
{
    /* @var \common\modules\files\models\Files|\common\modules\files\models\Files[] Откуда брать изображения для превью */
    public $fileSource;

    /* @var string Поле где хранится номер файла */
    public $fieldName;

    public function init()
    {
        if (!isset($this->fileSource) && $this->hasModel()) {
            $this->fileSource = Files::findOne($this->model->{$this->fieldName});
        }

        if (isset($this->options['class'])) {
            $this->options['class'] .= ' imageInput-js';
        } else {
            $this->options['class'] = 'imageInput-js';
        }

        FileInputAsset::register($this->getView());
    }

    public function run()
    {
        $result = '';

        if ($this->hasModel() && !is_null($this->fileSource)) {
            $result .= '<div class="form-group">
                <label class="control-label" for="productImgInput">';
            $result .= $this->name;
            $result .= '</label>
                <div class="filesEditWrapper">
                    <ul id="galleryPreviewList" class="filesPreviewList galleryList-js">';


            if (!is_array($this->fileSource)) {
                $this->fileSource = [$this->fileSource];
            }

            foreach ($this->fileSource as $file) {
                $result .= $this->render('_file.php', [
                        'file'      => $file,
                        'model'      => $this->model,
                        'fieldName'  => !is_null($this->fieldName) ? $this->fieldName : $this->attribute,
                        'isMultiple' => isset($this->options['multiple']),
                    ]);
            }

            $result .= '</ul>
                </div>
            </div>';
        }

        if (isset($this->options['multiple'])) {
            $result .= $this->renderMultiple();
        } else {
            $result .= $this->renderSingle();
        }

        echo $result;
    }

    public function renderSingle()
    {
        if ($this->hasModel()) {
            return Html::activeFileInput($this->model, $this->attribute, $this->options);
        } else {
            return Html::fileInput($this->name, $this->value, $this->options);
        }
    }

    public function renderMultiple()
    {
        if ($this->hasModel()) {
            return Html::activeFileInput($this->model, $this->attribute . '[]', $this->options);
        } else {
            return Html::fileInput($this->name . '[]', $this->value, $this->options);
        }
    }
}
