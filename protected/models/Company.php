<?php

/**
 * This is the model class for table "{{company}}".
 *
 * The followings are the available columns in table '{{company}}':
 * @property integer $id
 * @property integer $pid
 * @property string $name
 * @property integer $type
 * @property string $tel
 * @property string $fax
 * @property string $address
 * @property string $email
 * @property string $company_profile
 * @property string $website
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class Company extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{company}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, type, flag', 'numerical', 'integerOnly'=>true),
			array('name, website', 'length', 'max'=>100),
			array('tel, fax, email', 'length', 'max'=>32),
			array('address', 'length', 'max'=>225),
			array('company_profile, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pid, name, type, tel, fax, address, email, company_profile, website, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'pid' => 'Pid',
			'name' => 'Name',
			'type' => 'Type',
			'tel' => 'Tel',
			'fax' => 'Fax',
			'address' => 'Address',
			'email' => 'Email',
			'company_profile' => 'Company Profile',
			'website' => 'Website',
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
		$criteria->compare('pid',$this->pid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('company_profile',$this->company_profile,true);
		$criteria->compare('website',$this->website,true);
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
	 * @return Company the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
