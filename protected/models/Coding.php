<?php

/**
 * This is the model class for table "coding".
 *
 * The followings are the available columns in table 'coding':
 * @property string $tweetid
 * @property integer $id
 * @property integer $coding
 * @property integer $userid
 * @property integer $level
 *
 * The followings are the available model relations:
 * @property User $user
 */
class Coding extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Coding the static model class
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
		return 'coding';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		array('userid', 'required'),
		array('coding, userid, level', 'numerical', 'integerOnly'=>true),
		array('tweetid', 'length', 'max'=>20),
		// The following rule is used by search().
		// Please remove those attributes that should not be searched.
		array('tweetid, id, coding, userid, level', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'userid'),
			'tweet' => array(self::BELONGS_TO, 'Tweets', 'tweetid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'tweetid' => 'Tweetid',
			'id' => 'ID',
			'coding' => 'Coding',
			'userid' => 'Userid',
			'level' => 'Level',
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

		$criteria->compare('tweetid',$this->tweetid,true);
		$criteria->compare('id',$this->id);
		$criteria->compare('coding',$this->coding);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('level',$this->level);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}