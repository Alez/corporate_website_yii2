<?php
use backend\assets_b\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\modules\pages\models\Pages;
use backend\components\widgets\Menu;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
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
<body class="skin-blue">
<?php $this->beginBody() ?>
<!-- header logo: style can be found in header.less -->
<header class="header">
<a href="<?= Url::to('@web/')?>" class="logo">
    <!-- Add the class icon to your logo image or logo icon to add the margining -->
    Admin Panel
</a>
<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top" role="navigation">
<!-- Sidebar toggle button-->
<a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
</a>
<div class="navbar-right">
<ul class="nav navbar-nav">
<!-- User Account: style can be found in dropdown.less -->
<li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="glyphicon glyphicon-user"></i>
        <span><?= Yii::$app->user->identity->email ?>&nbsp;<i class="caret"></i></span>
    </a>
    <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header bg-light-blue">
<!--            <img src="../img/avatar3.png" class="img-circle" alt="User Image" />-->
            <p>
                <?= Yii::$app->user->identity->email ?> - Администратор
                <small>Member since <?= date('d.m.Y', Yii::$app->user->identity->created_at) ?></small>
            </p>
        </li>
        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
                <a href="#" class="btn btn-default btn-flat">Профиль</a>
            </div>
            <div class="pull-right">
                <?= Html::a('Выйти', Url::to('@web/logout'), [
                    'data-method' => 'post',
                    'class' => 'btn btn-default btn-flat',
                ]) ?>
            </div>
        </li>
    </ul>
</li>
</ul>
</div>
</nav>
</header>
<div class="wrapper row-offcanvas row-offcanvas-left">
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="left-side sidebar-offcanvas">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <?= Menu::widget([
                'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
                'showAddPage' => false,
                'options' => [
                    'class' => 'sidebar-menu',
                ],
            ]) ?>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Right side column. Contains the navbar and content of the page -->
    <aside class="right-side">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?= Html::encode($this->title) ?>
                <small>Control panel</small>
            </h1>
            <?= Breadcrumbs::widget([
                'tag' => 'ol',
                'homeLink' => [
                    'label' => 'Главная',
                    //'url' => Yii::$app->homeUrl,
                    'template' => "<li><a href=\"" . Yii::$app->homeUrl . "\"><i class=\"fa fa-dashboard\"></i>Главная</a></li>\n",
                ],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        </section>

        <!-- Main content -->
        <section class="content">
        <?= $content ?>
        </section><!-- /.content -->
    </aside><!-- /.right-side -->
</div><!-- ./wrapper -->


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
