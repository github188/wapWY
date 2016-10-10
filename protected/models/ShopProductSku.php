<?php

/**
 * This is the model class for table "{{shop_product_sku}}".
 *
 * The followings are the available columns in table '{{shop_product_sku}}':
 * @property integer $id
 * @property integer $product_id
 * @property string $name
 * @property double $price
 * @property double $original_price
 * @property integer $num
 * @property string $merchant_no
 * @property integer $sold_num
 * @property string $create_time
 * @property integer $flag
 * @property string $last_time
 */
class ShopProductSku extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{shop_product_sku}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, num, sold_num, flag', 'numerical', 'integerOnly'=>true),
			array('price, original_price', 'numerical'),
			array('name', 'length', 'max'=>100),
			array('merchant_no', 'length', 'max'=>50),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, product_id, name, price, original_price, num, merchant_no, sold_num, create_time, flag, last_time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'product_id' => 'Product',
			'name' => 'Name',
			'price' => 'Price',
			'original_price' => 'Original Price',
			'num' => 'Num',
			'merchant_no' => 'Merchant No',
			'sold_num' => 'Sold Num',
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
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('original_price',$this->original_price);
		$criteria->compare('num',$this->num);
		$criteria->compare('merchant_no',$this->merchant_no,true);
		$criteria->compare('sold_num',$this->sold_num);
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
	 * @return ShopProductSku the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
