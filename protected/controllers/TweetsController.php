<?php

class TweetsController extends Controller
{
	// Uncomment the following methods and override them if needed
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
		//'inlineFilterName',
		//array(
		//	'class'=>'path.to.FilterClass',
		//	'propertyName'=>'propertyValue',
		//),
		);
	}

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

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function actionLoadTweets()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		//$limit=20-$user->numassigned;
		$limit=20;
	
		// Find the most recent tweets for the selected event
		$sql = "select * from tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=:currentevent order by tweetid DESC limit :limit";
		$params=array(':limit'=>$limit,':currentevent'=>$user->selectedevent);


		$models=Tweets::model()->findAllBySql($sql, $params);

		if($models===null)
		throw new CHttpException(404,'The requested page does not exist.');

		$user->laststreamedtweetid=CHtml::value($models[0], 'tweetid');
		$user->save();
		
		date_default_timezone_set($user->timezone);
		
		foreach ($models as $model) {
			$model->created=date("Y-m-d H:i:s T", strtotime($model->created." GMT"));			
		}
		
		header('Content-type:application/json');
		echo CJSON::encode($models);
	}
	
	public function actionLoadTweet()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$limit=1;
		
			// If the coding category is the root category the database query selects tweets that
			//have no coding associated with them
			$sql = "select * from tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=:currentevent and tweetid > :laststreamedtweetid order by tweetid ASC limit :limit";
			$params=array(':laststreamedtweetid'=>$user->laststreamedtweetid, ':currentevent'=>$user->selectedevent,':limit'=>$limit);
		
	
		$models=Tweets::model()->findAllBySql($sql, $params);
	
		if($models===null)
		throw new CHttpException(404,'The requested page does not exist.');
	
		foreach ($models as $model) {
			$user->laststreamedtweetid=CHtml::value($model, 'tweetid');
			$model->created=date("Y-m-d H:i:s T", strtotime($model->created." GMT"));
			$user->save();
		}
	
		$user->numassigned=$user->numassigned+count($models);
		$user->save();
		header('Content-type:application/json');
		echo CJSON::encode($models);
	}
	
	function actionExport()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$event= Event::model()->find('id=?', array($user->selectedevent));
		$level=$user->level;
		
		date_default_timezone_set($user->timezone);
		
		// generate a resultset
		if ($level==$event->rootcategoryid)
		{
			$sql = "SELECT text, created, fromuser, name, followers, friends, location, tweetid FROM tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=:currentevent";
			$params=array(':currentevent'=>$user->selectedevent);
		
			$data=Tweets::model()->findAllBySql($sql, $params);
		}
		else 
		{
			$sql = "SELECT text, created, fromuser, name, followers, friends, location, tweetid  FROM tweets a INNER JOIN coding b USING(tweetid) where b.coding=:level";
			$params=array(':level'=>$level);
			
			$data=Tweets::model()->findAllBySql($sql, $params);
		}
	
		// Unregister the Yii autoload so that phpocx autoload will work properly
		spl_autoload_unregister(array('YiiBase','autoload'));
		
		/** Include PHPExcel */
		require_once 'phpexcel/PHPExcel.php';
		
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("PIO Monitoring Application")
		->setLastModifiedBy("PIO Monitoring Application")
		->setTitle("Office 2007 XLSX Document")
		->setSubject("Office 2007 XLSX Document");
		
		$count=1;
		$tweetAttributeLabels=Tweets::attributeLabels();
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$count, $tweetAttributeLabels['text'])
		->setCellValue('B'.$count, $tweetAttributeLabels['created'])
		->setCellValue('C'.$count, $tweetAttributeLabels['fromuser'])
		->setCellValue('D'.$count, $tweetAttributeLabels['name'])
		->setCellValue('E'.$count, $tweetAttributeLabels['followers'])
		->setCellValue('F'.$count, $tweetAttributeLabels['friends'])
		->setCellValue('G'.$count, $tweetAttributeLabels['location'])
		->setCellValue('H'.$count, "Tweet URL");
		$count++;
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A')
		->getAlignment()->setWrapText(true);
		
		$objPHPExcel->getDefaultStyle()->getFont()->setBold(false);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(70);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(23);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(70);
		
		foreach($data as $tweet)
		{
			// Add some data
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$count, $tweet['text'])
			->setCellValue('B'.$count, date("Y-m-d H:i:s T", strtotime($tweet['created']." GMT")))
			->setCellValue('C'.$count, $tweet['fromuser'])
			->setCellValue('D'.$count, $tweet['name'])
			->setCellValue('E'.$count, $tweet['followers'])
			->setCellValue('F'.$count, $tweet['friends'])
			->setCellValue('G'.$count, $tweet['location'])
			->setCellValue('H'.$count, "http://twitter.com/".$tweet['fromuser']."/status/".$tweet['tweetid'])
			->getCell('H'.$count)->getHyperlink()->setUrl("http://twitter.com/".$tweet['fromuser']."/status/".$tweet['tweetid']);
			
			$count++;
		}
				
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$event->name.'"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		
		// Once the phpdocx work is done register the Yii autoload again
		spl_autoload_register(array('YiiBase','autoload'));
		exit;
		
	}
	
	function actionGetNumQueued()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		
		// If the coding category is the root category the database query selects tweets that
		//have no coding associated with them
		$sql = "select count(*) from tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=:currentevent and tweetid > :laststreamedtweetid";
		$params=array(':laststreamedtweetid'=>$user->laststreamedtweetid,':currentevent'=>$user->selectedevent);
		
		echo Tweets::model()->countBySql($sql, $params);
	}
	
	/**
	* Deletes a particular model.
	* If deletion is successful do nothing since this is a delete request.
	* @param integer $id the ID of the model to be deleted
	*/
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();
	
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			//if(!isset($_GET['ajax']))
			//$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
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
		$model=Tweets::model()->findByPk($id);
		if($model===null)
		throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}