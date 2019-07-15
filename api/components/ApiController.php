<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 27/04/15
 * Time: 5:48 PM
 */

namespace api\components;
use Yii;
use yii\rest\ActiveController;

class ApiController extends ActiveController
{
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter']=  [
            'class' => \yii\filters\Cors::className(),

        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['create'] = [
            'class' => 'api\components\CreateActionNR',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'scenario' => $this->createScenario,
        ];
        return $actions;
    }
}