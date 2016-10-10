<?php

/**
 * This is the model class for table "{{shop_subfreight}}".
 *
 * The followings are the available columns in table '{{shop_subfreight}}':
 * @property integer $id
 * @property integer $freight_id
 * @property string $area
 * @property integer $first_num
 * @property double $first_freight
 * @property integer $second_num
 * @property double $second_freight
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class ShopSubfreight extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{shop_subfreight}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('freight_id, first_num, second_num, flag', 'numerical', 'integerOnly'=>true),
			array('first_freight, second_freight', 'numerical'),
			array('area, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, freight_id, area, first_num, first_freight, second_num, second_freight, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
            'freight' => array(self::BELONGS_TO,'ShopFreight','freight_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'freight_id' => 'Freight',
			'area' => 'Area',
			'first_num' => 'First Num',
			'first_freight' => 'First Freight',
			'second_num' => 'Second Num',
			'second_freight' => 'Second Freight',
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
		$criteria->compare('freight_id',$this->freight_id);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('first_num',$this->first_num);
		$criteria->compare('first_freight',$this->first_freight);
		$criteria->compare('second_num',$this->second_num);
		$criteria->compare('second_freight',$this->second_freight);
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
	 * @return ShopSubfreight the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
