<?php

// *** API component config *** //
// These parameters are useful for console command, since if set, you can omit setting
// corresponding options in the command line.
Yii::$container->set(
    \madand\teleduino\components\Api::className(),
    [
        'apiEndpoint'=>'https://my-endpoint.com',
        'apiKey'=>'00000000000000000000000000000000',
    ]
);

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    // *** Added teleduino element here: *** //
    'bootstrap' => ['log', 'gii', 'teleduino'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
        // *** Added teleduino module config here: *** //
        'teleduino'=>[
            'class'=>'madand\teleduino\Module',
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];
