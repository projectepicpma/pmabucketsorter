<?php

class CodingController extends Controller
{
	// Uncomment the following methods and override them if needed
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
		/*'inlineFilterName',
			array(
		'class'=>'path.to.FilterClass',
		'propertyName'=>'propertyValue',
		),*/
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

	public function actionCreate()
	{
		$tweetid=$_POST['tweetid'];
		$code=$_POST['code'];
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$event= Event::model()->find('id=?', array($user->selectedevent));
		$eventid = $user->selectedevent;
		
		$link = mysqli_connect("localhost", "root", "DJEZb3xTRyPvM9Y9","twitterbucketsort")or die("cant connect to database ".mysqli_error($link));
        
        $checkifexist = "SELECT tweetid, coding FROM coding where tweetid=".$tweetid." and coding=".$code;
		$existresult = mysqli_query($link, $checkifexist) or die("cannot query at 46 ".mysqli_error($link));
		if(mysqli_num_rows($existresult)==0)
		{  
	        $checksql = "select tweetcount FROM categorycount 
				         WHERE eventid=".$eventid." AND categoryid=".$code;
			$checkresult = mysqli_query($link, $checksql) or die("cant query database at line 47 ".mysqli_error($link));
			if(mysqli_num_rows($checkresult) == 1)
			{
			  $count = mysqli_fetch_array($checkresult); 
			  $newcount = $count['tweetcount'] + 1;
	          
			  $updatequery = "UPDATE categorycount SET tweetcount=".$newcount." 
			                  WHERE eventid=".$eventid." AND categoryid=".$code;
			  $updateresult = mysqli_query($link, $updatequery) or die("cannot update result at line 55 ".mysqli_error($link));
			}
			elseif (mysqli_num_rows($checkresult) == 0) 
			{
				$insertquery = "INSERT INTO categorycount(categoryid, eventid,tweetcount) 
				                VALUES('$code','$eventid',1)";
				$insertresult = mysqli_query($link, $insertquery) or die("not inserted in categorycount at line 61 ".mysqli_error($link));
			}
		}
		//Do not assign a code to the root categories
		if ($code!=$event->rootcategoryid)
		{
			$this->createCode($tweetid, $code);
		}
		
		$codes="";
		
		// Select the categories displayed to the user
		$sql = "select * from coding where tweetid=:tweetid order by coding ASC;";
		$params=array(':tweetid'=>$tweetid);
		$models=Coding::model()->findAllBySql($sql, $params);

		
		$first=true;
		foreach ($models as $model) {
			$category=Category::model()->find('id=?', array($model->coding));
			if ($first) {
				$codes=$codes.$category->name;
				$first=false;
			}
			else {
				$codes=$codes.", ".$category->name;
			}
			
		}
		$tweet=Tweets::model()->find('tweetid=?', array($tweetid));
		
		$tweet->categories=$codes;
		$tweet->save();
		echo $codes;
		
	}

	public function actionCreateFromId()
	{
		$code=$_POST['code'];
		$id=$_POST['id'];
		
		$tweet=Tweets::model()->find('id=?', array($id));
		$tweetid=$tweet->tweetid;
		
		
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$event= Event::model()->find('id=?', array($user->selectedevent));
		
		//Do not assign a code to the root categories
		if ($code!=$event->rootcategoryid)
		{
			$this->createCode($tweetid, $code);
		}
	
		$codes="";
	
		// Select the categories displayed to the user
		$sql = "select * from coding where tweetid=:tweetid order by coding ASC;";
		$params=array(':tweetid'=>$tweetid);
		$models=Coding::model()->findAllBySql($sql, $params);
	
	
		$first=true;
		foreach ($models as $model) {
			$category=Category::model()->find('id=?', array($model->coding));
			if ($first) {
				$codes=$codes.$category->name;
				$first=false;
			}
			else {
				$codes=$codes.", ".$category->name;
			}
				
		}
		$tweet->categories=$codes;
		$tweet->save();
		echo $codes;
	
	}
	public function actionUnassigntweets()
	{
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$user->numassigned=0;
		$user->save();

		// Delete the coding that hasn't been completed
		$sql = "coding=0 and userid=:user";
		$params=array(':user'=>$user->id);
		Coding::model()->deleteAll($sql, $params);
	}

}