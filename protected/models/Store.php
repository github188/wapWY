<?php

/**
 * This is the model class for table "{{store}}".
 *
 * The followings are the available columns in table '{{store}}':
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $management_id
 * @property string $name
 * @property string $branch_name
 * @property string $number
 * @property string $alipay_store_id
 * @property string $telephone
 * @property string $address
 * @property string $address_code
 * @property string $lng
 * @property string $lat
 * @property string $logo
 * @property string $introduction
 * @property integer $alipay_sync_type
 * @property string $alipay_sync_time
 * @property integer $alipay_sync_verify_status
 * @property string $first_img
 * @property string $first_img_id
 * @property string $open_time
 * @property string $brand
 * @property string $brand_logo
 * @property string $brand_logo_id
 * @property string $phone_num
 * @property double $per_capita
 * @property string $image
 * @property string $image_id
 * @property string $category_id
 * @property string $category
 * @property string $business_license
 * @property string $business_license_id
 * @property string $auth_letter
 * @property string $auth_letter_id
 * @property integer $status
 * @property integer $flag
 * @property integer $is_print
 * @property string $create_time
 * @property string $last_time
 * @property string $print_name
 * @property string $alipay_seller_id
 * @property integer $if_wx_open
 * @property integer $wx_use_pro
 * @property integer $wx_merchant_type
 * @property string $wx_apiclient_cert
 * @property string $wx_apiclient_key
 * @property string $wx_appid
 * @property string $wx_appsecret
 * @property string $wx_api
 * @property string $wx_mchid
 * @property string $t_wx_appid
 * @property string $t_wx_mchid
 * @property integer $if_alipay_open
 * @property integer $alipay_use_pro
 * @property integer $alipay_api_version
 * @property string $alipay_pid
 * @property string $alipay_key
 * @property string $alipay_appid
 * @property string $licence_code
 * @property string $licence_name
 * @property string $business_certificate
 * @property string $business_certificate_id
 * @property string $business_certificate_expires
 * @property string $koubei_store_id
 * @property string $audit_desc
 * @property integer $relation_store_id
 * @property string $relation_right
 */
class Store extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{store}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('merchant_id, management_id, alipay_sync_type, alipay_sync_verify_status, status, flag, is_print, if_wx_open, wx_use_pro, wx_merchant_type, if_alipay_open, alipay_use_pro, alipay_api_version, relation_store_id', 'numerical', 'integerOnly'=>true),
			array('per_capita', 'numerical'),
			array('name, branch_name, logo, wx_appid, wx_appsecret, wx_api, wx_mchid, t_wx_appid, t_wx_mchid, alipay_pid, alipay_key, alipay_appid, koubei_store_id', 'length', 'max'=>50),
			array('number', 'length', 'max'=>30),
			array('alipay_store_id, phone_num, category_id, alipay_seller_id, licence_code, relation_right', 'length', 'max'=>32),
			array('telephone, business_certificate_expires', 'length', 'max'=>20),
			array('address, address_code, open_time, brand, category, print_name, licence_name', 'length', 'max'=>100),
			array('lng, lat', 'length', 'max'=>10),
			array('introduction, first_img, first_img_id, brand_logo, brand_logo_id, image, image_id, business_license, business_license_id, auth_letter, auth_letter_id, business_certificate, business_certificate_id', 'length', 'max'=>255),
			array('wx_apiclient_cert, wx_apiclient_key', 'length', 'max'=>150),
			array('audit_desc', 'length', 'max'=>225),
			array('alipay_sync_time, create_time, last_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, merchant_id, management_id, name, branch_name, number, alipay_store_id, telephone, address, address_code, lng, lat, logo, introduction, alipay_sync_type, alipay_sync_time, alipay_sync_verify_status, first_img, first_img_id, open_time, brand, brand_logo, brand_logo_id, phone_num, per_capita, image, image_id, category_id, category, business_license, business_license_id, auth_letter, auth_letter_id, status, flag, is_print, create_time, last_time, print_name, alipay_seller_id, if_wx_open, wx_use_pro, wx_merchant_type, wx_apiclient_cert, wx_apiclient_key, wx_appid, wx_appsecret, wx_api, wx_mchid, t_wx_appid, t_wx_mchid, if_alipay_open, alipay_use_pro, alipay_api_version, alipay_pid, alipay_key, alipay_appid, licence_code, licence_name, business_certificate, business_certificate_id, business_certificate_expires, koubei_store_id, audit_desc, relation_store_id, relation_right', 'safe', 'on'=>'search'),
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
				'management' => array(self::BELONGS_TO,'Management','management_id'),
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
			'management_id' => 'Management',
			'name' => 'Name',
			'branch_name' => 'Branch Name',
			'number' => 'Number',
			'alipay_store_id' => 'Alipay Store',
			'telephone' => 'Telephone',
			'address' => 'Address',
			'address_code' => 'Address Code',
			'lng' => 'Lng',
			'lat' => 'Lat',
			'logo' => 'Logo',
			'introduction' => 'Introduction',
			'alipay_sync_type' => 'Alipay Sync Type',
			'alipay_sync_time' => 'Alipay Sync Time',
			'alipay_sync_verify_status' => 'Alipay Sync Verify Status',
			'first_img' => 'First Img',
			'first_img_id' => 'First Img',
			'open_time' => 'Open Time',
			'brand' => 'Brand',
			'brand_logo' => 'Brand Logo',
			'brand_logo_id' => 'Brand Logo',
			'phone_num' => 'Phone Num',
			'per_capita' => 'Per Capita',
			'image' => 'Image',
			'image_id' => 'Image',
			'category_id' => 'Category',
			'category' => 'Category',
			'business_license' => 'Business License',
			'business_license_id' => 'Business License',
			'auth_letter' => 'Auth Letter',
			'auth_letter_id' => 'Auth Letter',
			'status' => 'Status',
			'flag' => 'Flag',
			'is_print' => 'Is Print',
			'create_time' => 'Create Time',
			'last_time' => 'Last Time',
			'print_name' => 'Print Name',
			'alipay_seller_id' => 'Alipay Seller',
			'if_wx_open' => 'If Wx Open',
			'wx_use_pro' => 'Wx Use Pro',
			'wx_merchant_type' => 'Wx Merchant Type',
			'wx_apiclient_cert' => 'Wx Apiclient Cert',
			'wx_apiclient_key' => 'Wx Apiclient Key',
			'wx_appid' => 'Wx Appid',
			'wx_appsecret' => 'Wx Appsecret',
			'wx_api' => 'Wx Api',
			'wx_mchid' => 'Wx Mchid',
			't_wx_appid' => 'T Wx Appid',
			't_wx_mchid' => 'T Wx Mchid',
			'if_alipay_open' => 'If Alipay Open',
			'alipay_use_pro' => 'Alipay Use Pro',
			'alipay_api_version' => 'Alipay Api Version',
			'alipay_pid' => 'Alipay Pid',
			'alipay_key' => 'Alipay Key',
			'alipay_appid' => 'Alipay Appid',
			'licence_code' => 'Licence Code',
			'licence_name' => 'Licence Name',
			'business_certificate' => 'Business Certificate',
			'business_certificate_id' => 'Business Certificate',
			'business_certificate_expires' => 'Business Certificate Expires',
			'koubei_store_id' => 'Koubei Store',
			'audit_desc' => 'Audit Desc',
			'relation_store_id' => 'Relation Store',
			'relation_right' => 'Relation Right',
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
		$criteria->compare('management_id',$this->management_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('branch_name',$this->branch_name,true);
		$criteria->compare('number',$this->number,true);
		$criteria->compare('alipay_store_id',$this->alipay_store_id,true);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('address_code',$this->address_code,true);
		$criteria->compare('lng',$this->lng,true);
		$criteria->compare('lat',$this->lat,true);
		$criteria->compare('logo',$this->logo,true);
		$criteria->compare('introduction',$this->introduction,true);
		$criteria->compare('alipay_sync_type',$this->alipay_sync_type);
		$criteria->compare('alipay_sync_time',$this->alipay_sync_time,true);
		$criteria->compare('alipay_sync_verify_status',$this->alipay_sync_verify_status);
		$criteria->compare('first_img',$this->first_img,true);
		$criteria->compare('first_img_id',$this->first_img_id,true);
		$criteria->compare('open_time',$this->open_time,true);
		$criteria->compare('brand',$this->brand,true);
		$criteria->compare('brand_logo',$this->brand_logo,true);
		$criteria->compare('brand_logo_id',$this->brand_logo_id,true);
		$criteria->compare('phone_num',$this->phone_num,true);
		$criteria->compare('per_capita',$this->per_capita);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('image_id',$this->image_id,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('business_license',$this->business_license,true);
		$criteria->compare('business_license_id',$this->business_license_id,true);
		$criteria->compare('auth_letter',$this->auth_letter,true);
		$criteria->compare('auth_letter_id',$this->auth_letter_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('is_print',$this->is_print);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_time',$this->last_time,true);
		$criteria->compare('print_name',$this->print_name,true);
		$criteria->compare('alipay_seller_id',$this->alipay_seller_id,true);
		$criteria->compare('if_wx_open',$this->if_wx_open);
		$criteria->compare('wx_use_pro',$this->wx_use_pro);
		$criteria->compare('wx_merchant_type',$this->wx_merchant_type);
		$criteria->compare('wx_apiclient_cert',$this->wx_apiclient_cert,true);
		$criteria->compare('wx_apiclient_key',$this->wx_apiclient_key,true);
		$criteria->compare('wx_appid',$this->wx_appid,true);
		$criteria->compare('wx_appsecret',$this->wx_appsecret,true);
		$criteria->compare('wx_api',$this->wx_api,true);
		$criteria->compare('wx_mchid',$this->wx_mchid,true);
		$criteria->compare('t_wx_appid',$this->t_wx_appid,true);
		$criteria->compare('t_wx_mchid',$this->t_wx_mchid,true);
		$criteria->compare('if_alipay_open',$this->if_alipay_open);
		$criteria->compare('alipay_use_pro',$this->alipay_use_pro);
		$criteria->compare('alipay_api_version',$this->alipay_api_version);
		$criteria->compare('alipay_pid',$this->alipay_pid,true);
		$criteria->compare('alipay_key',$this->alipay_key,true);
		$criteria->compare('alipay_appid',$this->alipay_appid,true);
		$criteria->compare('licence_code',$this->licence_code,true);
		$criteria->compare('licence_name',$this->licence_name,true);
		$criteria->compare('business_certificate',$this->business_certificate,true);
		$criteria->compare('business_certificate_id',$this->business_certificate_id,true);
		$criteria->compare('business_certificate_expires',$this->business_certificate_expires,true);
		$criteria->compare('koubei_store_id',$this->koubei_store_id,true);
		$criteria->compare('audit_desc',$this->audit_desc,true);
		$criteria->compare('relation_store_id',$this->relation_store_id);
		$criteria->compare('relation_right',$this->relation_right,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Store the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
