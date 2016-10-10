<?php

/**
 * This is the model class for table "{{pv}}".
 *
 * The followings are the available columns in table '{{pv}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $ip
 * @property string $come_url
 * @property string $pv_url
 * @property string $visit_date
 * @property string $head
 * @property string $address
 * @property integer $from_platform
 * @property integer $channel
 */
class Pv extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{pv}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, from_platform, channel', 'numerical', 'integerOnly'=>true),
			array('ip', 'length', 'max'=>20),
			array('come_url, pv_url, address', 'length', 'max'=>255),
			array('visit_date, head', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, ip, come_url, pv_url, visit_date, head, address, from_platform, channel', 'safe', 'on'=>'search'),
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
			'ip' => 'Ip',
			'come_url' => 'Come Url',
			'pv_url' => 'Pv Url',
			'visit_date' => 'Visit Date',
			'head' => 'Head',
			'address' => 'Address',
			'from_platform' => 'From Platform',
			'channel' => 'Channel',
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
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('come_url',$this->come_url,true);
		$criteria->compare('pv_url',$this->pv_url,true);
		$criteria->compare('visit_date',$this->visit_date,true);
		$criteria->compare('head',$this->head,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('from_platform',$this->from_platform);
		$criteria->compare('channel',$this->channel);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Pv the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
