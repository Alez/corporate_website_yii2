<?php
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $page common\modules\pages\models\Pages */
/* @var $params common\modules\pages\models\PagesParams */

$this->title = $page->getAttribute('name') ? 'Редактирование страницы - ' . $page->name : 'Добавить страницу';
$this->params['breadcrumbs'][] = $this->title;
?>
<? Pjax::begin(['id' => 'templateFields']) ?>
<div class="pages-update">
    <?= $this->render('_form', [
        'page'   => $page,
        'params' => $params,
    ]) ?>
</div>
<? Pjax::end() ?>