<?php

/* @var $this yii\web\View */
/* @var $name string Название удалённой страницы */

$this->title = 'Страница удалена';

echo "Страница \"$name\" успешно удалена";
echo '<br>';
echo \yii\helpers\Html::a('Вернуться на главную', \yii\helpers\Url::to('@web'));