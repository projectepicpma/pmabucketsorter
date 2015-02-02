<?php

class EventController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('admin','index','view'),
				'users'=>array('*'),
			),
			//commented for rbac to take effect and handle it
			/*array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete','update'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('blocked'),
			),*/
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if(!Yii::app()->user->checkAccess('createEvent') || Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		else{
		$model=new Event;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Event']))
		{
			$model->attributes=$_POST['Event'];
			$category=new Category();
			$new_root=new Category;
			$new_root->name=$model->name;
			$new_root->saveNode();
			$model->rootcategoryid=$new_root->primaryKey;
			
			// If the start date is blank, set it to NULL to avoid a DB error.
			if ($model->startdate=='')
				$model->startdate=NULL;
		
			// If the end date is blank, set it to NULL to avoid a DB error.
			if ($model->enddate=='')
				$model->enddate=NULL;
			
			if($model->save())
			{
				//set the selected Event to the newly created event
				$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
				$user->selectedevent=$model->id;
				$user->save();
				$auth = Yii::app()->authManager;
				// event is associated with the user who created it
				$userEvent = new Userevent();
				$userEvent->userid = $user->id;
				$userEvent->eventid = $model->id;
				$userEvent->save();
				$connection = mysqli_connect("localhost", "root", "DJEZb3xTRyPvM9Y9", "twitterbucketsort");
				
		  $connection=Yii::app()->db;
        $sql = "SELECT * FROM authassignment WHERE userid= {$user->id} AND itemname = 'author' ";
        $command = $connection->createCommand($sql);
        $query = $command->queryAll();
				//$resultData = mysqli_fetch_array($data);
				if(empty($query))
				$auth->assign('author', $user->id);
				
				//echo "<script type='text/javascript'>alert('passed!');</script>";
				$tablename = "maxrt".$model->id;
				$connection=Yii::app()->db;
        		$sql = "CREATE TABLE IF NOT EXISTS ".$tablename." (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `tweetid` bigint(21) NOT NULL,
						  `text` varchar(141) NOT NULL,
						  `fromuser` varchar(141) NOT NULL,
						  `retweetcount` bigint(20) NOT NULL,
						  PRIMARY KEY (`id`),
						  KEY `retweetcount` (`retweetcount`),
						  KEY `tweetid` (`tweetid`)
						) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        		//$command = $connection->createCommand($sql);
        		$query = mysqli_query(mysqli_connect("localhost", "root", "DJEZb3xTRyPvM9Y9", "twitterbucketsort"), $sql);
        		
        		
        		// for max hashtag count
        		$tablename = "maxhash".$model->id;
				$connection=Yii::app()->db;
        		$sql = "CREATE TABLE IF NOT EXISTS ".$tablename." (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `hashtag` varchar(141) NOT NULL,
						  `count` bigint(20) NOT NULL,
						  PRIMARY KEY (`id`),
						  KEY `count` (`count`),
						  KEY `hashtag` (`hashtag`)						  
						) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        		//$command = $connection->createCommand($sql);
        		$query = mysqli_query(mysqli_connect("localhost", "root", "DJEZb3xTRyPvM9Y9", "twitterbucketsort"), $sql);
        		
        		
        		// for topuser report feature
        		$tablename = "topuser".$model->id;
				$connection=Yii::app()->db;
        		
        		$sql = "CREATE TABLE IF NOT EXISTS ".$tablename." (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `userid` varchar(20) NOT NULL,
						  `username` varchar(20) NOT NULL,
						  `count` bigint(20) NOT NULL,
						  PRIMARY KEY (`id`),
						  KEY `userid` (`userid`),
						  KEY `count` (`count`),
						  CONSTRAINT `".$tablename."` FOREIGN KEY (`userid`) 
						  REFERENCES `tweets` (`fromuserid`) ON DELETE CASCADE ON UPDATE CASCADE
						) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";	
				$link = mysqli_connect("localhost", "root", "DJEZb3xTRyPvM9Y9", "twitterbucketsort");		
        		$query = mysqli_query($link, $sql) or die("cant create topuser table ".mysqli_error($link));
        		
        		        		
        		mysqli_close(mysqli_connect("localhost", "root", "DJEZb3xTRyPvM9Y9", "twitterbucketsort"));
				$this->redirect(Yii::app()->createUrl("site/streaming"));
				
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		if(!Yii::app()->user->checkAccess('updateOwnEvent') || Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
		
		
		if(isset($_POST['Event']))
		{
			$model->attributes=$_POST['Event'];
			$eventRootCategoryId = $model->rootcategoryid;
			$categoryModel = Category::model()->findByPk($eventRootCategoryId);
			// changing the category name as well so that the 
			// new name is displayed in the streaming and archive mode in
			// the left side category tree.
			if($categoryModel->id == $categoryModel->root)
			{
				echo $model->name;
				$categoryModel->name = $model->name;
				$categoryModel->saveNode();
			}
			// If the start date is blank, set it to NULL to avoid a DB error.
			if ($model->startdate=='')
			$model->startdate=NULL;
			
			// If the end date is blank, set it to NULL to avoid a DB error.
			if ($model->enddate=='')
			$model->enddate=NULL;
				
			if($model->save())
				$this->redirect(Yii::app()->createUrl("site/eventaccess"));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();
			$rttable = "maxrt".$id;
			$hashtagtable= "maxhash".$id;
			$topusertable = "topuser".$id;
			$dropsql = "DROP TABLE IF EXISTS ".$rttable.",".$hashtagtable.",".$topusertable;
			$dropres=mysqli_query(mysqli_connect("localhost", "root", "DJEZb3xTRyPvM9Y9", "twitterbucketsort"), $dropsql);
        		
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		if(!Yii::app()->user->checkAccess('admin') || Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		else{
		$dataProvider=new CActiveDataProvider('Event');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
		}
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		if(!Yii::app()->user->checkAccess('admin') || Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		else{
		$model=new Event('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Event']))
			$model->attributes=$_GET['Event'];

		$this->render('admin',array(
			'model'=>$model,
		));}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Event::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='event-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function getCorrespondingUserName()
	{
		$userName = User::model()->find('LOWER(username)=?',array(Yii::app()->user->id));
		return ($userName);
		 
		 /*$userName = User::model()->find('LOWER(username)=?',array(Yii::app()->user->id));
		$eventName = Event::model()->find('LOWER(name)=?',array($this->getAttributeLabel('eventid')));
		
		return array(
		'username' => $userName,
		'eventname' => $eventName,
		);*/
	}
}
