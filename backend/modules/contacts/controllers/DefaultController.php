<?php

namespace backend\modules\contacts\controllers;

use Yii;
use common\modules\contacts\models\Contacts;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $model = Contacts::find()->indexBy('id')->all();
        if (!$model) {
            throw new NotFoundHttpException("Служебные парамеры не найдены");
        }

        if (Contacts::loadMultiple($model, Yii::$app->request->post()) && Contacts::validateMultiple($model)) {
            $count = 0;
            foreach ($model as $item) {
                if ($item->save(false)) {
                    $count++;
                }
            }
            return $this->redirect(['index']);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
