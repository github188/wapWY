<?php
class LoginForm extends CFormModel
{
    public $account;
    public $pwd;    
    private $_identity;
    public $rememberMe= true;
    public $isError = false;
    public $verifyCode;
	public $login_time;
	public $login_ip;


    public function rules()
    {
        return array(
            array('account', 'required', 'message' =>'<font style="color:red">请填写账号</font>'),
            array('pwd', 'required', 'message' =>'<font style="color:red">请填写密码</font>'),            
            array('verifyCode', 'required', 'message' => '<font style="color:red">请填写验证码</font>'),
            array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements(), 'message' => '<font style="color:red">验证码错误</font>'),
            array('rememberMe', 'boolean'),
        );
    }
    
 
    /*
     * 用户登陆
     * */
    public function login() {
        if ($this->_identity === null) {
            $this->_identity = new SytIdentity($this->account, $this->pwd);
//             $this->_identity -> authenticate();
        }
        if($this->_identity->authenticate()) {
            $duration = $this -> rememberMe ? 3600 * 24 * 30 : 0; // 30 天
            Yii::app() -> user -> login($this->_identity,$duration);
//            $user = Merchant::model()->updateByPk(Yii::app()->user->id, array('lastlogintime' => new CDbExpression('now()')));
//            if(empty($user)){
//                $user = Store::model()->updateByPk(Yii::app()->user->id, array('lastlogintime' => new CDbExpression('now()')));
//            }
            return true;
        } else {
            $this -> isError = true;
            $this -> addError('pwd','账号或密码错误');
            return false;
        }
    }
	
	
	
}
?>