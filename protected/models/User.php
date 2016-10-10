<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $type
 * @property string $account
 * @property string $pwd
 * @property string $avatar
 * @property string $nickname
 * @property string $name
 * @property integer $sex
 * @property string $birthday
 * @property string $social_security_number
 * @property string $email
 * @property integer $marital_status
 * @property string $work
 * @property string $country
 * @property string $province
 * @property string $province_code
 * @property string $city
 * @property string $city_code
 * @property double $free_secret
 * @property double $money
 * @property integer $points
 * @property integer $membershipgrade_id
 * @property string $membership_card_no
 * @property string $login_time
 * @property string $login_ip
 * @property integer $login_client
 * @property string $regist_time
 * @property string $from
 * @property string $address
 * @property string $alipay_fuwu_id
 * @property integer $alipay_status
 * @property string $alipay_avatar
 * @property string $alipay_nickname
 * @property string $alipay_province
 * @property string $alipay_city
 * @property string $alipay_gender
 * @property integer $alipay_user_type_value
 * @property string $alipay_is_licence_auth
 * @property string $alipay_is_certified
 * @property string $alipay_certified_grade_a
 * @property string $alipay_is_student_certified
 * @property string $alipay_is_bank_auth
 * @property string $alipay_is_mobile_auth
 * @property string $alipay_user_status
 * @property string $alipay_is_id_auth
 * @property string $alipay_logon_id
 * @property string $alipay_user_name
 * @property string $alipay_subscribe_time
 * @property string $alipay_cancel_subscribe_time
 * @property integer $alipay_subscribe_store_id
 * @property string $register_address
 * @property integer $wechat_status
 * @property string $wechat_id
 * @property string $wechat_nickname
 * @property integer $wechat_sex
 * @property string $wechat_country
 * @property string $wechat_province
 * @property string $wechat_city
 * @property string $wechat_language
 * @property string $wechat_headimgurl
 * @property string $wechat_unionid
 * @property string $wechat_remark
 * @property string $wechat_groupid
 * @property string $wechat_subscribe_time
 * @property string $wechat_cancel_subscribe_time
 * @property integer $wechat_subscribe_store_id
 * @property integer $switch
 * @property string $last_time
 * @property string $create_time
 * @property integer $flag
 * @property integer $if_perfect
 */
class User extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, type, sex, marital_status, points, membershipgrade_id, login_client, alipay_status, alipay_user_type_value, alipay_subscribe_store_id, wechat_status, wechat_sex, wechat_subscribe_store_id, switch, flag', 'numerical', 'integerOnly'=>true),
			array('free_secret, money', 'numerical'),
			array('account', 'length', 'max'=>20),
			array('pwd, nickname, name, social_security_number, email, work, country, province, province_code, city, city_code, membership_card_no, login_ip, from, alipay_fuwu_id, alipay_province, alipay_city, alipay_logon_id, alipay_user_name, register_address, wechat_id, wechat_country, wechat_province, wechat_city, wechat_language, wechat_unionid, wechat_groupid', 'length', 'max'=>32),
			array('avatar, alipay_avatar, alipay_nickname, wechat_headimgurl, wechat_remark', 'length', 'max'=>255),
			array('address, wechat_nickname', 'length', 'max'=>100),
			array('alipay_gender, alipay_is_licence_auth, alipay_is_certified, alipay_certified_grade_a, alipay_is_student_certified, alipay_is_bank_auth, alipay_is_mobile_auth, alipay_user_status, alipay_is_id_auth', 'length', 'max'=>3),
			array('birthday, login_time, regist_time, alipay_subscribe_time, alipay_cancel_subscribe_time, wechat_subscribe_time, wechat_cancel_subscribe_time, last_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, type, account, pwd, avatar, nickname, name, sex, birthday, social_security_number, email, marital_status, work, country, province, province_code, city, city_code, free_secret, money, points, membershipgrade_id, membership_card_no, login_time, login_ip, login_client, regist_time, from, address, alipay_fuwu_id, alipay_status, alipay_avatar, alipay_nickname, alipay_province, alipay_city, alipay_gender, alipay_user_type_value, alipay_is_licence_auth, alipay_is_certified, alipay_certified_grade_a, alipay_is_student_certified, alipay_is_bank_auth, alipay_is_mobile_auth, alipay_user_status, alipay_is_id_auth, alipay_logon_id, alipay_user_name, alipay_subscribe_time, alipay_cancel_subscribe_time, alipay_subscribe_store_id, register_address, wechat_status, wechat_id, wechat_nickname, wechat_sex, wechat_country, wechat_province, wechat_city, wechat_language, wechat_headimgurl, wechat_unionid, wechat_remark, wechat_groupid, wechat_subscribe_time, wechat_cancel_subscribe_time, wechat_subscribe_store_id, switch, last_time, create_time, flag', 'safe', 'on'=>'search'),
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
				'grade' => array(self::BELONGS_TO, 'UserGrade', 'membershipgrade_id'),
		        'group' => array(self::HAS_MANY, 'Group', 'user_id'),
		        'order' => array(self::HAS_MANY, 'Order', 'user_id', 'order'=>'order.pay_time desc'),
		        'score' => array(self::HAS_MANY, 'UserPointsdetail', 'user_id'),
		        'tag'   => array(self::HAS_MANY, 'UserTag', 'user_id'),
		        'growupRecord' => array(self::HAS_MANY, 'UserGrowupRecord', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'merchant_id' => 'Merchant',
			'type' => 'Type',
			'account' => 'Account',
			'pwd' => 'Pwd',
			'avatar' => 'Avatar',
			'nickname' => 'Nickname',
			'name' => 'Name',
			'sex' => 'Sex',
			'birthday' => 'Birthday',
			'social_security_number' => 'Social Security Number',
			'email' => 'Email',
			'marital_status' => 'Marital Status',
			'work' => 'Work',
			'country' => 'Country',
			'province' => 'Province',
			'province_code' => 'Province Code',
			'city' => 'City',
			'city_code' => 'City Code',
			'free_secret' => 'Free Secret',
			'money' => 'Money',
			'points' => 'Points',
			'membershipgrade_id' => 'Membershipgrade',
			'membership_card_no' => 'Membership Card No',
			'login_time' => 'Login Time',
			'login_ip' => 'Login Ip',
			'login_client' => 'Login Client',
			'regist_time' => 'Regist Time',
			'from' => 'From',
			'address' => 'Address',
			'alipay_fuwu_id' => 'Alipay Fuwu',
			'alipay_status' => 'Alipay Status',
			'alipay_avatar' => 'Alipay Avatar',
			'alipay_nickname' => 'Alipay Nickname',
			'alipay_province' => 'Alipay Province',
			'alipay_city' => 'Alipay City',
			'alipay_gender' => 'Alipay Gender',
			'alipay_user_type_value' => 'Alipay User Type Value',
			'alipay_is_licence_auth' => 'Alipay Is Licence Auth',
			'alipay_is_certified' => 'Alipay Is Certified',
			'alipay_certified_grade_a' => 'Alipay Certified Grade A',
			'alipay_is_student_certified' => 'Alipay Is Student Certified',
			'alipay_is_bank_auth' => 'Alipay Is Bank Auth',
			'alipay_is_mobile_auth' => 'Alipay Is Mobile Auth',
			'alipay_user_status' => 'Alipay User Status',
			'alipay_is_id_auth' => 'Alipay Is Id Auth',
			'alipay_logon_id' => 'Alipay Logon',
			'alipay_user_name' => 'Alipay User Name',
			'alipay_subscribe_time' => 'Alipay Subscribe Time',
			'alipay_cancel_subscribe_time' => 'Alipay Cancel Subscribe Time',
			'alipay_subscribe_store_id' => 'Alipay Subscribe Store',
			'register_address' => 'Register Address',
			'wechat_status' => 'Wechat Status',
			'wechat_id' => 'Wechat',
			'wechat_nickname' => 'Wechat Nickname',
			'wechat_sex' => 'Wechat Sex',
			'wechat_country' => 'Wechat Country',
			'wechat_province' => 'Wechat Province',
			'wechat_city' => 'Wechat City',
			'wechat_language' => 'Wechat Language',
			'wechat_headimgurl' => 'Wechat Headimgurl',
			'wechat_unionid' => 'Wechat Unionid',
			'wechat_remark' => 'Wechat Remark',
			'wechat_groupid' => 'Wechat Groupid',
			'wechat_subscribe_time' => 'Wechat Subscribe Time',
			'wechat_cancel_subscribe_time' => 'Wechat Cancel Subscribe Time',
			'wechat_subscribe_store_id' => 'Wechat Subscribe Store',
			'switch' => 'Switch',
			'last_time' => 'Last Time',
			'create_time' => 'Create Time',
			'flag' => 'Flag',
			'if_perfect' => 'If perfect'
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
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('account',$this->account,true);
		$criteria->compare('pwd',$this->pwd,true);
		$criteria->compare('avatar',$this->avatar,true);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('social_security_number',$this->social_security_number,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('marital_status',$this->marital_status);
		$criteria->compare('work',$this->work,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('province',$this->province,true);
		$criteria->compare('province_code',$this->province_code,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('free_secret',$this->free_secret);
		$criteria->compare('money',$this->money);
		$criteria->compare('points',$this->points);
		$criteria->compare('membershipgrade_id',$this->membershipgrade_id);
		$criteria->compare('membership_card_no',$this->membership_card_no,true);
		$criteria->compare('login_time',$this->login_time,true);
		$criteria->compare('login_ip',$this->login_ip,true);
		$criteria->compare('login_client',$this->login_client);
		$criteria->compare('regist_time',$this->regist_time,true);
		$criteria->compare('from',$this->from,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('alipay_fuwu_id',$this->alipay_fuwu_id,true);
		$criteria->compare('alipay_status',$this->alipay_status);
		$criteria->compare('alipay_avatar',$this->alipay_avatar,true);
		$criteria->compare('alipay_nickname',$this->alipay_nickname,true);
		$criteria->compare('alipay_province',$this->alipay_province,true);
		$criteria->compare('alipay_city',$this->alipay_city,true);
		$criteria->compare('alipay_gender',$this->alipay_gender,true);
		$criteria->compare('alipay_user_type_value',$this->alipay_user_type_value);
		$criteria->compare('alipay_is_licence_auth',$this->alipay_is_licence_auth,true);
		$criteria->compare('alipay_is_certified',$this->alipay_is_certified,true);
		$criteria->compare('alipay_certified_grade_a',$this->alipay_certified_grade_a,true);
		$criteria->compare('alipay_is_student_certified',$this->alipay_is_student_certified,true);
		$criteria->compare('alipay_is_bank_auth',$this->alipay_is_bank_auth,true);
		$criteria->compare('alipay_is_mobile_auth',$this->alipay_is_mobile_auth,true);
		$criteria->compare('alipay_user_status',$this->alipay_user_status,true);
		$criteria->compare('alipay_is_id_auth',$this->alipay_is_id_auth,true);
		$criteria->compare('alipay_logon_id',$this->alipay_logon_id,true);
		$criteria->compare('alipay_user_name',$this->alipay_user_name,true);
		$criteria->compare('alipay_subscribe_time',$this->alipay_subscribe_time,true);
		$criteria->compare('alipay_cancel_subscribe_time',$this->alipay_cancel_subscribe_time,true);
		$criteria->compare('alipay_subscribe_store_id',$this->alipay_subscribe_store_id);
		$criteria->compare('register_address',$this->register_address,true);
		$criteria->compare('wechat_status',$this->wechat_status);
		$criteria->compare('wechat_id',$this->wechat_id,true);
		$criteria->compare('wechat_nickname',$this->wechat_nickname,true);
		$criteria->compare('wechat_sex',$this->wechat_sex);
		$criteria->compare('wechat_country',$this->wechat_country,true);
		$criteria->compare('wechat_province',$this->wechat_province,true);
		$criteria->compare('wechat_city',$this->wechat_city,true);
		$criteria->compare('wechat_language',$this->wechat_language,true);
		$criteria->compare('wechat_headimgurl',$this->wechat_headimgurl,true);
		$criteria->compare('wechat_unionid',$this->wechat_unionid,true);
		$criteria->compare('wechat_remark',$this->wechat_remark,true);
		$criteria->compare('wechat_groupid',$this->wechat_groupid,true);
		$criteria->compare('wechat_subscribe_time',$this->wechat_subscribe_time,true);
		$criteria->compare('wechat_cancel_subscribe_time',$this->wechat_cancel_subscribe_time,true);
		$criteria->compare('wechat_subscribe_store_id',$this->wechat_subscribe_store_id);
		$criteria->compare('switch',$this->switch);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('if_perfect',$this->if_perfect);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
