<?php

/**
 * This is the model class for table "{{onlineshop}}".
 *
 * The followings are the available columns in table '{{onlineshop}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $name
 * @property string $img
 * @property string $logo_img
 * @property string $store_id
 * @property integer $if_book
 * @property integer $if_check
 * @property integer $if_coupons
 * @property integer $if_hongbao
 * @property integer $if_online_shoppingmall
 * @property string $coupons_id
 * @property string $product_id
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 * @property string $introduction
 */
class Onlineshop extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{onlineshop}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, if_book, if_check, if_coupons, if_hongbao, if_online_shoppingmall, flag', 'numerical', 'integerOnly'=>true),
			array('name, img, store_id, coupons_id, product_id', 'length', 'max'=>100),
			array('logo_img', 'length', 'max'=>50),
			array('introduction', 'length', 'max'=>512),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, name, img, logo_img, store_id, if_book, if_check, if_coupons, if_hongbao, if_online_shoppingmall, coupons_id, product_id, create_time, last_time, flag, introduction', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'img' => 'Img',
			'logo_img' => 'Logo Img',
			'store_id' => 'Store',
			'if_book' => 'If Book',
			'if_check' => 'If Check',
			'if_coupons' => 'If Coupons',
			'if_hongbao' => 'If Hongbao',
			'if_online_shoppingmall' => 'If Online Shoppingmall',
			'coupons_id' => 'Coupons',
			'product_id' => 'Product',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'flag' => 'Flag',
			'introduction' => 'Introduction',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('img',$this->img,true);
		$criteria->compare('logo_img',$this->logo_img,true);
		$criteria->compare('store_id',$this->store_id,true);
		$criteria->compare('if_book',$this->if_book);
		$criteria->compare('if_check',$this->if_check);
		$criteria->compare('if_coupons',$this->if_coupons);
		$criteria->compare('if_hongbao',$this->if_hongbao);
		$criteria->compare('if_online_shoppingmall',$this->if_online_shoppingmall);
		$criteria->compare('coupons_id',$this->coupons_id,true);
		$criteria->compare('product_id',$this->product_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('introduction',$this->introduction,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Onlineshop the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
