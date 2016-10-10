<?php

/**
 * This is the model class for table "{{hotel_order}}".
 *
 * The followings are the available columns in table '{{hotel_order}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $merchant_id
 * @property integer $store_id
 * @property string $order_no
 * @property integer $hotel_room_id
 * @property integer $num
 * @property string $person_name
 * @property string $person_tel
 * @property string $check_in_time
 * @property string $check_out_time
 * @property integer $status
 * @property string $refuse_time
 * @property string $confirm_time
 * @property string $cancel_time
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class HotelOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{hotel_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, user_id, store_id, hotel_room_id, num, status, flag', 'numerical', 'integerOnly'=>true),
			array('order_no, person_name, person_tel', 'length', 'max'=>32),
			array('check_in_time, check_out_time, refuse_time, confirm_time, cancel_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, user_id, store_id, order_no, hotel_room_id, num, person_name, person_tel, check_in_time, check_out_time, status, refuse_time, confirm_time, cancel_time, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'merchant_id' => 'Mmerchant',
			'user_id' => 'User',
			'store_id' => 'Store',
			'order_no' => 'Order No',
			'hotel_room_id' => 'Hotel Room',
			'num' => 'Num',
			'person_name' => 'Person Name',
			'person_tel' => 'Person Tel',
			'check_in_time' => 'Check In Time',
			'check_out_time' => 'Check Out Time',
			'status' => 'Status',
			'refuse_time' => 'Refuse Time',
			'confirm_time' => 'Confirm Time',
			'cancel_time' => 'Cancel Time',
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
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('order_no',$this->order_no,true);
		$criteria->compare('hotel_room_id',$this->hotel_room_id);
		$criteria->compare('num',$this->num);
		$criteria->compare('person_name',$this->person_name,true);
		$criteria->compare('person_tel',$this->person_tel,true);
		$criteria->compare('check_in_time',$this->check_in_time,true);
		$criteria->compare('check_out_time',$this->check_out_time,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('refuse_time',$this->refuse_time,true);
		$criteria->compare('confirm_time',$this->confirm_time,true);
		$criteria->compare('cancel_time',$this->cancel_time,true);
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
	 * @return HotelOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
