<?php

namespace common\components\widgets\transliterateInput;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class TransliterateInput extends InputWidget
{
    public $from;
    public $options;

    public function init()
    {
        if (!isset($this->options['id'])) {
            if ($this->hasModel()) {
                $this->options['id'] = Html::getInputId($this->model, $this->attribute);
            } else {
                $this->options['id'] = $this->getId();
            }
        }

        if (!isset($from)) {
            $this->from = '[name="' . $this->model->formName() . '[name]"]';
        }
        if (isset($this->options['class'])) {
            $this->options['class'] .= ' form-control';
        } else {
            $this->options['class'] = 'form-control';
        }
    }

    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextInput($this->model, $this->attribute, $this->options) . "\n";
        } else {
            echo Html::textInput($this->name, $this->value, $this->options) . "\n";
        }
        echo Html::checkbox('', true, [
                    'label' => 'Транслитерация из названия',
                    'class' => 'translitEnabled-js'
                ]) . "\n";

        echo $this->render('_js', [
                'from'    => $this->from,
                'to'      => '#' . $this->options['id'],
                'toLower' => true,
            ]);
    }
}
