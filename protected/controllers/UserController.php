<?php

class UserController extends Controller
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
			'postOnly + delete',
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
				'actions'=>array('update','admin','index','view','selectEvent','selectEventRedirect','returnForm'),
				'users'=>array('*'),
			),
			//commented to let rbac handle it
			/*//array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','delete'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
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
		if(!Yii::app()->user->checkAccess('createUser') || Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		

		else{
		$model=new User;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		if(isset($_POST['User']))
		{
			
			$model->attributes=$_POST['User'];
			$model->role = ($_POST['pick_role']);
			//$model->selectedEvent = "1";
			//echo 'the role attribute is '.$model->role;
			$auth = Yii::app()->authManager;
			if($model->save())
			  {
			  	// add more definitions as required. dont forget to update those roles in site/setup and user/update.
			  	// run the site/setup with just the updated code and comment out the existing code and then uncomment it
			  	// otherwise the database will have multiple entries.
			  	if($model->role == 1)
			       	$auth->assign('admin', $model->id);
				else if($model->role == 2)
				   $auth->assign('author', $model->id);
				else if($model->role == 3)
				   $auth->assign('public', $model->id);
				else if($model->role == 4)
				   $auth->assign('blocked', $model->id);
				else $auth->assign('public', $model->id);
				$this->redirect(array('view','id'=>$model->id));
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
		if(!Yii::app()->user->checkAccess('public',array('id' => $id))  || Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		else{
		$model=$this->loadModel($id);
		if(!Yii::app()->user->checkAccess('updateUser',
		    array('id' => $id)) || Yii::app()->user->checkAccess('blocked')){
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		    	echo " You are not authorize to do it, go back using your browser back button";
		    }
		else{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			
			/*$model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));*/
				$model->attributes=$_POST['User'];
			$model->role = ($_POST['pick_role']);
			//$model->selectedEvent = "1";
			//echo 'the role attribute is '.$model->role;
			$auth = Yii::app()->authManager;
			if($model->save())
			  {
			  	if($model->role == 1)
			       	$auth->assign('admin', $model->id);
				else if($model->role == 2)
				   $auth->assign('author', $model->id);
				else if($model->role == 3)
				   $auth->assign('public', $model->id);
				else if($model->role == 4)
				   $auth->assign('blocked', $model->id);
				else $auth->assign('public', $model->id);
				$this->redirect(array('view','id'=>$model->id));
			  }
		}
		
		// delete this user record in authassignment table if he was first blocked
		// write a sql query to locate this user in authassignment table, use userid(primary key) column to do this
		// see if the itemname equals "blocked", if yes, delete that record.
		
		$this->render('update',array(
			'model'=>$model,
		));
	}}}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		echo "<script type='text/javascript'>alert('Reached');</script>";
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

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
		if(!Yii::app()->user->checkAccess('admin')|| Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		else{
		$dataProvider=new CActiveDataProvider('User');
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
		
		if(!Yii::app()->user->checkAccess('admin')|| Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		else{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

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
		$model=User::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionSelectEvent()
	{
		$event_id = (!empty($_POST['event_id'])) ? $_POST['event_id'] : 0;
		
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$user->selectedevent=$event_id;
		$user->save();
	}

	public function actionSelectEventRedirect()
	{
		$event_id = (!empty($_GET['event_id'])) ? $_GET['event_id'] : 0;
	
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$user->selectedevent=$event_id;
		$user->save();
		
		$this->redirect(Yii::app()->createUrl("site/streaming"));
	}
	
	public function actionReturnForm(){

	if (isset($_POST['Add']))
    {
        if (isset($_POST['selectedIds']))
        {
        	echo "selected entered";
            foreach ($_POST['selectedIds'] as $id)
            {
            	
				$user = User::model()->findByPk(Yii::app()->user->getId());
            	
				echo 'selected event id = '.$user->selectedevent;
                $userEvent = new Userevent();
				$userEvent->userid = $id;
				$userEvent->eventid = $user->selectedevent;
				$userEvent->save();
				echo "saved ";
				
            }
				$this->redirect(Yii::app()->createUrl("site/eventaccess"));
        }
    }
		//don't reload these scripts or they will mess up the page
		//yiiactiveform.js still needs to be loaded that's why we don't use
		// Yii::app()->clientScript->scriptMap['*.js'] = false;
		$cs=Yii::app()->clientScript;
		$cs->scriptMap=array(
		                                                 'jquery.min.js'=>false,
		                                                 'jquery.js'=>false,
		                                                 'jquery.fancybox-1.3.4.js'=>false,
		                                                 'jquery.jstree.js'=>false,
		                                                 'jquery-ui-1.8.12.custom.min.js'=>false,
		                                                 'json2.js'=>false,

		);


		//Figure out if we are updating a Model or creating a new one.
		//$model=new User;


		//$this->renderPartial('selection', array('model'=>$model,
		//                                                             'parent_id'=>!empty($_POST['parent_id'])?$_POST['parent_id']:''
		//),
		//false, true);
        
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		//$model->eventid=$user->selectedevent;
		
		//$model->categoryid=$_POST['category_id'];
		$dataProvider=new CActiveDataProvider('User');
		//Figure out if we are updating a Model or creating a new one.
		//if(isset($_POST['update_id']))$model= $this->loadModel($_POST['update_id']);else $model=new Categorydemo;
		//$rules=Rules::model()->findAll('categoryid=?',array($_POST['category_id']));
		$categories=Category::loadCategories();
		//$output = $this->renderPartial('_form', array('model'=>$model),	true);
		$this->renderPartial('selection', array('model'=>$model,'dataProvider'=>$dataProvider,  'categories'=>$categories,),false, true);
	}

}
