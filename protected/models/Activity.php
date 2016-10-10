<?php

/**
 * This is the model class for table "{{activity}}".
 *
 * The followings are the available columns in table '{{activity}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $name
 * @property integer $type
 * @property string $start_time
 * @property string $end_time
 * @property string $first_prize
 * @property integer $first_prize_num
 * @property double $first_prize_probability
 * @property string $second_prize
 * @property integer $second_prize_num
 * @property double $second_prize_probability
 * @property string $third_prize
 * @property integer $third_prize_num
 * @property double $third_prize_probability
 * @property string $fourth_prize
 * @property integer $fourth_prize_num
 * @property double $fourth_prize_probability
 * @property string $fifth_prize
 * @property integer $fifth_prize_num
 * @property double $fifth_prize_probability
 * @property integer $everyday_num
 * @property integer $if_show_num
 * @property integer $everyone_num
 * @property integer $everyone_everyday_num
 * @property string $illustrate
 * @property string $create_time
 * @property integer $flag
 * @property string $last_time
 */
class Activity extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{activity}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, type, first_prize_num, second_prize_num, third_prize_num, fourth_prize_num, fifth_prize_num, everyday_num, if_show_num, everyone_num, everyone_everyday_num, flag', 'numerical', 'integerOnly'=>true),
			array('first_prize_probability, second_prize_probability, third_prize_probability, fourth_prize_probability, fifth_prize_probability', 'numerical'),
			array('name, first_prize, second_prize, third_prize, fourth_prize, fifth_prize', 'length', 'max'=>100),
			array('illustrate', 'length', 'max'=>225),
			array('start_time, end_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, name, type, start_time, end_time, first_prize, first_prize_num, first_prize_probability, second_prize, second_prize_num, second_prize_probability, third_prize, third_prize_num, third_prize_probability, fourth_prize, fourth_prize_num, fourth_prize_probability, fifth_prize, fifth_prize_num, fifth_prize_probability, everyday_num, if_show_num, everyone_num, everyone_everyday_num, illustrate, create_time, flag, last_time', 'safe', 'on'=>'search'),
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
			'type' => 'Type',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'first_prize' => 'First Prize',
			'first_prize_num' => 'First Prize Num',
			'first_prize_probability' => 'First Prize Probability',
			'second_prize' => 'Second Prize',
			'second_prize_num' => 'Second Prize Num',
			'second_prize_probability' => 'Second Prize Probability',
			'third_prize' => 'Third Prize',
			'third_prize_num' => 'Third Prize Num',
			'third_prize_probability' => 'Third Prize Probability',
			'fourth_prize' => 'Fourth Prize',
			'fourth_prize_num' => 'Fourth Prize Num',
			'fourth_prize_probability' => 'Fourth Prize Probability',
			'fifth_prize' => 'Fifth Prize',
			'fifth_prize_num' => 'Fifth Prize Num',
			'fifth_prize_probability' => 'Fifth Prize Probability',
			'everyday_num' => 'Everyday Num',
			'if_show_num' => 'If Show Num',
			'everyone_num' => 'Everyone Num',
			'everyone_everyday_num' => 'Everyone Everyday Num',
			'illustrate' => 'Illustrate',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('first_prize',$this->first_prize,true);
		$criteria->compare('first_prize_num',$this->first_prize_num);
		$criteria->compare('first_prize_probability',$this->first_prize_probability);
		$criteria->compare('second_prize',$this->second_prize,true);
		$criteria->compare('second_prize_num',$this->second_prize_num);
		$criteria->compare('second_prize_probability',$this->second_prize_probability);
		$criteria->compare('third_prize',$this->third_prize,true);
		$criteria->compare('third_prize_num',$this->third_prize_num);
		$criteria->compare('third_prize_probability',$this->third_prize_probability);
		$criteria->compare('fourth_prize',$this->fourth_prize,true);
		$criteria->compare('fourth_prize_num',$this->fourth_prize_num);
		$criteria->compare('fourth_prize_probability',$this->fourth_prize_probability);
		$criteria->compare('fifth_prize',$this->fifth_prize,true);
		$criteria->compare('fifth_prize_num',$this->fifth_prize_num);
		$criteria->compare('fifth_prize_probability',$this->fifth_prize_probability);
		$criteria->compare('everyday_num',$this->everyday_num);
		$criteria->compare('if_show_num',$this->if_show_num);
		$criteria->compare('everyone_num',$this->everyone_num);
		$criteria->compare('everyone_everyday_num',$this->everyone_everyday_num);
		$criteria->compare('illustrate',$this->illustrate,true);
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
	 * @return Activity the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
