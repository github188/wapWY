<?php

/**
 * This is the model class for table "{{operator_log}}".
 *
 * The followings are the available columns in table '{{operator_log}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $operator_id
 * @property string $create_time
 * @property string $operation
 * @property string $client
 * @property string $ip
 */
class OperatorLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{operator_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, operator_id', 'numerical', 'integerOnly'=>true),
			array('operation', 'length', 'max'=>100),
			array('client, ip', 'length', 'max'=>50),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, operator_id, create_time, operation, client, ip', 'safe', 'on'=>'search'),
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
			'operator_id' => 'Operator',
			'create_time' => 'Create Time',
			'operation' => 'Operation',
			'client' => 'Client',
			'ip' => 'Ip',
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
		$criteria->compare('operator_id',$this->operator_id);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('operation',$this->operation,true);
		$criteria->compare('client',$this->client,true);
		$criteria->compare('ip',$this->ip,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OperatorLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
