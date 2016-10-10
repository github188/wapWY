<?php
include_once(dirname(__FILE__).'/../mainClass.php');

class GjproductC extends mainClass{
	//获取管家产品
	public function getGjproductList(){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		
		$product = GjProduct::model() -> findAll();
		$data = array();
		if(!empty($product)){
			foreach ($product as $k => $v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['name'] = $v -> name;
				$data['list'][$k]['price'] = $v -> price;
				$data['list'][$k]['original_price'] = $v -> original_price;
				$data['list'][$k]['create_time'] = $v -> create_time;
				$data['list'][$k]['last_time'] = $v -> last_time;
				$data['list'][$k]['sold_num'] = $v -> sold_num;
			}
			$result['status'] = ERROR_NONE;
			$result['data'] = $data;
			return json_encode($result);
		}else{
			$result['status'] = ERROR_NONE;
			$data['list'] = array();
			$result['data'] = $data;
			return json_encode($result);
		}
	}
	
	//获取管家产品详情
	public function getGjproductDetails($id){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(!isset($id) || empty($id)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数id缺失';
			return json_encode($result);
		}
		$gjproduct = GjProduct::model() -> find('id=:id',array(
				':id' => $id
		));
		if($gjproduct){
			$result['status'] = ERROR_NONE;
			$result['data'] = array(
					'id' => $gjproduct -> id,
					'name' => $gjproduct -> name,
					'price' => $gjproduct -> price,
					'original_price' => $gjproduct -> original_price,
					'sold_num' => $gjproduct -> sold_num,
			);
			return json_encode($result);
		}else{
			$result['status'] = ERROR_PARAMETER_FORMAT;
			$result['errMsg'] = '该产品不存在';
			return json_encode($result);
		}
		
	}
}