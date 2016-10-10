<?php

/**
 * This is the model class for table "{{gj_order}}".
 *
 * The followings are the available columns in table '{{gj_order}}':
 * @property integer $id
 * @property string $order_no
 * @property integer $agent_id
 * @property integer $merchant_id
 * @property integer $wq_product_id
 * @property string $trade_no
 * @property integer $points_pay
 * @property integer $pay_channel
 * @property double $pay_money
 * @property integer $pay_status
 * @property integer $order_status
 * @property string $pay_time
 * @property string $cancel_time
 * @property integer $flag
 * @property string $remark
 * @property string $invite_code
 * @property string $code_use_time
 * @property integer $code_merchant_id
 * @property string $create_time
 * @property string $last_time
 * @property integer $time_limit
 * @property integer $order_type
 * @property integer $if_tryout
 */
class GjOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{gj_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('agent_id, merchant_id, wq_product_id, points_pay, pay_channel, pay_status, order_status, flag, code_merchant_id, time_limit, order_type, if_tryout', 'numerical', 'integerOnly'=>true),
			array('pay_money', 'numerical'),
			array('order_no', 'length', 'max'=>20),
			array('trade_no, invite_code', 'length', 'max'=>50),
			array('remark', 'length', 'max'=>255),
			array('pay_time, cancel_time, code_use_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_no, agent_id, merchant_id, wq_product_id, trade_no, points_pay, pay_channel, pay_money, pay_status, order_status, pay_time, cancel_time, flag, remark, invite_code, code_use_time, code_merchant_id, create_time, last_time, time_limit, order_type, if_tryout', 'safe', 'on'=>'search'),
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
				'gjproduct' => array(self::BELONGS_TO,'GjProduct','wq_product_id'),
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
			'agent_id' => 'Agent',
			'merchant_id' => 'Merchant',
			'wq_product_id' => 'Wq Product',
			'trade_no' => 'Trade No',
			'points_pay' => 'Points Pay',
			'pay_channel' => 'Pay Channel',
			'pay_money' => 'Pay Money',
			'pay_status' => 'Pay Status',
			'order_status' => 'Order Status',
			'pay_time' => 'Pay Time',
			'cancel_time' => 'Cancel Time',
			'flag' => 'Flag',
			'remark' => 'Remark',
			'invite_code' => 'Invite Code',
			'code_use_time' => 'Code Use Time',
			'code_merchant_id' => 'Code Merchant',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'time_limit' => 'Time Limit',
			'order_type' => 'Order Type',
			'if_tryout' => 'If Tryout',
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
		$criteria->compare('agent_id',$this->agent_id);
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('wq_product_id',$this->wq_product_id);
		$criteria->compare('trade_no',$this->trade_no,true);
		$criteria->compare('points_pay',$this->points_pay);
		$criteria->compare('pay_channel',$this->pay_channel);
		$criteria->compare('pay_money',$this->pay_money);
		$criteria->compare('pay_status',$this->pay_status);
		$criteria->compare('order_status',$this->order_status);
		$criteria->compare('pay_time',$this->pay_time,true);
		$criteria->compare('cancel_time',$this->cancel_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('invite_code',$this->invite_code,true);
		$criteria->compare('code_use_time',$this->code_use_time,true);
		$criteria->compare('code_merchant_id',$this->code_merchant_id);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('time_limit',$this->time_limit);
		$criteria->compare('order_type',$this->order_type);
		$criteria->compare('if_tryout',$this->if_tryout);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GjOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
