<?php

/**
 * This is the model class for table "{{prestore_order}}".
 *
 * The followings are the available columns in table '{{prestore_order}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $merchant_id
 * @property string $order_no
 * @property double $prestore_money
 * @property integer $pay_status
 * @property double $user_prestore
 * @property integer $pay_channel
 * @property string $trade_no
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class PrestoreOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{prestore_order}}';
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
			array('user_id, merchant_id, pay_status, pay_channel, flag', 'numerical', 'integerOnly'=>true),
			array('prestore_money, user_prestore', 'numerical'),
			array('order_no, trade_no', 'length', 'max'=>32),
			array('last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, merchant_id, order_no, prestore_money, pay_status, user_prestore, pay_channel, trade_no, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'merchant_id' => 'Merchant',
			'order_no' => 'Order No',
			'prestore_money' => 'Prestore Money',
			'pay_status' => 'Pay Status',
			'user_prestore' => 'User Prestore',
			'pay_channel' => 'Pay Channel',
			'trade_no' => 'Trade No',
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
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('order_no',$this->order_no,true);
		$criteria->compare('prestore_money',$this->prestore_money);
		$criteria->compare('pay_status',$this->pay_status);
		$criteria->compare('user_prestore',$this->user_prestore);
		$criteria->compare('pay_channel',$this->pay_channel);
		$criteria->compare('trade_no',$this->trade_no,true);
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
	 * @return PrestoreOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
