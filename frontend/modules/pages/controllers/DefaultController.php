<?php

namespace frontend\modules\pages\controllers;

use common\modules\pages\models\PagesParams;
use common\modules\pages\models\PagesTemplates;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\modules\pages\models\Pages;
use common\modules\files\models\Files;

/**
 * DefaultController implements the CRUD actions for Pages model.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionPage($slug = 'main')
    {
        // Выберем страницу по Slug
        if (!Pages::setPage($slug)) {
            throw new NotFoundHttpException('Такая страница не найдена');
        }
        // Дёрнем параметры этой страницы
        PagesParams::retrieveParams(Pages::$page);

        // Подготовим файлы (если они используются) для этой страницы
        $params = [];
        foreach (PagesParams::$pagesParams as $pageParam) {
            $params = ArrayHelper::merge($params, $pageParam);
        }
        $files = Files::find()
            ->where(['id' => PagesParams::extractAllFilesId($params)])
            ->indexBy('id')
            ->all();

        return $this->render(Pages::$page['pagesTemplate']['slug'], [
                'files'  => $files,
            ]);
    }
}
