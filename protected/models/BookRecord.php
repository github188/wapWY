<?php

/**
 * This is the model class for table "{{book_record}}".
 *
 * The followings are the available columns in table '{{book_record}}':
 * @property integer $id
 * @property integer $store_id
 * @property integer $resource_id
 * @property integer $user_id
 * @property string $book_time
 * @property string $cancel_time
 * @property string $create_time
 * @property string $deal_time
 * @property string $arrive_time
 * @property integer $operator_id
 * @property string $remark
 */
class BookRecord extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{book_record}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, resource_id, user_id, operator_id', 'numerical', 'integerOnly'=>true),
			array('remark', 'length', 'max'=>255),
			array('book_time, cancel_time, create_time, deal_time, arrive_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, store_id, resource_id, user_id, book_time, cancel_time, create_time, deal_time, arrive_time, operator_id, remark', 'safe', 'on'=>'search'),
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
			'store_id' => 'Store',
			'resource_id' => 'Resource',
			'user_id' => 'User',
			'book_time' => 'Book Time',
			'cancel_time' => 'Cancel Time',
			'create_time' => 'Create Time',
			'deal_time' => 'Deal Time',
			'arrive_time' => 'Arrive Time',
			'operator_id' => 'Operator',
			'remark' => 'Remark',
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
		$criteria->compare('resource_id',$this->resource_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('book_time',$this->book_time,true);
		$criteria->compare('cancel_time',$this->cancel_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('deal_time',$this->deal_time,true);
		$criteria->compare('arrive_time',$this->arrive_time,true);
		$criteria->compare('operator_id',$this->operator_id);
		$criteria->compare('remark',$this->remark,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BookRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
