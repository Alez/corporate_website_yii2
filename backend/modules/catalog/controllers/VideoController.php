<?php

namespace backend\modules\catalog\controllers;

use common\modules\catalog\models\MaterialVideo;
use Yii;
use common\modules\catalog\models\Material;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MaterialController implements the CRUD actions for Material model.
 */
class VideoController extends Controller
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

    public function actionCreate()
    {
        $model = new MaterialVideo();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            if ($model->save()) {
                return $this->redirect('@web/catalog/material/update' . '?id=' . $model->getAttribute('material_id') . '#tableMarkvideo');
            }
        }

        $model->setAttribute('material_id', Yii::$app->request->getQueryParam('material'));

        return $this->render('videoForm', [
                'model' => $model,
            ]);
    }

    public function actionUpdate($id)
    {
        $model = MaterialVideo::find()->where(['id' => $id])->one();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            if ($model->save()) {
                return $this->redirect('@web/catalog/material/update' . '?id=' . $model->getAttribute('material_id') . '#tableMarkvideo', 302);
            }
        }

        return $this->render('videoForm', [
                'model' => $model,
            ]);
    }

    public function actionDelete($id)
    {
        $video = MaterialVideo::findOne($id);
        $material_id = $video->getAttribute('material_id');
        $video->delete();

        return $this->redirect("@web/catalog/material/update?id=$material_id#tableMarkvideo");
    }

    /**
     * Finds the Material model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Material the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Material::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Такого материала не существует.');
        }
    }

    public function actionDeletefile($id, $fileId = null, $fieldName = null)
    {
        Yii::$app->response->format = 'json';

        $material = MaterialVideo::findOne($id);

        if ($fileId) {
            return $material->deleteFile($fieldName, (int)$fileId);
        } else {
            return $material->deleteFile($fieldName);
        }
    }
}
