<?php

/**
 * This is the model class for table "{{shop_express}}".
 *
 * The followings are the available columns in table '{{shop_express}}':
 * @property integer $id
 * @property integer $order_id
 * @property string $name
 * @property string $express_no
 * @property string $send_time
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class ShopExpress extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{shop_express}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, flag', 'numerical', 'integerOnly'=>true),
			array('name, express_no', 'length', 'max'=>100),
			array('send_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, name, express_no, send_time, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'name' => 'Name',
			'express_no' => 'Express No',
			'send_time' => 'Send Time',
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
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('express_no',$this->express_no,true);
		$criteria->compare('send_time',$this->send_time,true);
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
	 * @return ShopExpress the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function beforeSave(){
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->create_time = date('Y-m-d H:i:s');
			}else{
				$this->last_time = date('Y-m-d H:i:s');
			}
			return true;
		}else{
			return false;
		}
	}
}
