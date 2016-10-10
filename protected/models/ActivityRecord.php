<?php

/**
 * This is the model class for table "{{activity_record}}".
 *
 * The followings are the available columns in table '{{activity_record}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $activity_id
 * @property integer $merchant_id
 * @property integer $prize_type
 * @property integer $status
 * @property string $user_name
 * @property string $user_phone
 * @property string $user_address
 * @property string $wechat_openid
 * @property string $alipay_openid
 * @property string $create_time
 * @property integer $flag
 * @property string $last_time
 */
class ActivityRecord extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{activity_record}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, activity_id, merchant_id, prize_type, status, flag', 'numerical', 'integerOnly'=>true),
			array('user_name, user_phone', 'length', 'max'=>32),
			array('user_address', 'length', 'max'=>225),
			array('wechat_openid, alipay_openid', 'length', 'max'=>50),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, activity_id, merchant_id, prize_type, status, user_name, user_phone, user_address, wechat_openid, alipay_openid, create_time, flag, last_time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'activity_id' => 'Activity',
			'merchant_id' => 'Merchant',
			'prize_type' => 'Prize Type',
			'status' => 'Status',
			'user_name' => 'User Name',
			'user_phone' => 'User Phone',
			'user_address' => 'User Address',
			'wechat_openid' => 'Wechat Openid',
			'alipay_openid' => 'Alipay Openid',
			'create_time' => 'Create Time',
			'flag' => 'Flag',
			'last_time' => 'Last Time',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('activity_id',$this->activity_id);
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('prize_type',$this->prize_type);
		$criteria->compare('status',$this->status);
		$criteria->compare('user_name',$this->user_name,true);
		$criteria->compare('user_phone',$this->user_phone,true);
		$criteria->compare('user_address',$this->user_address,true);
		$criteria->compare('wechat_openid',$this->wechat_openid,true);
		$criteria->compare('alipay_openid',$this->alipay_openid,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('last_time',$this->last_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActivityRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
