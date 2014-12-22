<?php

namespace backend\modules\catalog\controllers;

use Yii;
use common\modules\catalog\models\Age;
use backend\modules\catalog\models\AgeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AgeController implements the CRUD actions for Age model.
 */
class AgeController extends Controller
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
     * Lists all Age models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AgeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Age model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Age();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                if (Yii::$app->request->getBodyParam('submit') === 'apply') {
                    return $this->redirect('update?id=' . $model->id, 302);
                }

                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
                'model' => $model->loadDefaultValues(),
            ]);
    }

    /**
     * Updates an existing Age model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                if (Yii::$app->request->getBodyParam('submit') === 'apply') {
                    return $this->redirect('update?id=' . $model->id, 302);
                }

                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
                'model' => $model->loadDefaultValues(),
            ]);
    }

    /**
     * Deletes an existing Age model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Age model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Age the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Age::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
