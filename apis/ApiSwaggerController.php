<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/3/25
 * Time: 13:19
 */

namespace app\apis;


use yii\web\Controller;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class ApiSwaggerController extends Controller{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => Url::to(['/apiswagger/api'], true),
            ],
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                'scanDir' => [
                    Yii::getAlias('@app/modules/v1/controllers'),
                    Yii::getAlias('@app/apis'),
                ],
                // 'api_key' => 'test'
            ],
        ];
    }
}