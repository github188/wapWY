<?php

/**
 * This is the model class for table "{{report_repair_record}}".
 *
 * The followings are the available columns in table '{{report_repair_record}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $user_id
 * @property string $repair_person
 * @property string $tel
 * @property integer $community_id
 * @property string $address
 * @property integer $area_type
 * @property string $detail
 * @property string $img
 * @property string $remark
 * @property integer $status
 * @property string $repair_time
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class ReportRepairRecord extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{report_repair_record}}';
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
			array('merchant_id, user_id, community_id, area_type, status, flag', 'numerical', 'integerOnly'=>true),
			array('repair_person', 'length', 'max'=>100),
			array('tel', 'length', 'max'=>32),
			array('address, detail, img, remark', 'length', 'max'=>255),
			array('repair_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, user_id, repair_person, tel, community_id, address, area_type, detail, img, remark, status, repair_time, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'repair_person' => 'Repair Person',
			'tel' => 'Tel',
			'community_id' => 'Community',
			'address' => 'Address',
			'area_type' => 'Area Type',
			'detail' => 'Detail',
			'img' => 'Img',
			'remark' => 'Remark',
			'status' => 'Status',
			'repair_time' => 'Repair Time',
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
		$criteria->compare('repair_person',$this->repair_person,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('community_id',$this->community_id);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('area_type',$this->area_type);
		$criteria->compare('detail',$this->detail,true);
		$criteria->compare('img',$this->img,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('repair_time',$this->repair_time,true);
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
	 * @return ReportRepairRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
