<?php

/**
 * This is the model class for table "{{agent_order}}".
 *
 * The followings are the available columns in table '{{agent_order}}':
 * @property integer $id
 * @property string $order_no
 * @property integer $agent_id
 * @property double $pay_money
 * @property integer $pay_status
 * @property integer $pay_channel
 * @property string $trade_no
 * @property string $pay_time
 * @property string $create_time
 * @property string $last_time
 * @property integer $flag
 */
class AgentOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{agent_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('agent_id, pay_status, pay_channel, flag', 'numerical', 'integerOnly'=>true),
			array('pay_money', 'numerical'),
			array('order_no, trade_no', 'length', 'max'=>32),
			array('pay_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_no, agent_id, pay_money, pay_status, pay_channel, trade_no, pay_time, create_time, last_time, flag', 'safe', 'on'=>'search'),
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
			'order_no' => 'Order No',
			'agent_id' => 'Agent',
			'pay_money' => 'Pay Money',
			'pay_status' => 'Pay Status',
			'pay_channel' => 'Pay Channel',
			'trade_no' => 'Trade No',
			'pay_time' => 'Pay Time',
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
		$criteria->compare('order_no',$this->order_no,true);
		$criteria->compare('agent_id',$this->agent_id);
		$criteria->compare('pay_money',$this->pay_money);
		$criteria->compare('pay_status',$this->pay_status);
		$criteria->compare('pay_channel',$this->pay_channel);
		$criteria->compare('trade_no',$this->trade_no,true);
		$criteria->compare('pay_time',$this->pay_time,true);
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
	 * @return AgentOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function beforeSave()
	{
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
