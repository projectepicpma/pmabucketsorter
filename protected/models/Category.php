<?php

/**
 * This is the Nested Set  model class for table "category".
 *
 * The followings are the available columns in table 'category':
 * @property string $id
 * @property string $root
 * @property string $lft
 * @property string $rgt
 * @property integer $level
 * @property string $name
 */
class Category extends CActiveRecord
{

   /**
	 * Id of the div in which the tree will berendered.
	 */
    const ADMIN_TREE_CONTAINER_ID='category_admin_tree';


	/**
	 * Returns the static model of the specified AR class.
	 * @return Category the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

        /**
	 * @return string the class name
	 */
          public static function className()
	{
		return __CLASS__;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE1: you should only define rules for those attributes that
		// will receive user inputs.
                // NOTE2: Remove ALL rules associated with the nested Behavior:
                //rgt,lft,root,level,id.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, description, parent_id', 'safe', 'on'=>'search'),
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
			'root' => 'Root',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
			'name' => 'Name',
			'parent_id' => 'Parent',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('root',$this->root,true);
		$criteria->compare('lft',$this->lft,true);
		$criteria->compare('rgt',$this->rgt,true);
		$criteria->compare('level',$this->level);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        public function behaviors()
{
    return array(
        'NestedSetBehavior'=>array(
            'class'=>'ext.nestedBehavior.NestedSetBehavior',
            'leftAttribute'=>'lft',
            'rightAttribute'=>'rgt',
            'levelAttribute'=>'level',
            'hasManyRoots'=>true
            )
    );
}

  public static  function printULTree(){
  	$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
  	$event= Event::model()->find('id=?', array($user->selectedevent));
    $categories=Category::model()->findAll('root=? order by lft',array($event->rootcategoryid));
    $level=0;

	foreach($categories as $n=>$category)
	{
	
	    if($category->level==$level)
	        echo CHtml::closeTag('li')."\n";
	    else if($category->level>$level)
	        echo CHtml::openTag('ul')."\n";
	    else
	    {
	        echo CHtml::closeTag('li')."\n";
	
	        for($i=$level-$category->level;$i;$i--)
	        {
	            echo CHtml::closeTag('ul')."\n";
	            echo CHtml::closeTag('li')."\n";
	        }
	    }
	
	    if ($category->root==$category->id)
	    	echo CHtml::openTag('li',array('id'=>'node_'.$category->id,'rel'=>$category->name,'root'=>true));
	    else
	    	echo CHtml::openTag('li',array('id'=>'node_'.$category->id,'rel'=>$category->name));
	    echo CHtml::openTag('a',array('href'=>'#'));
	    echo CHtml::encode($category->name);
	    echo CHtml::closeTag('a');

	    $level=$category->level;
	}

	for($i=$level;$i;$i--)
	{
		echo CHtml::closeTag('li')."\n";
		echo CHtml::closeTag('ul')."\n";
	}

  }

  public static  function printULTree_noAnchors(){
  	$categories=Category::model()->findAll(array('order'=>'lft'));
  	$level=0;

  	foreach($categories as $n=>$category)
  	{
	    if($category->level == $level)
	    	echo CHtml::closeTag('li')."\n";
	    else if ($category->level > $level)
	    	echo CHtml::openTag('ul')."\n";
	    else         //if $category->level<$level
	    {
	    	echo CHtml::closeTag('li')."\n";
	
	    	for ($i = $level - $category->level; $i; $i--) {
	    		echo CHtml::closeTag('ul') . "\n";
	    		echo CHtml::closeTag('li') . "\n";
	    	}
	    }
	
	    echo CHtml::openTag('li');
	    echo CHtml::encode($category->name);
	    $level=$category->level;
	}	

for ($i = $level; $i; $i--) {
            echo CHtml::closeTag('li') . "\n";
            echo CHtml::closeTag('ul') . "\n";
        }

}


public function createRootNode($name)
{

$new_root=new Category;
$new_root->attributes=array('name'=>$name);
$attributes=$new_root->attributes;
if($new_root->saveNode()){
return $new_root->primaryKey;
} else
{
echo json_encode(array('success'=>false,
'message'=>'Error.Root Category was not created.'
)
);
			exit;
		}
	}
	
	/**
	* Return a list of all categories sorted by name as 'id' and 'name' pairs, this is to
	* be used for a dropboxlist
	* @return list of all Category 'id' and 'name' pairs
	*/
	public static function loadCategories()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$event= Event::model()->find('id=?', array($user->selectedevent));
		
		$sql = "SELECT * FROM category where root=:rootcategoryid order by lft";
		$params=array(':rootcategoryid'=>$event->rootcategoryid);
		
		$models=self::model()->findAllBySql($sql, $params);
		
		return CHtml::listData($models, 'id', 'name');
	}

}