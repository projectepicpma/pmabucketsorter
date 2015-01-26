<?php

/**
 * This is the model class for table "report".
 *
 * The followings are the available columns in table 'report':
 * @property integer $id
 * @property string $name
 * @property integer $showtwittertopten
 * @property integer $showtwitterdailybreakdown
 */
class Report extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Report the static model class
	 */
	 /*
=========== for using virtual attribute. i am not using it because it is not getting tied to a model so cant access it.=========================
	 public $fromdate;
	 public $todate;
	 
	public function getFromdate(){
        return $this->fromdate;
    }
	 public function getTodate(){
        return $this->todate;
    }
	public function setFromdate($value){
        $this->fromdate = $value;
		//echo '<pre> inside'.$value;die();
    }
	public function setTodate($value){
        $this->todate = $value;
    }
================================for virtual attribute =====================
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
		return 'report';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	  
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('showtwittertopten, showtwitterdailybreakdown, option1, option2, option3', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			array('breakdownfromd, breakdowntod', 'date', 'format'=>'yyyy-M-d', 'message'=>'The format of {attribute} is invalid. The expected format is yyyy-M-d'),
			array('breakdownfromd, breakdowntod', 'safe'),
			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, showtwittertopten, showtwitterdailybreakdown,breakdownfromd, breakdowntod', 'safe', 'on'=>'search'),
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
			'name' => 'Report Name',
			'showtwittertopten' => 'Top Ten Twitter Users',
			'showtwitterdailybreakdown' => 'Daily Twitter Activity',
			'option1' => 'Twitter Category Report',
			'option2' => 'Top Twenty Hashtags',
			'option3' => 'Top Twenty Retweets',
			'breakdownfromd' => 'Date From',
			'breakdowntod' => 'Date To',
			
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
		$criteria->compare('showtwittertopten',$this->showtwittertopten);
		$criteria->compare('showtwitterdailybreakdown',$this->showtwitterdailybreakdown);
		$criteria->compare('breakdownfromd',$this->breakdownfromd);
		$criteria->compare('breakdowntod',$this->breakdowntod);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}