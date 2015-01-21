<?php

/**
 * This is the model class for table "rule".
 *
 * The followings are the available columns in table 'rule':
 * @property integer $id
 * @property integer $categoryid
 * @property integer $movetocategoryid
 * @property integer $ruletype
 * @property string $rulestring
 */
class Rule extends CActiveRecord
{
	/**
	* Id of the div in which the tree will berendered.
	*/
	const ADMIN_TREE_CONTAINER_ID='rule_admin_tree';
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Rule the static model class
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
		return 'rule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('categoryid, movetocategoryid', 'required'),
			array('categoryid, movetocategoryid, ruletype', 'numerical', 'integerOnly'=>true),
			array('rulestring', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, categoryid, movetocategoryid, ruletype, rulestring, eventid', 'safe', 'on'=>'search'),
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
			'categoryid' => 'Categoryid',
			'movetocategoryid' => 'Movetocategoryid',
			'ruletype' => 'Ruletype',
			'rulestring' => 'Rulestring',
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
		$criteria->compare('categoryid',$this->categoryid);
		$criteria->compare('movetocategoryid',$this->movetocategoryid);
		$criteria->compare('ruletype',$this->ruletype);
		$criteria->compare('rulestring',$this->rulestring,true);
		$criteria->compare('eventid',$this->eventid);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}