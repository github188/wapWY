<?php

/**
 * This is the model class for table "{{hotel_room}}".
 *
 * The followings are the available columns in table '{{hotel_room}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $store_id
 * @property string $name
 * @property double $price
 * @property string $room_img
 * @property string $room_details
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class HotelRoom extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{hotel_room}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, store_id, flag', 'numerical', 'integerOnly'=>true),
			array('price', 'numerical'),
			array('name', 'length', 'max'=>32),
			array('room_img', 'length', 'max'=>225),
			array('room_details, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, store_id, name, price, room_img, room_details, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'merchant_id' => 'Merchant',
			'store_id' => 'Store',
			'name' => 'Name',
			'price' => 'Price',
			'room_img' => 'Room Img',
			'room_details' => 'Room Details',
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
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('room_img',$this->room_img,true);
		$criteria->compare('room_details',$this->room_details,true);
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
	 * @return HotelRoom the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
