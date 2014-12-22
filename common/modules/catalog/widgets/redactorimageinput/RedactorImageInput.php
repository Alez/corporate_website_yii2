<?php

namespace common\modules\catalog\widgets\redactorimageinput;

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
        if ($this->hasModel() && isset($this->imageSource[0])) {
            echo $this->render('_preview', [
                    'widget' => $this,
                ]);
        }

        echo $this->renderInput(isset($this->options['multiple']));
    }
}
