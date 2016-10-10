<?php

/**
 * This is the model class for table "{{a_statistics}}".
 *
 * The followings are the available columns in table '{{a_statistics}}':
 * @property integer $id
 * @property integer $agent_id
 * @property string $date
 * @property double $total_trade_money
 * @property double $total_trade_actual_money
 * @property integer $total_trade_num
 * @property double $new_trade_money
 * @property double $new_trade_actual_money
 * @property integer $new_trade_num
 * @property double $new_trade_refund_money
 * @property integer $new_trade_refund_num
 * @property double $total_trade_alipay_money
 * @property double $total_trade_actual_alipay_money
 * @property integer $total_trade_alipay_num
 * @property double $new_trade_alipay_money
 * @property double $new_trade_actual_alipay_money
 * @property integer $new_trade_alipay_num
 * @property double $new_trade_alipay_refund_money
 * @property integer $new_trade_alipay_refund_num
 * @property double $total_trade_wechat_money
 * @property double $total_trade_actual_wechat_money
 * @property integer $total_trade_wechat_num
 * @property double $new_trade_wechat_money
 * @property double $new_trade_actual_wechat_money
 * @property integer $new_trade_wechat_num
 * @property double $new_trade_wechat_refund_money
 * @property integer $new_trade_wechat_refund_num
 * @property double $total_trade_unionpay_money
 * @property double $total_trade_actual_unionpay_money
 * @property integer $total_trade_unionpay_num
 * @property double $new_trade_unionpay_money
 * @property double $new_trade_actual_unionpay_money
 * @property integer $new_trade_unionpay_num
 * @property double $new_trade_unionpay_refund_money
 * @property integer $new_trade_unionpay_refund_num
 * @property double $total_trade_stored_money
 * @property double $total_trade_actual_stored_money
 * @property integer $total_trade_stored_num
 * @property double $new_trade_stored_money
 * @property double $new_trade_actual_stored_money
 * @property integer $new_trade_stored_num
 * @property double $new_trade_stored_refund_money
 * @property integer $new_trade_stored_refund_num
 * @property double $total_trade_cash_money
 * @property double $total_trade_actual_cash_money
 * @property integer $total_trade_cash_num
 * @property double $new_trade_cash_money
 * @property double $new_trade_actual_cash_money
 * @property integer $new_trade_cash_num
 * @property double $new_trade_cash_refund_money
 * @property integer $new_trade_cash_refund_num
 * @property integer $total_trade_coupon_num
 * @property double $total_alipay_commision_money
 * @property double $total_wechat_commision_money
 * @property integer $total_alipay_commision_num
 * @property integer $total_wechat_commision_num
 * @property integer $new_trade_coupon_num
 * @property double $new_alipay_commision_money
 * @property double $new_wechat_commision_money
 * @property integer $new_alipay_commision_num
 * @property integer $new_wechat_commision_num
 * @property integer $total_user_num
 * @property integer $total_alipayfans_num
 * @property integer $total_wechatfans_num
 * @property integer $total_member_num
 * @property integer $new_user_num
 * @property integer $new_alipayfans_num
 * @property integer $new_wechatfans_num
 * @property integer $new_member_num
 * @property integer $total_store_num
 * @property integer $new_store_num
 * @property integer $active_store_num
 * @property integer $total_merchant_num
 * @property integer $new_merchant_num
 * @property integer $total_yx_merchant_num
 * @property integer $total_sy_merchant_num
 * @property integer $new_yx_merchant_num
 * @property integer $new_sy_merchant_num
 * @property double $total_yx_servicecharge
 * @property double $new_yx_servicecharge
 * @property integer $total_one_level_agent_num
 * @property integer $total_two_level_agent_num
 * @property double $total_one_level_agent_fee
 * @property double $total_two_level_agent_fee
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class AStatistics extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{a_statistics}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('agent_id, total_trade_num, new_trade_num, new_trade_refund_num, total_trade_alipay_num, new_trade_alipay_num, new_trade_alipay_refund_num, total_trade_wechat_num, new_trade_wechat_num, new_trade_wechat_refund_num, total_trade_unionpay_num, new_trade_unionpay_num, new_trade_unionpay_refund_num, total_trade_stored_num, new_trade_stored_num, new_trade_stored_refund_num, total_trade_cash_num, new_trade_cash_num, new_trade_cash_refund_num, total_trade_coupon_num, total_alipay_commision_num, total_wechat_commision_num, new_trade_coupon_num, new_alipay_commision_num, new_wechat_commision_num, total_user_num, total_alipayfans_num, total_wechatfans_num, total_member_num, new_user_num, new_alipayfans_num, new_wechatfans_num, new_member_num, total_store_num, new_store_num, active_store_num, total_merchant_num, new_merchant_num, total_yx_merchant_num, total_sy_merchant_num, new_yx_merchant_num, new_sy_merchant_num, total_one_level_agent_num, total_two_level_agent_num, flag', 'numerical', 'integerOnly'=>true),
			array('total_trade_money, total_trade_actual_money, new_trade_money, new_trade_actual_money, new_trade_refund_money, total_trade_alipay_money, total_trade_actual_alipay_money, new_trade_alipay_money, new_trade_actual_alipay_money, new_trade_alipay_refund_money, total_trade_wechat_money, total_trade_actual_wechat_money, new_trade_wechat_money, new_trade_actual_wechat_money, new_trade_wechat_refund_money, total_trade_unionpay_money, total_trade_actual_unionpay_money, new_trade_unionpay_money, new_trade_actual_unionpay_money, new_trade_unionpay_refund_money, total_trade_stored_money, total_trade_actual_stored_money, new_trade_stored_money, new_trade_actual_stored_money, new_trade_stored_refund_money, total_trade_cash_money, total_trade_actual_cash_money, new_trade_cash_money, new_trade_actual_cash_money, new_trade_cash_refund_money, total_alipay_commision_money, total_wechat_commision_money, new_alipay_commision_money, new_wechat_commision_money, total_yx_servicecharge, new_yx_servicecharge, total_one_level_agent_fee, total_two_level_agent_fee', 'numerical'),
			array('date, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, agent_id, date, total_trade_money, total_trade_actual_money, total_trade_num, new_trade_money, new_trade_actual_money, new_trade_num, new_trade_refund_money, new_trade_refund_num, total_trade_alipay_money, total_trade_actual_alipay_money, total_trade_alipay_num, new_trade_alipay_money, new_trade_actual_alipay_money, new_trade_alipay_num, new_trade_alipay_refund_money, new_trade_alipay_refund_num, total_trade_wechat_money, total_trade_actual_wechat_money, total_trade_wechat_num, new_trade_wechat_money, new_trade_actual_wechat_money, new_trade_wechat_num, new_trade_wechat_refund_money, new_trade_wechat_refund_num, total_trade_unionpay_money, total_trade_actual_unionpay_money, total_trade_unionpay_num, new_trade_unionpay_money, new_trade_actual_unionpay_money, new_trade_unionpay_num, new_trade_unionpay_refund_money, new_trade_unionpay_refund_num, total_trade_stored_money, total_trade_actual_stored_money, total_trade_stored_num, new_trade_stored_money, new_trade_actual_stored_money, new_trade_stored_num, new_trade_stored_refund_money, new_trade_stored_refund_num, total_trade_cash_money, total_trade_actual_cash_money, total_trade_cash_num, new_trade_cash_money, new_trade_actual_cash_money, new_trade_cash_num, new_trade_cash_refund_money, new_trade_cash_refund_num, total_trade_coupon_num, total_alipay_commision_money, total_wechat_commision_money, total_alipay_commision_num, total_wechat_commision_num, new_trade_coupon_num, new_alipay_commision_money, new_wechat_commision_money, new_alipay_commision_num, new_wechat_commision_num, total_user_num, total_alipayfans_num, total_wechatfans_num, total_member_num, new_user_num, new_alipayfans_num, new_wechatfans_num, new_member_num, total_store_num, new_store_num, active_store_num, total_merchant_num, new_merchant_num, total_yx_merchant_num, total_sy_merchant_num, new_yx_merchant_num, new_sy_merchant_num, total_yx_servicecharge, new_yx_servicecharge, total_one_level_agent_num, total_two_level_agent_num, total_one_level_agent_fee, total_two_level_agent_fee, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'agent_id' => 'Agent',
			'date' => 'Date',
			'total_trade_money' => 'Total Trade Money',
			'total_trade_actual_money' => 'Total Trade Actual Money',
			'total_trade_num' => 'Total Trade Num',
			'new_trade_money' => 'New Trade Money',
			'new_trade_actual_money' => 'New Trade Actual Money',
			'new_trade_num' => 'New Trade Num',
			'new_trade_refund_money' => 'New Trade Refund Money',
			'new_trade_refund_num' => 'New Trade Refund Num',
			'total_trade_alipay_money' => 'Total Trade Alipay Money',
			'total_trade_actual_alipay_money' => 'Total Trade Actual Alipay Money',
			'total_trade_alipay_num' => 'Total Trade Alipay Num',
			'new_trade_alipay_money' => 'New Trade Alipay Money',
			'new_trade_actual_alipay_money' => 'New Trade Actual Alipay Money',
			'new_trade_alipay_num' => 'New Trade Alipay Num',
			'new_trade_alipay_refund_money' => 'New Trade Alipay Refund Money',
			'new_trade_alipay_refund_num' => 'New Trade Alipay Refund Num',
			'total_trade_wechat_money' => 'Total Trade Wechat Money',
			'total_trade_actual_wechat_money' => 'Total Trade Actual Wechat Money',
			'total_trade_wechat_num' => 'Total Trade Wechat Num',
			'new_trade_wechat_money' => 'New Trade Wechat Money',
			'new_trade_actual_wechat_money' => 'New Trade Actual Wechat Money',
			'new_trade_wechat_num' => 'New Trade Wechat Num',
			'new_trade_wechat_refund_money' => 'New Trade Wechat Refund Money',
			'new_trade_wechat_refund_num' => 'New Trade Wechat Refund Num',
			'total_trade_unionpay_money' => 'Total Trade Unionpay Money',
			'total_trade_actual_unionpay_money' => 'Total Trade Actual Unionpay Money',
			'total_trade_unionpay_num' => 'Total Trade Unionpay Num',
			'new_trade_unionpay_money' => 'New Trade Unionpay Money',
			'new_trade_actual_unionpay_money' => 'New Trade Actual Unionpay Money',
			'new_trade_unionpay_num' => 'New Trade Unionpay Num',
			'new_trade_unionpay_refund_money' => 'New Trade Unionpay Refund Money',
			'new_trade_unionpay_refund_num' => 'New Trade Unionpay Refund Num',
			'total_trade_stored_money' => 'Total Trade Stored Money',
			'total_trade_actual_stored_money' => 'Total Trade Actual Stored Money',
			'total_trade_stored_num' => 'Total Trade Stored Num',
			'new_trade_stored_money' => 'New Trade Stored Money',
			'new_trade_actual_stored_money' => 'New Trade Actual Stored Money',
			'new_trade_stored_num' => 'New Trade Stored Num',
			'new_trade_stored_refund_money' => 'New Trade Stored Refund Money',
			'new_trade_stored_refund_num' => 'New Trade Stored Refund Num',
			'total_trade_cash_money' => 'Total Trade Cash Money',
			'total_trade_actual_cash_money' => 'Total Trade Actual Cash Money',
			'total_trade_cash_num' => 'Total Trade Cash Num',
			'new_trade_cash_money' => 'New Trade Cash Money',
			'new_trade_actual_cash_money' => 'New Trade Actual Cash Money',
			'new_trade_cash_num' => 'New Trade Cash Num',
			'new_trade_cash_refund_money' => 'New Trade Cash Refund Money',
			'new_trade_cash_refund_num' => 'New Trade Cash Refund Num',
			'total_trade_coupon_num' => 'Total Trade Coupon Num',
			'total_alipay_commision_money' => 'Total Alipay Commision Money',
			'total_wechat_commision_money' => 'Total Wechat Commision Money',
			'total_alipay_commision_num' => 'Total Alipay Commision Num',
			'total_wechat_commision_num' => 'Total Wechat Commision Num',
			'new_trade_coupon_num' => 'New Trade Coupon Num',
			'new_alipay_commision_money' => 'New Alipay Commision Money',
			'new_wechat_commision_money' => 'New Wechat Commision Money',
			'new_alipay_commision_num' => 'New Alipay Commision Num',
			'new_wechat_commision_num' => 'New Wechat Commision Num',
			'total_user_num' => 'Total User Num',
			'total_alipayfans_num' => 'Total Alipayfans Num',
			'total_wechatfans_num' => 'Total Wechatfans Num',
			'total_member_num' => 'Total Member Num',
			'new_user_num' => 'New User Num',
			'new_alipayfans_num' => 'New Alipayfans Num',
			'new_wechatfans_num' => 'New Wechatfans Num',
			'new_member_num' => 'New Member Num',
			'total_store_num' => 'Total Store Num',
			'new_store_num' => 'New Store Num',
			'active_store_num' => 'Active Store Num',
			'total_merchant_num' => 'Total Merchant Num',
			'new_merchant_num' => 'New Merchant Num',
			'total_yx_merchant_num' => 'Total Yx Merchant Num',
			'total_sy_merchant_num' => 'Total Sy Merchant Num',
			'new_yx_merchant_num' => 'New Yx Merchant Num',
			'new_sy_merchant_num' => 'New Sy Merchant Num',
			'total_yx_servicecharge' => 'Total Yx Servicecharge',
			'new_yx_servicecharge' => 'New Yx Servicecharge',
			'total_one_level_agent_num' => 'Total One Level Agent Num',
			'total_two_level_agent_num' => 'Total Two Level Agent Num',
			'total_one_level_agent_fee' => 'Total One Level Agent Fee',
			'total_two_level_agent_fee' => 'Total Two Level Agent Fee',
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
		$criteria->compare('agent_id',$this->agent_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('total_trade_money',$this->total_trade_money);
		$criteria->compare('total_trade_actual_money',$this->total_trade_actual_money);
		$criteria->compare('total_trade_num',$this->total_trade_num);
		$criteria->compare('new_trade_money',$this->new_trade_money);
		$criteria->compare('new_trade_actual_money',$this->new_trade_actual_money);
		$criteria->compare('new_trade_num',$this->new_trade_num);
		$criteria->compare('new_trade_refund_money',$this->new_trade_refund_money);
		$criteria->compare('new_trade_refund_num',$this->new_trade_refund_num);
		$criteria->compare('total_trade_alipay_money',$this->total_trade_alipay_money);
		$criteria->compare('total_trade_actual_alipay_money',$this->total_trade_actual_alipay_money);
		$criteria->compare('total_trade_alipay_num',$this->total_trade_alipay_num);
		$criteria->compare('new_trade_alipay_money',$this->new_trade_alipay_money);
		$criteria->compare('new_trade_actual_alipay_money',$this->new_trade_actual_alipay_money);
		$criteria->compare('new_trade_alipay_num',$this->new_trade_alipay_num);
		$criteria->compare('new_trade_alipay_refund_money',$this->new_trade_alipay_refund_money);
		$criteria->compare('new_trade_alipay_refund_num',$this->new_trade_alipay_refund_num);
		$criteria->compare('total_trade_wechat_money',$this->total_trade_wechat_money);
		$criteria->compare('total_trade_actual_wechat_money',$this->total_trade_actual_wechat_money);
		$criteria->compare('total_trade_wechat_num',$this->total_trade_wechat_num);
		$criteria->compare('new_trade_wechat_money',$this->new_trade_wechat_money);
		$criteria->compare('new_trade_actual_wechat_money',$this->new_trade_actual_wechat_money);
		$criteria->compare('new_trade_wechat_num',$this->new_trade_wechat_num);
		$criteria->compare('new_trade_wechat_refund_money',$this->new_trade_wechat_refund_money);
		$criteria->compare('new_trade_wechat_refund_num',$this->new_trade_wechat_refund_num);
		$criteria->compare('total_trade_unionpay_money',$this->total_trade_unionpay_money);
		$criteria->compare('total_trade_actual_unionpay_money',$this->total_trade_actual_unionpay_money);
		$criteria->compare('total_trade_unionpay_num',$this->total_trade_unionpay_num);
		$criteria->compare('new_trade_unionpay_money',$this->new_trade_unionpay_money);
		$criteria->compare('new_trade_actual_unionpay_money',$this->new_trade_actual_unionpay_money);
		$criteria->compare('new_trade_unionpay_num',$this->new_trade_unionpay_num);
		$criteria->compare('new_trade_unionpay_refund_money',$this->new_trade_unionpay_refund_money);
		$criteria->compare('new_trade_unionpay_refund_num',$this->new_trade_unionpay_refund_num);
		$criteria->compare('total_trade_stored_money',$this->total_trade_stored_money);
		$criteria->compare('total_trade_actual_stored_money',$this->total_trade_actual_stored_money);
		$criteria->compare('total_trade_stored_num',$this->total_trade_stored_num);
		$criteria->compare('new_trade_stored_money',$this->new_trade_stored_money);
		$criteria->compare('new_trade_actual_stored_money',$this->new_trade_actual_stored_money);
		$criteria->compare('new_trade_stored_num',$this->new_trade_stored_num);
		$criteria->compare('new_trade_stored_refund_money',$this->new_trade_stored_refund_money);
		$criteria->compare('new_trade_stored_refund_num',$this->new_trade_stored_refund_num);
		$criteria->compare('total_trade_cash_money',$this->total_trade_cash_money);
		$criteria->compare('total_trade_actual_cash_money',$this->total_trade_actual_cash_money);
		$criteria->compare('total_trade_cash_num',$this->total_trade_cash_num);
		$criteria->compare('new_trade_cash_money',$this->new_trade_cash_money);
		$criteria->compare('new_trade_actual_cash_money',$this->new_trade_actual_cash_money);
		$criteria->compare('new_trade_cash_num',$this->new_trade_cash_num);
		$criteria->compare('new_trade_cash_refund_money',$this->new_trade_cash_refund_money);
		$criteria->compare('new_trade_cash_refund_num',$this->new_trade_cash_refund_num);
		$criteria->compare('total_trade_coupon_num',$this->total_trade_coupon_num);
		$criteria->compare('total_alipay_commision_money',$this->total_alipay_commision_money);
		$criteria->compare('total_wechat_commision_money',$this->total_wechat_commision_money);
		$criteria->compare('total_alipay_commision_num',$this->total_alipay_commision_num);
		$criteria->compare('total_wechat_commision_num',$this->total_wechat_commision_num);
		$criteria->compare('new_trade_coupon_num',$this->new_trade_coupon_num);
		$criteria->compare('new_alipay_commision_money',$this->new_alipay_commision_money);
		$criteria->compare('new_wechat_commision_money',$this->new_wechat_commision_money);
		$criteria->compare('new_alipay_commision_num',$this->new_alipay_commision_num);
		$criteria->compare('new_wechat_commision_num',$this->new_wechat_commision_num);
		$criteria->compare('total_user_num',$this->total_user_num);
		$criteria->compare('total_alipayfans_num',$this->total_alipayfans_num);
		$criteria->compare('total_wechatfans_num',$this->total_wechatfans_num);
		$criteria->compare('total_member_num',$this->total_member_num);
		$criteria->compare('new_user_num',$this->new_user_num);
		$criteria->compare('new_alipayfans_num',$this->new_alipayfans_num);
		$criteria->compare('new_wechatfans_num',$this->new_wechatfans_num);
		$criteria->compare('new_member_num',$this->new_member_num);
		$criteria->compare('total_store_num',$this->total_store_num);
		$criteria->compare('new_store_num',$this->new_store_num);
		$criteria->compare('active_store_num',$this->active_store_num);
		$criteria->compare('total_merchant_num',$this->total_merchant_num);
		$criteria->compare('new_merchant_num',$this->new_merchant_num);
		$criteria->compare('total_yx_merchant_num',$this->total_yx_merchant_num);
		$criteria->compare('total_sy_merchant_num',$this->total_sy_merchant_num);
		$criteria->compare('new_yx_merchant_num',$this->new_yx_merchant_num);
		$criteria->compare('new_sy_merchant_num',$this->new_sy_merchant_num);
		$criteria->compare('total_yx_servicecharge',$this->total_yx_servicecharge);
		$criteria->compare('new_yx_servicecharge',$this->new_yx_servicecharge);
		$criteria->compare('total_one_level_agent_num',$this->total_one_level_agent_num);
		$criteria->compare('total_two_level_agent_num',$this->total_two_level_agent_num);
		$criteria->compare('total_one_level_agent_fee',$this->total_one_level_agent_fee);
		$criteria->compare('total_two_level_agent_fee',$this->total_two_level_agent_fee);
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
	 * @return AStatistics the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
