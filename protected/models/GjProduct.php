<?php

/**
 * This is the model class for table "{{gj_product}}".
 *
 * The followings are the available columns in table '{{gj_product}}':
 * @property integer $id
 * @property string $name
 * @property double $price
 * @property double $original_price
 * @property string $create_time
 * @property string $last_time
 * @property integer $sold_num
 */
class GjProduct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{gj_product}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sold_num', 'numerical', 'integerOnly'=>true),
			array('price, original_price', 'numerical'),
			array('name', 'length', 'max'=>50),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, price, original_price, create_time, last_time, sold_num', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'price' => 'Price',
			'original_price' => 'Original Price',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'sold_num' => 'Sold Num',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('original_price',$this->original_price);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('sold_num',$this->sold_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GjProduct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
