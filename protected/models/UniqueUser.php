<?php

/**
 * This is the model class for table "{{unique_user}}".
 *
 * The followings are the available columns in table '{{unique_user}}':
 * @property integer $id
 * @property string $user_id
 * @property string $phone_num
 * @property string $alipay_openid
 * @property string $wechat_openid
 * @property integer $flag
 * @property string $create_time
 * @property string $last_time
 */
class UniqueUser extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{unique_user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('flag', 'numerical', 'integerOnly'=>true),
			array('user_id, phone_num', 'length', 'max'=>32),
			array('alipay_openid, wechat_openid', 'length', 'max'=>100),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, phone_num, alipay_openid, wechat_openid, flag, create_time, last_time', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'phone_num' => 'Phone Num',
			'alipay_openid' => 'Alipay Openid',
			'wechat_openid' => 'Wechat Openid',
			'flag' => 'Flag',
			'create_time' => 'Create Time',
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
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('phone_num',$this->phone_num,true);
		$criteria->compare('alipay_openid',$this->alipay_openid,true);
		$criteria->compare('wechat_openid',$this->wechat_openid,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UniqueUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
