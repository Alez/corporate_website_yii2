<?php
namespace backend\components\behaviors;

use yii\base\Behavior;
use yii;
use yii\base\Application;
//use yii\filters\AccessControl;


class Access extends Behavior
{
    public function events()
    {
        return [
            Application::EVENT_BEFORE_ACTION => 'beforeAccess',
        ];
    }

    public function beforeAccess($event)
    {
        if ($event->action->id !== 'login') {
            if (Yii::$app->user->isGuest) {
                Yii::$app->getResponse()->redirect('@web/login');
            }
            if (!Yii::$app->user->isGuest && (int)Yii::$app->user->identity->role !== 1) {
                Yii::$app->getResponse()->redirect('@web/login');
            }
        }

//        $accessControl = new AccessControl();
//
//        $accessControl->rules = [
//            [
//                'allow'   => true,
//                'actions' => ['login'],
//            ],
//            [
//                'allow' => true,
//                'roles' => ['@'],
//            ],
//        ];
//
//        $accessControl->init();
//        $accessControl->beforeAction($event->action);
    }
}