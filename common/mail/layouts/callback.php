<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $name string main view render result */
/* @var $phone string main view render result */
/* @var $url string main view render result */
/* @var $article string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<h1>Обращение с сайта</h1>
<div>Имя: <?= $name ?></div>
<div>Телефон: <?= $phone ?></div>
<div>Адрес отправки запроса: <?= $_SERVER['REQUEST_SCHEME'] ?>://<?= $_SERVER['SERVER_NAME'] ?><?= $url ?></div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
