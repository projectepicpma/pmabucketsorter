<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @property string $fullname
 * @property string $password
 * @property string $salt
 * @property string $email
 * @property string $profile
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		array('username, fullname, password', 'required'),
		array('username, fullname, password, salt, email', 'length', 'max'=>128),
		array('profile', 'safe'),
		// The following rule is used by search().
		// Please remove those attributes that should not be searched.
		array('id, username, fullname, password, salt, email, profile, role', 'safe', 'on'=>'search'),
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
		'role' => array(self::BELONGS_TO, 'Roletypes', 'id'),
		'codings' => array(self::HAS_MANY, 'Coding', 'userid'),
			
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'fullname' => 'Fullname',
			'password' => 'Password',
			'salt' => 'Salt',
			'email' => 'Email',
			'profile' => 'Profile',
			'role' => 'Role',
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('fullname',$this->fullname,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('profile',$this->profile,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function validatePassword($password)
	{
		return $this->hashPassword($password,$this->salt)===$this->password;
	}

	public function hashPassword($password,$salt)
	{
		return md5($salt.$password);
	}
	
	/**
	* Generates a salt that can be used to generate a password hash.
	* @return string the salt
	*/
	public function generateSalt()
	{
		return uniqid('',true);
	}
	
	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$this->salt = $this->generateSalt();
				$this->password = $this->hashPassword($this->password, $this->salt);
			}
			return true;
		}
		else
		return false;
	}
	
	public function getUserSelectedEvent()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		if (isset($user))
		return $user->selectedevent;
	
	}
	
	/**
	* Return a list of all categories sorted by name as 'id' and 'name' pairs, this is to
	* be used for a dropboxlist
	* @return list of all Category 'id' and 'name' pairs
	*/
	public static function loadUsers()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$event= Event::model()->find('id=?', array($user->selectedevent));
	
		//$sql = "SELECT * FROM user where root=:rootcategoryid order by lft";
		//$params=array(':rootcategoryid'=>$event->rootcategoryid);
	
		//$models=self::model()->findAllBySql($sql, $params);
		$models=self::model()->findAll();
		
		return CHtml::listData($models, 'id', 'fullname');
	}
	public function searchForAdding()
	{
		
		$userSearch = $this->search();
		$userEventModel = new Userevent;
		$userEventSearch = $userEventModel->searchBasedOnId(User::model()->findByPk(Yii::app()->user->getId())->selectedevent);
		$userArray=array();
		$userEventArray=array();
		for($i=0 ; $i<$userSearch->totalItemCount;$i++)
		{
			$a = $userSearch->data[$i]->id;
			array_push($userArray, $a);
			//echo 'user array = '.$userArray[$i]; for debugging
		}
		for($b=0;$b<$userEventSearch->totalItemCount;$b++)
		{
			$c = $userEventSearch->data[$b]->userid;
			array_push($userEventArray,$c);
			//echo 'user event array = '.$userEventArray[$b]; for debugging
		}
		
		for($s = 0 ; $s < sizeof($userEventArray) ; $s++)
		{
			for($t = 0 ; $t < sizeof($userArray) ; $t++)
			{
				if($userArray[$t] == $userEventArray[$s])
				{
					unset($userArray[$t]);
					$userArray = array_values($userArray);
					$t--;
				}
			}
		}
		$records = array();
		for($z = 0; $z<sizeof($userArray) ; $z++)
		{
			for($y = 0 ; $y < $userSearch->totalItemCount ; $y++)
			{
				if($userArray[$z] == $userSearch->data[$y]->id)
				array_push($records,$userSearch->data[$y]);
			}
		}
		
	
		
		return new CArrayDataProvider($records);
	}
}