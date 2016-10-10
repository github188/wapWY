<?php

/**
 * This is the model class for table "{{report_form_day}}".
 *
 * The followings are the available columns in table '{{report_form_day}}':
 * @property integer $id
 * @property integer $store_id
 * @property double $total_money
 * @property integer $total_num
 * @property double $wechat_money
 * @property integer $wechat_num
 * @property double $alipay_money
 * @property integer $alipay_num
 * @property double $cash_money
 * @property integer $cash_num
 * @property double $stored_money
 * @property integer $stored_num
 * @property double $unionpay_money
 * @property integer $unionpay_num
 * @property string $date
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class ReportFormDay extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{report_form_day}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, total_num, wechat_num, alipay_num, cash_num, stored_num, unionpay_num, flag', 'numerical', 'integerOnly'=>true),
			array('total_money, wechat_money, alipay_money, cash_money, stored_money, unionpay_money', 'numerical'),
			array('date, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, store_id, total_money, total_num, wechat_money, wechat_num, alipay_money, alipay_num, cash_money, cash_num, stored_money, stored_num, unionpay_money, unionpay_num, date, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'total_money' => 'Total Money',
			'total_num' => 'Total Num',
			'wechat_money' => 'Wechat Money',
			'wechat_num' => 'Wechat Num',
			'alipay_money' => 'Alipay Money',
			'alipay_num' => 'Alipay Num',
			'cash_money' => 'Cash Money',
			'cash_num' => 'Cash Num',
			'stored_money' => 'Stored Money',
			'stored_num' => 'Stored Num',
			'unionpay_money' => 'Unionpay Money',
			'unionpay_num' => 'Unionpay Num',
			'date' => 'Date',
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
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('total_money',$this->total_money);
		$criteria->compare('total_num',$this->total_num);
		$criteria->compare('wechat_money',$this->wechat_money);
		$criteria->compare('wechat_num',$this->wechat_num);
		$criteria->compare('alipay_money',$this->alipay_money);
		$criteria->compare('alipay_num',$this->alipay_num);
		$criteria->compare('cash_money',$this->cash_money);
		$criteria->compare('cash_num',$this->cash_num);
		$criteria->compare('stored_money',$this->stored_money);
		$criteria->compare('stored_num',$this->stored_num);
		$criteria->compare('unionpay_money',$this->unionpay_money);
		$criteria->compare('unionpay_num',$this->unionpay_num);
		$criteria->compare('date',$this->date,true);
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
	 * @return ReportFormDay the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
