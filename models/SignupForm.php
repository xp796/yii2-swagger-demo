<?php
namespace app\models;

use app\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $name;
    public $email;
    public $password;
    public $replace_password;
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            //['username', 'unique','targetClass'=>new User(),'targetAttribute'=>'username'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z]{1}(\w{3,})/i', 'message' => '使用以字母开头数字4位以上的字符'],
            ['password', 'required'],
            // ['role_name', 'required'],
            ['password', 'string', 'min' => 6],
            ['password', 'string', 'max' => 16],
            ['replace_password', 'required'],
            ['replace_password', 'compare', 'compareAttribute' => 'password'],
            ['verifyCode', 'required'],
            ['verifyCode', 'captcha'],
        ];
    }

    public function uniqueCheck()
    {
        $user = User::find()->where('username=:username or mobile=:mobile', [':username' => $this->username, ':mobile' => $this->username])->asArray()->one();
        if (!empty($user)) {
            $this->addError('username', Yii::t('app', '该用户名已存在.'));
            return false;
            //  var_dump($user,$this->errors);//die;
        }
        return true;
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate() && $this->uniqueCheck()) {
            $user = new User();
            $user->username = $this->username;
            $user->setPassword($this->password);
            $user->status = 0;
            //  $user->is_admin = 1;
            if ($user->save()) {
                return $user->id;
            }
        } else {
            //  var_dump($this->errors);die;
        }
        return null;

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', '用户名'),
            'password' => Yii::t('app', '密码'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'email' => Yii::t('app', 'Email'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'obj_status' => Yii::t('app', 'Obj Status'),
            'replace_password' => Yii::t('app', '重复密码'),
            'verifyCode' => Yii::t('app', '验证码'),
        ];
    }
}

