<?php

namespace backend\modules\service_info\controllers;

use Yii;
use common\modules\service_info\models\ServiceInfo;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * Lists all
     * @return mixed
     */
    public function actionIndex()
    {
        $model = ServiceInfo::find()->indexBy('id')->all();
        if (!$model) {
            throw new NotFoundHttpException("Служебные парамеры не найдены");
        }

        if (ServiceInfo::loadMultiple($model, Yii::$app->request->post()) && ServiceInfo::validateMultiple($model)) {
            $count = 0;
            foreach ($model as $item) {
                // populate and save records for each model
                if ($item->save(false)) {
                    $count++;
                }
            }
            Yii::$app->session->setFlash('success', "Processed {$count} records successfully.");
            return $this->redirect(['index']); // redirect to your next desired page
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
