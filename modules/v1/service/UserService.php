<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/18
 * Time: 14:32
 */
namespace app\modules\v1\service;

use app\helpers\Util;
use app\modules\v1\models\User;
use Yii;
use yii\base\Exception;
class UserService 
{
    /**
     * @param array $params
     * @return array
     */
    public static function View($params=[])
    {
        $returnData = [];
        $user = User::find()->where(['id'=>$params['id'],'obj_status'=>1])->one();
        if($user){
            $returnData['result'] = $user;
            $returnData['message'] = '查询成功';
            $returnData['code'] = 200;
        }else{
            $returnData['result'] = '';
            $returnData['message'] = '用户不存在';
            $returnData['code'] = 200;
        }
        return $returnData;
    }

    /**
     * @param array $params
     * @return array
     */
    public static function Create($params=[]){
        $returnData = [];
        $model = new User();
        //Util::dump($params);
        $model->load(['user'=>$params],'user');
        $model->setPassword($params['password']);
        $model->status = 0;
        if($model->save()){

            $returnData['message'] = '创建成功';
            $returnData['code'] = 200;
        }else {
            $returnData['message'] = '创建失败';
            $returnData['code'] = 200;
        }
        $returnData['result'] = '';
        return $returnData;
    }

    public static function Delete($params = []){
        $returnData = [];
        $user = User::find()->where(['id'=>$params['id'],'obj_status'=>1])->one();;
        if($user->delete()){
            $returnData['message'] = '删除成功';
            $returnData['code'] = 200;
        }else {
            $returnData['message'] = '删除失败';
            $returnData['code'] = 200;
        }
        $returnData['result'] = '';
        return $returnData;
    }
}