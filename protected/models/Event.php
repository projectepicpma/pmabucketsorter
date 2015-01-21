<?php

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property integer $id
 * @property string $name
 * @property string $location
 * @property string $startdate
 * @property string $enddate
 * @property string $summary
 * @property integer $rootcategoryid
 */
class Event extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Event the static model class
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
		return 'event';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, rootcategoryid', 'required'),
			array('rootcategoryid', 'numerical', 'integerOnly'=>true),
			array('name, location', 'length', 'max'=>128),
			array('startdate, enddate', 'date', 'format'=>'yyyy-M-d', 'message'=>'The format of {attribute} is invalid. The expected format is yyyy-M-d'),
			array('summary', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, location, startdate, enddate, summary, rootcategoryid', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'location' => 'Location',
			'startdate' => 'Start Date',
			'enddate' => 'End Date',
			'summary' => 'Summary',
			'rootcategoryid' => 'Rootcategoryid',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('startdate',$this->startdate,true);
		$criteria->compare('enddate',$this->enddate,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('rootcategoryid',$this->rootcategoryid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	* Return a list of all events sorted by name as 'id' and 'name' pairs, this is to
	* be used for a dropboxlist
	* @return list of all Event 'id' and 'name' pairs
	*/
	public static function loadEvents()
	{
		$models=self::model()->findAll(
		array('order'=> 'name'));
		return CHtml::listData($models, 'id', 'name');
	}
	
	public static function loadEventsByUser()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		
		$sql = "select t1.id, t1.name from event t1 INNER JOIN userevent t2 on t1.id=t2.eventid where t2.userid=:userid";
		$params=array(':userid'=>$user->id);
		
		$models=self::model()->findAllBySql($sql, $params);
		
		$list=CHtml::listData($models, 'id', 'name');
		return $list;
	}
}