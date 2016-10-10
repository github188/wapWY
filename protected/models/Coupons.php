<?php

/**
 * This is the model class for table "{{coupons}}".
 *
 * The followings are the available columns in table '{{coupons}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $merchant_short_name
 * @property string $card_id
 * @property integer $type
 * @property integer $if_wechat
 * @property string $color
 * @property string $title
 * @property string $vice_title
 * @property integer $money_type
 * @property string $money_random
 * @property double $money
 * @property double $discount
 * @property string $prompt
 * @property integer $if_share
 * @property integer $if_give
 * @property integer $num
 * @property integer $get_num
 * @property integer $time_type
 * @property string $start_time
 * @property string $end_time
 * @property integer $start_days
 * @property integer $effective_days
 * @property integer $receive_num
 * @property integer $use_restriction
 * @property double $mini_consumption
 * @property integer $if_with_userdiscount
 * @property string $store_limit
 * @property string $tel
 * @property string $use_illustrate
 * @property string $discount_illustrate
 * @property integer $if_invalid
 * @property integer $flag
 * @property string $create_time
 * @property string $last_time
 * @property integer $status
 * @property integer $use_channel
 */
class Coupons extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{coupons}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, type, if_wechat, money_type, if_share, if_give, num, get_num, time_type, start_days, effective_days, receive_num, use_restriction, if_with_userdiscount, if_invalid, flag, status, use_channel', 'numerical', 'integerOnly'=>true),
			array('money, discount, mini_consumption', 'numerical'),
			array('merchant_short_name, prompt', 'length', 'max'=>100),
			array('card_id, color, money_random, tel', 'length', 'max'=>32),
			array('title, vice_title', 'length', 'max'=>50),
			array('store_limit', 'length', 'max'=>255),
			array('start_time, end_time, use_illustrate, discount_illustrate, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, merchant_short_name, card_id, type, if_wechat, color, title, vice_title, money_type, money_random, money, discount, prompt, if_share, if_give, num, get_num, time_type, start_time, end_time, start_days, effective_days, receive_num, use_restriction, mini_consumption, if_with_userdiscount, store_limit, tel, use_illustrate, discount_illustrate, if_invalid, flag, create_time, last_time, status, use_channel', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'merchant_id' => 'Merchant',
			'merchant_short_name' => 'Merchant Short Name',
			'card_id' => 'Card',
			'type' => 'Type',
			'if_wechat' => 'If Wechat',
			'color' => 'Color',
			'title' => 'Title',
			'vice_title' => 'Vice Title',
			'money_type' => 'Money Type',
			'money_random' => 'Money Random',
			'money' => 'Money',
			'discount' => 'Discount',
			'prompt' => 'Prompt',
			'if_share' => 'If Share',
			'if_give' => 'If Give',
			'num' => 'Num',
			'get_num' => 'Get Num',
			'time_type' => 'Time Type',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'start_days' => 'Start Days',
			'effective_days' => 'Effective Days',
			'receive_num' => 'Receive Num',
			'use_restriction' => 'Use Restriction',
			'mini_consumption' => 'Mini Consumption',
			'if_with_userdiscount' => 'If With Userdiscount',
			'store_limit' => 'Store Limit',
			'tel' => 'Tel',
			'use_illustrate' => 'Use Illustrate',
			'discount_illustrate' => 'Discount Illustrate',
			'if_invalid' => 'If Invalid',
			'flag' => 'Flag',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'status' => 'Status',
			'use_channel' => 'Use Channel',
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
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('merchant_short_name',$this->merchant_short_name,true);
		$criteria->compare('card_id',$this->card_id,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('if_wechat',$this->if_wechat);
		$criteria->compare('color',$this->color,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('vice_title',$this->vice_title,true);
		$criteria->compare('money_type',$this->money_type);
		$criteria->compare('money_random',$this->money_random,true);
		$criteria->compare('money',$this->money);
		$criteria->compare('discount',$this->discount);
		$criteria->compare('prompt',$this->prompt,true);
		$criteria->compare('if_share',$this->if_share);
		$criteria->compare('if_give',$this->if_give);
		$criteria->compare('num',$this->num);
		$criteria->compare('get_num',$this->get_num);
		$criteria->compare('time_type',$this->time_type);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('start_days',$this->start_days);
		$criteria->compare('effective_days',$this->effective_days);
		$criteria->compare('receive_num',$this->receive_num);
		$criteria->compare('use_restriction',$this->use_restriction);
		$criteria->compare('mini_consumption',$this->mini_consumption);
		$criteria->compare('if_with_userdiscount',$this->if_with_userdiscount);
		$criteria->compare('store_limit',$this->store_limit,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('use_illustrate',$this->use_illustrate,true);
		$criteria->compare('discount_illustrate',$this->discount_illustrate,true);
		$criteria->compare('if_invalid',$this->if_invalid);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('use_channel',$this->use_channel);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Coupons the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
