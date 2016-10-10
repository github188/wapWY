<?php

/**
 * This is the model class for table "{{refund_record}}".
 *
 * The followings are the available columns in table '{{refund_record}}':
 * @property integer $id
 * @property integer $order_id
 * @property integer $merchant_id
 * @property integer $store_id
 * @property integer $operator_id
 * @property integer $operator_admin_id
 * @property double $refund_money
 * @property integer $refund_channel
 * @property string $refund_no
 * @property string $refund_time
 * @property string $create_time
 * @property integer $flag
 * @property string $last_time
 * @property string $refund_order_no
 * @property integer $type
 * @property integer $status
 * @property integer $terminal_type
 * @property string $terminal_id
 * @property integer $refund_reason
 * @property string $refund_tel
 * @property string $refund_remark
 * @property string $refund_img
 * @property integer $order_sku_id
 * @property integer $if_return
 * @property string $refund_address
 * @property integer $user_express
 * @property string $user_express_no
 * @property string $user_remark
 * @property string $user_tel
 * @property string $apply_refund_time
 * @property string $agree_refund_time
 * @property string $refuse_refund_remark
 */
class RefundRecord extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{refund_record}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, merchant_id, store_id, operator_id, operator_admin_id, refund_channel, flag, type, status, terminal_type, refund_reason, order_sku_id, if_return, user_express', 'numerical', 'integerOnly'=>true),
			array('refund_money', 'numerical'),
			array('refund_no, refund_order_no, refund_tel, user_tel', 'length', 'max'=>32),
			array('terminal_id, user_express_no', 'length', 'max'=>50),
			array('refund_remark, refund_img, refund_address, user_remark, refuse_refund_remark', 'length', 'max'=>255),
			array('refund_time, create_time, last_time, apply_refund_time, agree_refund_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, merchant_id, store_id, operator_id, operator_admin_id, refund_money, refund_channel, refund_no, refund_time, create_time, flag, last_time, refund_order_no, type, status, terminal_type, terminal_id, refund_reason, refund_tel, refund_remark, refund_img, order_sku_id, if_return, refund_address, user_express, user_express_no, user_remark, user_tel, apply_refund_time, agree_refund_time, refuse_refund_remark', 'safe', 'on'=>'search'),
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
		    'operator'=>array(self::BELONGS_TO,'Operator','operator_id'),
		    'operator_admin'=>array(self::BELONGS_TO,'Operator','operator_admin_id'),
		    'store' => array(self::BELONGS_TO, 'Order', 'order_id'),
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
			'merchant_id' => 'Merchant',
			'store_id' => 'Store',
			'operator_id' => 'Operator',
			'operator_admin_id' => 'Operator Admin',
			'refund_money' => 'Refund Money',
			'refund_channel' => 'Refund Channel',
			'refund_no' => 'Refund No',
			'refund_time' => 'Refund Time',
			'create_time' => 'Create Time',
			'flag' => 'Flag',
			'last_time' => 'Last Time',
			'refund_order_no' => 'Refund Order No',
			'type' => 'Type',
			'status' => 'Status',
			'terminal_type' => 'Terminal Type',
			'terminal_id' => 'Terminal',
			'refund_reason' => 'Refund Reason',
			'refund_tel' => 'Refund Tel',
			'refund_remark' => 'Refund Remark',
			'refund_img' => 'Refund Img',
			'order_sku_id' => 'Order Sku',
			'if_return' => 'If Return',
			'refund_address' => 'Refund Address',
			'user_express' => 'User Express',
			'user_express_no' => 'User Express No',
			'user_remark' => 'User Remark',
			'user_tel' => 'User Tel',
			'apply_refund_time' => 'Apply Refund Time',
			'agree_refund_time' => 'Agree Refund Time',
			'refuse_refund_remark' => 'Refuse Refund Remark',
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
		$criteria->compare('merchant_id',$this->merchant_id);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('operator_id',$this->operator_id);
		$criteria->compare('operator_admin_id',$this->operator_admin_id);
		$criteria->compare('refund_money',$this->refund_money);
		$criteria->compare('refund_channel',$this->refund_channel);
		$criteria->compare('refund_no',$this->refund_no,true);
		$criteria->compare('refund_time',$this->refund_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('refund_order_no',$this->refund_order_no,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('terminal_type',$this->terminal_type);
		$criteria->compare('terminal_id',$this->terminal_id,true);
		$criteria->compare('refund_reason',$this->refund_reason);
		$criteria->compare('refund_tel',$this->refund_tel,true);
		$criteria->compare('refund_remark',$this->refund_remark,true);
		$criteria->compare('refund_img',$this->refund_img,true);
		$criteria->compare('order_sku_id',$this->order_sku_id);
		$criteria->compare('if_return',$this->if_return);
		$criteria->compare('refund_address',$this->refund_address,true);
		$criteria->compare('user_express',$this->user_express);
		$criteria->compare('user_express_no',$this->user_express_no,true);
		$criteria->compare('user_remark',$this->user_remark,true);
		$criteria->compare('user_tel',$this->user_tel,true);
		$criteria->compare('apply_refund_time',$this->apply_refund_time,true);
		$criteria->compare('agree_refund_time',$this->agree_refund_time,true);
		$criteria->compare('refuse_refund_remark',$this->refuse_refund_remark,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RefundRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
