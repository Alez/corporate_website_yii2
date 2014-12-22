<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$modules = require((__DIR__) . '/modules.php');
$routes = require(__DIR__ . '/routes.php');

return [
    'id' => 'app-practical-a-backend',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'defaultRoute' => 'main/default/index',
    'bootstrap' => ['log'],
    'modules' => $modules,
    'components' => [
        'user' => [
            'identityClass' => 'common\modules\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => '@web/login',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'rules' => $routes,
        ],
    ],
    'params' => $params,
    'as beforeAction' => [
        'class' => 'backend\components\behaviors\Access',
    ],
];
