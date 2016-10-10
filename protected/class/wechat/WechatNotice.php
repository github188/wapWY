<?php

/**
 * 微信公众号模板通知类
*/
class WechatNotice extends WechatBase{
	
	public $appid = 'wxec84afe11d9da7c4'; //第三方用户唯一凭证
	public $appsecret = 'd41af6b0d7efdfb5fb925db13b205533'; //第三方用户唯一凭证密钥

	//系统模板编号
	protected $short_id = array(
			'pay' => 'OPENTM401642936', //移动收银台收款通知short_id
			'book' => 'OPENTM202522955', //房间预定成功通知short_id
	);
	//系统模板id
	protected $template_id = array(
			'pay' => 'jvTlY4MoRRq41Xh4uX2uvm9Rlb7cwEmf_fz3cjTTZfA', //移动收银台收款通知
			'book' => 'k8KNEa2F4Vq0wGTrazAPrCx2hQGnX_SwL2fOhlISNrY', //房间预定成功通知
	);
	
	protected $sysParam = TRUE; //是否使用系统参数
	protected $access_token = NULL; //接口调用凭证
	protected $expires_in = NULL; //凭证有效期
	
	const SET_INDUSTRY_URL = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry'; //设置所属行业
	const GET_INDUSTRY_URL = 'https://api.weixin.qq.com/cgi-bin/template/get_industry'; //获取设置的行业信息
	const ADD_TEMPLATE_URL = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template'; //获得模板ID
	const ALL_TEMPLATE_URL = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template'; //获取模板列表
	const DEL_TEMPLATE_URL = 'https://api,weixin.qq.com/cgi-bin/template/del_private_template'; //删除模板
	const SEND_URL = 'https://api.weixin.qq.com/cgi-bin/message/template/send'; //发送模板消息
	
	const ERRCODE_OK = '0'; //成功返回码
	
	const NOTICE_TYPE_PAY = 'PAY'; //支付的通知
	const NOTICE_TYPE_BOOK = 'BOOK'; //预订的通知
	
	/**
	 * 构造方法（appid和appsecret置空则使用系统公众号参数）
	 * @param unknown $appid
	 * @param unknown $appsecret
	 */
	public function __construct($appid = NULL, $appsecret = NULL) {
		if ($appid && $appsecret) {
			$this->appid = $appid;
			$this->appsecret = $appsecret;
			$this->sysParam = false;
		}
	}
	
	/**
	 * 获取接口凭证access_token
	 * @return boolean
	 */
	protected function updateAccessToken() {
		$appid = $this->appid;
		$appsecret = $this->appsecret;
		//获取接口凭证信息
		$token = $this->getAccessToken($appid, $appsecret);
		if (isset($token['error']) && $token['error']) {
			$this->access_token = $token['access_token'];
			$this->expires_in = $token['expires_in'];
			return true;
		}
		return false;
	}
	
	/************************************接口调用方法**************************************/
	/**
	 * 设置行业
	 */
	protected function ApiSetIndustry($industry1, $industry2) {
		//接口凭证是否有效
		if (!$this->access_token) {
			//更新接口凭证access_token
			$ret = $this->updateAccessToken();
			if (!$ret) {
				Yii::log('微信接口凭证获取失败-'.date('Y-m-d H:i:s'),'warning');
				return;
			}
		}
		
		//请求地址
		$url = self::SET_INDUSTRY_URL.'?access_token='.$this->access_token;
		//请求参数
		$param = array(
				'industry_id1' => $industry1,
				'industry_id2' => $industry2
		);
		//接口请求
		$response = $this->https_request($url, json_encode($param));
		$json = json_decode($response, true);
		if (is_array($json)) {
			$errcode = $json['errcode'];
			$errmsg = $json['errmsg'];
		}
	}
	
	/**
	 * 获取模板id
	 * @return string
	 */
	protected function ApiGetTemplateId($short_id) {
		$template_id = '';
		
		//接口凭证是否有效
		if (!$this->access_token) {
			//更新接口凭证access_token
			$ret = $this->updateAccessToken();
			if (!$ret) {
				Yii::log('微信接口凭证获取失败-'.date('Y-m-d H:i:s'),'warning');
				return $template_id;
			}
		}
		
		//请求地址
		$url = self::ADD_TEMPLATE_URL.'?access_token='.$this->access_token;
		//请求参数
		$param = array(
			'template_id_short' => $short_id
		);
		//接口请求
		$response = $this->https_request($url, json_encode($param));
		$json = json_decode($response, true);
		if (is_array($json)) {
			$errcode = $json['errcode'];
			$errmsg = $json['errmsg'];
			if ($errcode == self::ERRCODE_OK) {
				$template_id = $json['template_id'];
			}
		}
		
		return $template_id;
	}
	
	/**
	 * 发送模板消息
	 * @param unknown $openid
	 * @param unknown $template_id
	 * @param unknown $url
	 * @param unknown $data
	 * @return unknown|Ambigous <string, mixed>
	 */
	public function ApiSend($openid, $template_id, $detail_url, $data) {
		$msg_id = '';
		
		//接口凭证是否有效
		if (!$this->access_token) {
			//更新接口凭证access_token
			$ret = $this->updateAccessToken();
			if (!$ret) {
				Yii::log('微信接口凭证获取失败-'.date('Y-m-d H:i:s'),'warning');
				return $template_id;
			}
		}
		
		//请求地址
		$url = self::SEND_URL.'?access_token='.$this->access_token;
		//请求参数
		$param = array(
				'touser' => $openid,
				'template_id' => $template_id,
				'url' => $detail_url,
				'data' => $data
		);
		//接口请求
		$response = $this->https_request($url, json_encode($param));
		$json = json_decode($response, true);
		if (is_array($json)) {
			$errcode = $json['errcode'];
			$errmsg = $json['errmsg'];
			if ($errcode == self::ERRCODE_OK) {
				$msg_id = $json['msgid'];
			}
		}
		
		return $msg_id;
	}
	
	
	
	/************************************业务处理方法**************************************/
	public function setIndustry() {
		$this->ApiSetIndustry('1', '11');
	}
	
	/**
	 * 获取支付通知模板消息id
	 * @param unknown $merchant_id
	 * @param string $short_id
	 * @throws Exception
	 * @return string
	 */
	public function getTemplateId4Pay($merchant_id, $short_id = NULL) {
		$result = array();
		try {
			//获取商户信息
			$merchant = Merchant::model()->findByPk($merchant_id);
			if (empty($merchant)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('商户不存在');
			}
			$template_id = $merchant['wechat_template_id'];
			
			$template_id = $this->template_id['pay']; //强制使用系统templat_id，后期可以注释掉
			
			//如果模板消息id为空，则请求接口进行获取
			if (empty($template_id)) {
				if ($this->sysParam) {
					$template_id = $this->template_id['pay'];
				}else {
					if (empty($short_id)) {
						$short_id = $this->short_id['pay']; //使用系统short_id
					}
						
					$template_id = $this->ApiGetTemplateId($short_id);
						
					$merchant['wechat_template_id'] = $template_id;
					if (!$merchant->save()) {
						$result['status'] = ERROR_SAVE_FAIL;
						throw new Exception('数据保存失败');
					}
				}
			}
			
			$result['template_id'] = $template_id;
			$result['status'] = ERROR_NONE;
		
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 获取预订通知模板消息id
	 * @param unknown $merchant_id
	 * @param string $short_id
	 * @throws Exception
	 * @return string
	 */
	public function getTemplateId4Book($merchant_id, $short_id = NULL) {
		$result = array();
		try {
			//获取商户信息
			$merchant = Merchant::model()->findByPk($merchant_id);
			if (empty($merchant)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('商户不存在');
			}
			//$template_id = $merchant['wechat_template_id']; //数据库字段未定义，待定
				
			$template_id = $this->template_id['book']; //强制使用系统templat_id，后期可以注释掉
				
			//如果模板消息id为空，则请求接口进行获取
			if (empty($template_id)) {
				if ($this->sysParam) {
					$template_id = $this->template_id['book'];
				}else {
					if (empty($short_id)) {
						$short_id = $this->short_id['book']; //使用系统short_id
					}
						
					$template_id = $this->ApiGetTemplateId($short_id);
						
					/*数据库字段未定义，待定
					 $merchant['wechat_template_id'] = $template_id;
					if (!$merchant->save()) {
					$result['status'] = ERROR_SAVE_FAIL;
					throw new Exception('数据保存失败');
					}
					*/
				}
			}
				
			$result['template_id'] = $template_id;
			$result['status'] = ERROR_NONE;
		
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 发送消息
	 * @param unknown $notice_type 通知类型
	 * @param unknown $data 通知的内容
	 * @param string $store_id
	 * @throws Exception
	 * @return string
	 */
	public function send($notice_type, $data, $store_id = NULL) {
		if ($notice_type == self::NOTICE_TYPE_PAY) {
			return $this->sendPayNotice($store_id, $data);
		}
		if ($notice_type == self::NOTICE_TYPE_BOOK) {
			return $this->sendBookNotice($store_id, $data);
		}
	}
	
	/**
	 * 发送支付通知
	 * @param unknown $store_id
	 * @param unknown $data
	 * @throws Exception
	 * @return string
	 */
	public function sendPayNotice($store_id, $data) {
		$result = array();
		try {
			$msg_arr = array(); //消息id集合
				
			//查询待接收用户的openid
			$criteria = new CDbCriteria();
			$criteria->addCondition('store_id = :store_id');
			$criteria->params[':store_id'] = $store_id;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$criteria->addCondition('wechat_open_id IS NOT NULL');
				
			$notice_user = TradeNoticeUser::model()->findAll($criteria);
			if (!empty($notice_user)) {
				//查询门店信息
				$store = Store::model()->findByPk($store_id);
				if (empty($store)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('门店不存在');
				}
				$merchant_id = $store['merchant_id'];
				//获取模板消息id
				$ret = $this->getTemplateId4Pay($merchant_id);
				$res1 = json_decode($ret, true);
				if ($res1['status'] != ERROR_NONE) {
					throw new Exception($res1['errMsg']);
				}
				$template_id = $res1['template_id'];
				//消息跳转页
				$url = '';
	
				foreach ($notice_user as $k => $v) {
					$openid = $v['wechat_open_id']; //接收者openid
					//内容组装
					$tmp = array();
					foreach ($data as $k1 => $v1) {
						$tmp[$k1] = array('value' => $v1, 'color' => '#173177');
					}
					//发送消息
					$msg_id = $this->ApiSend($openid, $template_id, $url, $tmp);
					//保存msgid
					if (!empty($msg_id)) {
						$msg_arr[] = $msg_id;
					}
				}
			}
	
			$result['msg_arr'] = $msg_arr;
			$result['status'] = ERROR_NONE;
	
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
	
		return json_encode($result);
	}
	
	/**
	 * 发送预订通知
	 * @param unknown $store_id
	 * @param unknown $data
	 * @throws Exception
	 * @return string
	 */
	public function sendBookNotice($store_id, $data) {
		$result = array();
		try {
			$msg_arr = array(); //消息id集合
				
			//查询待接收用户的openid
			$criteria = new CDbCriteria();
			$criteria->addCondition('store_id = :store_id');
			$criteria->params[':store_id'] = $store_id;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$criteria->addCondition('wechat_open_id IS NOT NULL');
				
			$notice_user = TradeNoticeUser::model()->findAll($criteria);
			if (!empty($notice_user)) {
				//查询门店信息
				$store = Store::model()->findByPk($store_id);
				if (empty($store)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('门店不存在');
				}
				$merchant_id = $store['merchant_id'];
				//获取模板消息id
				$ret = $this->getTemplateId4Book($merchant_id);
				$res1 = json_decode($ret, true);
				if ($res1['status'] != ERROR_NONE) {
					throw new Exception($res1['errMsg']);
				}
				$template_id = $res1['template_id'];
				//消息跳转页
				$url = '';
		
				foreach ($notice_user as $k => $v) {
					$openid = $v['wechat_open_id']; //接收者openid
					//内容组装
					$tmp = array();
					foreach ($data as $k1 => $v1) {
						$tmp[$k1] = array('value' => $v1, 'color' => '#173177');
					}
					//发送消息
					$msg_id = $this->ApiSend($openid, $template_id, $url, $tmp);
					//保存msgid
					if (!empty($msg_id)) {
						$msg_arr[] = $msg_id;
					}
				}
			}
		
			$result['msg_arr'] = $msg_arr;
			$result['status'] = ERROR_NONE;
		
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
}