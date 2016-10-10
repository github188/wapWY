<?php

/**
 * This is the model class for table "{{merchantinfo}}".
 *
 * The followings are the available columns in table '{{merchantinfo}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $type
 * @property string $img
 * @property string $address
 * @property string $industry
 * @property string $fax
 * @property string $zip_code
 * @property string $contact
 * @property string $register_money
 * @property integer $income
 * @property integer $employees_num
 * @property integer $is_qs
 * @property integer $signed_intention
 * @property integer $business_area
 * @property integer $customer_groups
 * @property string $wx_contact
 * @property string $wx_tel
 * @property string $wx_email
 * @property string $wx_abbreviation
 * @property string $wx_business_category
 * @property string $wx_qualifications
 * @property string $wx_product_description
 * @property string $wx_customer_service
 * @property string $wx_company_website
 * @property string $wx_supply
 * @property string $wx_merchant_name
 * @property string $wx_registered_address
 * @property string $wx_business_license_no
 * @property string $wx_operating_range
 * @property string $wx_business_deadline_start
 * @property string $wx_business_deadline_end
 * @property integer $wx_business_deadline_longterm
 * @property string $wx_business_license_img
 * @property string $wx_organization_code
 * @property string $wx_organization_code_start
 * @property string $wx_organization_code_end
 * @property integer $wx_organization_code_longterm
 * @property string $wx_organization_code_img
 * @property integer $wx_credentials_user_type
 * @property string $wx_credentials_user_name
 * @property integer $wx_credentials_type
 * @property string $wx_credentials_positive
 * @property string $wx_credentials_opposite
 * @property string $wx_credentials_start
 * @property string $wx_credentials_end
 * @property integer $wx_credentials_longterm
 * @property string $wx_credentials_no
 * @property integer $wx_account_type
 * @property integer $wx_bank_name
 * @property string $wx_bank_area
 * @property string $wx_bank_subbranch
 * @property string $wx_bank_account
 * @property string $wx_account_name
 * @property string $wx_jsydgh_license
 * @property string $wx_jsgcgh_license
 * @property string $wx_jzgckg_license
 * @property string $wx_gytd_license
 * @property string $wx_spfys_license
 * @property string $wx_wwpm_license
 * @property string $wx_wnjy_license
 * @property string $wx_frdj_license
 */
class Merchantinfo extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{merchantinfo}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, type, income, employees_num, is_qs, signed_intention, business_area, customer_groups, wx_business_deadline_longterm, wx_organization_code_longterm, wx_credentials_user_type, wx_credentials_type, wx_credentials_longterm, wx_account_type, wx_bank_name', 'numerical', 'integerOnly'=>true),
			array('img, wx_product_description, wx_bank_subbranch', 'length', 'max'=>255),
			array('address, industry, contact, wx_abbreviation, wx_company_website, wx_merchant_name, wx_registered_address, wx_account_name', 'length', 'max'=>100),
			array('fax, wx_contact, wx_tel, wx_email, wx_business_category, wx_customer_service, wx_business_license_no, wx_organization_code, wx_credentials_user_name, wx_credentials_no, wx_bank_area, wx_bank_account', 'length', 'max'=>32),
			array('zip_code', 'length', 'max'=>16),
			array('register_money', 'length', 'max'=>10),
			array('wx_qualifications, wx_supply, wx_business_license_img, wx_organization_code_img, wx_credentials_positive, wx_credentials_opposite, wx_jsydgh_license, wx_jsgcgh_license, wx_jzgckg_license, wx_gytd_license, wx_spfys_license, wx_wwpm_license, wx_wnjy_license, wx_frdj_license', 'length', 'max'=>50),
			array('wx_operating_range', 'length', 'max'=>225),
			array('wx_business_deadline_start, wx_business_deadline_end, wx_organization_code_start, wx_organization_code_end, wx_credentials_start, wx_credentials_end', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, type, img, address, industry, fax, zip_code, contact, register_money, income, employees_num, is_qs, signed_intention, business_area, customer_groups, wx_contact, wx_tel, wx_email, wx_abbreviation, wx_business_category, wx_qualifications, wx_product_description, wx_customer_service, wx_company_website, wx_supply, wx_merchant_name, wx_registered_address, wx_business_license_no, wx_operating_range, wx_business_deadline_start, wx_business_deadline_end, wx_business_deadline_longterm, wx_business_license_img, wx_organization_code, wx_organization_code_start, wx_organization_code_end, wx_organization_code_longterm, wx_organization_code_img, wx_credentials_user_type, wx_credentials_user_name, wx_credentials_type, wx_credentials_positive, wx_credentials_opposite, wx_credentials_start, wx_credentials_end, wx_credentials_longterm, wx_credentials_no, wx_account_type, wx_bank_name, wx_bank_area, wx_bank_subbranch, wx_bank_account, wx_account_name, wx_jsydgh_license, wx_jsgcgh_license, wx_jzgckg_license, wx_gytd_license, wx_spfys_license, wx_wwpm_license, wx_wnjy_license, wx_frdj_license', 'safe', 'on'=>'search'),
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
            'merchant' => array(self::BELONGS_TO,'Merchant','merchant_id'),
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
			'type' => 'Type',
			'img' => 'Img',
			'address' => 'Address',
			'industry' => 'Industry',
			'fax' => 'Fax',
			'zip_code' => 'Zip Code',
			'contact' => 'Contact',
			'register_money' => 'Register Money',
			'income' => 'Income',
			'employees_num' => 'Employees Num',
			'is_qs' => 'Is Qs',
			'signed_intention' => 'Signed Intention',
			'business_area' => 'Business Area',
			'customer_groups' => 'Customer Groups',
			'wx_contact' => 'Wx Contact',
			'wx_tel' => 'Wx Tel',
			'wx_email' => 'Wx Email',
			'wx_abbreviation' => 'Wx Abbreviation',
			'wx_business_category' => 'Wx Business Category',
			'wx_qualifications' => 'Wx Qualifications',
			'wx_product_description' => 'Wx Product Description',
			'wx_customer_service' => 'Wx Customer Service',
			'wx_company_website' => 'Wx Company Website',
			'wx_supply' => 'Wx Supply',
			'wx_merchant_name' => 'Wx Merchant Name',
			'wx_registered_address' => 'Wx Registered Address',
			'wx_business_license_no' => 'Wx Business License No',
			'wx_operating_range' => 'Wx Operating Range',
			'wx_business_deadline_start' => 'Wx Business Deadline Start',
			'wx_business_deadline_end' => 'Wx Business Deadline End',
			'wx_business_deadline_longterm' => 'Wx Business Deadline Longterm',
			'wx_business_license_img' => 'Wx Business License Img',
			'wx_organization_code' => 'Wx Organization Code',
			'wx_organization_code_start' => 'Wx Organization Code Start',
			'wx_organization_code_end' => 'Wx Organization Code End',
			'wx_organization_code_longterm' => 'Wx Organization Code Longterm',
			'wx_organization_code_img' => 'Wx Organization Code Img',
			'wx_credentials_user_type' => 'Wx Credentials User Type',
			'wx_credentials_user_name' => 'Wx Credentials User Name',
			'wx_credentials_type' => 'Wx Credentials Type',
			'wx_credentials_positive' => 'Wx Credentials Positive',
			'wx_credentials_opposite' => 'Wx Credentials Opposite',
			'wx_credentials_start' => 'Wx Credentials Start',
			'wx_credentials_end' => 'Wx Credentials End',
			'wx_credentials_longterm' => 'Wx Credentials Longterm',
			'wx_credentials_no' => 'Wx Credentials No',
			'wx_account_type' => 'Wx Account Type',
			'wx_bank_name' => 'Wx Bank Name',
			'wx_bank_area' => 'Wx Bank Area',
			'wx_bank_subbranch' => 'Wx Bank Subbranch',
			'wx_bank_account' => 'Wx Bank Account',
			'wx_account_name' => 'Wx Account Name',
			'wx_jsydgh_license' => 'Wx Jsydgh License',
			'wx_jsgcgh_license' => 'Wx Jsgcgh License',
			'wx_jzgckg_license' => 'Wx Jzgckg License',
			'wx_gytd_license' => 'Wx Gytd License',
			'wx_spfys_license' => 'Wx Spfys License',
			'wx_wwpm_license' => 'Wx Wwpm License',
			'wx_wnjy_license' => 'Wx Wnjy License',
			'wx_frdj_license' => 'Wx Frdj License',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('img',$this->img,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('industry',$this->industry,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('zip_code',$this->zip_code,true);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('register_money',$this->register_money,true);
		$criteria->compare('income',$this->income);
		$criteria->compare('employees_num',$this->employees_num);
		$criteria->compare('is_qs',$this->is_qs);
		$criteria->compare('signed_intention',$this->signed_intention);
		$criteria->compare('business_area',$this->business_area);
		$criteria->compare('customer_groups',$this->customer_groups);
		$criteria->compare('wx_contact',$this->wx_contact,true);
		$criteria->compare('wx_tel',$this->wx_tel,true);
		$criteria->compare('wx_email',$this->wx_email,true);
		$criteria->compare('wx_abbreviation',$this->wx_abbreviation,true);
		$criteria->compare('wx_business_category',$this->wx_business_category,true);
		$criteria->compare('wx_qualifications',$this->wx_qualifications,true);
		$criteria->compare('wx_product_description',$this->wx_product_description,true);
		$criteria->compare('wx_customer_service',$this->wx_customer_service,true);
		$criteria->compare('wx_company_website',$this->wx_company_website,true);
		$criteria->compare('wx_supply',$this->wx_supply,true);
		$criteria->compare('wx_merchant_name',$this->wx_merchant_name,true);
		$criteria->compare('wx_registered_address',$this->wx_registered_address,true);
		$criteria->compare('wx_business_license_no',$this->wx_business_license_no,true);
		$criteria->compare('wx_operating_range',$this->wx_operating_range,true);
		$criteria->compare('wx_business_deadline_start',$this->wx_business_deadline_start,true);
		$criteria->compare('wx_business_deadline_end',$this->wx_business_deadline_end,true);
		$criteria->compare('wx_business_deadline_longterm',$this->wx_business_deadline_longterm);
		$criteria->compare('wx_business_license_img',$this->wx_business_license_img,true);
		$criteria->compare('wx_organization_code',$this->wx_organization_code,true);
		$criteria->compare('wx_organization_code_start',$this->wx_organization_code_start,true);
		$criteria->compare('wx_organization_code_end',$this->wx_organization_code_end,true);
		$criteria->compare('wx_organization_code_longterm',$this->wx_organization_code_longterm);
		$criteria->compare('wx_organization_code_img',$this->wx_organization_code_img,true);
		$criteria->compare('wx_credentials_user_type',$this->wx_credentials_user_type);
		$criteria->compare('wx_credentials_user_name',$this->wx_credentials_user_name,true);
		$criteria->compare('wx_credentials_type',$this->wx_credentials_type);
		$criteria->compare('wx_credentials_positive',$this->wx_credentials_positive,true);
		$criteria->compare('wx_credentials_opposite',$this->wx_credentials_opposite,true);
		$criteria->compare('wx_credentials_start',$this->wx_credentials_start,true);
		$criteria->compare('wx_credentials_end',$this->wx_credentials_end,true);
		$criteria->compare('wx_credentials_longterm',$this->wx_credentials_longterm);
		$criteria->compare('wx_credentials_no',$this->wx_credentials_no,true);
		$criteria->compare('wx_account_type',$this->wx_account_type);
		$criteria->compare('wx_bank_name',$this->wx_bank_name);
		$criteria->compare('wx_bank_area',$this->wx_bank_area,true);
		$criteria->compare('wx_bank_subbranch',$this->wx_bank_subbranch,true);
		$criteria->compare('wx_bank_account',$this->wx_bank_account,true);
		$criteria->compare('wx_account_name',$this->wx_account_name,true);
		$criteria->compare('wx_jsydgh_license',$this->wx_jsydgh_license,true);
		$criteria->compare('wx_jsgcgh_license',$this->wx_jsgcgh_license,true);
		$criteria->compare('wx_jzgckg_license',$this->wx_jzgckg_license,true);
		$criteria->compare('wx_gytd_license',$this->wx_gytd_license,true);
		$criteria->compare('wx_spfys_license',$this->wx_spfys_license,true);
		$criteria->compare('wx_wwpm_license',$this->wx_wwpm_license,true);
		$criteria->compare('wx_wnjy_license',$this->wx_wnjy_license,true);
		$criteria->compare('wx_frdj_license',$this->wx_frdj_license,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Merchantinfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
