<?php

/**
 * This is the model class for table "RealEstateOffices".
 *
 * The followings are the available columns in table 'RealEstateOffices':
 * @property string $RealEstateID
 * @property string $CommercialName
 * @property integer $OwnerName
 * @property string $RegisteredDate
 * @property string $ExpiryDate
 * @property string $Address
 * @property string $MobilePhone
 * @property string $Email
 *
 * The followings are the available model relations:
 * @property ContractsDetail[] $contractsDetails
 * @property CustomerMaster $ownerName
 */
class RealEstateOffices extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RealEstateOffices the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'RealEstateOffices';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		//	array('RealEstateID, CommercialName, OwnerName, RegisteredDate, ExpiryDate, Address, MobilePhone, Email', 'required'),
		//	array('OwnerName', 'numerical', 'integerOnly'=>true),
		//	array('RealEstateID', 'length', 'max'=>15),
		//	array('CommercialName', 'length', 'max'=>100),
		//	array('Address', 'length', 'max'=>200),
		//	array('MobilePhone', 'length', 'max'=>10),
		//	array('Email', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('RealEstateID, CommercialName, OwnerName, RegisteredDate, ExpiryDate, Address, MobilePhone, Email', 'safe', 'on'=>'search'),
			array('RealEstateID, CommercialName, OwnerName, RegisteredDate, ExpiryDate, Address, MobilePhone, Email', 'safe'),
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
			'contractsDetails' => array(self::HAS_MANY, 'ContractsDetail', 'RealEstateID'),
			'ownerName' => array(self::BELONGS_TO, 'CustomerMaster', 'OwnerName'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'RealEstateID' => 'رقم المكتب',
			'CommercialName' => 'الاسم التجاري',
			'OwnerName' => 'اسم المالك',
			'RegisteredDate' => 'تاريخ التسجيل',
			'ExpiryDate' => 'تاريخ انتهاء',
			'Address' => 'العنوان',
			'MobilePhone' => 'رقم الموبيل',
			'Email' => 'البريد الإلكتروني',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('RealEstateID',$this->RealEstateID,true);
		$criteria->compare('CommercialName',$this->CommercialName,true);
		$criteria->compare('OwnerName',$this->OwnerName);
		$criteria->compare('RegisteredDate',$this->RegisteredDate,true);
		$criteria->compare('ExpiryDate',$this->ExpiryDate,true);
		$criteria->compare('Address',$this->Address,true);
		$criteria->compare('MobilePhone',$this->MobilePhone,true);
		$criteria->compare('Email',$this->Email,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
