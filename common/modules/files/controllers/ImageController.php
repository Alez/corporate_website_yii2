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
}
