<?php

/**
 * This is the model class for table "{{user_address}}".
 *
 * The followings are the available columns in table '{{user_address}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $tel
 * @property string $address
 * @property string $code
 * @property integer $if_default
 * @property string $create_time
 * @property integer $flag
 * @property string $last_time
 */
class UserAddress extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_address}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, if_default, flag', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>100),
			array('tel, code', 'length', 'max'=>32),
			array('address', 'length', 'max'=>255),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, name, tel, address, code, if_default, create_time, flag, last_time', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'tel' => 'Tel',
			'address' => 'Address',
			'code' => 'Code',
			'if_default' => 'If Default',
			'create_time' => 'Create Time',
			'flag' => 'Flag',
			'last_time' => 'Last Time',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('if_default',$this->if_default);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('last_time',$this->last_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserAddress the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
