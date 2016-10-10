<?php

/**
 * This is the model class for table "{{user_grade_draft}}".
 *
 * The followings are the available columns in table '{{user_grade_draft}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $name
 * @property double $points_rule
 * @property double $discount
 * @property string $discount_illustrate
 * @property string $membercard_img
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 * @property string $membership_card_name
 * @property integer $points_ratio
 * @property integer $if_default
 * @property integer $if_hideword
 * @property integer $rule_type
 * @property double $birthday_rate
 */
class UserGradeDraft extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_grade_draft}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, flag, points_ratio, if_default, if_hideword, rule_type', 'numerical', 'integerOnly'=>true),
			array('points_rule, discount, birthday_rate', 'numerical'),
			array('name', 'length', 'max'=>50),
			array('discount_illustrate', 'length', 'max'=>255),
			array('membercard_img', 'length', 'max'=>32),
			array('membership_card_name', 'length', 'max'=>100),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, name, points_rule, discount, discount_illustrate, membercard_img, create_time, last_time, flag, membership_card_name, points_ratio, if_default, if_hideword, rule_type, birthday_rate', 'safe', 'on'=>'search'),
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
			'points_rule' => 'Points Rule',
			'discount' => 'Discount',
			'discount_illustrate' => 'Discount Illustrate',
			'membercard_img' => 'Membercard Img',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'flag' => 'Flag',
			'membership_card_name' => 'Membership Card Name',
			'points_ratio' => 'Points Ratio',
			'if_default' => 'If Default',
			'if_hideword' => 'If Hideword',
			'rule_type' => 'Rule Type',
			'birthday_rate' => 'Birthday Rate',
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
		$criteria->compare('points_rule',$this->points_rule);
		$criteria->compare('discount',$this->discount);
		$criteria->compare('discount_illustrate',$this->discount_illustrate,true);
		$criteria->compare('membercard_img',$this->membercard_img,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('membership_card_name',$this->membership_card_name,true);
		$criteria->compare('points_ratio',$this->points_ratio);
		$criteria->compare('if_default',$this->if_default);
		$criteria->compare('if_hideword',$this->if_hideword);
		$criteria->compare('rule_type',$this->rule_type);
		$criteria->compare('birthday_rate',$this->birthday_rate);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserGradeDraft the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
