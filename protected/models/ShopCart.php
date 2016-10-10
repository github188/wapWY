<?php

/**
 * This is the model class for table "{{shop_cart}}".
 *
 * The followings are the available columns in table '{{shop_cart}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $product_id
 * @property integer $sku_id
 * @property integer $num
 * @property string $product_name
 * @property string $create_time
 * @property integer $flag
 * @property string $last_time
 */
class ShopCart extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{shop_cart}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, product_id, sku_id, num, flag', 'numerical', 'integerOnly'=>true),
			array('product_name', 'length', 'max'=>100),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, product_id, sku_id, num, product_name, create_time, flag, last_time', 'safe', 'on'=>'search'),
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
				'product' => array(self::BELONGS_TO,'ShopProduct','product_id'),
				'sku' => array(self::BELONGS_TO,'ShopProductSku','sku_id'),
				'user' => array(self::BELONGS_TO,'User','user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'product_id' => 'Product',
			'sku_id' => 'Sku',
			'num' => 'Num',
			'product_name' => 'Product Name',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('sku_id',$this->sku_id);
		$criteria->compare('num',$this->num);
		$criteria->compare('product_name',$this->product_name,true);
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
	 * @return ShopCart the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
