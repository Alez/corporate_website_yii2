<?php

namespace common\modules\files\controllers;

use common\modules\files\models\FileRecord;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

class ImageController extends Controller
{
    public function actionImageeditpopup($id)
    {
        /** @var \common\modules\files\models\ImageRecord $image */
        $image = FileRecord::findOne($id);
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
        }

        return $this->renderAjax('_photoEditPopup', ['image' => $image]);
    }
}
