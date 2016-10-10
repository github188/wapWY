<?php

/**
 * This is the model class for table "{{alipay_commission}}".
 *
 * The followings are the available columns in table '{{alipay_commission}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $merchant_name
 * @property double $amount
 * @property double $commission
 * @property string $business_completion_time
 * @property integer $flag
 * @property string $create_time
 * @property string $last_time
 */
class AlipayCommission extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{alipay_commission}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, flag', 'numerical', 'integerOnly'=>true),
			array('amount, commission', 'numerical'),
			array('merchant_name', 'length', 'max'=>100),
			array('business_completion_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, merchant_name, amount, commission, business_completion_time, flag, create_time, last_time', 'safe', 'on'=>'search'),
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
			'merchant_name' => 'Merchant Name',
			'amount' => 'Amount',
			'commission' => 'Commission',
			'business_completion_time' => 'Business Completion Time',
			'flag' => 'Flag',
			'create_time' => 'Create Time',
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
		$criteria->compare('merchant_name',$this->merchant_name,true);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('commission',$this->commission);
		$criteria->compare('business_completion_time',$this->business_completion_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AlipayCommission the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
