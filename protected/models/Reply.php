<?php

/**
 * This is the model class for table "{{reply}}".
 *
 * The followings are the available columns in table '{{reply}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $key_word
 * @property integer $match_type
 * @property string $content
 * @property string $material_id
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 * @property string $rule_name
 * @property string $rule_id
 * @property integer $from_platform
 * @property integer $type
 * @property integer $group_id
 * @property integer $success_num
 * @property integer $fail_num
 */
class Reply extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{reply}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, match_type, flag, from_platform, type, group_id, success_num, fail_num', 'numerical', 'integerOnly'=>true),
			array('key_word', 'length', 'max'=>50),
			array('content', 'length', 'max'=>500),
			array('material_id, rule_name', 'length', 'max'=>32),
			array('rule_id', 'length', 'max'=>20),
			array('create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, key_word, match_type, content, material_id, create_time, last_time, flag, rule_name, rule_id, from_platform, type, group_id, success_num, fail_num', 'safe', 'on'=>'search'),
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
			'key_word' => 'Key Word',
			'match_type' => 'Match Type',
			'content' => 'Content',
			'material_id' => 'Material',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'flag' => 'Flag',
			'rule_name' => 'Rule Name',
			'rule_id' => 'Rule',
			'from_platform' => 'From Platform',
			'type' => 'Type',
			'group_id' => 'Group',
			'success_num' => 'Success Num',
			'fail_num' => 'Fail Num',
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
		$criteria->compare('key_word',$this->key_word,true);
		$criteria->compare('match_type',$this->match_type);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('material_id',$this->material_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('rule_name',$this->rule_name,true);
		$criteria->compare('rule_id',$this->rule_id,true);
		$criteria->compare('from_platform',$this->from_platform);
		$criteria->compare('type',$this->type);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('success_num',$this->success_num);
		$criteria->compare('fail_num',$this->fail_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Reply the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
