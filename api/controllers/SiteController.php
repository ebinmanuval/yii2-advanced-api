<?php
namespace api\controllers;

use common\models\LoginForm;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\base\UserException;
use yii\rest\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use api\models\SignupForm;
use \Yii;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::className(),
            'optional' => [
                'login','register'
            ],
        ];
        return $behaviors;
    }

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'dataProvider',
    ];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'api\components\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return [
            "message" => "Everything work fine,if you need configure response format do it in config",
        ];
    }

    public function actionRegister()
    {
        $model = new SignupForm();
        $params[$model->formName()] = Yii::$app->request->post();
        if ($model->load($params) && $model->signup()) {
            return Yii::$app->user->identity;
        } else
            return $model->errors;
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        $params[$model->formName()] = Yii::$app->request->post();
        $model->load($params);
        if($model->login()){
            $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
            /** @var Jwt $jwt */
            $jwt = Yii::$app->jwt;
            $token = $jwt->getBuilder()
                ->setIssuer('http://example.com')// Configures the issuer (iss claim)
                ->setAudience('http://example.org')// Configures the audience (aud claim)
                ->setId('4f1g23a12aa', true)// Configures the id (jti claim), replicating as a header item
                ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
                ->setExpiration(time() + 3600)// Configures the expiration time of the token (exp claim)
                ->set('uid', Yii::$app->user->id)// Configures a new claim, called "uid"
                ->sign($signer, $jwt->key)// creates a signature using [[Jwt::$key]]
                ->getToken(); // Retrieves the generated token

            return [
                'token' => (string)$token,
            ];
        }
        else{
            throw new UserException("Wrong login credential");
        }
    }

    public function actionUserDetails(){
        return Yii::$app->user->identity;
    }

}
