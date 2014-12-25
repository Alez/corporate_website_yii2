<?php

namespace common\modules\files\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use common\modules\files\models\Files;

class ImageController extends Controller
{
    public function actionImageeditpopup($id)
    {
        $image = Files::findOne($id);
        $image->setScenario('image');
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = 'json';
            $cropData = Json::decode(Yii::$app->request->getBodyParam('cropData'));
            if ($cropData) {
                $image->cropImage($cropData['x'], $cropData['y'], $cropData['width'], $cropData['height']);
            }

            if ($image->load(Yii::$app->request->post()) && $image->save()) {
                return [
                    'result' => true,
                    'imageId' => $image->id,
                    'imageSrc' => $image->getSrc(),
                ];
            }

            return ['result' => false];
        } else {
            return $this->renderAjax('_photoEditPopup', ['image' => $image]);
        }
    }

    public function actionDeletefile($id, $model, $fileid = null, $field = null)
    {
        Yii::$app->response->format = 'json';

        if (((Yii::$app->user->identity && (int)Yii::$app->user->identity->role !== 1) || is_null(Yii::$app->user->identity))) {
            return false;
        }

        if (!class_exists($model)) {
            return false;
        }

        $record = $model::findOne($id);

        if ($fileid) {
            return $record->deleteFile($field, (int)$fileid);
        } else {
            return $record->deleteFile($field);
        }
    }
}
