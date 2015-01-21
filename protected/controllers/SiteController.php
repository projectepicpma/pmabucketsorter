<?php

class SiteController extends Controller
{
	public function   init() {
		$this->registerAssets();
		parent::init();
	}
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
	
	public function accessRules()
	{
		return array(
		array('allow', // allow authenticated user to perform 'create' and 'update' actions
					'actions'=>array('login'),
					'users'=>array('*'),
		),
		array('allow', // allow authenticated user to perform 'create' and 'update' actions
					'actions'=>array('eventaccess','searchterms','index','coding','streaming','about','logout','splash','links'),
					'users'=>array('@'),
		),
		array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete','setup'),
                'users' => array('@'),
            ),
		array('deny',  // deny all users
					'users'=>array('*'),
		),
		);
	}
	
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
		// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
		),
		// page action renders "static" pages stored under 'protected/views/site/pages'
		// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
		),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionCoding()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$event= Event::model()->find('id=?', array($user->selectedevent));
		
		if ($user)
		{
			if(isset($_GET['level']))
			{
				$user->level=$_GET['level'];
				$user->save();
			}
			else
			{
				$user->level=$event->rootcategoryid;
				$user->save();
			}
			$level=$user->level;
		}
		else
		{
			$level=$event->rootcategoryid;
		}
	

		$currentBucket=Category::model()->find('id=?',array($level));
		
		//create an array open_nodes with the ids of the nodes that we want to be initially open
		//when the tree is loaded.Modify this to suit your needs.Here,we open all nodes on load.
		$categories= Category::model()->findAll(array('order'=>'lft'));
		$identifiers=array();
		foreach($categories as $n=>$category)
		{
			$identifiers[]="'".'node_'.$category->id."'";
		}
		$open_nodes=implode(',', $identifiers);
		
		$baseUrl=Yii::app()->baseUrl;
		
		$model=new Tweets('search');
		$model->unsetAttributes();  // clear any default values
		
		// If the current level is not the root node then use the search criteria to 
		// return a subset of the data
		if ($level!=$event->rootcategoryid)
		{	
			// This corresponds to a search criteria that checks to see if each tweet is
			// coded in the current User model level. If the tweet is coded it returns
			// a count of 1, which matches this criteria.
			$model->codedforcurrentlevel=1;
		}
		else
		{
			// This corresponds to a search criteria that checks to see if each tweet is
			// associated with the current event. If the tweet is, it returns
			// a count of 1, which matches this criteria.
			$model->currentlevelroot=1;
		}
		if(isset($_GET['Tweets']))
			$model->attributes=$_GET['Tweets'];
		
		//$this->render('admin',array(
		//			'model'=>$model,
		//));
		//$this->registerAssets();

		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('coding',array('currentBucket'=>$currentBucket,
											'userTimezone'=>$user->timezone,
			                                'baseUrl'=> $baseUrl,
		                                    'open_nodes'=> $open_nodes,
											'model'=>$model,
											'level'=>$level));
	}
	
	/**
	* This is the default 'index' action that is invoked
	* when an action is not explicitly requested by users.
	*/
	public function actionStreaming()
	{
		//create an array open_nodes with the ids of the nodes that we want to be initially open
		//when the tree is loaded.Modify this to suit your needs.Here,we open all nodes on load.
		$categories= Category::model()->findAll(array('order'=>'lft'));
		$identifiers=array();
		foreach($categories as $n=>$category)
		{
			$identifiers[]="'".'node_'.$category->id."'";
		}
		$open_nodes=implode(',', $identifiers);
	
		$baseUrl=Yii::app()->baseUrl;
	
		$dataProvider=new CActiveDataProvider('Category');
	
	
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		
		// count the number of active search terms for the current event
		$sql = "select count(*) from filter where eventid=:currentevent and active=true";
		$params=array(':currentevent'=>$user->selectedevent);
		
		$numActiveSearchTerms=Tweets::model()->countBySql($sql, $params);
		
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('streaming',array('dataProvider'=>$dataProvider,
												'userTimezone'=>$user->timezone,
			                                    'baseUrl'=> $baseUrl,
			                                    'open_nodes'=> $open_nodes,
												'numActiveSearchTerms'=> $numActiveSearchTerms));
	}
	
	public function actionSearchTerms()
	{
		$model=new Filter('search');
		$model->unsetAttributes();  // clear any default values
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$model->eventid=$user->selectedevent;
	
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('searchterms',array('model'=>$model));
	}
	

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
			echo $error['message'];
			else
			$this->render('error', $error);
		}
	}

	public function createTree(&$array, $level)
	{
		$buckets=Buckets::model()->findAll('level=?',array($level));
		//$children=array();
		foreach($buckets as $bucket)
		{
			$tree=array();
			$tree["id"]=$bucket->id;
			$tree["name"]=$bucket->name;
			$tree["level"]=$bucket->level;
			$tree["children"]=array();
			$this->createTree($tree["children"], $bucket->id);
			array_push($array,$tree);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionIndex()
	{
		$selectedEvent=User::getUserSelectedEvent();
		if ($selectedEvent!=NULL)
		{
			// If the user has a selected event, retrieve that event
			$model=Event::model()->findByPk($selectedEvent);
		}
		else
		{
			// If the user does not have a selected event, choose the first event available
			$model=Event::model()->find();
			$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
			$user->selectedevent=$model->id;
			$user->save();
		}

		$this->render('index',array('model'=>$model));
	}

	/**
	* Displays the contact page
	*/
	public function actionEventAccess()
	{
		//$m = new User; for debugging
		//$m->searchForAdding(); for debugging
		$model=new Userevent('search');
		$model->unsetAttributes();  // clear any default values
		
		// Select the users based on the currently selected event
		$model->eventid=User::getUserSelectedEvent();
		
		$this->render('eventaccess',array('model'=>$model));
	}
	
	/**
	* Displays the about page
	*/
	public function actionAbout()
	{
		//$model=Event::model()->find('id=?',array(User::getUserSelectedEvent()));
		$this->render('pages/about');
	}
	
	public function actionLinks()
	{
		
		$this->render('links');
	}
	/**
	* Displays the about page
	*/
	public function actionSplash()
	{
		$this->layout = 'splash';
		$model=Event::model()->find('id=?',array(User::getUserSelectedEvent()));
		$this->render('pages/splash',array('splash'=>true));
		}
		
	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
			$this->redirect(Yii::app()->createUrl("site/splash"));
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	// for rbac
	public function actionSetup()
	{
		$auth = Yii::app()->authManager;
		$role = $auth->createRole('blocked');
		$auth->createOperation('createEvent');
		$auth->createOperation('updateEvent');
		$auth->createOperation('deleteEvent');
		
		$auth->createOperation('createUser');
		$auth->createOperation('updateUser');
		$auth->createOperation('deleteUser');
	
		$auth->createOperation('createUserEvents');
		$auth->createOperation('updateUserEvents');
		$auth->createOperation('deleteUserEvents');
		//$task = $auth->createTask('userEventTasks', 'tasks related to userevent management');
		//$task->addChild('deleteUserEvents');
		$task = $auth->createTask('eventTasks', 'tasks related to event management');
		$task->addChild('createEvent');
		
		
		$task = $auth->createTask('userEventTasks', 'tasks related to userevent management');
		$task->addChild('createUserEvents');
		
	
		$task = $auth->createTask('updateUserEvent', 'Allows a user to update their own created user event'
		,		         'return $params["id"] == Yii::app()->user->id;');
		$task->addchild('updateUserEvents');
		$task->addChild('deleteUserEvents');
		
		$task = $auth->createTask('updateOwnEvent', 'Allows a user to update their own created event',
		         'return $params["id"] == Yii::app()->user->id;');
		$task->addchild('updateEvent');
		$task->addChild('deleteEvent');
		
		$task = $auth->createTask('userTasks', 'tasks related to user management');
		$task->addChild('createUser');
		$task->addChild('deleteUser');
		$task->addChild('updateUser');
		
		$task = $auth->createTask('updateOwnUser', 'Allows a user to update their own user info',
		         'return $params["id"] == Yii::app()->user->id;');
		$task->addchild('updateUser');
		
		$role = $auth->createRole('public');
		$role->addChild('updateOwnUser');
		$role->addChild('eventTasks');
		
		$role = $auth->createRole('author');
		$role->addChild('public');
		$role->addChild('updateOwnEvent');
		$role->addChild('updateUserEvent');
		
		$role = $auth->createRole('admin');
		$role->addchild('author');
		$role->addChild('userTasks');
		$role->addChild('eventTasks');
		$role->addChild('userEventTasks');		
		
		
		
		$auth->assign('public','demo');
		$auth->assign('admin','admin');
	}

	private function registerAssets(){
	
		Yii::app()->clientScript->registerCoreScript('jquery');
		$this->registerJs('webroot.js_plugins.jstree','/jquery.jstree.js');
		$this->registerCssAndJs('webroot.js_plugins.fancybox',
	                                                     '/jquery.fancybox-1.3.4.js',
	                                                     '/jquery.fancybox-1.3.4.css');
		$this->registerCssAndJs('webroot.js_plugins.jqui1812',
	                                                      '/js/jquery-ui-1.8.12.custom.min.js',
	                                                      '/css/dark-hive/jquery-ui-1.8.12.custom.css');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_plugins/json2/json2.js');
		Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/client_val_form.css','screen');
	}
	
	//UTILITY FUNCTIONS
	public static  function registerCssAndJs($folder, $jsfile, $cssfile) {
		$sourceFolder = YiiBase::getPathOfAlias($folder);
		$publishedFolder = Yii::app()->assetManager->publish($sourceFolder);
		Yii::app()->clientScript->registerScriptFile($publishedFolder . $jsfile, CClientScript::POS_HEAD);
		Yii::app()->clientScript->registerCssFile($publishedFolder . $cssfile);
	}
	
	public static function registerCss($folder, $cssfile) {
		$sourceFolder = YiiBase::getPathOfAlias($folder);
		$publishedFolder = Yii::app()->assetManager->publish($sourceFolder);
		Yii::app()->clientScript->registerCssFile($publishedFolder .'/'. $cssfile);
		return $publishedFolder .'/'. $cssfile;
	}
	
	public static function registerJs($folder, $jsfile) {
		$sourceFolder = YiiBase::getPathOfAlias($folder);
		$publishedFolder = Yii::app()->assetManager->publish($sourceFolder);
		Yii::app()->clientScript->registerScriptFile($publishedFolder .'/'.  $jsfile);
		return $publishedFolder .'/'. $jsfile;
	}
	
}