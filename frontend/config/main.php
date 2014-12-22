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
    'id' => 'app-practical-a-frontend',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'bootstrap' => ['log'],
    'defaultRoute' => 'pages/default/page',
    'components' => [
        'user' => [
            'identityClass' => 'common\modules\user\models\User',
            'enableAutoLogin' => true,
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
            'errorAction' => '/pages/default/error',
        ],
        'urlManager' => [
            'rules' => $routes,
        ],
    ],
    'modules' => $modules,
    'params' => $params,
];
