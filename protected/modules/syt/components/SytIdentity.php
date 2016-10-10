<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class SytIdentity extends CUserIdentity
{
	public $username;
	private $_id;
	private $_role;


	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		//操作员登录
		$operator = new OperatorC();
		$ret = $operator->operatorLogin($this->username, $this-> password);
		$result = json_decode($ret, true);
		if($result['status']  == ERROR_NONE){
			$this->errorCode = self::ERROR_NONE;
			$this -> _id = $result['data']['id'];
			$this -> username = $result['data']['account'];
			//存session
			Yii::app()->session['operator_id'] = $result['data']['id'];
			Yii::app()->session['operator_name'] = $result['data']['name'];
			Yii::app()->session['store_name'] = $result['data']['store_name'];
			Yii::app()->session['store_id'] = $result['data']['store_id'];
			Yii::app()->session['merchant_name'] = $result['data']['merchant_name'];
		}else{
			Yii::app()->user->setFlash('login_error',$result['errMsg']);
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		}
		
			
		return $this->errorCode == self::ERROR_NONE;
	}
	
	public function getId(){
		return $this->_id;
	}
	
	public function getName(){
		return $this -> username;
	}
	
}