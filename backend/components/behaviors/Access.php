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
        if (((Yii::$app->user->identity && (int)Yii::$app->user->identity->role !== 1)
            || is_null(Yii::$app->user->identity)) && $event->action->id !== 'login'
        ) {
            Yii::$app->getResponse()->redirect('@web/login');
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