<?php

/**
 * This is the model class for table "{{management}}".
 *
 * The followings are the available columns in table '{{management}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $p_mid
 * @property integer $if_wx_open
 * @property integer $wx_merchant_type
 * @property integer $wx_use_pro
 * @property string $wx_apiclient_cert
 * @property string $wx_apiclient_key
 * @property string $wx_appid
 * @property string $wx_appsecret
 * @property string $wx_api
 * @property string $wx_mchid
 * @property string $t_wx_appid
 * @property string $t_wx_mchid
 * @property integer $if_alipay_open
 * @property integer $alipay_api_version
 * @property integer $alipay_use_pro
 * @property string $alipay_pid
 * @property string $alipay_key
 * @property string $alipay_appid
 * @property string $name
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class Management extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{management}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, p_mid, if_wx_open, wx_merchant_type, wx_use_pro, if_alipay_open, alipay_api_version, alipay_use_pro, flag', 'numerical', 'integerOnly'=>true),
			array('wx_apiclient_cert, wx_apiclient_key', 'length', 'max'=>150),
			array('wx_appid, wx_appsecret, wx_api, wx_mchid, t_wx_appid, t_wx_mchid, alipay_pid, alipay_key, alipay_appid', 'length', 'max'=>50),
			array('name', 'length', 'max'=>100),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, p_mid, if_wx_open, wx_merchant_type, wx_use_pro, wx_apiclient_cert, wx_apiclient_key, wx_appid, wx_appsecret, wx_api, wx_mchid, t_wx_appid, t_wx_mchid, if_alipay_open, alipay_api_version, alipay_use_pro, alipay_pid, alipay_key, alipay_appid, name, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
				'merchant' => array(self::BELONGS_TO,'Merchant','merchant_id'),
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
			'p_mid' => 'P Mid',
			'if_wx_open' => 'If Wx Open',
			'wx_merchant_type' => 'Wx Merchant Type',
			'wx_use_pro' => 'Wx Use Pro',
			'wx_apiclient_cert' => 'Wx Apiclient Cert',
			'wx_apiclient_key' => 'Wx Apiclient Key',
			'wx_appid' => 'Wx Appid',
			'wx_appsecret' => 'Wx Appsecret',
			'wx_api' => 'Wx Api',
			'wx_mchid' => 'Wx Mchid',
			't_wx_appid' => 'T Wx Appid',
			't_wx_mchid' => 'T Wx Mchid',
			'if_alipay_open' => 'If Alipay Open',
			'alipay_api_version' => 'Alipay Api Version',
			'alipay_use_pro' => 'Alipay Use Pro',
			'alipay_pid' => 'Alipay Pid',
			'alipay_key' => 'Alipay Key',
			'alipay_appid' => 'Alipay Appid',
			'name' => 'Name',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'flag' => 'Flag',
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
		$criteria->compare('p_mid',$this->p_mid);
		$criteria->compare('if_wx_open',$this->if_wx_open);
		$criteria->compare('wx_merchant_type',$this->wx_merchant_type);
		$criteria->compare('wx_use_pro',$this->wx_use_pro);
		$criteria->compare('wx_apiclient_cert',$this->wx_apiclient_cert,true);
		$criteria->compare('wx_apiclient_key',$this->wx_apiclient_key,true);
		$criteria->compare('wx_appid',$this->wx_appid,true);
		$criteria->compare('wx_appsecret',$this->wx_appsecret,true);
		$criteria->compare('wx_api',$this->wx_api,true);
		$criteria->compare('wx_mchid',$this->wx_mchid,true);
		$criteria->compare('t_wx_appid',$this->t_wx_appid,true);
		$criteria->compare('t_wx_mchid',$this->t_wx_mchid,true);
		$criteria->compare('if_alipay_open',$this->if_alipay_open);
		$criteria->compare('alipay_api_version',$this->alipay_api_version);
		$criteria->compare('alipay_use_pro',$this->alipay_use_pro);
		$criteria->compare('alipay_pid',$this->alipay_pid,true);
		$criteria->compare('alipay_key',$this->alipay_key,true);
		$criteria->compare('alipay_appid',$this->alipay_appid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('flag',$this->flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Management the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
