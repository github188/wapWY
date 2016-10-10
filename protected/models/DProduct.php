<?php

/**
 * This is the model class for table "{{d_product}}".
 *
 * The followings are the available columns in table '{{d_product}}':
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
 * @property integer $stock_num
 * @property string $third_party_product_id
 * @property integer $third_party_source
 * @property integer $use_time_type
 * @property integer $date_num
 * @property integer $check_time_type
 * @property integer $check_day
 * @property integer $check_hour
 * @property integer $check_minute
 * @property string $create_time
 * @property integer $flag
 * @property string $last_time
 */
class DProduct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{d_product}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, page_id, status, type, freight_type, freight_id, limit_num, if_invoice, if_guarantee, if_show_num, stock_num, third_party_source, use_time_type, date_num, check_time_type, check_day, check_hour, check_minute, flag', 'numerical', 'integerOnly'=>true),
			array('price, freight_money', 'numerical'),
			array('category_id, group_id', 'length', 'max'=>50),
			array('standard', 'length', 'max'=>1000),
			array('name', 'length', 'max'=>100),
			array('img, brief_introduction', 'length', 'max'=>255),
			array('third_party_product_id', 'length', 'max'=>32),
			array('start_time, detailed_introduction, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, category_id, group_id, standard, page_id, status, type, name, price, img, freight_type, freight_money, freight_id, limit_num, start_time, if_invoice, if_guarantee, if_show_num, brief_introduction, detailed_introduction, stock_num, third_party_product_id, third_party_source, use_time_type, date_num, check_time_type, check_day, check_hour, check_minute, create_time, flag, last_time', 'safe', 'on'=>'search'),
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
			'stock_num' => 'Stock Num',
			'third_party_product_id' => 'Third Party Product',
			'third_party_source' => 'Third Party Source',
			'use_time_type' => 'Use Time Type',
			'date_num' => 'Date Num',
			'check_time_type' => 'Check Time Type',
			'check_day' => 'Check Day',
			'check_hour' => 'Check Hour',
			'check_minute' => 'Check Minute',
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
		$criteria->compare('stock_num',$this->stock_num);
		$criteria->compare('third_party_product_id',$this->third_party_product_id,true);
		$criteria->compare('third_party_source',$this->third_party_source);
		$criteria->compare('use_time_type',$this->use_time_type);
		$criteria->compare('date_num',$this->date_num);
		$criteria->compare('check_time_type',$this->check_time_type);
		$criteria->compare('check_day',$this->check_day);
		$criteria->compare('check_hour',$this->check_hour);
		$criteria->compare('check_minute',$this->check_minute);
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
	 * @return DProduct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
