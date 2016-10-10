<?php

class AuthController extends sytController {
	public $layout = "column1";
	
	public function actionIndex() {
		$this->redirect('auth/login');
	}
	
	/**
	 * 重写init方法， 不执行父类的 未登录就提示登录程序
	 */
	public function init() {
	
	}
	
	/**
	 * 登录
	 */
	public function actionLogin() {
		//判断是否已登录
		if (!Yii::app()->user->isGuest) {
			//进入收银台
			//$this->redirect(Yii::app()->createUrl('syt/index/index'));
		}
		if (isset($_POST['account']) && isset($_POST['password'])) {
			try {
				$account = $_POST['account'];
				$password = $_POST['password'];
				if(empty($account)) {
					throw new Exception('请填写账号');
				}
				if(empty($password)) {
					throw new Exception('请填写密码');
				}
				//操作员登录
				$loginform = new LoginForm();
				$loginform -> account = $account;
				$loginform -> pwd = md5($password);
				if ($loginform -> login()) {
				
					//进入收银台
					$this->redirect(Yii::app()->createUrl('syt/index/index'));
				}else {
					if(Yii::app()->user->hasFlash('login_error')){
						throw new Exception(Yii::app()->user->getFlash('login_error'));
					}else{
						throw new Exception('登录失败');
					}
				}
			} catch (Exception $e) {
				Yii::app()->user->setFlash('error', $e->getMessage());
			}
		}
		
		$this->render('login');
	}
	
	/**
	 * 登出
	 */
	
	public function actionLogout(){
		Yii::app()->user->logout();
// 		Yii::app()->session->clear();
// 		Yii::app()->session->destroy();
		$this->redirect(Yii::app()->createUrl('syt/auth/login'));
	}
}