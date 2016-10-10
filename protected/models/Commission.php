<?php

/**
 * This is the model class for table "{{commission}}".
 *
 * The followings are the available columns in table '{{commission}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $agent_id
 * @property double $amount
 * @property double $commission
 * @property string $date
 * @property string $create_time
 * @property integer $flag
 * @property string $last_time
 */
class Commission extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{commission}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, agent_id, flag', 'numerical', 'integerOnly'=>true),
			array('amount, commission', 'numerical'),
			array('date, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, agent_id, amount, commission, date, create_time, flag, last_time', 'safe', 'on'=>'search'),
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
				'agent' => array(self::BELONGS_TO,'Agent','agent_id'),
				'merchant' => array(self::BELONGS_TO,'Merchant','merchant_id'),
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
			'agent_id' => 'Agent',
			'amount' => 'Amount',
			'commission' => 'Commission',
			'date' => 'Date',
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
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('agent_id',$this->agent_id);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('commission',$this->commission);
		$criteria->compare('date',$this->date,true);
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
	 * @return Commission the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
