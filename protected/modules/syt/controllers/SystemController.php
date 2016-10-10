<?php

/**
 * 系统设置
 */
class SystemController extends sytController {
	/**
	 * 修改密码
	 */
	public function actionEditPwd() {
		$newPwd = '';
		$newPwdAgain = '';
		$post = '';
		$systemsc = new SystemSC ();
		$operator_id = Yii::app ()->session ['operator_id'];
		$role = $systemsc -> getRole($operator_id);
		if (isset ( $_POST ['Operator'] ) && ! empty ( $_POST ['Operator'] )) {
			
			$post = $_POST['Operator'];
			if(isset($_POST['newPwd']) && !empty($_POST['newPwd'])){
				$newPwd = $_POST['newPwd'];
			}
			
			if(isset($_POST['newPwdAgain']) && !empty($_POST['newPwdAgain'])){
				$newPwdAgain = $_POST['newPwdAgain'];
			}
			
			$systemsc = new SystemSC ();
			$operator_id = Yii::app ()->session ['operator_id'];
			$result = $systemsc->editPwd ($post, $operator_id ,$newPwd ,$newPwdAgain);
			$result = json_decode($result,true);
			if($result['status'] == ERROR_NONE){
				Yii::app()->user->logout();
				$url = Yii::app()->createUrl('syt/auth/login');
				echo "<script>alert('密码修改成功,请重新登录');parent.window.location.href='$url'</script>";
			}
		}
		
		$this->render ( 'editPwd' ,array('role'=>$role));
	}
	
	/**
	 * 打印机管理
	 */
	public function actionPrintOperat()
	{
	
		$systemsc = new SystemSC ();
		$operator_id = Yii::app ()->session ['operator_id'];
		$role = $systemsc -> getRole($operator_id);
		$is_print = '1';
	
		$result = $systemsc->printStatus($operator_id);
		$result = json_decode($result,true);
		if($result['status'] == ERROR_NONE){
			$print = $result['data'];
		}
	
		$this->render('printOperat',array(
				'role'=>$role, 
				'print' => $print
		));
	}
	
	
	/**
	 * 打印机管理-指定默认打印机
	 */
	public function actionSetPrint()
	{
		if(isset($_POST['name']) && !empty($_POST['name'])){
			$store = new StoreC();
			$result = $store -> editPrintName($_POST['name'], Yii::app() -> session['store_id']);
			echo $result;
		}
	}
	
	/*
	 * 打印机管理-关闭或开启打印机
	 * */
	public function actionOpenPrint(){
		if(isset($_POST['state']) && !empty($_POST['state'])){
			$is_print = $_POST['state'] == 'true'?PRINT_YES:PRINT_NO;
			$store = new StoreC();
			$result = $store -> setPrint($is_print, Yii::app() -> session['store_id'],$_POST['state']);
			echo $result;
		}
	}
	
	/**
	 * 管理员密码
	 */
	public function actionAdminPwd()
	{
		$pwd = '';
		
		$systemsc = new SystemSC ();
		$operator_id = Yii::app ()->session ['operator_id'];
		$role = $systemsc -> getRole($operator_id);
		
		if(isset($_POST['pwd'])){
	    $pwd = $_POST['pwd'];
		$systemsc = new SystemSC();
		$operator_id = Yii::app ()->session ['operator_id'];
		$result = $systemsc -> getAdminPwd($operator_id,$pwd);
		$result = json_decode($result,true);
		
		if($result['status'] == ERROR_NONE){
			$this -> redirect(Yii::app()->createUrl('syt/system/randomPwd'));
		}
		}	
		$this -> render('adminPwd',array('role'=>$role));
	}
	
	/**
	 * 管理员密码(获取退款密码)
	 */
	public function actionRandomPwd()
	{
		$data = '';
		$systemsc = new SystemSC();
		$operator_id = Yii::app ()->session ['operator_id'];
		$role = $systemsc -> getRole($operator_id);
		$ret = $systemsc->getRandomPwd($operator_id);
		$result = json_decode($ret,true);
		if($result['status'] == ERROR_NONE){
			$data = $result['data'];
		}
		if (isset($_POST['randPwd']) && !empty($_POST['randPwd'])) {
			$result = $systemsc -> resetPwd($operator_id);
			
			$result = json_decode($result,true);
			
			if($result['status'] == ERROR_NONE){
				$data = $result['randomNum'];
			}
		}
		
		
		$this -> render('randomPwd',array('data'=>$data,'role'=>$role));
	}
}