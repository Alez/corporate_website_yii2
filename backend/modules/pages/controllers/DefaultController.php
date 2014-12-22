<?php

namespace backend\modules\pages\controllers;

use common\modules\pages\models\PagesParams;
use Yii;
use common\modules\pages\models\Pages;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

class DefaultController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param string|null $slug Slug странички
     * @param string|null $template Id темплейта страницы
     *
     * @throws NotFoundHttpException
     */
    public function actionEdit($slug = null, $template = null)
    {
        if (Yii::$app->request->isPost) {
            // Проверить принимаемый тип и текущий тип, если не сходятся - удалить
            // все параметры страницы, поменять ей тип и записать новые параметры
            // (параметры удалять не надо так как есть связь в БД)
            $postPages = Yii::$app->request->post('Pages');

            if (!isset($postPages['id'])) {
                // Если нет id, значит абсолютно новая страница
                $page = new Pages();
                $page->load(Yii::$app->request->post(), 'Pages');
                $page->save();

                $params = PagesParams::newPageParams($page->getAttribute('pages_template_id'));
                foreach ($params as $param) {
                    $param->setAttribute('page_id', $page->getAttribute('id'));
                    $param->save(false);
                }
                $isUpdateParams = false;
                $slug = $page->getAttribute('slug');
            } elseif ((int)Pages::findOne($postPages['id'])->getAttribute('pages_template_id') === (int)$postPages['pages_template_id']) {
                // Если шаблон страницы не поменялся
                // Возможно поменялись name или slug
                $page = Pages::find()->where(['id' => $postPages['id']])->one();
                $page->load(Yii::$app->request->post(), 'Pages');
                $page->save();
                $isUpdateParams = true;
            } else {
                // Если шаблон страницы сменился
                $page = Pages::findOne($postPages['id']);
                foreach ($page->pagesParams as $oldParam) {
                    $oldParam->delete();
                }
                $page->load(Yii::$app->request->post(), 'Pages');
                $page->save();
                $params = PagesParams::newPageParams($page->getAttribute('pages_template_id'));
                foreach ($params as $param) {
                    $param->setAttribute('page_id', $page->getAttribute('id'));
                    $param->save(false);
                }
                $isUpdateParams = false;
            }

            // Возьмём все параметры страницы
            $params = $page->getPagesParams()->joinWith('pagesTemplatesParams')->all();

            /*
             * Если это обновление параметров, то проиндексируем элементы массива ID'шниками.
             * А если это запись новых параметров, то проиндексируем эл. мас. ID шаблонов параметров.
             */
            $rawParams = [];
            if ($isUpdateParams) {
                $params = ArrayHelper::index($params, 'id');

            } else {
                $params = ArrayHelper::index($params, function($element) {
                        return 'template_' . $element->pagesTemplatesParams->id;
                    });
            }
            foreach ($params as $key => $param) {
                if (!$param->isFileType()) {
                    $rawParams[$key] = $param;
                }
            }

            // Загрузим простые поля. Поля так же попадут в $params по ссылке
            foreach ($rawParams as $key => $rawParam) {
                PagesParams::loadMultiple([$key => $rawParam], Yii::$app->request->post());
            }

            // Загрузим файловые поля
            foreach ($params as $key => $param) {
                if ($param->isFileType()) {
                    if ($param->isMultifileType()) {
                        $param->uploadFile = UploadedFile::getInstances($param, '[' . $key . ']uploadFile');
                    } else {
                        $param->uploadFile = UploadedFile::getInstance($param, '[' . $key . ']uploadFile');
                    }
                }
            }

            // Все загружено, проверим правильность загруженных данных
            if (PagesParams::validateMultiple($params)) {
                foreach ($params as $param) {
                    $param->save(false);
                }
            }

            return $this->redirect($this->action->id . '?slug=' . $slug, 302);
        }

        // Запрос редактирования конкретной страницы
        if ($slug) {
            $page = Pages::find()->where(['slug' => $slug])->one();

            if (!$page) {
                throw new NotFoundHttpException("Страница - \"$slug\" не найдена");
            }

            if (Yii::$app->request->getQueryParam('template') !== null
                && $page->getAttribute('pages_template_id') !== Yii::$app->request->getQueryParam('template')
            ) {
                $page->setAttribute('pages_template_id', Yii::$app->request->getQueryParam('template'));
                $params = PagesParams::newPageParams(Yii::$app->request->getQueryParam('template'));
            } else {
                $params = $page->getPagesParams()->joinWith('pagesTemplatesParams')->all();
            }
            return $this->render('edit', [
                    'page' => $page,
                    'params' => $params,
                ]);
        } else {
        // Запрос создания новой страницы
            $page = (new Pages())->loadDefaultValues();
            if ($template) {
                $page->setAttribute('pages_template_id', Yii::$app->request->getQueryParam('template'));
            }

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('edit', [
                        'page' => $page,
                        'params' => PagesParams::newPageParams($template),
                    ]
                );
            } else {
                return $this->render('edit', [
                        'page' => $page,
                        'params' => PagesParams::newPageParams($template),
                    ]);
            }
        }
    }

    /**
     * @param $id int Номер записи параметра динамической страницы
     * @param $fileId int Номер файла который нужно удалить
     *
     * @return array
     */
    public function actionDeletefile($id, $fileId = null)
    {
        Yii::$app->response->format = 'json';

        $pageParam = PagesParams::findOne($id);

        if ($fileId) {
            return $pageParam->deleteFile((int)$fileId);
        } else {
            return $pageParam->deleteFile();
        }

    }

    public function actionDelete($id)
    {
        $page = Pages::findOne($id);
        $name = $page->name;
        $page->delete();

        return $this->render('delete', [
                'name' => $name,
            ]);
    }
}
