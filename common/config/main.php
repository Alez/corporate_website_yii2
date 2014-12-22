<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class' => 'common\modules\user\models\clients\GoogleOAuth',
                    'clientId' => '',
                    'clientSecret' => '',
                ],
                'facebook' => [
                    'class' => 'common\modules\user\models\clients\Facebook',
                    'clientId' => '',
                    'clientSecret' => '',
                    'authUrl' => 'https://www.facebook.com/dialog/oauth?display=popup',
                    //'scope' => 'user_birthday,user_location',
                ],
                'vkontakte' => [
                    'class' => 'common\modules\user\models\clients\VKontakte',
                    'clientId' => '',
                    'clientSecret' => '',
                ],
                'twitter' => [
                    'class' => 'common\modules\user\models\clients\Twitter',
                    'consumerKey' => '',
                    'consumerSecret' => '',
                ],
                'odnoklassniki' => [
                    'class' => 'common\modules\user\models\clients\Odnoklassniki',
                    'clientId' => '',
                    'clientPublic' => '',
                    'clientSecret' => '',
                ],
                'mailru' => [
                    'class' => 'common\modules\user\models\clients\Mailru',
                    'clientId' => '',
                    'clientSecret' => '',
                ],
            ],
        ],
    ],
];
