<?php

/**
 * This is the model class for table "{{stored_order}}".
 *
 * The followings are the available columns in table '{{stored_order}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $store_id
 * @property integer $operator_id
 * @property integer $stored_id
 * @property string $order_no
 * @property string $trade_no
 * @property integer $pay_channel
 * @property integer $pay_status
 * @property integer $order_status
 * @property string $pay_time
 * @property string $cancel_time
 * @property integer $flag
 * @property string $refund_time
 * @property string $create_time
 * @property string $last_time
 * @property integer $num
 * @property string $user_code
 * @property string $alipay_account
 * @property integer $terminal_type
 * @property string $terminal_id
 * @property integer $refund_terminal_type
 * @property string $refund_terminal_id
 * @property double $merchant_discount_money
 * @property double $alipay_discount_money
 */
class StoredOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{stored_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, store_id, operator_id, stored_id, pay_channel, pay_status, order_status, flag, num, terminal_type, refund_terminal_type', 'numerical', 'integerOnly'=>true),
			array('merchant_discount_money, alipay_discount_money', 'numerical'),
			array('order_no, alipay_account', 'length', 'max'=>32),
			array('trade_no, terminal_id, refund_terminal_id', 'length', 'max'=>50),
			array('user_code', 'length', 'max'=>20),
			array('pay_time, cancel_time, refund_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, store_id, operator_id, stored_id, order_no, trade_no, pay_channel, pay_status, order_status, pay_time, cancel_time, flag, refund_time, create_time, last_time, num, user_code, alipay_account, terminal_type, terminal_id, refund_terminal_type, refund_terminal_id, merchant_discount_money, alipay_discount_money', 'safe', 'on'=>'search'),
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
				'user' => array(self::BELONGS_TO,'User','user_id'),
				'stored' => array(self::BELONGS_TO,'Stored','stored_id'),
				'operator' => array(self::BELONGS_TO,'Operator','operator_id'),
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
			'store_id' => 'Store',
			'operator_id' => 'Operator',
			'stored_id' => 'Stored',
			'order_no' => 'Order No',
			'trade_no' => 'Trade No',
			'pay_channel' => 'Pay Channel',
			'pay_status' => 'Pay Status',
			'order_status' => 'Order Status',
			'pay_time' => 'Pay Time',
			'cancel_time' => 'Cancel Time',
			'flag' => 'Flag',
			'refund_time' => 'Refund Time',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'num' => 'Num',
			'user_code' => 'User Code',
			'alipay_account' => 'Alipay Account',
			'terminal_type' => 'Terminal Type',
			'terminal_id' => 'Terminal',
			'refund_terminal_type' => 'Refund Terminal Type',
			'refund_terminal_id' => 'Refund Terminal',
			'merchant_discount_money' => 'Merchant Discount Money',
			'alipay_discount_money' => 'Alipay Discount Money',
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
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('operator_id',$this->operator_id);
		$criteria->compare('stored_id',$this->stored_id);
		$criteria->compare('order_no',$this->order_no,true);
		$criteria->compare('trade_no',$this->trade_no,true);
		$criteria->compare('pay_channel',$this->pay_channel);
		$criteria->compare('pay_status',$this->pay_status);
		$criteria->compare('order_status',$this->order_status);
		$criteria->compare('pay_time',$this->pay_time,true);
		$criteria->compare('cancel_time',$this->cancel_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('refund_time',$this->refund_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('num',$this->num);
		$criteria->compare('user_code',$this->user_code,true);
		$criteria->compare('alipay_account',$this->alipay_account,true);
		$criteria->compare('terminal_type',$this->terminal_type);
		$criteria->compare('terminal_id',$this->terminal_id,true);
		$criteria->compare('refund_terminal_type',$this->refund_terminal_type);
		$criteria->compare('refund_terminal_id',$this->refund_terminal_id,true);
		$criteria->compare('merchant_discount_money',$this->merchant_discount_money);
		$criteria->compare('alipay_discount_money',$this->alipay_discount_money);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return StoredOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
