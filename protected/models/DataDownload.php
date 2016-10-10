<?php

/**
 * This is the model class for table "{{data_download}}".
 *
 * The followings are the available columns in table '{{data_download}}':
 * @property integer $id
 * @property string $title
 * @property integer $staff_id
 * @property integer $type
 * @property integer $status
 * @property string $download_url
 * @property string $release_time
 * @property integer $release_to
 * @property integer $add_to
 * @property string $content
 * @property integer $weight
 * @property integer $flag
 * @property string $create_time
 * @property string $last_time
 */
class DataDownload extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{data_download}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('staff_id, type, status, release_to, add_to, weight, flag', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
			array('download_url', 'length', 'max'=>200),
			array('release_time, content, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, staff_id, type, status, download_url, release_time, release_to, add_to, content, weight, flag, create_time, last_time', 'safe', 'on'=>'search'),
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
			'staff'=>array(self::BELONGS_TO,'Staff','staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'staff_id' => 'Staff',
			'type' => 'Type',
			'status' => 'Status',
			'download_url' => 'Download Url',
			'release_time' => 'Release Time',
			'release_to' => 'Release To',
			'add_to' => 'Add To',
			'content' => 'Content',
			'weight' => 'Weight',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('staff_id',$this->staff_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('download_url',$this->download_url,true);
		$criteria->compare('release_time',$this->release_time,true);
		$criteria->compare('release_to',$this->release_to);
		$criteria->compare('add_to',$this->add_to);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('weight',$this->weight);
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
	 * @return DataDownload the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
