<?php

/**
 * This is the model class for table "{{contract}}".
 *
 * The followings are the available columns in table '{{contract}}':
 * @property integer $id
 * @property string $contract_no
 * @property integer $merchant_id
 * @property integer $product
 * @property integer $status
 * @property string $create_time
 * @property string $remark
 * @property double $rate
 * @property integer $flag
 * @property string $last_time
 */
class Contract extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{contract}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, product, status, flag', 'numerical', 'integerOnly'=>true),
			array('rate', 'numerical'),
			array('contract_no', 'length', 'max'=>32),
			array('remark', 'length', 'max'=>255),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, contract_no, merchant_id, product, status, create_time, remark, rate, flag, last_time', 'safe', 'on'=>'search'),
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
			'contract_no' => 'Contract No',
			'merchant_id' => 'Merchant',
			'product' => 'Product',
			'status' => 'Status',
			'create_time' => 'Create Time',
			'remark' => 'Remark',
			'rate' => 'Rate',
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
		$criteria->compare('contract_no',$this->contract_no,true);
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('product',$this->product);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('rate',$this->rate);
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
	 * @return Contract the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
