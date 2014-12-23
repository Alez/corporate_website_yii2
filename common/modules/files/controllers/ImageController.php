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

        if (!class_exists($model)) {
            return false;
        }

        $material = $model::findOne($id);

        if ($fileid) {
            return $material->deleteFile($field, (int)$fileid);
        } else {
            return $material->deleteFile($field);
        }
    }
}
