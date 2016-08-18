<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\rest\Controller;
use yii\rest\OptionsAction;
use app\modules\v1\controllers\RestController;
use app\modules\v1\service\UserService;
use app\helpers\Filter;

class UserController extends RestController
{
    
    /**
     * @SWG\Get(path="/user/view",
     *     tags={"user"},
     *     summary="获取用户列表",
     *     description="测试直接返回一个array",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *        in = "query",
     *        name = "id",
     *        description = "用户id",
     *        required = true,
     *        type = "string"
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     */
    public function actionList()
    {
        $params = [
            'id' => Filter::int('id')
        ];
        $result = UserService::View($params);
        $this->code = $result['code'];
        $this->message = $result['message'];
        $this->result = $result['result'];
    }
    /**
     * @SWG\Post(path="/user/create",
     *     tags={"user"},
     *     summary="创建用户接口",
     *     description="创建用户接口",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "username",
     *        description = "用户姓名",
     *        required = true,
     *        type = "string"
     *     ),
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "password",
     *        description = "密码",
     *        required = true,
     *        type = "string"
     *     ),
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "mobile",
     *        description = "手机",
     *        required = true,
     *        type = "string"
     *     ),
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "email",
     *        description = "邮箱",
     *        required = true,
     *        type = "string"
     *     ),
     * @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     *
     */
    public function actionCreate()
    {
        $params = [
            'username' => Filter::str('username'),
            'password' => Filter::str('password'),
            'mobile' => Filter::str('mobile'),
            'email' => Filter::str('email'),
        ];
        $result = UserService::Create($params);
        $this->code = $result['code'];
        $this->message = $result['message'];
        $this->result = $result['result'];
    }
    /**
     * @SWG\Post(path="/user/delete",
     *     tags={"user"},
     *     summary="删除用户接口",
     *     description="删除用户接口",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *        in = "formData",
     *        name = "id",
     *        description = "用户id",
     *        required = true,
     *        type = "string"
     *     ),
     * @SWG\Response(
     *         response = 200,
     *         description = " success"
     *     )
     * )
     *
     */
    public function actionDelete()
    {
      
        $params = [
            'id' => Filter::int('id')
        ];
        $result = UserService::Delete($params);
        $this->code = $result['code'];
        $this->message = $result['message'];
        $this->result = $result['result'];
    }
}
