<?php

/**
 * This is the model class for table "{{user_coupons}}".
 *
 * The followings are the available columns in table '{{user_coupons}}':
 * @property string $id
 * @property integer $user_id
 * @property integer $coupons_id
 * @property integer $status
 * @property double $money
 * @property string $start_time
 * @property string $end_time
 * @property string $use_time
 * @property string $get_openid
 * @property string $give_openid
 * @property string $wechat_coupons_id
 * @property integer $if_give
 * @property string $code
 * @property string $before_code
 * @property string $scene
 * @property integer $flag
 * @property integer $order_id
 * @property string $create_time
 * @property string $last_time
 * @property integer $if_wechat
 * @property integer $marketing_activity_type
 * @property integer $marketing_activity_id
 */
class UserCoupons extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_coupons}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, coupons_id, status, if_give, flag, order_id, if_wechat, marketing_activity_type, marketing_activity_id', 'numerical', 'integerOnly'=>true),
			array('money', 'numerical'),
			array('get_openid, give_openid, wechat_coupons_id, code, before_code, scene', 'length', 'max'=>32),
			array('start_time, end_time, use_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, coupons_id, status, money, start_time, end_time, use_time, get_openid, give_openid, wechat_coupons_id, if_give, code, before_code, scene, flag, order_id, create_time, last_time, if_wechat, marketing_activity_type, marketing_activity_id', 'safe', 'on'=>'search'),
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
		    'user'=>array(self::BELONGS_TO,'User','user_id'),
		    'coupons'=>array(self::BELONGS_TO,'Coupons','coupons_id'),
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
			'coupons_id' => 'Coupons',
			'status' => 'Status',
			'money' => 'Money',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'use_time' => 'Use Time',
			'get_openid' => 'Get Openid',
			'give_openid' => 'Give Openid',
			'wechat_coupons_id' => 'Wechat Coupons',
			'if_give' => 'If Give',
			'code' => 'Code',
			'before_code' => 'Before Code',
			'scene' => 'Scene',
			'flag' => 'Flag',
			'order_id' => 'Order',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'if_wechat' => 'If Wechat',
			'marketing_activity_type' => 'Marketing Activity Type',
			'marketing_activity_id' => 'Marketing Activity',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('coupons_id',$this->coupons_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('money',$this->money);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('use_time',$this->use_time,true);
		$criteria->compare('get_openid',$this->get_openid,true);
		$criteria->compare('give_openid',$this->give_openid,true);
		$criteria->compare('wechat_coupons_id',$this->wechat_coupons_id,true);
		$criteria->compare('if_give',$this->if_give);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('before_code',$this->before_code,true);
		$criteria->compare('scene',$this->scene,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('if_wechat',$this->if_wechat);
		$criteria->compare('marketing_activity_type',$this->marketing_activity_type);
		$criteria->compare('marketing_activity_id',$this->marketing_activity_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserCoupons the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
