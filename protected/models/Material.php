<?php

/**
 * This is the model class for table "{{material}}".
 *
 * The followings are the available columns in table '{{material}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property string $material_id
 * @property string $title
 * @property string $cover_img
 * @property string $abstract
 * @property integer $jump_type
 * @property string $content
 * @property string $link_content
 * @property integer $rate
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 * @property integer $from_platform
 * @property string $img_path
 */
class Material extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{material}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, jump_type, rate, flag, from_platform', 'numerical', 'integerOnly'=>true),
			array('material_id, title', 'length', 'max'=>32),
			array('cover_img', 'length', 'max'=>50),
			array('abstract, link_content', 'length', 'max'=>255),
			array('img_path', 'length', 'max'=>100),
			array('content, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, material_id, title, cover_img, abstract, jump_type, content, link_content, rate, create_time, last_time, flag, from_platform, img_path', 'safe', 'on'=>'search'),
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
			'material_id' => 'Material',
			'title' => 'Title',
			'cover_img' => 'Cover Img',
			'abstract' => 'Abstract',
			'jump_type' => 'Jump Type',
			'content' => 'Content',
			'link_content' => 'Link Content',
			'rate' => 'Rate',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'flag' => 'Flag',
			'from_platform' => 'From Platform',
			'img_path' => 'Img Path',
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
		$criteria->compare('material_id',$this->material_id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('cover_img',$this->cover_img,true);
		$criteria->compare('abstract',$this->abstract,true);
		$criteria->compare('jump_type',$this->jump_type);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('link_content',$this->link_content,true);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('from_platform',$this->from_platform);
		$criteria->compare('img_path',$this->img_path,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Material the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
