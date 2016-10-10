<?php

/**
 * This is the model class for table "{{agent}}".
 *
 * The followings are the available columns in table '{{agent}}':
 * @property integer $id
 * @property integer $pid
 * @property integer $ppid
 * @property string $gid
 * @property string $name
 * @property string $account
 * @property string $pwd
 * @property string $business_license
 * @property string $address
 * @property string $legal_person
 * @property string $legal_person_id
 * @property string $legal_person_id_card_positive
 * @property string $legal_person_id_card_opposite
 * @property string $contact_name
 * @property string $contact
 * @property string $contact_email
 * @property integer $partner
 * @property integer $league_type
 * @property integer $store_num
 * @property integer $league_fee_type
 * @property double $league_fee
 * @property double $cash_deposit
 * @property double $modify_fee
 * @property string $league_start_time
 * @property string $league_end_time
 * @property double $directly_operated_ratio
 * @property double $first_level_ratio
 * @property double $second_level_ratio
 * @property string $remark
 * @property integer $status
 * @property integer $flag
 * @property string $create_time
 * @property string $last_time
 * @property string $login_time
 * @property string $login_ip
 * @property integer $points
 * @property integer $if_subaccount
 * @property double $withdraw_cash
 * @property double $discount
 * @property string $e_mail
 * @property integer $cooperation_grade
 * @property string $cooperation_area
 * @property string $contact_person
 * @property integer $cooperation_type
 * @property string $img
 * @property string $company_address
 * @property string $company_scale
 * @property string $scope_of_business
 * @property string $business_resources
 * @property integer $agent_type
 * @property integer $audit_status
 * @property string $reject_remark
 * @property integer $if_recommend
 * @property integer $role
 * @property integer $if_show_old
 * @property string $activation_code
 * @property integer $activation_code_status
 * @property integer $pay_type
 * @property integer $invoice_type
 * @property string $invoice_title
 * @property string $invoice_content
 * @property string $invoice_address
 * @property string $invoice_person
 * @property string $invoice_phone
 * @property string $remittance_name
 * @property string $remittance_account
 * @property string $remittance_bank
 * @property integer $smgr_id
 * @property integer $contract_status
 * @property integer $team_num
 * @property string $scope_of_service
 * @property string $advantage
 * @property string $agent_no
 */
class Agent extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{agent}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, ppid, partner, league_type, store_num, league_fee_type, status, flag, points, if_subaccount, cooperation_grade, cooperation_type, agent_type, audit_status, if_recommend, role, if_show_old, activation_code_status, pay_type, invoice_type, smgr_id, contract_status, team_num', 'numerical', 'integerOnly'=>true),
			array('league_fee, cash_deposit, modify_fee, directly_operated_ratio, first_level_ratio, second_level_ratio, withdraw_cash, discount', 'numerical'),
			array('gid, remark, reject_remark, invoice_address', 'length', 'max'=>255),
			array('name, account, contact', 'length', 'max'=>50),
			array('pwd, legal_person, legal_person_id, contact_name, contact_email, login_ip, e_mail, contact_person, activation_code, invoice_person, invoice_phone, agent_no', 'length', 'max'=>32),
			array('business_license, address, legal_person_id_card_positive, legal_person_id_card_opposite, cooperation_area, img, company_address, invoice_title, remittance_name, remittance_account, remittance_bank', 'length', 'max'=>100),
			array('company_scale, scope_of_business, business_resources, scope_of_service, advantage', 'length', 'max'=>1000),
			array('invoice_content', 'length', 'max'=>225),
			array('league_start_time, league_end_time, create_time, last_time, login_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pid, ppid, gid, name, account, pwd, business_license, address, legal_person, legal_person_id, legal_person_id_card_positive, legal_person_id_card_opposite, contact_name, contact, contact_email, partner, league_type, store_num, league_fee_type, league_fee, cash_deposit, modify_fee, league_start_time, league_end_time, directly_operated_ratio, first_level_ratio, second_level_ratio, remark, status, flag, create_time, last_time, login_time, login_ip, points, if_subaccount, withdraw_cash, discount, e_mail, cooperation_grade, cooperation_area, contact_person, cooperation_type, img, company_address, company_scale, scope_of_business, business_resources, agent_type, audit_status, reject_remark, if_recommend, role, if_show_old, activation_code, activation_code_status, pay_type, invoice_type, invoice_title, invoice_content, invoice_address, invoice_person, invoice_phone, remittance_name, remittance_account, remittance_bank, smgr_id, contract_status, team_num, scope_of_service, advantage, agent_no', 'safe', 'on'=>'search'),
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
		    'smgr' => array(self::BELONGS_TO,'Smgr','smgr_id'),
		    'agentOrder' => array(self::HAS_ONE, 'AgentOrder', 'agent_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pid' => 'Pid',
			'ppid' => 'Ppid',
			'gid' => 'Gid',
			'name' => 'Name',
			'account' => 'Account',
			'pwd' => 'Pwd',
			'business_license' => 'Business License',
			'address' => 'Address',
			'legal_person' => 'Legal Person',
			'legal_person_id' => 'Legal Person',
			'legal_person_id_card_positive' => 'Legal Person Id Card Positive',
			'legal_person_id_card_opposite' => 'Legal Person Id Card Opposite',
			'contact_name' => 'Contact Name',
			'contact' => 'Contact',
			'contact_email' => 'Contact Email',
			'partner' => 'Partner',
			'league_type' => 'League Type',
			'store_num' => 'Store Num',
			'league_fee_type' => 'League Fee Type',
			'league_fee' => 'League Fee',
			'cash_deposit' => 'Cash Deposit',
			'modify_fee' => 'Modify Fee',
			'league_start_time' => 'League Start Time',
			'league_end_time' => 'League End Time',
			'directly_operated_ratio' => 'Directly Operated Ratio',
			'first_level_ratio' => 'First Level Ratio',
			'second_level_ratio' => 'Second Level Ratio',
			'remark' => 'Remark',
			'status' => 'Status',
			'flag' => 'Flag',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'login_time' => 'Login Time',
			'login_ip' => 'Login Ip',
			'points' => 'Points',
			'if_subaccount' => 'If Subaccount',
			'withdraw_cash' => 'Withdraw Cash',
			'discount' => 'Discount',
			'e_mail' => 'E Mail',
			'cooperation_grade' => 'Cooperation Grade',
			'cooperation_area' => 'Cooperation Area',
			'contact_person' => 'Contact Person',
			'cooperation_type' => 'Cooperation Type',
			'img' => 'Img',
			'company_address' => 'Company Address',
			'company_scale' => 'Company Scale',
			'scope_of_business' => 'Scope Of Business',
			'business_resources' => 'Business Resources',
			'agent_type' => 'Agent Type',
			'audit_status' => 'Audit Status',
			'reject_remark' => 'Reject Remark',
			'if_recommend' => 'If Recommend',
			'role' => 'Role',
			'if_show_old' => 'If Show Old',
			'activation_code' => 'Activation Code',
			'activation_code_status' => 'Activation Code Status',
			'pay_type' => 'Pay Type',
			'invoice_type' => 'Invoice Type',
			'invoice_title' => 'Invoice Title',
			'invoice_content' => 'Invoice Content',
			'invoice_address' => 'Invoice Address',
			'invoice_person' => 'Invoice Person',
			'invoice_phone' => 'Invoice Phone',
			'remittance_name' => 'Remittance Name',
			'remittance_account' => 'Remittance Account',
			'remittance_bank' => 'Remittance Bank',
			'smgr_id' => 'Smgr',
			'contract_status' => 'Contract Status',
			'team_num' => 'Team Num',
			'scope_of_service' => 'Scope Of Service',
			'advantage' => 'Advantage',
			'agent_no' => 'Agent No',
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
		$criteria->compare('pid',$this->pid);
		$criteria->compare('ppid',$this->ppid);
		$criteria->compare('gid',$this->gid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('account',$this->account,true);
		$criteria->compare('pwd',$this->pwd,true);
		$criteria->compare('business_license',$this->business_license,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('legal_person',$this->legal_person,true);
		$criteria->compare('legal_person_id',$this->legal_person_id,true);
		$criteria->compare('legal_person_id_card_positive',$this->legal_person_id_card_positive,true);
		$criteria->compare('legal_person_id_card_opposite',$this->legal_person_id_card_opposite,true);
		$criteria->compare('contact_name',$this->contact_name,true);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('contact_email',$this->contact_email,true);
		$criteria->compare('partner',$this->partner);
		$criteria->compare('league_type',$this->league_type);
		$criteria->compare('store_num',$this->store_num);
		$criteria->compare('league_fee_type',$this->league_fee_type);
		$criteria->compare('league_fee',$this->league_fee);
		$criteria->compare('cash_deposit',$this->cash_deposit);
		$criteria->compare('modify_fee',$this->modify_fee);
		$criteria->compare('league_start_time',$this->league_start_time,true);
		$criteria->compare('league_end_time',$this->league_end_time,true);
		$criteria->compare('directly_operated_ratio',$this->directly_operated_ratio);
		$criteria->compare('first_level_ratio',$this->first_level_ratio);
		$criteria->compare('second_level_ratio',$this->second_level_ratio);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('login_time',$this->login_time,true);
		$criteria->compare('login_ip',$this->login_ip,true);
		$criteria->compare('points',$this->points);
		$criteria->compare('if_subaccount',$this->if_subaccount);
		$criteria->compare('withdraw_cash',$this->withdraw_cash);
		$criteria->compare('discount',$this->discount);
		$criteria->compare('e_mail',$this->e_mail,true);
		$criteria->compare('cooperation_grade',$this->cooperation_grade);
		$criteria->compare('cooperation_area',$this->cooperation_area,true);
		$criteria->compare('contact_person',$this->contact_person,true);
		$criteria->compare('cooperation_type',$this->cooperation_type);
		$criteria->compare('img',$this->img,true);
		$criteria->compare('company_address',$this->company_address,true);
		$criteria->compare('company_scale',$this->company_scale,true);
		$criteria->compare('scope_of_business',$this->scope_of_business,true);
		$criteria->compare('business_resources',$this->business_resources,true);
		$criteria->compare('agent_type',$this->agent_type);
		$criteria->compare('audit_status',$this->audit_status);
		$criteria->compare('reject_remark',$this->reject_remark,true);
		$criteria->compare('if_recommend',$this->if_recommend);
		$criteria->compare('role',$this->role);
		$criteria->compare('if_show_old',$this->if_show_old);
		$criteria->compare('activation_code',$this->activation_code,true);
		$criteria->compare('activation_code_status',$this->activation_code_status);
		$criteria->compare('pay_type',$this->pay_type);
		$criteria->compare('invoice_type',$this->invoice_type);
		$criteria->compare('invoice_title',$this->invoice_title,true);
		$criteria->compare('invoice_content',$this->invoice_content,true);
		$criteria->compare('invoice_address',$this->invoice_address,true);
		$criteria->compare('invoice_person',$this->invoice_person,true);
		$criteria->compare('invoice_phone',$this->invoice_phone,true);
		$criteria->compare('remittance_name',$this->remittance_name,true);
		$criteria->compare('remittance_account',$this->remittance_account,true);
		$criteria->compare('remittance_bank',$this->remittance_bank,true);
		$criteria->compare('smgr_id',$this->smgr_id);
		$criteria->compare('contract_status',$this->contract_status);
		$criteria->compare('team_num',$this->team_num);
		$criteria->compare('scope_of_service',$this->scope_of_service,true);
		$criteria->compare('advantage',$this->advantage,true);
		$criteria->compare('agent_no',$this->agent_no,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Agent the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
