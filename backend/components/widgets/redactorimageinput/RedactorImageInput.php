<?php

namespace backend\components\widgets\redactorimageinput;

use Yii;
use common\modules\files\widgets\imageinput\ImageInput;

class RedactorImageInput extends ImageInput
{
    public $redactorId;

    public function init()
    {
        parent::init();

        RedactorImageInputAsset::register($this->getView());
    }

    public function run()
    {
        if (!$this->imageSource) {
            $this->imageSource = $this->model->getFiles('post_images_id');
        }

        if ($this->hasModel() && isset($this->imageSource[0])) {
            echo $this->render('_preview', [
                    'widget' => $this,
                ]);
        }

        echo $this->renderInput(isset($this->options['multiple']));
    }
}
