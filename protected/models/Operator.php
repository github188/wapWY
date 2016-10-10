<?php

/**
 * This is the model class for table "{{operator}}".
 *
 * The followings are the available columns in table '{{operator}}':
 * @property integer $id
 * @property integer $store_id
 * @property string $number
 * @property string $name
 * @property integer $role
 * @property string $account
 * @property string $pwd
 * @property string $create_time
 * @property string $last_time
 * @property string $admin_pwd
 * @property string $login_time
 * @property string $login_ip
 * @property integer $flag
 * @property integer $status
 * @property string $token
 */
class Operator extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{operator}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, role, flag, status', 'numerical', 'integerOnly'=>true),
			array('number', 'length', 'max'=>11),
			array('name, account', 'length', 'max'=>20),
			array('pwd', 'length', 'max'=>32),
			array('admin_pwd', 'length', 'max'=>10),
			array('login_ip, token', 'length', 'max'=>50),
			array('create_time, last_time, login_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, store_id, number, name, role, account, pwd, create_time, last_time, admin_pwd, login_time, login_ip, flag, status, token', 'safe', 'on'=>'search'),
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
		    'store' => array(self::BELONGS_TO,'Store','store_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'store_id' => 'Store',
			'number' => 'Number',
			'name' => 'Name',
			'role' => 'Role',
			'account' => 'Account',
			'pwd' => 'Pwd',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'admin_pwd' => 'Admin Pwd',
			'login_time' => 'Login Time',
			'login_ip' => 'Login Ip',
			'flag' => 'Flag',
			'status' => 'Status',
			'token' => 'Token',
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
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('number',$this->number,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('role',$this->role);
		$criteria->compare('account',$this->account,true);
		$criteria->compare('pwd',$this->pwd,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('admin_pwd',$this->admin_pwd,true);
		$criteria->compare('login_time',$this->login_time,true);
		$criteria->compare('login_ip',$this->login_ip,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('status',$this->status);
		$criteria->compare('token',$this->token,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Operator the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
