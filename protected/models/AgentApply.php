<?php

/**
 * This is the model class for table "{{agent_apply}}".
 *
 * The followings are the available columns in table '{{agent_apply}}':
 * @property integer $id
 * @property string $name
 * @property string $phone_num
 * @property string $company
 * @property string $address
 * @property string $advantage
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class AgentApply extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{agent_apply}}';
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
			array('name', 'length', 'max'=>100),
			array('phone_num', 'length', 'max'=>32),
			array('company, address', 'length', 'max'=>255),
			array('advantage, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, phone_num, company, address, advantage, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'phone_num' => 'Phone Num',
			'company' => 'Company',
			'address' => 'Address',
			'advantage' => 'Advantage',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('phone_num',$this->phone_num,true);
		$criteria->compare('company',$this->company,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('advantage',$this->advantage,true);
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
	 * @return AgentApply the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
