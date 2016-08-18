<?php

/**
 * CopyRight © 2016
 * 基础rest类
 *
 * @author
 * @version  1.0(系统当前版本号)
 * @name:     RestController（类名/函数名）
 * @date:     2016-3-30
 * @namespace: app\modules\v1\controllers;
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class RestController extends ActiveController
{
    protected $message = '服务调用成功';
    protected $code = '200';
    protected $result = [];
    protected $format;
    protected $params;

	const PAGE_SIZE = 10;
    
    // 初始化
    public function init()
    {
        $this->format = Yii::$app->request->get('format');
        $this->params = isset($_REQUEST['params']) && json_decode(isset($_REQUEST['params']));
    }
    
	public function checkAccess($action, $model = null, $params = [])
	{
		$response = [
			'code' => $this->code,
			'message' => $this->message,
			'result' => $this->result,
		];
		return $response;
	}


    // 规则设置
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        switch ($this->format) {
            default :
            case 'json' :
            case 'jsonp' :
                $behaviors['contentNegotiator']['formats'] = [];
                $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
                break;
            case 'xml' :
                $behaviors['contentNegotiator']['formats'] = [];
                $behaviors['contentNegotiator']['formats']['application/xml'] = Response::FORMAT_XML;;
                break;
            case 'html' :
                $behaviors['contentNegotiator']['formats'] = [];
                $behaviors['contentNegotiator']['formats']['html/text'] = Response::FORMAT_HTML;;
                break;
        }
        return $behaviors;
    }
    // 设置求求规则覆盖父级 不设置的话执行删除delete操作会出现405错误
    protected function verbs()
    {
        return [
            'index' => ['GET', 'POST'],
            'view' => ['GET', 'POST'],
            'update' => ['GET', 'POST'],
            'create' => ['POST', 'HEAD'],
            'options' => ['GET']
        ];
    }
    // 重写获取资源
    public function actions()
    {
    }

    public function afterAction($action, $result)
    {
        $response = [
            'code' => $this->code,
            'message' => $this->message,
            'result' => $this->result,
        ];
        return $response;
    }

}
