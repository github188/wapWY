<?php

/**
 * This is the model class for table "{{order}}".
 *
 * The followings are the available columns in table '{{order}}':
 * @property integer $id
 * @property integer $store_id
 * @property integer $merchant_id
 * @property integer $operator_id
 * @property integer $user_id
 * @property string $order_no
 * @property integer $order_type
 * @property string $trade_no
 * @property integer $pay_channel
 * @property double $stored_paymoney
 * @property double $online_paymoney
 * @property double $unionpay_paymoney
 * @property double $cash_paymoney
 * @property integer $pay_status
 * @property integer $order_status
 * @property integer $stored_confirm_status
 * @property string $pay_time
 * @property integer $flag
 * @property string $address
 * @property string $remark
 * @property string $send_time
 * @property string $receive_time
 * @property integer $print
 * @property integer $freight_print
 * @property string $cancel_time
 * @property string $create_time
 * @property string $last_time
 * @property integer $if_use_coupons
 * @property double $order_paymoney
 * @property double $freight_money
 * @property string $alipay_account
 * @property string $user_code
 * @property double $hongbao_money
 * @property double $coupons_money
 * @property double $discount_money
 * @property double $undiscount_paymoney
 * @property string $seller_remark
 * @property string $complete_time
 * @property string $ums_code
 * @property string $ums_card_no
 * @property integer $terminal_type
 * @property string $terminal_id
 * @property double $merchant_discount_money
 * @property double $alipay_discount_money
 * @property string $third_party_order_id
 * @property integer $pay_passageway
 * @property double $commission_ratio
 * @property string $thrid_party_code
 * @property string $wechat_user_id
 * @property string $alipay_user_id
 * @property string $wechat_user_p_id
 */
class Order extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, merchant_id, operator_id, user_id, order_type, pay_channel, pay_status, order_status, stored_confirm_status, flag, print, freight_print, if_use_coupons, terminal_type, pay_passageway', 'numerical', 'integerOnly'=>true),
			array('stored_paymoney, online_paymoney, unionpay_paymoney, cash_paymoney, order_paymoney, freight_money, hongbao_money, coupons_money, discount_money, undiscount_paymoney, merchant_discount_money, alipay_discount_money, commission_ratio', 'numerical'),
			array('order_no, alipay_account, ums_code, ums_card_no, third_party_order_id, thrid_party_code, wechat_user_id, alipay_user_id, wechat_user_p_id', 'length', 'max'=>32),
			array('trade_no, terminal_id', 'length', 'max'=>50),
			array('address, remark, seller_remark', 'length', 'max'=>255),
			array('user_code', 'length', 'max'=>20),
			array('pay_time, send_time, receive_time, cancel_time, create_time, last_time, complete_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, store_id, merchant_id, operator_id, user_id, order_no, order_type, trade_no, pay_channel, stored_paymoney, online_paymoney, unionpay_paymoney, cash_paymoney, pay_status, order_status, stored_confirm_status, pay_time, flag, address, remark, send_time, receive_time, print, freight_print, cancel_time, create_time, last_time, if_use_coupons, order_paymoney, freight_money, alipay_account, user_code, hongbao_money, coupons_money, discount_money, undiscount_paymoney, seller_remark, complete_time, ums_code, ums_card_no, terminal_type, terminal_id, merchant_discount_money, alipay_discount_money, third_party_order_id, pay_passageway, commission_ratio, thrid_party_code, wechat_user_id, alipay_user_id, wechat_user_p_id', 'safe', 'on'=>'search'),
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
				'operator'=>array(self::BELONGS_TO,'Operator','operator_id'),
				'store'=>array(self::BELONGS_TO,'Store','store_id'),
				'user'=>array(self::BELONGS_TO,'User','user_id'),
				'order_sku'=>array(self::HAS_MANY,'OrderSku','id'),
		        'merchant'=>array(self::BELONGS_TO,'Merchant','merchant_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'store_id' => 'Store',
			'merchant_id' => 'Merchant',
			'operator_id' => 'Operator',
			'user_id' => 'User',
			'order_no' => 'Order No',
			'order_type' => 'Order Type',
			'trade_no' => 'Trade No',
			'pay_channel' => 'Pay Channel',
			'stored_paymoney' => 'Stored Paymoney',
			'online_paymoney' => 'Online Paymoney',
			'unionpay_paymoney' => 'Unionpay Paymoney',
			'cash_paymoney' => 'Cash Paymoney',
			'pay_status' => 'Pay Status',
			'order_status' => 'Order Status',
			'stored_confirm_status' => 'Stored Confirm Status',
			'pay_time' => 'Pay Time',
			'flag' => 'Flag',
			'address' => 'Address',
			'remark' => 'Remark',
			'send_time' => 'Send Time',
			'receive_time' => 'Receive Time',
			'print' => 'Print',
			'freight_print' => 'Freight Print',
			'cancel_time' => 'Cancel Time',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'if_use_coupons' => 'If Use Coupons',
			'order_paymoney' => 'Order Paymoney',
			'freight_money' => 'Freight Money',
			'alipay_account' => 'Alipay Account',
			'user_code' => 'User Code',
			'hongbao_money' => 'Hongbao Money',
			'coupons_money' => 'Coupons Money',
			'discount_money' => 'Discount Money',
			'undiscount_paymoney' => 'Undiscount Paymoney',
			'seller_remark' => 'Seller Remark',
			'complete_time' => 'Complete Time',
			'ums_code' => 'Ums Code',
			'ums_card_no' => 'Ums Card No',
			'terminal_type' => 'Terminal Type',
			'terminal_id' => 'Terminal',
			'merchant_discount_money' => 'Merchant Discount Money',
			'alipay_discount_money' => 'Alipay Discount Money',
			'third_party_order_id' => 'Third Party Order',
			'pay_passageway' => 'Pay Passageway',
			'commission_ratio' => 'Commission Ratio',
			'thrid_party_code' => 'Thrid Party Code',
			'wechat_user_id' => 'Wechat User',
			'alipay_user_id' => 'Alipay User',
			'wechat_user_p_id' => 'Wechat User P',
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
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('operator_id',$this->operator_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('order_no',$this->order_no,true);
		$criteria->compare('order_type',$this->order_type);
		$criteria->compare('trade_no',$this->trade_no,true);
		$criteria->compare('pay_channel',$this->pay_channel);
		$criteria->compare('stored_paymoney',$this->stored_paymoney);
		$criteria->compare('online_paymoney',$this->online_paymoney);
		$criteria->compare('unionpay_paymoney',$this->unionpay_paymoney);
		$criteria->compare('cash_paymoney',$this->cash_paymoney);
		$criteria->compare('pay_status',$this->pay_status);
		$criteria->compare('order_status',$this->order_status);
		$criteria->compare('stored_confirm_status',$this->stored_confirm_status);
		$criteria->compare('pay_time',$this->pay_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('send_time',$this->send_time,true);
		$criteria->compare('receive_time',$this->receive_time,true);
		$criteria->compare('print',$this->print);
		$criteria->compare('freight_print',$this->freight_print);
		$criteria->compare('cancel_time',$this->cancel_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('if_use_coupons',$this->if_use_coupons);
		$criteria->compare('order_paymoney',$this->order_paymoney);
		$criteria->compare('freight_money',$this->freight_money);
		$criteria->compare('alipay_account',$this->alipay_account,true);
		$criteria->compare('user_code',$this->user_code,true);
		$criteria->compare('hongbao_money',$this->hongbao_money);
		$criteria->compare('coupons_money',$this->coupons_money);
		$criteria->compare('discount_money',$this->discount_money);
		$criteria->compare('undiscount_paymoney',$this->undiscount_paymoney);
		$criteria->compare('seller_remark',$this->seller_remark,true);
		$criteria->compare('complete_time',$this->complete_time,true);
		$criteria->compare('ums_code',$this->ums_code,true);
		$criteria->compare('ums_card_no',$this->ums_card_no,true);
		$criteria->compare('terminal_type',$this->terminal_type);
		$criteria->compare('terminal_id',$this->terminal_id,true);
		$criteria->compare('merchant_discount_money',$this->merchant_discount_money);
		$criteria->compare('alipay_discount_money',$this->alipay_discount_money);
		$criteria->compare('third_party_order_id',$this->third_party_order_id,true);
		$criteria->compare('pay_passageway',$this->pay_passageway);
		$criteria->compare('commission_ratio',$this->commission_ratio);
		$criteria->compare('thrid_party_code',$this->thrid_party_code,true);
		$criteria->compare('wechat_user_id',$this->wechat_user_id,true);
		$criteria->compare('alipay_user_id',$this->alipay_user_id,true);
		$criteria->compare('wechat_user_p_id',$this->wechat_user_p_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Order the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
