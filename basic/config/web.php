<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'gallery' => [
            'class' => 'app\modules\Gallery\Gallery',    
        ]
    ],
    'container' => [
        'definitions' => [
            'app\modules\Gallery\services\abstractions\ICategoryService' => 'app\modules\Gallery\services\implementations\CategoryService',
            'app\modules\Gallery\repositories\abstractions\ICategoryRepository' => 'app\modules\Gallery\repositories\implementations\CategoryRepository',
            'app\modules\Gallery\repositories\abstractions\IImageRepository' => 'app\modules\Gallery\repositories\implementations\ImageRepository',
            'app\modules\Gallery\services\abstractions\IImageService' => 'app\modules\Gallery\services\implementations\ImageService' 
        ]
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '12345679',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'db' => require(__DIR__ . '/db.php'),
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            //'defaultRoles' => ['admin', 'user']
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            //'encodeParams' => false,
            'showScriptName' => false,
            'suffix' => '.html',
            'rules' => [
                'category/get/<slug:\\w+>/<page:\d+>' => 'gallery/category/get-category',
                'category/create' => 'gallery/category/create-category',
                'category/update/<slug:\\w+>' => 'gallery/category/update-category',
                'category/all/<page:\d+>' => 'gallery/category/get-all-categories',
                'category/delete/<slug:\\w+>' => 'gallery/category/delete-category',
                'category/admin/<page:\d+>' => 'gallery/category/admin-page',
                'image/create' => 'gallery/image/create-image',
                'image/update/<id:\\w+>' => 'gallery/image/update-image',
                'image/delete' => 'gallery/image/delete-image',
                'image/admin-page/<categorySlug:\\w+>/<page:\d+>' => 'gallery/image/admin-page',
                'image/user-page/<page:\d+>' => 'gallery/image/user-page'
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
