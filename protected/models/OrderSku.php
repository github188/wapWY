<?php

/**
 * This is the model class for table "{{order_sku}}".
 *
 * The followings are the available columns in table '{{order_sku}}':
 * @property integer $id
 * @property integer $order_id
 * @property integer $sku_id
 * @property string $product_name
 * @property integer $num
 * @property double $price
 * @property string $create_time
 * @property integer $status
 * @property integer $flag
 * @property string $last_time
 * @property integer $if_send
 */
class OrderSku extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{order_sku}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, sku_id, num, status, flag, if_send', 'numerical', 'integerOnly'=>true),
			array('price', 'numerical'),
			array('product_name', 'length', 'max'=>100),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, sku_id, product_name, num, price, create_time, status, flag, last_time, if_send', 'safe', 'on'=>'search'),
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
				'shop_product_sku'=>array(self::BELONGS_TO,'ShopProductSku','sku_id'),
				'order'=>array(self::BELONGS_TO,'Order','order_id'),
                'dshop_product_sku'=>array(self::BELONGS_TO,'DProductSku','sku_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'order_id' => 'Order',
			'sku_id' => 'Sku',
			'product_name' => 'Product Name',
			'num' => 'Num',
			'price' => 'Price',
			'create_time' => 'Create Time',
			'status' => 'Status',
			'flag' => 'Flag',
			'last_time' => 'Last Time',
			'if_send' => 'If Send',
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
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('sku_id',$this->sku_id);
		$criteria->compare('product_name',$this->product_name,true);
		$criteria->compare('num',$this->num);
		$criteria->compare('price',$this->price);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('if_send',$this->if_send);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrderSku the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
