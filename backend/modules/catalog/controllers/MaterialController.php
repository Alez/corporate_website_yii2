<?php

namespace backend\modules\catalog\controllers;

use common\modules\catalog\models\Author;
use common\modules\catalog\models\MaterialAudio;
use common\modules\catalog\models\MaterialBackingTrack;
use common\modules\catalog\models\MaterialCrosslinking;
use common\modules\catalog\models\MaterialExternalCrosslinking;
use common\modules\catalog\models\MaterialImages;
use common\modules\catalog\models\MaterialVideo;
use Yii;
use common\modules\catalog\models\Material;
use backend\modules\catalog\models\MaterialSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

/**
 * MaterialController implements the CRUD actions for Material model.
 */
class MaterialController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'deletevideo' => ['post'],
                    'deleteaudio' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Material models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MaterialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(['pageSize' => 200]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Material model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Material();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->preview_file = UploadedFile::getInstance($model, 'preview_file');
            $model->coloring_image_bw_file = UploadedFile::getInstance($model, 'coloring_image_bw_file');
            $model->coloring_image_file = UploadedFile::getInstance($model, 'coloring_image_file');

            if ($model->save()) {
                if (Yii::$app->request->getBodyParam('submit') === 'apply') {
                    return $this->redirect('update?id=' . $model->id, 302);
                }

                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Material model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = Material::find()->where(['id' => $id])->one();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->preview_file = UploadedFile::getInstance($model, 'preview_file');
            $model->coloring_image_bw_file = UploadedFile::getInstance($model, 'coloring_image_bw_file');
            $model->coloring_image_file = UploadedFile::getInstance($model, 'coloring_image_file');
            $model->post_images_file = UploadedFile::getInstances($model, 'post_images_file');

            if ($model->save()) {
                if (Yii::$app->request->getBodyParam('submit') === 'apply') {
                    return $this->redirect('update?id=' . $model->id, 302);
                }

                return $this->redirect(['index']);
            }
        }

        // Найдём все категории для модели после её загрузки
        $model->categoryIds = ArrayHelper::getColumn(
            $model->linkMaterialCategories,
            'category_id'
        );
        // Найдём всех авторов для модели после её загрузки
        $model->authorIds = ArrayHelper::getColumn(
            $model->linkMaterialAuthors,
            'author_id'
        );
        // Найдём все возрасты для модели после её загрузки
        $model->ageIds = ArrayHelper::getColumn(
            $model->linkMaterialAges,
            'age_id'
        );

        return $this->render('update', [
            'model' => $model,
            'videoDataProvider' => new ActiveDataProvider([
                    'query' => MaterialVideo::find()->where(['material_id' => $model->getAttribute('id')]),
                ]),
            'audioDataProvider' => new ActiveDataProvider([
                    'query' => MaterialAudio::find()->where(['material_id' => $model->getAttribute('id')]),
                ]),
            'backingtrackDataProvider' => new ActiveDataProvider([
                    'query' => MaterialBackingTrack::find()->where(['material_id' => $model->getAttribute('id')]),
                ]),
            'crosslinkingDataProvider' => new ActiveDataProvider([
                    'query' => MaterialCrosslinking::find()
                        ->where(['material_id' => $model->getAttribute('id')])
                        ->with('crosslinkedMaterial'),
                ]),
            'externalCrosslinkingDataProvider' => new ActiveDataProvider([
                    'query' => MaterialExternalCrosslinking::find()
                        ->where(['material_id' => $model->getAttribute('id')]),
                ]),
        ]);
    }

    /**
     * Deletes an existing Material model.
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

        $material = Material::findOne($id);

        if ($fileId) {
            return $material->deleteFile($fieldName, (int)$fileId);
        } else {
            return $material->deleteFile($fieldName);
        }
    }
}
