<?php

/**
 * This is the model class for table "{{oa_log}}".
 *
 * The followings are the available columns in table '{{oa_log}}':
 * @property integer $id
 * @property integer $staff_id
 * @property integer $operation
 * @property string $object
 * @property string $ip
 * @property string $operation_time
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class OaLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{oa_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('staff_id, operation, flag', 'numerical', 'integerOnly'=>true),
			array('object', 'length', 'max'=>100),
			array('ip', 'length', 'max'=>32),
			array('operation_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, staff_id, operation, object, ip, operation_time, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'staff'=>array(self::BELONGS_TO,'Staff','staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'staff_id' => 'Staff',
			'operation' => 'Operation',
			'object' => 'Object',
			'ip' => 'Ip',
			'operation_time' => 'Operation Time',
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
		$criteria->compare('staff_id',$this->staff_id);
		$criteria->compare('operation',$this->operation);
		$criteria->compare('object',$this->object,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('operation_time',$this->operation_time,true);
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
	 * @return OaLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
