<?php

class UserController extends PropertyController
{

	/**
	 * 用户注册
	 */
	public function actionSign()
	{
		if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
			$encrypt_id = $_GET['encrypt_id'];
		}

		$user = new MobileUserUC();
		$re = json_decode($user->getMerchant($encrypt_id));

		$merchant_id = $re->data->id;

		$communityC = new CommunityC();
		$community_list = '';
		$result = json_decode($communityC -> getCommunityList($merchant_id),true);

		if ($result['status'] == APPLY_CLASS_SUCCESS){
			$community_list = $result['data'];
		}

		$proprietorC = new ProprietorC();
		if (!empty($_POST) && isset($_POST)) {
			//获取表单
			$post = $_POST;

			$account = $post['mobile_phone'];

			$is_res = $user->accountExist($merchant_id, $account);
			if ($is_res) {
				Yii::app()->user->setFlash('error', '该手机号已被注册');
			}else{
				//添加表单数据
				$result = json_decode($proprietorC -> registerProprietor($post,$merchant_id),true);

				if ($result['status'] == APPLY_CLASS_SUCCESS) {
					$user_id = $result['data'];
					Yii::app()->session[$encrypt_id . 'user_id'] = $user_id;

					//会员绑定
					if (Yii::app()->session['source'] == 'wechat') {
						//查找open_id对应的粉丝
						$res_fans = json_decode($user->getFansByOpenid($merchant_id, Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
						if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
							//查找是否存在与该粉丝同样open_id的会员
							$res_user_same_open_id = json_decode($user->getUserByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
							if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
								//解除会员微信粉丝绑定
								$user->clearUserWechatBind($res_user_same_open_id['data']['id']);
							}

							//判断登录用户是否绑定粉丝（open_id字段是否为空）
							$res_user = json_decode($user->getWechatOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
							if ($res_user['status'] == ERROR_NONE) {
								$user_open_id = $res_user['data'];
								if ($user_open_id != '') {
									//查找会员绑定的粉丝
									$res_fans_same_open_id = json_decode($user->getNewFansByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
									if ($res_fans_same_open_id['status'] == ERROR_NONE) {
										//解除粉丝绑定
										$user->clearFansBind($res_fans_same_open_id['data']['id']);
									}
								}
							}

							//修改会员信息
							$user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
						}
					} elseif (Yii::app()->session['source'] == 'alipay') {
						//查找open_id对应的粉丝
						$res_fans = json_decode($user->getFansByOpenid($merchant_id, '', Yii::app()->session[$encrypt_id . 'open_id']), true);
						if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
							//查找是否存在与该粉丝同样open_id的会员
							$res_user_same_open_id = json_decode($user->getUserByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
							if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
								//解除会员支付宝粉丝绑定
								$user->clearUserAlipayBind($res_user_same_open_id['data']['id']);
							}

							//判断登录用户是否绑定粉丝（open_id字段是否为空）
							$res_user = json_decode($user->getAlipayOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
							if ($res_user['status'] == ERROR_NONE) {
								$user_open_id = $res_user['data'];
								if ($user_open_id != '') {
									//查找会员绑定的粉丝
									$res_fans_same_open_id = json_decode($user->getNewFansByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
									if ($res_fans_same_open_id['status'] == ERROR_NONE) {
										//解除粉丝绑定
										$user->clearFansBind($res_fans_same_open_id['data']['id']);
									}
								}
							}

							//修改会员信息
							$user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
						}
					}

					//删除缓存中的短信验证码
					$this->delMsgPwd($account);
					
					$this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/Review', array(
						'encrypt_id' => $encrypt_id
					)));
				} else {
					if ($result['status'] == ERROR_PARAMETER_MISS){
						Yii::app()->user->setFlash('error', $result['errMsg']);
					}
					if ($result['status'] == ERROR_PARAMETER_FORMAT){
						Yii::app()->user->setFlash('error', $result['errMsg']);
					}
				}
			}
		}

		$this->render('sign',array(
			'encrypt_id' => $encrypt_id,
			'community_list' => $community_list,
		));
	}

	/**
	 * 用户注册失败信息修改
	 */
	public function actionSignFail()
	{
		if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
			$encrypt_id = $_GET['encrypt_id'];
		}
		$user_id =$this->getUserId();

		$user = new MobileUserUC();
		$re = json_decode($user->getMerchant($encrypt_id));

		$merchant_id = $re->data->id;

		$communityC = new CommunityC();
		$community_list = '';
		$result = json_decode($communityC -> getCommunityList($merchant_id),true);

		if ($result['status'] == APPLY_CLASS_SUCCESS){
			$community_list = $result['data'];
		}
		$proprietorC = new ProprietorC();
		$proprietor_Info = '';
		if (!empty($user_id)){
			$res = $proprietorC -> getProprietorInfo($user_id);
			if ($res['status'] == APPLY_CLASS_SUCCESS){
				$proprietor_Info = $res['data'];
			}
		}

		if (!empty($_POST) && isset($_POST)) {
			//获取表单
			$post = $_POST;
			$account = $post['mobile_phone'];
				//修改表单数据
				$result = json_decode($proprietorC -> registerFailProprietor($post,$merchant_id,$user_id),true);

				if ($result['status'] == APPLY_CLASS_SUCCESS) {
					$user_id = $result['data'];
					Yii::app()->session[$encrypt_id . 'user_id'] = $user_id;

					//会员绑定
					if (Yii::app()->session['source'] == 'wechat') {
						//查找open_id对应的粉丝
						$res_fans = json_decode($user->getFansByOpenid($merchant_id, Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
						if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
							//查找是否存在与该粉丝同样open_id的会员
							$res_user_same_open_id = json_decode($user->getUserByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
							if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
								//解除会员微信粉丝绑定
								$user->clearUserWechatBind($res_user_same_open_id['data']['id']);
							}

							//判断登录用户是否绑定粉丝（open_id字段是否为空）
							$res_user = json_decode($user->getWechatOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
							if ($res_user['status'] == ERROR_NONE) {
								$user_open_id = $res_user['data'];
								if ($user_open_id != '') {
									//查找会员绑定的粉丝
									$res_fans_same_open_id = json_decode($user->getNewFansByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
									if ($res_fans_same_open_id['status'] == ERROR_NONE) {
										//解除粉丝绑定
										$user->clearFansBind($res_fans_same_open_id['data']['id']);
									}
								}
							}

							//修改会员信息
							$user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
						}
					} elseif (Yii::app()->session['source'] == 'alipay') {
						//查找open_id对应的粉丝
						$res_fans = json_decode($user->getFansByOpenid($merchant_id, '', Yii::app()->session[$encrypt_id . 'open_id']), true);
						if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
							//查找是否存在与该粉丝同样open_id的会员
							$res_user_same_open_id = json_decode($user->getUserByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
							if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
								//解除会员支付宝粉丝绑定
								$user->clearUserAlipayBind($res_user_same_open_id['data']['id']);
							}

							//判断登录用户是否绑定粉丝（open_id字段是否为空）
							$res_user = json_decode($user->getAlipayOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
							if ($res_user['status'] == ERROR_NONE) {
								$user_open_id = $res_user['data'];
								if ($user_open_id != '') {
									//查找会员绑定的粉丝
									$res_fans_same_open_id = json_decode($user->getNewFansByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
									if ($res_fans_same_open_id['status'] == ERROR_NONE) {
										//解除粉丝绑定
										$user->clearFansBind($res_fans_same_open_id['data']['id']);
									}
								}
							}

							//修改会员信息
							$user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
						}
					}

					//删除缓存中的短信验证码
					$this->delMsgPwd($account);

					$this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/Review', array(
						'encrypt_id' => $encrypt_id
					)));
				} else {
					if ($result['status'] == ERROR_PARAMETER_MISS){
						Yii::app()->user->setFlash('error', $result['errMsg']);
					}
					if ($result['status'] == ERROR_PARAMETER_FORMAT){
						Yii::app()->user->setFlash('error', $result['errMsg']);
					}
				}
		}

		$this->render('editSign',array(
			'encrypt_id' => $encrypt_id,
			'community_list' => $community_list,
			'proprietor_Info' => $proprietor_Info,
		));
	}
	
	/**
	 * 用户登录
	 */
	public function actionLogin()
	{
		if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
			$encrypt_id = $_GET['encrypt_id'];
		}
		
		if (!empty($_POST) && isset($_POST)){
			$post = $_POST;

			$user = new MobileUserUC();
			$re = json_decode($user->getMerchant($encrypt_id));
			$merchant_id = $re->data->id;

			$proprietorC = new ProprietorC();
			$result = json_decode($proprietorC -> loginProprietor($post,$merchant_id),true);

			if ($result['status'] == ''){
				//生成用户登录状态session
				Yii::app()->session[$encrypt_id . 'user_id'] = $result['data'];
				$this->redirect(Yii::app()->createUrl('mobile/property/User/SignFail', array(
					'encrypt_id' => $encrypt_id,
				)));
			}
			if ($result['status'] == APPLY_CLASS_SUCCESS) {
				if($result['verify_status'] == PROPRIETOR_VERIFY_STATUS_PENDING_AUDIT){
					$this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/Review', array(
						'encrypt_id' => $encrypt_id
					)));
				}
				if ($result['verify_status'] == PROPRIETOR_VERIFY_STATUS_REJECT){
					$this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/ReviewNopass', array(
						'encrypt_id' => $encrypt_id
					)));
				}else{
					/*$this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/ReviewPass', array(
						'encrypt_id' => $encrypt_id
					)));*/
					//生成用户登录状态session
					Yii::app()->session[$encrypt_id . 'user_id'] = $result['data'];

					$login_ip = Yii::app()->request->userHostAddress; //ip地址
					$login_client = Yii::app()->session['source']; //客户端
					switch ($login_client) {
						case 'wechat':
							$login_client = 1;
							break;
						case 'alipay':
							$login_client = 2;
							break;
						case 'web':
							$login_client = 3;
							break;
					}

					//保存登录信息
					$user->saveLoginInfo(Yii::app()->session[$encrypt_id . 'user_id'], $login_ip, $login_client);

					//会员绑定
					if (Yii::app()->session['source'] == 'wechat') {
						//查找open_id对应的粉丝
						$res_fans = json_decode($user->getFansByOpenid($merchant_id, Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
						if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
							//查找登录用户的open_id
							$res_user = json_decode($user->getWechatOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
							if ($res_user['status'] == ERROR_NONE) {
								$user_open_id = $res_user['data'];
								//判断登录用户的open_id和session里的open_id是否一致
								if (Yii::app()->session[$encrypt_id . 'open_id'] != $user_open_id) { //不一致
									//查找是否存在与该粉丝同样open_id的会员
									$res_user_same_open_id = json_decode($user->getUserByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
									if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
										//解除会员微信粉丝绑定
										$user->clearUserWechatBind($res_user_same_open_id['data']['id']);
									}

									//判断登录用户是否绑定粉丝
									if ($user_open_id != '') {
										//查找会员绑定的粉丝
										$res_fans_same_open_id = json_decode($user->getNewFansByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
										if ($res_fans_same_open_id['status'] == ERROR_NONE) {
											//解除粉丝绑定
											$user->clearFansBind($res_fans_same_open_id['data']['id']);
										}
									}

									//修改会员信息
									$user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
								}
							}
						}
					} elseif (Yii::app()->session['source'] == 'alipay') {
						//查找open_id对应的粉丝
						$res_fans = json_decode($user->getFansByOpenid($merchant_id, '', Yii::app()->session[$encrypt_id . 'open_id']), true);
						if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
							//查找登录用户的open_id
							$res_user = json_decode($user->getAlipayOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
							if ($res_user['status'] == ERROR_NONE) {
								$user_open_id = $res_user['data'];
								//判断登录用户的open_id和session里的open_id是否一致
								if (Yii::app()->session[$encrypt_id . 'open_id'] != $user_open_id) { //不一致
									//查找是否存在与该粉丝同样open_id的会员
									$res_user_same_open_id = json_decode($user->getUserByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
									if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
										//解除会员支付宝粉丝绑定
										$user->clearUserAlipayBind($res_user_same_open_id['data']['id']);
									}

									//判断登录用户是否绑定粉丝
									if ($user_open_id != '') {
										//查找会员绑定的粉丝
										$res_fans_same_open_id = json_decode($user->getNewFansByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
										if ($res_fans_same_open_id['status'] == ERROR_NONE) {
											//解除粉丝绑定
											$user->clearFansBind($res_fans_same_open_id['data']['id']);
										}
									}

									//修改会员信息
									$user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
								}
							}
						}
					}

					$this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/Index', array(
						'encrypt_id' => $encrypt_id
					)));
				}
			} else {
				if ($result['status'] = ERROR_PARAMETER_MISS){
					Yii::app()->user->setFlash('error', $result['errMsg']);
				}
				if ($result['status'] = ERROR_DATA_BASE_SELECT){
					Yii::app()->user->setFlash('error', $result['errMsg']);
				}
			}
		}
		
		$this->render('login', array(
			'encrypt_id' => $encrypt_id
		));
	}

	/**
	 * 验证手机号是否存在
	 */
	public function actionIsExist()
	{
		$result = '';
		$user = new UserUC();
		if (isset($_POST['account']) && !empty($_POST['account']) && isset($_POST['encrypt_id']) && !empty($_POST['encrypt_id'])) {
			$result = json_decode($user->getMerchant($_POST['encrypt_id']));
			$merchant_id = $result->data->id;
			$account = $_POST['account'];
			$res = $user->accountExist($merchant_id, $account);
			if ($res) {
				$result = 'exist';
			} else {
				$result = 'not';
			}
		}
		echo json_encode(array('result' => $result));
	}

	/**
	 * 获取短信验证码
	 */
	public function actionSendMsgPassword()
	{
		$message = new DuanXin();
		$user = new UserUC();

		$tel_res = false;
		$tel = $_POST['mobile'];
		$is_check = $_POST['check'];

		if (isset($_POST['encrypt_id']) && !empty($_POST['encrypt_id'])) {
			$encrypt_id = $_POST['encrypt_id'];
			$merchat = new MerchantC();
			$result = json_decode($merchat->getMerchantByEncrypt($encrypt_id));
			$merchant_id = $result->data->id;
		}

		//验证手机号是否存在
		if ($is_check == 'yes') {
			$tel_res = $user->accountExist($merchant_id, $tel);
		}

		if ($tel_res && $is_check == 'yes') {
			$tel_result['status'] = ERROR_DUPLICATE_DATA;
			$tel_result['errMsg'] = '该号码已被注册';

			echo json_encode($tel_result);
		} else {
			//判断商户短信余额是否足够
			$check_res = $user->checkMsgNum($merchant_id);
			$check_result = json_decode($check_res, true);
			if ($check_result['status'] == ERROR_NONE) {
				$res = $message->Sms($tel);
				$result = json_decode($res, true);

				if ($result['status'] == ERROR_NONE) {
					if (isset($result['data'])) {
						$phone_num = $result['data']['phone_num'];
						$msg_pwd = $result['data']['msg_pwd'];
						// 把短信验证码保存到memcache里保存时间30分钟
						$this->saveMsgPwd($phone_num, $msg_pwd);
						//将商户短信余额减少1条
						$res = $user->minusMsgNum($merchant_id);
					}
				}
				echo $res;
			} else {
				echo $check_res;
			}
		}
	}


	/**
	 * 将短信验证码保存到缓存中30分钟
	 */
	public function saveMsgPwd($phone_num, $msg_pwd)
	{
		$key = $phone_num;
		$ckKey = Yii::app()->memcache->get($key);
		if ($ckKey != null) {
			Yii::app()->memcache->delete($key);
			$value = $msg_pwd;
			$expire = 1800;
			Yii::app()->memcache->set($key, $value, $expire);
		} else {
			$value = $msg_pwd;
			$expire = 1800;
			Yii::app()->memcache->set($key, $value, $expire);
		}
	}

	/**
	 * 将缓存中的短信验证码删除
	 */
	public function delMsgPwd($phone_num)
	{
		$key = $phone_num;
		$ckKey = Yii::app()->memcache->get($key);
		if ($ckKey != null) {
			Yii::app()->memcache->delete($key);
		}
	}

	/**
	 * 用户退出登录
	 */
	public function actionLogout(){
		$encrypt_id = $this->getEncryptId();
		Yii::app()->session->clear();
		Yii::app()->session->destroy();
		$this->redirect($this->createUrl('User/Login', array('encrypt_id' => $encrypt_id)));
	}
}