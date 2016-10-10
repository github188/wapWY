<?php
/**
 *地址管理
 * */
class AddressController extends UCenterController {
	
	//添加地址
	public function actionAddAddress(){
		if(isset($_POST) && !empty($_POST)){
			$result = array();
			$flag = 0;
			//验证是否非空
			if(isset($_POST['name']) && !empty($_POST['name'])){
				$name = $_POST['name'];
			}else{
				$name = '';
			}
			
			if(isset($_POST['tel']) && !empty($_POST['tel'])){
				$tel = $_POST['tel'];
			}else {
				$tel = '';
			}
			
			if(isset($_POST['province']) && !empty($_POST['province'])){
				$province = $_POST['province'];
			}else {
				$province = '';
			}
			
			if(isset($_POST['city']) && !empty($_POST['city'])){
				$city = $_POST['city'];
			}else {
				$city = '';
			}
			
			if(isset($_POST['area']) && !empty($_POST['area'])){
				$area = $_POST['area'];
			}else {
				$area = '';
			}
			
			if(isset($_POST['address']) && !empty($_POST['address'])){
				$address = $_POST['address'];
			}else {
				$address = '';
			}
			
			if(isset($_POST['code']) && !empty($_POST['code'])){
				$code = $_POST['code'];
			}else{
				$code = '';
			}
			$user_id = Yii::app() -> session['user_id'];
			$user = new UserUC();
			$result = $user -> addAddress($user_id, $name, $tel, $province, $city , $area,$address, $code);
			echo $result; 
		}
	}
	
	//设置默认地址
	public function actionSetDefaultAddress(){
		if(isset($_POST['id']) && !empty($_POST['id'])){
			$user = new UserUC();
			$result = $user -> setDefaultAddress($_POST['id']);
			echo $result;
		}
	}
}