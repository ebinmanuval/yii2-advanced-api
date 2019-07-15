<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
$callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : false;
$format = $callback ? 'jsonp' : 'json';
return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => true,
        ],
        'jwt' => [
            'class' => 'sizeg\jwt\Jwt',
            'key'   => 'secret',
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
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => $format, // json or jsonp
            'on beforeSend' => function ($event) {
                $callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : false;
                $response = $event->sender;
                if ($response->data !== null) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                    if ($callback) {
                        $response->data += ['callback' => $callback];
                    }
                    $response->statusCode = 200;
                }
            },
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            //'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['user', 'site'],
                    'extraPatterns' => [
                        'POST,OPTIONS search' => 'search',
                        'GET site/error'=>'error',
                    ]
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
