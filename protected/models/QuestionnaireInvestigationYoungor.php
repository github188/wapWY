<?php

/**
 * This is the model class for table "{{questionnaire_investigation_youngor}}".
 *
 * The followings are the available columns in table '{{questionnaire_investigation_youngor}}':
 * @property string $id
 * @property integer $merchant_id
 * @property string $branch_company
 * @property string $contacts
 * @property string $tel
 * @property integer $question1
 * @property integer $question2
 * @property integer $question3
 * @property integer $question4
 * @property string $question5
 * @property string $question6
 * @property integer $question7
 * @property string $question8
 * @property integer $question9
 * @property string $last_time
 * @property string $create_time
 * @property integer $flag
 */
class QuestionnaireInvestigationYoungor extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{questionnaire_investigation_youngor}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, question1, question2, question3, question4, question7, question9, flag', 'numerical', 'integerOnly'=>true),
			array('branch_company, contacts, tel', 'length', 'max'=>50),
			array('question5, question6, question8, last_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, branch_company, contacts, tel, question1, question2, question3, question4, question5, question6, question7, question8, question9, last_time, create_time, flag', 'safe', 'on'=>'search'),
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
			'branch_company' => 'Branch Company',
			'contacts' => 'Contacts',
			'tel' => 'Tel',
			'question1' => 'Question1',
			'question2' => 'Question2',
			'question3' => 'Question3',
			'question4' => 'Question4',
			'question5' => 'Question5',
			'question6' => 'Question6',
			'question7' => 'Question7',
			'question8' => 'Question8',
			'question9' => 'Question9',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('branch_company',$this->branch_company,true);
		$criteria->compare('contacts',$this->contacts,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('question1',$this->question1);
		$criteria->compare('question2',$this->question2);
		$criteria->compare('question3',$this->question3);
		$criteria->compare('question4',$this->question4);
		$criteria->compare('question5',$this->question5,true);
		$criteria->compare('question6',$this->question6,true);
		$criteria->compare('question7',$this->question7);
		$criteria->compare('question8',$this->question8,true);
		$criteria->compare('question9',$this->question9);
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
	 * @return QuestionnaireInvestigationYoungor the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
