<?php
use yii\helpers\Html;
use backend\assets_b\LoginAsset;

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $model \common\modules\user\models\LoginForm */
$this->title = 'Авторизация';
LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="robots" content="noindex"/>
    <meta name="robots" content="nofollow"/>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
<div class="container">
    <?= $content ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
