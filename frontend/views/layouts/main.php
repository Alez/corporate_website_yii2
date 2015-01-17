<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\modules\contacts\models\Contacts;
use common\modules\settings\models\Settings;
use frontend\components\widgets\Menu;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Settings::get('title') ?><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <header>
        <nav class="navbar" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle icon-menu pull-left" data-toggle="collapse" data-target="#menu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <button type="button" class="navbar-toggle icon-phone" data-toggle="collapse" data-target="#call"><i class="fa fa-phone"></i></button>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-2 col-md-2">
                        <a href="/" class="logo"></a>
                    </div>
                    <div class="col-xs-12 col-sm-10 col-md-10">
                        <div class="collapse navbar-collapse call" id="call">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-5">
                                            <div class="phone">
                                                <a href="tel:(4742) <?= Contacts::get('phone1') ?>"><span>(4742)</span> <?= Contacts::get('phone1') ?></a>
                                                <a href="tel:(4742) <?= Contacts::get('phone2') ?>"><span>(4742)</span> <?= Contacts::get('phone2') ?></a>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-7">
                                            <div class="adress"><?= Contacts::get('address') ?></div>
                                            <a href="/about" class="look-map">Посмотреть на карте</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-4">
                                    <button type="button" class="btn_style" data-toggle="modal" data-target="#modal_1"><i class="fa fa-phone"></i>Обратная связь</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="collapse navbar-collapse menu" id="menu">
                <div class="container">
                    <?= Menu::widget([
                        'options' => [
                            'class' => 'nav navbar-nav',
                        ],
                        'linkTemplate' => '<a href="{url}">{label}</a>',
                    ]) ?>
                </div>
            </div>

        </nav>
        <div class="border-mob visible-xs"></div>
    </header>
    <section>
        <? if (Yii::$app->defaultRoute !== Yii::$app->controller->getRoute() ||
            (Yii::$app->defaultRoute === Yii::$app->controller->getRoute() && Yii::$app->controller->actionParams['slug'] !== 'main')): ?>
        <div class="container hidden-xs">
            <div class="row">
                <div class="col-xs-12">
                    <?= Breadcrumbs::widget([
                        'tag' => 'ol',
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
                </div>
            </div>
        </div>
        <? endif ?>
        <?= $content ?>
    </section>
    <footer>
        <div class="border-mob visible-xs"></div>
        <nav class="navbar hidden-xs" role="navigation">

            <div class="collapse navbar-collapse menu" id="menu">
                <div class="container">
                    <?= Menu::widget([
                        'options' => [
                            'class' => 'nav navbar-nav',
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="container">
                <div class="row">

                    <div class="col-xs-12 col-sm-2 col-md-2">
                        <a href="" class="logo"></a>
                    </div>

                    <div class="col-xs-12 col-sm-10 col-md-10">
                        <div class="collapse navbar-collapse call" id="call">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-5">
                                            <div class="phone">
                                                <a href="tel:(4742) 25-01-82"><span>(4742)</span> 25-01-82</a>
                                                <a href="tel:(4742) 55-08-89"><span>(4742)</span> 55-08-89</a>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-md-7">
                                            <div class="adress">Липецк, ул. Скороходова д.11</div>
                                            <a href="/about" class="look-map">Посмотреть на карте</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-4">
                                    <button type="button" class="btn_style" data-toggle="modal" data-target="#modal_1"><i class="fa fa-phone"></i>Обратная связь</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </nav>

        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="footer_copy">&copy;<?= date('Y') ?> Aura.ru. Все права защищены</div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="footer_creat">Создание сайта <a href="http://www.webpaint.ru/" target="_blank">Webpaint</a></div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <?= $this->renderFile(Yii::getAlias('@frontend') . '/modules/contact_form/views/default/contactFormModal.php') ?>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
