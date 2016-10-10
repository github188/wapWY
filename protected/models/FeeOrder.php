<?php

/**
 * This is the model class for table "{{fee_order}}".
 *
 * The followings are the available columns in table '{{fee_order}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $community_id
 * @property integer $merchant_id
 * @property string $building_number
 * @property string $user_account
 * @property string $order_no
 * @property integer $order_type
 * @property string $date
 * @property double $peak
 * @property double $valley
 * @property double $electricity
 * @property double $water_ton
 * @property integer $parking_month_num
 * @property integer $property_fee_month_num
 * @property double $pay_money
 * @property double $order_money
 * @property integer $pay_status
 * @property string $trade_no
 * @property integer $order_status
 * @property string $pay_time
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class FeeOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{fee_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'required'),
			array('user_id, community_id, merchant_id, order_type, parking_month_num, property_fee_month_num, pay_status, order_status, flag', 'numerical', 'integerOnly'=>true),
			array('peak, valley, electricity, water_ton, pay_money, order_money', 'numerical'),
			array('building_number, user_account, order_no, trade_no', 'length', 'max'=>32),
			array('date, pay_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, community_id, merchant_id, building_number, user_account, order_no, order_type, date, peak, valley, electricity, water_ton, parking_month_num, property_fee_month_num, pay_money, order_money, pay_status, trade_no, order_status, pay_time, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'community_id' => 'Community',
			'merchant_id' => 'Merchant',
			'building_number' => 'Building Number',
			'user_account' => 'User Account',
			'order_no' => 'Order No',
			'order_type' => 'Order Type',
			'date' => 'Date',
			'peak' => 'Peak',
			'valley' => 'Valley',
			'electricity' => 'Electricity',
			'water_ton' => 'Water Ton',
			'parking_month_num' => 'Parking Month Num',
			'property_fee_month_num' => 'Property Fee Month Num',
			'pay_money' => 'Pay Money',
			'order_money' => 'Order Money',
			'pay_status' => 'Pay Status',
			'trade_no' => 'Trade No',
			'order_status' => 'Order Status',
			'pay_time' => 'Pay Time',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('community_id',$this->community_id);
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('building_number',$this->building_number,true);
		$criteria->compare('user_account',$this->user_account,true);
		$criteria->compare('order_no',$this->order_no,true);
		$criteria->compare('order_type',$this->order_type);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('peak',$this->peak);
		$criteria->compare('valley',$this->valley);
		$criteria->compare('electricity',$this->electricity);
		$criteria->compare('water_ton',$this->water_ton);
		$criteria->compare('parking_month_num',$this->parking_month_num);
		$criteria->compare('property_fee_month_num',$this->property_fee_month_num);
		$criteria->compare('pay_money',$this->pay_money);
		$criteria->compare('order_money',$this->order_money);
		$criteria->compare('pay_status',$this->pay_status);
		$criteria->compare('trade_no',$this->trade_no,true);
		$criteria->compare('order_status',$this->order_status);
		$criteria->compare('pay_time',$this->pay_time,true);
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
	 * @return FeeOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
