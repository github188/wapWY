<?php

/**
 * This is the model class for table "{{proprietor}}".
 *
 * The followings are the available columns in table '{{proprietor}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $community_id
 * @property integer $merchant_id
 * @property string $building_number
 * @property string $room_number
 * @property integer $verify_status
 * @property string $remark
 * @property string $access_control_card_no
 * @property integer $type
 * @property string $family_members
 * @property string $car_brand
 * @property string $car_no
 * @property string $car_img
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class Proprietor extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{proprietor}}';
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
			array('user_id, community_id, merchant_id, verify_status, type, flag', 'numerical', 'integerOnly'=>true),
			array('building_number, room_number, access_control_card_no, car_no', 'length', 'max'=>32),
			array('remark, car_img', 'length', 'max'=>255),
			array('family_members', 'length', 'max'=>1000),
			array('car_brand', 'length', 'max'=>100),
			array('last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, community_id, merchant_id, building_number, room_number, verify_status, remark, access_control_card_no, type, family_members, car_brand, car_no, car_img, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
            'community' => array(self::BELONGS_TO, 'Community', 'community_id')
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
			'community_id' => 'Community',
			'merchant_id' => 'Merchant',
			'building_number' => 'Building Number',
			'room_number' => 'Room Number',
			'verify_status' => 'Verify Status',
			'remark' => 'Remark',
			'access_control_card_no' => 'Access Control Card No',
			'type' => 'Type',
			'family_members' => 'Family Members',
			'car_brand' => 'Car Brand',
			'car_no' => 'Car No',
			'car_img' => 'Car Img',
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
		$criteria->compare('community_id',$this->community_id);
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('building_number',$this->building_number,true);
		$criteria->compare('room_number',$this->room_number,true);
		$criteria->compare('verify_status',$this->verify_status);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('access_control_card_no',$this->access_control_card_no,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('family_members',$this->family_members,true);
		$criteria->compare('car_brand',$this->car_brand,true);
		$criteria->compare('car_no',$this->car_no,true);
		$criteria->compare('car_img',$this->car_img,true);
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
	 * @return Proprietor the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
