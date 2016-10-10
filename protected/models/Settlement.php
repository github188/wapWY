<?php

/**
 * This is the model class for table "{{settlement}}".
 *
 * The followings are the available columns in table '{{settlement}}':
 * @property integer $id
 * @property integer $operator_id
 * @property integer $store_id
 * @property string $start_time
 * @property string $end_time
 * @property double $total_money
 * @property integer $total_num
 * @property double $total_refund_money
 * @property integer $total_refund_num
 * @property double $total_actual_money
 * @property double $total_discount_money
 * @property double $alipay_money
 * @property integer $alipay_num
 * @property double $alipay_refund_money
 * @property integer $alipay_refund_num
 * @property double $alipay_actual_money
 * @property double $alipay_discount_money
 * @property double $wechat_money
 * @property integer $wechat_num
 * @property double $wechat_refund_money
 * @property integer $wechat_refund_num
 * @property double $wechat_actual_money
 * @property double $wechat_discount_money
 * @property double $unionpay_money
 * @property integer $unionpay_num
 * @property double $unionpay_refund_money
 * @property integer $unionpay_refund_num
 * @property double $unionpay_actual_money
 * @property double $unionpay_discount_money
 * @property double $cash_money
 * @property integer $cash_num
 * @property double $cash_refund_money
 * @property integer $cash_refund_num
 * @property double $cash_actual_money
 * @property double $cash_discount_money
 * @property double $stored_money
 * @property integer $stored_num
 * @property double $stored_refund_money
 * @property integer $stored_refund_num
 * @property double $stored_actual_money
 * @property double $stored_discount_money
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class Settlement extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{settlement}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('operator_id, store_id, total_num, total_refund_num, alipay_num, alipay_refund_num, wechat_num, wechat_refund_num, unionpay_num, unionpay_refund_num, cash_num, cash_refund_num, stored_num, stored_refund_num, flag', 'numerical', 'integerOnly'=>true),
			array('total_money, total_refund_money, total_actual_money, total_discount_money, alipay_money, alipay_refund_money, alipay_actual_money, alipay_discount_money, wechat_money, wechat_refund_money, wechat_actual_money, wechat_discount_money, unionpay_money, unionpay_refund_money, unionpay_actual_money, unionpay_discount_money, cash_money, cash_refund_money, cash_actual_money, cash_discount_money, stored_money, stored_refund_money, stored_actual_money, stored_discount_money', 'numerical'),
			array('start_time, end_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, operator_id, store_id, start_time, end_time, total_money, total_num, total_refund_money, total_refund_num, total_actual_money, total_discount_money, alipay_money, alipay_num, alipay_refund_money, alipay_refund_num, alipay_actual_money, alipay_discount_money, wechat_money, wechat_num, wechat_refund_money, wechat_refund_num, wechat_actual_money, wechat_discount_money, unionpay_money, unionpay_num, unionpay_refund_money, unionpay_refund_num, unionpay_actual_money, unionpay_discount_money, cash_money, cash_num, cash_refund_money, cash_refund_num, cash_actual_money, cash_discount_money, stored_money, stored_num, stored_refund_money, stored_refund_num, stored_actual_money, stored_discount_money, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'operator_id' => 'Operator',
			'store_id' => 'Store',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'total_money' => 'Total Money',
			'total_num' => 'Total Num',
			'total_refund_money' => 'Total Refund Money',
			'total_refund_num' => 'Total Refund Num',
			'total_actual_money' => 'Total Actual Money',
			'total_discount_money' => 'Total Discount Money',
			'alipay_money' => 'Alipay Money',
			'alipay_num' => 'Alipay Num',
			'alipay_refund_money' => 'Alipay Refund Money',
			'alipay_refund_num' => 'Alipay Refund Num',
			'alipay_actual_money' => 'Alipay Actual Money',
			'alipay_discount_money' => 'Alipay Discount Money',
			'wechat_money' => 'Wechat Money',
			'wechat_num' => 'Wechat Num',
			'wechat_refund_money' => 'Wechat Refund Money',
			'wechat_refund_num' => 'Wechat Refund Num',
			'wechat_actual_money' => 'Wechat Actual Money',
			'wechat_discount_money' => 'Wechat Discount Money',
			'unionpay_money' => 'Unionpay Money',
			'unionpay_num' => 'Unionpay Num',
			'unionpay_refund_money' => 'Unionpay Refund Money',
			'unionpay_refund_num' => 'Unionpay Refund Num',
			'unionpay_actual_money' => 'Unionpay Actual Money',
			'unionpay_discount_money' => 'Unionpay Discount Money',
			'cash_money' => 'Cash Money',
			'cash_num' => 'Cash Num',
			'cash_refund_money' => 'Cash Refund Money',
			'cash_refund_num' => 'Cash Refund Num',
			'cash_actual_money' => 'Cash Actual Money',
			'cash_discount_money' => 'Cash Discount Money',
			'stored_money' => 'Stored Money',
			'stored_num' => 'Stored Num',
			'stored_refund_money' => 'Stored Refund Money',
			'stored_refund_num' => 'Stored Refund Num',
			'stored_actual_money' => 'Stored Actual Money',
			'stored_discount_money' => 'Stored Discount Money',
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
		$criteria->compare('operator_id',$this->operator_id);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('total_money',$this->total_money);
		$criteria->compare('total_num',$this->total_num);
		$criteria->compare('total_refund_money',$this->total_refund_money);
		$criteria->compare('total_refund_num',$this->total_refund_num);
		$criteria->compare('total_actual_money',$this->total_actual_money);
		$criteria->compare('total_discount_money',$this->total_discount_money);
		$criteria->compare('alipay_money',$this->alipay_money);
		$criteria->compare('alipay_num',$this->alipay_num);
		$criteria->compare('alipay_refund_money',$this->alipay_refund_money);
		$criteria->compare('alipay_refund_num',$this->alipay_refund_num);
		$criteria->compare('alipay_actual_money',$this->alipay_actual_money);
		$criteria->compare('alipay_discount_money',$this->alipay_discount_money);
		$criteria->compare('wechat_money',$this->wechat_money);
		$criteria->compare('wechat_num',$this->wechat_num);
		$criteria->compare('wechat_refund_money',$this->wechat_refund_money);
		$criteria->compare('wechat_refund_num',$this->wechat_refund_num);
		$criteria->compare('wechat_actual_money',$this->wechat_actual_money);
		$criteria->compare('wechat_discount_money',$this->wechat_discount_money);
		$criteria->compare('unionpay_money',$this->unionpay_money);
		$criteria->compare('unionpay_num',$this->unionpay_num);
		$criteria->compare('unionpay_refund_money',$this->unionpay_refund_money);
		$criteria->compare('unionpay_refund_num',$this->unionpay_refund_num);
		$criteria->compare('unionpay_actual_money',$this->unionpay_actual_money);
		$criteria->compare('unionpay_discount_money',$this->unionpay_discount_money);
		$criteria->compare('cash_money',$this->cash_money);
		$criteria->compare('cash_num',$this->cash_num);
		$criteria->compare('cash_refund_money',$this->cash_refund_money);
		$criteria->compare('cash_refund_num',$this->cash_refund_num);
		$criteria->compare('cash_actual_money',$this->cash_actual_money);
		$criteria->compare('cash_discount_money',$this->cash_discount_money);
		$criteria->compare('stored_money',$this->stored_money);
		$criteria->compare('stored_num',$this->stored_num);
		$criteria->compare('stored_refund_money',$this->stored_refund_money);
		$criteria->compare('stored_refund_num',$this->stored_refund_num);
		$criteria->compare('stored_actual_money',$this->stored_actual_money);
		$criteria->compare('stored_discount_money',$this->stored_discount_money);
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
	 * @return Settlement the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
