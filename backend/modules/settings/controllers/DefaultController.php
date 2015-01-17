<?php

namespace backend\modules\settings\controllers;

use Yii;
use common\modules\settings\models\Settings;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $model = Settings::find()->indexBy('id')->all();
        if (!$model) {
            throw new NotFoundHttpException("Служебные парамеры не найдены");
        }

        if (Settings::loadMultiple($model, Yii::$app->request->post()) && Settings::validateMultiple($model)) {
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
