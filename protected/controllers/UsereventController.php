<?php

class UsereventController extends Controller
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
				'actions'=>array('delete','admin','index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			
			array('deny',  // deny all users
				'users'=>array('*'),
			),
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
			//'username' => $this->getCorrespondingUserName($id)
		));
	}
	// returns the username of the person involved in the event
	public static function getCorrespondingUserName($id)
	{		
		$userEventModel = Userevent::model()->findByPk($id);
		$userId = $userEventModel->userid;	
		$userModel = User::model()->findByPk($userId);		
		$userName = $userModel->username;
		return ($userName);		 
	}
	
	// returns the name of the events, instead of the eventId
	public static function getCorrespondingEventName($id)
	{		
		$userEventModel = Userevent::model()->findByPk($id);		
		$eventId = $userEventModel->eventid;		
		$eventModel = Event::model()->findByPk($eventId);	
		$eventName = $eventModel->name;
		return ($eventName);		 
	}
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if(!Yii::app()->user->checkAccess('admin')  || Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		else{
		$model=new Userevent;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Userevent']))
		{
			$model->attributes=$_POST['Userevent'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Userevent']))
		{
			$model->attributes=$_POST['Userevent'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
		echo "entered";
		if(!Yii::app()->user->checkAccess('admin') && !Yii::app()->user->checkAccess('author') || Yii::app()->user->checkAccess('blocked')){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		else{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();
			echo "2nd entered";
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		if(!Yii::app()->user->checkAccess('admin') || Yii::app()->user->checkAccess('blocked') ){
			echo "You are not authorize to do it, go back using your browser back button";
		    	//throw new CHttpException(401, 'You are not allowed to do this');
		 }
		else{
		$dataProvider=new CActiveDataProvider('Userevent');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			//'username' => $this->getCorrespondingUserName($dataProvider->id)
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
		$model=new Userevent('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Userevent']))
			$model->attributes=$_GET['Userevent'];

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
		$model=Userevent::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='userevent-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
}
