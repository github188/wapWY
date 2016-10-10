<?php
//旺pos 网关 用于接收旺pos消息
class WangPosGatewayController extends Controller{
	
	public function init(){
	
	}
	
	//获取旺pos服务器发送的post请求
	public function actionIndex(){
		header('Content-type:text/html;charset=utf-8');
		$token = 'hello';
		$encrypt_id = $_GET["encrypt_id"];
		if(isset($_POST) && !empty($_POST)){
			$data = $_POST;
			$signature = $data['signature'];
			$echo_str = $data['echo_str'];
			unset($data['signature']);
			unset($data['echo_str']);
			unset($data['event']);
			$data['token'] = $token;
			//对value字典序排序
			asort($data);
			//拼接键值对
			$string = "";
			foreach ($data as $k => $v)
			{
				$string .= $v;
			}
			//sha1加密
			$string = sha1($string);
			//判断签名是否正确
			if($string == $signature){
				$response = array(
						'result' => 0,
						'data' => array('echo_str' => $echo_str)
				);
				echo json_encode($response);
			}else{
				echo "签名不正确";
			}
		}else{
			echo "POST数据为空";
		}
	}
	
	
}