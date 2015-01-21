<?php

/**
 * This is the model class for table "userevent".
 *
 * The followings are the available columns in table 'userevent':
 * @property integer $userid
 * @property integer $eventid
 * @property integer $id
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property User $user
 */
class Userevent extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Userevent the static model class
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
		return 'userevent';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userid, eventid', 'required'),
			array('userid, eventid', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('userid, eventid, id', 'safe', 'on'=>'search'),
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
			'event' => array(self::BELONGS_TO, 'Event', 'eventid'),
			'user' => array(self::BELONGS_TO, 'User', 'userid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'userid' => 'Userid',
			'eventid' => 'Eventid',
			'id' => 'ID',
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

		$criteria->compare('userid',$this->userid);
		$criteria->compare('eventid',$this->eventid);
		//$criteria->compare('id',$this->id);

		// sub query to retrieve the user event pairings for the current event
		$userevent_table = Userevent::model()->tableName();
		
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$eventid=$user->selectedevent;
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function searchBasedOnId($id)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('eventid',$id);
		//$criteria->compare('id',$this->id);

		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}