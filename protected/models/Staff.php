<?php

/**
 * This is the model class for table "{{staff}}".
 *
 * The followings are the available columns in table '{{staff}}':
 * @property integer $id
 * @property string $account
 * @property string $pwd
 * @property string $name
 * @property integer $sex
 * @property integer $company_id
 * @property integer $department_id
 * @property integer $post_id
 * @property string $tel
 * @property string $qq
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class Staff extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{staff}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sex, company_id, department_id, post_id, flag', 'numerical', 'integerOnly'=>true),
			array('account, pwd, name, tel, qq', 'length', 'max'=>32),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, account, pwd, name, sex, company_id, department_id, post_id, tel, qq, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'department'=>array(self::BELONGS_TO,'Department','department_id'),
			'company'=>array(self::BELONGS_TO,'Company','company_id'),
			'post'=>array(self::BELONGS_TO,'Post','post_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'account' => 'Account',
			'pwd' => 'Pwd',
			'name' => 'Name',
			'sex' => 'Sex',
			'company_id' => 'Company',
			'department_id' => 'Department',
			'post_id' => 'Post',
			'tel' => 'Tel',
			'qq' => 'Qq',
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
		$criteria->compare('account',$this->account,true);
		$criteria->compare('pwd',$this->pwd,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('company_id',$this->company_id);
		$criteria->compare('department_id',$this->department_id);
		$criteria->compare('post_id',$this->post_id);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('qq',$this->qq,true);
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
	 * @return Staff the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
