<?php

/**
 * This is the model class for table "{{merchant}}".
 *
 * The followings are the available columns in table '{{merchant}}':
 * @property integer $id
 * @property string $merchant_no
 * @property string $wechat_merchant_no
 * @property string $wq_mchid
 * @property integer $agent_id
 * @property string $channel_id
 * @property string $account
 * @property string $pwd
 * @property string $name
 * @property string $wq_m_short_name
 * @property integer $wq_m_type
 * @property string $wq_m_name
 * @property string $wq_m_industry
 * @property string $wq_m_address
 * @property string $wq_m_business_license_no
 * @property string $wq_m_business_license
 * @property string $wq_m_organization_code
 * @property string $wq_m_organization
 * @property string $wq_m_legal_person_name
 * @property string $wq_m_legal_person_id
 * @property string $wq_m_legal_person_positive
 * @property string $wq_m_legal_person_opposite
 * @property string $wq_m_contacts_name
 * @property string $wq_m_contacts_phone
 * @property integer $wq_m_verify_status
 * @property string $wq_m_verify_pass_time
 * @property string $wq_m_reject_remark
 * @property string $wx_name
 * @property string $seller_email
 * @property string $key
 * @property string $create_time
 * @property string $last_time
 * @property integer $status
 * @property integer $flag
 * @property string $alipay_code
 * @property integer $verify_status
 * @property integer $wechat_verify_status
 * @property string $wechat_verify_status_submit_time
 * @property string $wechat_verify_status_auditpass_time
 * @property string $wechat_verify_status_verify_time
 * @property string $wechat_verify_status_sign_time
 * @property string $wechat_verify_status_reject_time
 * @property string $remark
 * @property integer $msg_num
 * @property integer $if_stored
 * @property integer $points_rule
 * @property string $merchant_number
 * @property string $gj_start_time
 * @property string $gj_end_time
 * @property integer $gj_product_id
 * @property integer $if_tryout
 * @property integer $tryout_status
 * @property integer $gj_open_status
 * @property string $encrypt_id
 * @property string $wechat_id
 * @property string $wechat
 * @property integer $wechat_type
 * @property string $wechat_qrcode
 * @property string $wechat_account
 * @property string $wechat_appsecret
 * @property string $wechat_subscription_appsecret
 * @property string $wechat_key
 * @property string $wechat_mchid
 * @property string $t_wx_appid
 * @property string $wechat_interface_url
 * @property string $wechat_token
 * @property string $wechat_encodingaeskey
 * @property integer $wechat_encrypt_type
 * @property integer $operator_refund_time
 * @property string $wechat_apiclient_key
 * @property string $wechat_appid
 * @property string $wechat_subscription_appid
 * @property integer $dzoperator_refund_time
 * @property string $access_token_json
 * @property string $jsapi_ticket_json
 * @property string $fuwu_name
 * @property string $wechat_name
 * @property string $ums_3des_key
 * @property string $api_key
 * @property string $mchid
 * @property string $ums_mchid
 * @property string $alipay_qrcode
 * @property integer $if_wx_open
 * @property integer $wxpay_merchant_type
 * @property string $wechat_apiclient_cert
 * @property string $t_wx_mchid
 * @property integer $if_alipay_open
 * @property integer $alipay_api_version
 * @property string $partner
 * @property string $alipay_key
 * @property string $appid
 * @property string $alipay_auth_pid
 * @property string $alipay_auth_appid
 * @property string $alipay_auth_token
 * @property string $alipay_auth_refresh_token
 * @property string $alipay_auth_time
 * @property string $alipay_auth_token_expires_in
 * @property string $alipay_auth_refresh_token_expires_in
 * @property string $wechat_template_id
 * @property string $wechat_thirdparty_authorizer_info
 * @property string $wechat_thirdparty_authorizer_refresh_token
 * @property string $wechat_thirdparty_authorizer_appid
 * @property integer $wechat_thirdparty_authorizer_if_auth
 * @property string $wechat_thirdparty_authorization_info
 * @property string $wechat_thirdparty_authorizer_time
 * @property string $wechat_thirdparty_authorizer_cancel_time
 * @property string $wechat_thirdparty_authorizer_refresh_time
 */
class Merchant extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{merchant}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('agent_id, wq_m_type, wq_m_verify_status, status, flag, verify_status, wechat_verify_status, msg_num, if_stored, points_rule, gj_product_id, if_tryout, tryout_status, gj_open_status, wechat_type, wechat_encrypt_type, operator_refund_time, dzoperator_refund_time, if_wx_open, wxpay_merchant_type, if_alipay_open, alipay_api_version, wechat_thirdparty_authorizer_if_auth', 'numerical', 'integerOnly'=>true),
			array('merchant_no, wechat_merchant_no, channel_id, account, pwd, wq_m_industry, wq_m_business_license_no, wq_m_business_license, wq_m_organization, wq_m_legal_person_name, wq_m_legal_person_id, wq_m_legal_person_positive, wq_m_legal_person_opposite, wq_m_contacts_name, wq_m_contacts_phone, seller_email, key, wechat_id, wechat, wechat_qrcode, wechat_key, wechat_mchid, wechat_appid, wechat_subscription_appid, ums_3des_key, api_key, mchid, alipay_qrcode', 'length', 'max'=>32),
			array('wq_mchid', 'length', 'max'=>20),
			array('name, wq_m_short_name, wq_m_name, wx_name, wechat_account, wechat_appsecret, wechat_subscription_appsecret, wechat_token, wechat_encodingaeskey, wechat_apiclient_key, fuwu_name, wechat_name, wechat_apiclient_cert, appid, alipay_auth_token, alipay_auth_refresh_token', 'length', 'max'=>100),
			array('wq_m_address, wq_m_reject_remark', 'length', 'max'=>225),
			array('wq_m_organization_code, t_wx_appid, ums_mchid, t_wx_mchid, alipay_key, alipay_auth_pid, alipay_auth_appid, wechat_template_id, wechat_thirdparty_authorizer_appid', 'length', 'max'=>50),
			array('alipay_code', 'length', 'max'=>12),
			array('merchant_number, encrypt_id', 'length', 'max'=>10),
			array('wechat_interface_url, access_token_json, jsapi_ticket_json', 'length', 'max'=>255),
			array('partner', 'length', 'max'=>16),
			array('wechat_thirdparty_authorizer_info, wechat_thirdparty_authorization_info', 'length', 'max'=>1000),
			array('wechat_thirdparty_authorizer_refresh_token', 'length', 'max'=>600),
			array('wq_m_verify_pass_time, create_time, last_time, wechat_verify_status_submit_time, wechat_verify_status_auditpass_time, wechat_verify_status_verify_time, wechat_verify_status_sign_time, wechat_verify_status_reject_time, remark, gj_start_time, gj_end_time, alipay_auth_time, alipay_auth_token_expires_in, alipay_auth_refresh_token_expires_in, wechat_thirdparty_authorizer_time, wechat_thirdparty_authorizer_cancel_time, wechat_thirdparty_authorizer_refresh_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_no, wechat_merchant_no, wq_mchid, agent_id, channel_id, account, pwd, name, wq_m_short_name, wq_m_type, wq_m_name, wq_m_industry, wq_m_address, wq_m_business_license_no, wq_m_business_license, wq_m_organization_code, wq_m_organization, wq_m_legal_person_name, wq_m_legal_person_id, wq_m_legal_person_positive, wq_m_legal_person_opposite, wq_m_contacts_name, wq_m_contacts_phone, wq_m_verify_status, wq_m_verify_pass_time, wq_m_reject_remark, wx_name, seller_email, key, create_time, last_time, status, flag, alipay_code, verify_status, wechat_verify_status, wechat_verify_status_submit_time, wechat_verify_status_auditpass_time, wechat_verify_status_verify_time, wechat_verify_status_sign_time, wechat_verify_status_reject_time, remark, msg_num, if_stored, points_rule, merchant_number, gj_start_time, gj_end_time, gj_product_id, if_tryout, tryout_status, gj_open_status, encrypt_id, wechat_id, wechat, wechat_type, wechat_qrcode, wechat_account, wechat_appsecret, wechat_subscription_appsecret, wechat_key, wechat_mchid, t_wx_appid, wechat_interface_url, wechat_token, wechat_encodingaeskey, wechat_encrypt_type, operator_refund_time, wechat_apiclient_key, wechat_appid, wechat_subscription_appid, dzoperator_refund_time, access_token_json, jsapi_ticket_json, fuwu_name, wechat_name, ums_3des_key, api_key, mchid, ums_mchid, alipay_qrcode, if_wx_open, wxpay_merchant_type, wechat_apiclient_cert, t_wx_mchid, if_alipay_open, alipay_api_version, partner, alipay_key, appid, alipay_auth_pid, alipay_auth_appid, alipay_auth_token, alipay_auth_refresh_token, alipay_auth_time, alipay_auth_token_expires_in, alipay_auth_refresh_token_expires_in, wechat_template_id, wechat_thirdparty_authorizer_info, wechat_thirdparty_authorizer_refresh_token, wechat_thirdparty_authorizer_appid, wechat_thirdparty_authorizer_if_auth, wechat_thirdparty_authorization_info, wechat_thirdparty_authorizer_time, wechat_thirdparty_authorizer_cancel_time, wechat_thirdparty_authorizer_refresh_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		    'agent' => array(self::BELONGS_TO,'Agent','agent_id'),
		    'gjproduct' => array(self::BELONGS_TO,'GjProduct','gj_product_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'merchant_no' => '商户号',
			'wechat_merchant_no' => '微信商户号',
			'wq_mchid' => '玩券商户号 1+区号+随机5至6位随机数',
			'agent_id' => '合作商id',
			'channel_id' => '渠道商id',
			'account' => '账号',
			'pwd' => '密码',
			'name' => '商户名称',
			'wq_m_short_name' => '玩券商户简称',
			'wq_m_type' => '玩券商户类型 1 企业 2 个体工商户 3事业单位',
			'wq_m_name' => '玩券商户名',
			'wq_m_industry' => '玩券商户所属行业',
			'wq_m_address' => '玩券商户地址',
			'wq_m_business_license_no' => '玩券商户营业执照注册号',
			'wq_m_business_license' => '玩券商户营业执照',
			'wq_m_organization_code' => '玩券商户组织机构代码',
			'wq_m_organization' => '玩券商户组织机构代码证',
			'wq_m_legal_person_name' => '玩券商户法人姓名',
			'wq_m_legal_person_id' => '玩券商户法人身份证号',
			'wq_m_legal_person_positive' => '玩券商户法人身份证正面',
			'wq_m_legal_person_opposite' => '玩券商户法人身份证反面',
			'wq_m_contacts_name' => '玩券商户联系人姓名',
			'wq_m_contacts_phone' => '玩券商户联系人手机号',
			'wq_m_verify_status' => '玩券商户审核状态 1待审核 2驳回 3审核通过',
			'wq_m_verify_pass_time' => '玩券商户审核通过时间',
			'wq_m_reject_remark' => '玩券商户驳回原因',
			'wx_name' => '微信商户名',
			'seller_email' => '收款支付宝账号',
			'key' => '安全检验码',
			'create_time' => '创建时间',
			'last_time' => '修改时间',
			'status' => '状态 1 正常 2 锁定',
			'flag' => '删除标志 1正常 2 作废',
			'alipay_code' => '商户编号',
			'verify_status' => '审核状态',
			'wechat_verify_status' => '微信商户审核状态 默认1 未提交',
			'wechat_verify_status_submit_time' => '微信签约提交时间',
			'wechat_verify_status_auditpass_time' => '微信签约审核通过时间',
			'wechat_verify_status_verify_time' => '微信签约验证时间',
			'wechat_verify_status_sign_time' => '微信签约签约时间',
			'wechat_verify_status_reject_time' => '微信签约驳回时间',
			'remark' => '驳回原因',
			'msg_num' => '可用短信条数',
			'if_stored' => '是否开启储值功能',
			'points_rule' => '积分规则',
			'merchant_number' => '商户编号',
			'gj_start_time' => '玩券管家开通时间',
			'gj_end_time' => '玩券管家到期时间',
			'gj_product_id' => '玩券管家id',
			'if_tryout' => '是否试用版',
			'tryout_status' => '试用状态 1正常 2锁定',
			'gj_open_status' => '玩券管家开通状态 1 未开通 2 已开通 3 已过期',
			'encrypt_id' => '商户id',
			'wechat_id' => '微信号原始id',
			'wechat' => '微信号',
			'wechat_type' => '公众号类型 1：订阅号 2：订阅认证号 3：服务号 4：服务认证号',
			'wechat_qrcode' => '公众号二维码',
			'wechat_account' => '微信公众号账号名称',
			'wechat_appsecret' => '公众号appsecret',
			'wechat_subscription_appsecret' => '公众号appsecret(线上)',
			'wechat_key' => '微信商户支付密钥',
			'wechat_mchid' => '微信商户号',
			't_wx_appid' => '特约appid',
			'wechat_interface_url' => '微信公众号接口url',
			'wechat_token' => '接口token',
			'wechat_encodingaeskey' => 'EncodingAESKey',
			'wechat_encrypt_type' => '加密方式',
			'operator_refund_time' => '操作员允许退款时间',
			'wechat_apiclient_key' => 'apiclient_key.pem',
			'wechat_appid' => '公众号appid',
			'wechat_subscription_appid' => '公众号appid(线上)',
			'dzoperator_refund_time' => '店长允许退款时间',
			'access_token_json' => '微信公众号access_token',
			'jsapi_ticket_json' => '微信公众号jsapi_ticket',
			'fuwu_name' => '服务窗名称',
			'wechat_name' => '公众号名称',
			'ums_3des_key' => '银商分配的3des密钥',
			'api_key' => 'api密钥',
			'mchid' => '玩券商户号',
			'ums_mchid' => '银商分配的商户号',
			'alipay_qrcode' => '支付宝服务窗二维码',
			'if_wx_open' => '是否开启微信收款账号 1：关闭 2：开启',
			'wxpay_merchant_type' => '微信支付的商户类型，1：自助商户（默认），2：特约商户',
			'wechat_apiclient_cert' => 'apiclient_cert.pem',
			't_wx_mchid' => '特约微信商户号',
			'if_alipay_open' => '是否开启支付宝收款账号 1：关闭 2：开启',
			'alipay_api_version' => '支付宝接口版本，1:1.0接口，2:2.0接口 3:2.0授权',
			'partner' => '合作身份者id',
			'alipay_key' => '支付宝安全校验码',
			'appid' => 'appid',
			'alipay_auth_pid' => '授权商户pid 2088开头',
			'alipay_auth_appid' => '授权商户appid',
			'alipay_auth_token' => '支付宝2.0授权码',
			'alipay_auth_refresh_token' => '授权商户刷新token令牌',
			'alipay_auth_time' => '最近授权时间',
			'alipay_auth_token_expires_in' => 'token有效截止日期',
			'alipay_auth_refresh_token_expires_in' => '刷新token有效截止日期',
			'wechat_template_id' => '微信消息模板id',
			'wechat_thirdparty_authorizer_info' => '微信第三方授权-授权方信息',
			'wechat_thirdparty_authorizer_refresh_token' => '微信第三方授权-授权方刷新令牌',
			'wechat_thirdparty_authorizer_appid' => '微信第三方授权-授权方appid',
			'wechat_thirdparty_authorizer_if_auth' => '是否经过微信第三方授权 1否 2是',
			'wechat_thirdparty_authorization_info' => '微信第三方授权-授权信息',
			'wechat_thirdparty_authorizer_time' => '微信第三方授权-授权时间',
			'wechat_thirdparty_authorizer_cancel_time' => '微信第三方授权-取消授权时间',
			'wechat_thirdparty_authorizer_refresh_time' => '微信第三方授权-刷新授权时间',
			'wechat_thirdparty_authorizer_info' => 'Wechat Thirdparty Authorizer Info',
			'wechat_thirdparty_authorizer_refresh_token' => 'Wechat Thirdparty Authorizer Refresh Token',
			'wechat_thirdparty_authorizer_appid' => 'Wechat Thirdparty Authorizer Appid',
			'wechat_thirdparty_authorizer_if_auth' => 'Wechat Thirdparty Authorizer If Auth',
			'wechat_thirdparty_authorization_info' => 'Wechat Thirdparty Authorization Info',
			'wechat_thirdparty_authorizer_time' => 'Wechat Thirdparty Authorizer Time',
			'wechat_thirdparty_authorizer_cancel_time' => 'Wechat Thirdparty Authorizer Cancel Time',
			'wechat_thirdparty_authorizer_refresh_time' => 'Wechat Thirdparty Authorizer Refresh Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('merchant_no',$this->merchant_no,true);
		$criteria->compare('wechat_merchant_no',$this->wechat_merchant_no,true);
		$criteria->compare('wq_mchid',$this->wq_mchid,true);
		$criteria->compare('agent_id',$this->agent_id);
		$criteria->compare('channel_id',$this->channel_id,true);
		$criteria->compare('account',$this->account,true);
		$criteria->compare('pwd',$this->pwd,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('wq_m_short_name',$this->wq_m_short_name,true);
		$criteria->compare('wq_m_type',$this->wq_m_type);
		$criteria->compare('wq_m_name',$this->wq_m_name,true);
		$criteria->compare('wq_m_industry',$this->wq_m_industry,true);
		$criteria->compare('wq_m_address',$this->wq_m_address,true);
		$criteria->compare('wq_m_business_license_no',$this->wq_m_business_license_no,true);
		$criteria->compare('wq_m_business_license',$this->wq_m_business_license,true);
		$criteria->compare('wq_m_organization_code',$this->wq_m_organization_code,true);
		$criteria->compare('wq_m_organization',$this->wq_m_organization,true);
		$criteria->compare('wq_m_legal_person_name',$this->wq_m_legal_person_name,true);
		$criteria->compare('wq_m_legal_person_id',$this->wq_m_legal_person_id,true);
		$criteria->compare('wq_m_legal_person_positive',$this->wq_m_legal_person_positive,true);
		$criteria->compare('wq_m_legal_person_opposite',$this->wq_m_legal_person_opposite,true);
		$criteria->compare('wq_m_contacts_name',$this->wq_m_contacts_name,true);
		$criteria->compare('wq_m_contacts_phone',$this->wq_m_contacts_phone,true);
		$criteria->compare('wq_m_verify_status',$this->wq_m_verify_status);
		$criteria->compare('wq_m_verify_pass_time',$this->wq_m_verify_pass_time,true);
		$criteria->compare('wq_m_reject_remark',$this->wq_m_reject_remark,true);
		$criteria->compare('wx_name',$this->wx_name,true);
		$criteria->compare('seller_email',$this->seller_email,true);
		$criteria->compare('key',$this->key,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('alipay_code',$this->alipay_code,true);
		$criteria->compare('verify_status',$this->verify_status);
		$criteria->compare('wechat_verify_status',$this->wechat_verify_status);
		$criteria->compare('wechat_verify_status_submit_time',$this->wechat_verify_status_submit_time,true);
		$criteria->compare('wechat_verify_status_auditpass_time',$this->wechat_verify_status_auditpass_time,true);
		$criteria->compare('wechat_verify_status_verify_time',$this->wechat_verify_status_verify_time,true);
		$criteria->compare('wechat_verify_status_sign_time',$this->wechat_verify_status_sign_time,true);
		$criteria->compare('wechat_verify_status_reject_time',$this->wechat_verify_status_reject_time,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('msg_num',$this->msg_num);
		$criteria->compare('if_stored',$this->if_stored);
		$criteria->compare('points_rule',$this->points_rule);
		$criteria->compare('merchant_number',$this->merchant_number,true);
		$criteria->compare('gj_start_time',$this->gj_start_time,true);
		$criteria->compare('gj_end_time',$this->gj_end_time,true);
		$criteria->compare('gj_product_id',$this->gj_product_id);
		$criteria->compare('if_tryout',$this->if_tryout);
		$criteria->compare('tryout_status',$this->tryout_status);
		$criteria->compare('gj_open_status',$this->gj_open_status);
		$criteria->compare('encrypt_id',$this->encrypt_id,true);
		$criteria->compare('wechat_id',$this->wechat_id,true);
		$criteria->compare('wechat',$this->wechat,true);
		$criteria->compare('wechat_type',$this->wechat_type);
		$criteria->compare('wechat_qrcode',$this->wechat_qrcode,true);
		$criteria->compare('wechat_account',$this->wechat_account,true);
		$criteria->compare('wechat_appsecret',$this->wechat_appsecret,true);
		$criteria->compare('wechat_subscription_appsecret',$this->wechat_subscription_appsecret,true);
		$criteria->compare('wechat_key',$this->wechat_key,true);
		$criteria->compare('wechat_mchid',$this->wechat_mchid,true);
		$criteria->compare('t_wx_appid',$this->t_wx_appid,true);
		$criteria->compare('wechat_interface_url',$this->wechat_interface_url,true);
		$criteria->compare('wechat_token',$this->wechat_token,true);
		$criteria->compare('wechat_encodingaeskey',$this->wechat_encodingaeskey,true);
		$criteria->compare('wechat_encrypt_type',$this->wechat_encrypt_type);
		$criteria->compare('operator_refund_time',$this->operator_refund_time);
		$criteria->compare('wechat_apiclient_key',$this->wechat_apiclient_key,true);
		$criteria->compare('wechat_appid',$this->wechat_appid,true);
		$criteria->compare('wechat_subscription_appid',$this->wechat_subscription_appid,true);
		$criteria->compare('dzoperator_refund_time',$this->dzoperator_refund_time);
		$criteria->compare('access_token_json',$this->access_token_json,true);
		$criteria->compare('jsapi_ticket_json',$this->jsapi_ticket_json,true);
		$criteria->compare('fuwu_name',$this->fuwu_name,true);
		$criteria->compare('wechat_name',$this->wechat_name,true);
		$criteria->compare('ums_3des_key',$this->ums_3des_key,true);
		$criteria->compare('api_key',$this->api_key,true);
		$criteria->compare('mchid',$this->mchid,true);
		$criteria->compare('ums_mchid',$this->ums_mchid,true);
		$criteria->compare('alipay_qrcode',$this->alipay_qrcode,true);
		$criteria->compare('if_wx_open',$this->if_wx_open);
		$criteria->compare('wxpay_merchant_type',$this->wxpay_merchant_type);
		$criteria->compare('wechat_apiclient_cert',$this->wechat_apiclient_cert,true);
		$criteria->compare('t_wx_mchid',$this->t_wx_mchid,true);
		$criteria->compare('if_alipay_open',$this->if_alipay_open);
		$criteria->compare('alipay_api_version',$this->alipay_api_version);
		$criteria->compare('partner',$this->partner,true);
		$criteria->compare('alipay_key',$this->alipay_key,true);
		$criteria->compare('appid',$this->appid,true);
		$criteria->compare('alipay_auth_pid',$this->alipay_auth_pid,true);
		$criteria->compare('alipay_auth_appid',$this->alipay_auth_appid,true);
		$criteria->compare('alipay_auth_token',$this->alipay_auth_token,true);
		$criteria->compare('alipay_auth_refresh_token',$this->alipay_auth_refresh_token,true);
		$criteria->compare('alipay_auth_time',$this->alipay_auth_time,true);
		$criteria->compare('alipay_auth_token_expires_in',$this->alipay_auth_token_expires_in,true);
		$criteria->compare('alipay_auth_refresh_token_expires_in',$this->alipay_auth_refresh_token_expires_in,true);
		$criteria->compare('wechat_template_id',$this->wechat_template_id,true);
		$criteria->compare('wechat_thirdparty_authorizer_info',$this->wechat_thirdparty_authorizer_info,true);
		$criteria->compare('wechat_thirdparty_authorizer_refresh_token',$this->wechat_thirdparty_authorizer_refresh_token,true);
		$criteria->compare('wechat_thirdparty_authorizer_appid',$this->wechat_thirdparty_authorizer_appid,true);
		$criteria->compare('wechat_thirdparty_authorizer_if_auth',$this->wechat_thirdparty_authorizer_if_auth);
		$criteria->compare('wechat_thirdparty_authorization_info',$this->wechat_thirdparty_authorization_info,true);
		$criteria->compare('wechat_thirdparty_authorizer_time',$this->wechat_thirdparty_authorizer_time,true);
		$criteria->compare('wechat_thirdparty_authorizer_cancel_time',$this->wechat_thirdparty_authorizer_cancel_time,true);
		$criteria->compare('wechat_thirdparty_authorizer_refresh_time',$this->wechat_thirdparty_authorizer_refresh_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Merchant the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
