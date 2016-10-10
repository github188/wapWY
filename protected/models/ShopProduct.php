<?php

/**
 * This is the model class for table "{{shop_product}}".
 *
 * The followings are the available columns in table '{{shop_product}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $category_id
 * @property string $group_id
 * @property string $standard
 * @property integer $page_id
 * @property integer $status
 * @property integer $type
 * @property string $name
 * @property double $price
 * @property string $img
 * @property integer $freight_type
 * @property double $freight_money
 * @property integer $freight_id
 * @property integer $limit_num
 * @property string $start_time
 * @property integer $if_invoice
 * @property integer $if_guarantee
 * @property integer $if_show_num
 * @property string $brief_introduction
 * @property string $detailed_introduction
 * @property string $create_time
 * @property integer $flag
 * @property string $last_time
 * @property integer $stock_num
 */
class ShopProduct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{shop_product}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, page_id, status, type, freight_type, freight_id, limit_num, if_invoice, if_guarantee, if_show_num, flag, stock_num', 'numerical', 'integerOnly'=>true),
			array('price, freight_money', 'numerical'),
			array('category_id, group_id', 'length', 'max'=>50),
			array('standard', 'length', 'max'=>1000),
			array('name', 'length', 'max'=>100),
			array('img, brief_introduction', 'length', 'max'=>255),
			array('start_time, detailed_introduction, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, category_id, group_id, standard, page_id, status, type, name, price, img, freight_type, freight_money, freight_id, limit_num, start_time, if_invoice, if_guarantee, if_show_num, brief_introduction, detailed_introduction, create_time, flag, last_time, stock_num', 'safe', 'on'=>'search'),
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
			'category_id' => 'Category',
			'group_id' => 'Group',
			'standard' => 'Standard',
			'page_id' => 'Page',
			'status' => 'Status',
			'type' => 'Type',
			'name' => 'Name',
			'price' => 'Price',
			'img' => 'Img',
			'freight_type' => 'Freight Type',
			'freight_money' => 'Freight Money',
			'freight_id' => 'Freight',
			'limit_num' => 'Limit Num',
			'start_time' => 'Start Time',
			'if_invoice' => 'If Invoice',
			'if_guarantee' => 'If Guarantee',
			'if_show_num' => 'If Show Num',
			'brief_introduction' => 'Brief Introduction',
			'detailed_introduction' => 'Detailed Introduction',
			'create_time' => 'Create Time',
			'flag' => 'Flag',
			'last_time' => 'Last Time',
			'stock_num' => 'Stock Num',
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
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('group_id',$this->group_id,true);
		$criteria->compare('standard',$this->standard,true);
		$criteria->compare('page_id',$this->page_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('img',$this->img,true);
		$criteria->compare('freight_type',$this->freight_type);
		$criteria->compare('freight_money',$this->freight_money);
		$criteria->compare('freight_id',$this->freight_id);
		$criteria->compare('limit_num',$this->limit_num);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('if_invoice',$this->if_invoice);
		$criteria->compare('if_guarantee',$this->if_guarantee);
		$criteria->compare('if_show_num',$this->if_show_num);
		$criteria->compare('brief_introduction',$this->brief_introduction,true);
		$criteria->compare('detailed_introduction',$this->detailed_introduction,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('stock_num',$this->stock_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShopProduct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
