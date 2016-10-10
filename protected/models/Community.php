<?php

/**
 * This is the model class for table "{{community}}".
 *
 * The followings are the available columns in table '{{community}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $name
 * @property string $tel
 * @property string $address
 * @property string $remark
 * @property string $water_fee_set
 * @property string $electricity_fee_set
 * @property string $parking_fee_set
 * @property string $property_fee_set
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class Community extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{community}}';
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
			array('merchant_id, flag', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>50),
			array('tel', 'length', 'max'=>32),
			array('address, remark, water_fee_set, electricity_fee_set, parking_fee_set, property_fee_set', 'length', 'max'=>255),
			array('last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, name, tel, address, remark, water_fee_set, electricity_fee_set, parking_fee_set, property_fee_set, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'tel' => 'Tel',
			'address' => 'Address',
			'remark' => 'Remark',
			'water_fee_set' => 'Water Fee Set',
			'electricity_fee_set' => 'Electricity Fee Set',
			'parking_fee_set' => 'Parking Fee Set',
			'property_fee_set' => 'Property Fee Set',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('water_fee_set',$this->water_fee_set,true);
		$criteria->compare('electricity_fee_set',$this->electricity_fee_set,true);
		$criteria->compare('parking_fee_set',$this->parking_fee_set,true);
		$criteria->compare('property_fee_set',$this->property_fee_set,true);
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
	 * @return Community the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
