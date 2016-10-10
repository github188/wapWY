<?php

/**
 * This is the model class for table "{{trade_notice_user}}".
 *
 * The followings are the available columns in table '{{trade_notice_user}}':
 * @property integer $id
 * @property integer $store_id
 * @property string $avatar
 * @property string $nickname
 * @property string $wechat_open_id
 * @property string $last_time
 * @property string $create_time
 * @property integer $flag
 */
class TradeNoticeUser extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{trade_notice_user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('store_id, flag', 'numerical', 'integerOnly'=>true),
			array('avatar', 'length', 'max'=>225),
			array('nickname', 'length', 'max'=>32),
			array('wechat_open_id', 'length', 'max'=>50),
			array('last_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, store_id, avatar, nickname, wechat_open_id, last_time, create_time, flag', 'safe', 'on'=>'search'),
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
			'avatar' => 'Avatar',
			'nickname' => 'Nickname',
			'wechat_open_id' => 'Wechat Open',
			'last_time' => 'Last Time',
			'create_time' => 'Create Time',
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
		$criteria->compare('avatar',$this->avatar,true);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('wechat_open_id',$this->wechat_open_id,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('flag',$this->flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TradeNoticeUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
