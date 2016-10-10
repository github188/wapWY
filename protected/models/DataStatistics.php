<?php

/**
 * This is the model class for table "{{data_statistics}}".
 *
 * The followings are the available columns in table '{{data_statistics}}':
 * @property integer $id
 * @property string $date
 * @property integer $new_user_num
 * @property integer $user_num
 * @property integer $new_order_num
 * @property double $new_order_money
 * @property double $pct
 * @property integer $pv
 * @property integer $day_hongbao_num
 * @property integer $day_hongbao_use
 * @property integer $day_coupons_num
 * @property integer $day_coupons_use
 */
class DataStatistics extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{data_statistics}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('new_user_num, user_num, new_order_num, pv, day_hongbao_num, day_hongbao_use, day_coupons_num, day_coupons_use', 'numerical', 'integerOnly'=>true),
			array('new_order_money, pct', 'numerical'),
			array('date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, date, new_user_num, user_num, new_order_num, new_order_money, pct, pv, day_hongbao_num, day_hongbao_use, day_coupons_num, day_coupons_use', 'safe', 'on'=>'search'),
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
			'date' => 'Date',
			'new_user_num' => 'New User Num',
			'user_num' => 'User Num',
			'new_order_num' => 'New Order Num',
			'new_order_money' => 'New Order Money',
			'pct' => 'Pct',
			'pv' => 'Pv',
			'day_hongbao_num' => 'Day Hongbao Num',
			'day_hongbao_use' => 'Day Hongbao Use',
			'day_coupons_num' => 'Day Coupons Num',
			'day_coupons_use' => 'Day Coupons Use',
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('new_user_num',$this->new_user_num);
		$criteria->compare('user_num',$this->user_num);
		$criteria->compare('new_order_num',$this->new_order_num);
		$criteria->compare('new_order_money',$this->new_order_money);
		$criteria->compare('pct',$this->pct);
		$criteria->compare('pv',$this->pv);
		$criteria->compare('day_hongbao_num',$this->day_hongbao_num);
		$criteria->compare('day_hongbao_use',$this->day_hongbao_use);
		$criteria->compare('day_coupons_num',$this->day_coupons_num);
		$criteria->compare('day_coupons_use',$this->day_coupons_use);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DataStatistics the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
