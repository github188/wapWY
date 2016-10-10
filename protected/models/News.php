<?php

/**
 * This is the model class for table "{{news}}".
 *
 * The followings are the available columns in table '{{news}}':
 * @property integer $id
 * @property integer $if_hot
 * @property integer $news_group_id
 * @property string $title
 * @property string $author
 * @property string $information_from
 * @property string $show_time
 * @property string $img
 * @property string $abstract
 * @property string $main_body
 * @property integer $hot_rate
 * @property integer $pv
 * @property string $publish_time
 * @property string $publisher
 * @property integer $status
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class News extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{news}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('if_hot, news_group_id, hot_rate, pv, status, flag', 'numerical', 'integerOnly'=>true),
			array('title, img', 'length', 'max'=>100),
			array('author, information_from, publisher', 'length', 'max'=>32),
			array('show_time, abstract, main_body, publish_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, if_hot, news_group_id, title, author, information_from, show_time, img, abstract, main_body, hot_rate, pv, publish_time, publisher, status, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'if_hot' => 'If Hot',
			'news_group_id' => 'News Group',
			'title' => 'Title',
			'author' => 'Author',
			'information_from' => 'Information From',
			'show_time' => 'Show Time',
			'img' => 'Img',
			'abstract' => 'Abstract',
			'main_body' => 'Main Body',
			'hot_rate' => 'Hot Rate',
			'pv' => 'Pv',
			'publish_time' => 'Publish Time',
			'publisher' => 'Publisher',
			'status' => 'Status',
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
		$criteria->compare('if_hot',$this->if_hot);
		$criteria->compare('news_group_id',$this->news_group_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('information_from',$this->information_from,true);
		$criteria->compare('show_time',$this->show_time,true);
		$criteria->compare('img',$this->img,true);
		$criteria->compare('abstract',$this->abstract,true);
		$criteria->compare('main_body',$this->main_body,true);
		$criteria->compare('hot_rate',$this->hot_rate);
		$criteria->compare('pv',$this->pv);
		$criteria->compare('publish_time',$this->publish_time,true);
		$criteria->compare('publisher',$this->publisher,true);
		$criteria->compare('status',$this->status);
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
	 * @return News the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
