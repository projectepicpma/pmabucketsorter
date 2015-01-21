<?php

/**
 * This is the model class for table "tweets".
 *
 * The followings are the available columns in table 'tweets':
 * @property integer $id
 * @property string $text
 * @property string $tweetid
 * @property string $touserid
 * @property string $touser
 * @property string $fromuserid
 * @property string $created
 * @property string $fromuser
 */
class Tweets extends CActiveRecord
{
	public $codedforcurrentlevel;
	public $currentlevelroot;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Tweets the static model class
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
		return 'tweets';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		array('id', 'numerical', 'integerOnly'=>true),
		array('text', 'length', 'max'=>141),
		array('tweetid, touserid, touser, fromuserid, fromuser', 'length', 'max'=>20),
		array('created', 'safe'),
		// The following rule is used by search().
		// Please remove those attributes that should not be searched.
		array('currentlevelroot, codedforcurrentlevel, categories, id, text, tweetid, touserid, touser, fromuserid, created, fromuser', 'safe', 'on'=>'search'),
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
			'codings'=>array(self::HAS_MANY, 'Coding', 'tweetid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'text' => 'Tweet Message',
			'tweetid' => 'Tweetid',
			'touserid' => 'Touserid',
			'touser' => 'Touser',
			'fromuserid' => 'Fromuserid',
			'created' => 'Time Sent',
			'fromuser' => 'From User',
			'name' => 'Name',
			'followers' => 'Followers',
			'friends' => 'Following',
			'location' => 'Location',
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

		$criteria->compare('text',$this->text,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('fromuser',$this->fromuser,true);
		$criteria->compare('categories',$this->categories,true);
		
		// sub query to retrieve the count of codings at the current level
		// This is used to determine whether a tweet fits in a particular
		// category or not.
		$coding_table = Coding::model()->tableName();
		
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$level=$user->level;
		//TODO Need to check to see if this coding is for the current level too
		$coding_sql = "(select count(*) from $coding_table where $coding_table.tweetid = t.tweetid and $coding_table.coding = $level)";
		
		// where
		$criteria->compare($coding_sql, $this->codedforcurrentlevel, true);
		
		// sub query to retrieve the count of codings at the current level
		// This is used to determine whether a tweet fits in a particular
		// category or not.
		$tweetevent_table = Tweetevent::model()->tableName();
		
		$eventid=$user->selectedevent;
		//TODO Need to check to see if this coding is for the current event too
		$tweetevent_sql = "(select count(*) from $tweetevent_table where $tweetevent_table.tweetid = t.tweetid and $tweetevent_table.eventid = $eventid)";
		
		// where
		$criteria->compare($tweetevent_sql, $this->currentlevelroot, true);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}