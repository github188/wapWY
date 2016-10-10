<?php

/**
 * This is the model class for table "{{tag}}".
 *
 * The followings are the available columns in table '{{tag}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $name
 * @property string $code
 * @property integer $value_type
 * @property string $value
 * @property string $tag_explain
 * @property string $logic_type
 * @property string $parameter
 * @property integer $type
 * @property integer $if_combination_tag
 * @property string $last_time
 * @property string $create_time
 * @property integer $flag
 */
class Tag extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{tag}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, value_type, type, if_combination_tag, flag', 'numerical', 'integerOnly'=>true),
			array('name, code', 'length', 'max'=>32),
			array('value, tag_explain, logic_type, parameter', 'length', 'max'=>255),
			array('last_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, name, code, value_type, value, tag_explain, logic_type, parameter, type, if_combination_tag, last_time, create_time, flag', 'safe', 'on'=>'search'),
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
			'code' => 'Code',
			'value_type' => 'Value Type',
			'value' => 'Value',
			'tag_explain' => 'Tag Explain',
			'logic_type' => 'Logic Type',
			'parameter' => 'Parameter',
			'type' => 'Type',
			'if_combination_tag' => 'If Combination Tag',
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
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('value_type',$this->value_type);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('tag_explain',$this->tag_explain,true);
		$criteria->compare('logic_type',$this->logic_type,true);
		$criteria->compare('parameter',$this->parameter,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('if_combination_tag',$this->if_combination_tag);
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
	 * @return Tag the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
