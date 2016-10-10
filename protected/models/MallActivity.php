<?php

/**
 * This is the model class for table "{{mall_activity}}".
 *
 * The followings are the available columns in table '{{mall_activity}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $type
 * @property string $name
 * @property integer $num
 * @property double $original_price
 * @property string $start_time
 * @property string $end_time
 * @property integer $status
 * @property string $img
 * @property integer $coupons_id
 * @property integer $receive_num
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class MallActivity extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{mall_activity}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, type, num, status, coupons_id, receive_num, flag', 'numerical', 'integerOnly'=>true),
			array('original_price', 'numerical'),
			array('name', 'length', 'max'=>32),
			array('img', 'length', 'max'=>100),
			array('start_time, end_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, type, name, num, original_price, start_time, end_time, status, img, coupons_id, receive_num, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'coupons' => array(self::BELONGS_TO, 'Coupons', 'coupons_id')
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
			'type' => 'Type',
			'name' => 'Name',
			'num' => 'Num',
			'original_price' => 'Original Price',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'status' => 'Status',
			'img' => 'Img',
			'coupons_id' => 'Coupons',
			'receive_num' => 'Receive Num',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'flag' => 'Flag',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('num',$this->num);
		$criteria->compare('original_price',$this->original_price);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('img',$this->img,true);
		$criteria->compare('coupons_id',$this->coupons_id);
		$criteria->compare('receive_num',$this->receive_num);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('flag',$this->flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MallActivity the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
