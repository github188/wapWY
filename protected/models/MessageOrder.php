<?php

/**
 * This is the model class for table "{{message_order}}".
 *
 * The followings are the available columns in table '{{message_order}}':
 * @property integer $id
 * @property string $order_no
 * @property integer $merchant_id
 * @property integer $message_num
 * @property double $price
 * @property integer $pay_status
 * @property double $pay_money
 * @property integer $pay_channel
 * @property string $trade_no
 * @property string $pay_time
 * @property string $create_time
 * @property string $cancel_time
 */
class MessageOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{message_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, message_num, pay_status, pay_channel', 'numerical', 'integerOnly'=>true),
			array('price, pay_money', 'numerical'),
			array('order_no', 'length', 'max'=>20),
			array('trade_no', 'length', 'max'=>50),
			array('pay_time, create_time, cancel_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_no, merchant_id, message_num, price, pay_status, pay_money, pay_channel, trade_no, pay_time, create_time, cancel_time', 'safe', 'on'=>'search'),
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
			'order_no' => 'Order No',
			'merchant_id' => 'Merchant',
			'message_num' => 'Message Num',
			'price' => 'Price',
			'pay_status' => 'Pay Status',
			'pay_money' => 'Pay Money',
			'pay_channel' => 'Pay Channel',
			'trade_no' => 'Trade No',
			'pay_time' => 'Pay Time',
			'create_time' => 'Create Time',
			'cancel_time' => 'Cancel Time',
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
		$criteria->compare('order_no',$this->order_no,true);
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('message_num',$this->message_num);
		$criteria->compare('price',$this->price);
		$criteria->compare('pay_status',$this->pay_status);
		$criteria->compare('pay_money',$this->pay_money);
		$criteria->compare('pay_channel',$this->pay_channel);
		$criteria->compare('trade_no',$this->trade_no,true);
		$criteria->compare('pay_time',$this->pay_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('cancel_time',$this->cancel_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MessageOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}
