<?php

class RuleController extends Controller
{
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}*/

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	
	public function actionReturnForm(){
	
	
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
													 //'jquery.yiigridview.js'=>false,
		);
	
		$model=new Rule('search');
		$model->unsetAttributes();  // clear any default values
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$model->eventid=$user->selectedevent;
		
		//$model->categoryid=$_POST['category_id'];
		$dataProvider=new CActiveDataProvider('Rule');
		//Figure out if we are updating a Model or creating a new one.
		//if(isset($_POST['update_id']))$model= $this->loadModel($_POST['update_id']);else $model=new Categorydemo;
		//$rules=Rules::model()->findAll('categoryid=?',array($_POST['category_id']));
		$categories=Category::loadCategories();
		//$output = $this->renderPartial('_form', array('model'=>$model),	true);
		$this->renderPartial('list', array('model'=>$model,'dataProvider'=>$dataProvider,  'categories'=>$categories,),	false, true);
		
	}
		
	public function actionUpdateCategoryID()
	{
		if(isset($_POST['Rule']))
		{
			$model = Rule::model()->findByPk($_POST['Rule']['id']);
			if($model===null){
				throw new CHttpException(404,'The requested page does not exist.');
			}

			$model->attributes = $_POST['Rule'];
			if($model->save()){
				$categories=Category::loadCategories();
				echo $categories[$model->categoryid];
			}
		}
	}
		
	public function actionUpdateMoveToCategoryID()
	{
	    if(isset($_POST['Rule']))
	    {
	        $model = Rule::model()->findByPk($_POST['Rule']['id']);
	        if($model===null){
	            throw new CHttpException(404,'The requested page does not exist.');
	        }
	
	        $model->attributes = $_POST['Rule'];
	        if($model->save()){
	            $categories=Category::loadCategories();
	            echo $categories[$model->movetocategoryid];
	        }
	    }
	}
	
	public function actionUpdateRuleType()
	{
		if(isset($_POST['Rule']))
		{
			$model = Rule::model()->findByPk($_POST['Rule']['id']);
			if($model===null){
				throw new CHttpException(404,'The requested page does not exist.');
			}
	
			$model->attributes = $_POST['Rule'];
			if($model->save()){
				$ruletypes=array('0'=> 'The Twitter Username', '1'=>'The Twitter Message');
				echo $ruletypes[$model->ruletype];
			}
		}
	}
	
	public function actionUpdateRuleString()
	{
		if(isset($_POST['Rule']))
		{
			$model = Rule::model()->findByPk($_POST['Rule']['id']);
			if($model===null){
				throw new CHttpException(404,'The requested page does not exist.');
			}
	
			$model->attributes = $_POST['Rule'];
			if($model->save()){
				echo $model->rulestring;
			}
		}
	}
	
	public function actionCreate(){
		$model=new Rule();
		$categories=Category::model()->loadCategories();
		$model->ruletype=0;
		$model->rulestring="Search String";
		
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$model->eventid=$user->selectedevent;
		
		$event= Event::model()->find('id=?', array($user->selectedevent));
		$model->categoryid=$event->rootcategoryid;
		$model->movetocategoryid=$event->rootcategoryid;
		
		if($model->save()){
			echo "OK"; // echo or not, if an exception dont occur then is OK.
		}
		else{
			throw new Exception("Sorry, cant create a rule",500);
		}
		
	}	
	
	public function actionRunRules(){
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		
		$sql = "SELECT * FROM rule where eventid=:selectedevent";
		$params=array(':selectedevent'=>$user->selectedevent);
		
		$rules=Rule::model()->findAllBySql($sql, $params);
		
		$connection = Yii::app()->db;
			
		foreach ($rules as $rule){
			
			$sql="";
			if ($rule->ruletype==0)
			{
				$sql = "SELECT a.tweetid as tweetid FROM tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=".$user->selectedevent." and a.fromuser like '%".$rule->rulestring."%'";
			} elseif ($rule->ruletype==1)
			{
				$sql = "SELECT a.tweetid as tweetid FROM tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=".$user->selectedevent." and a.text like '%".$rule->rulestring."%'";;
			}
			
			if ($sql!="")
			{
				$command = $connection->createCommand($sql);
				$tweetmatches = $command->queryAll();
				foreach ($tweetmatches as $tweetmatch){
					$this->createCode($tweetmatch['tweetid'], $rule->movetocategoryid);
				}
			}
		}
		
	
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

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	/**
	* Returns the data model based on the primary key given in the GET variable.
	* If the data model is not found, an HTTP exception will be raised.
	* @param integer the ID of the model to be loaded
	*/
	public function loadModel($id)
	{
		$model=Rule::model()->findByPk($id);
		if($model===null)
		throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	
	/**
	* Return a list of all the rule types this is to
	* be used for a dropboxlist
	* @return list of all rule types 'id' and 'name' pairs
	*/
	public static function loadRuleTypes()
	{
		$ruletypes=array('0'=> 'The Twitter Username', '1'=>'The Twitter Message');
		return $ruletypes;
	}
}