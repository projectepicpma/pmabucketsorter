<?php

class ReportController extends Controller
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
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','generateReport','createReport'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
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
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Report;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Report']))
		{
			$model->attributes=$_POST['Report'];
			if($model->save())
			{
				if (Yii::app()->request->isAjaxRequest)
				{
					echo CJSON::encode(array(
                        'status'=>'success', 
                        'div'=>"Classroom successfully added"
					));
					exit;
				}
				else
					$this->redirect(array('view','id'=>$model->id));
			}
		}

		if (Yii::app()->request->isAjaxRequest)
        {
            echo CJSON::encode(array(
                'status'=>'failure', 
                'div'=>$this->renderPartial('_form', array('model'=>$model), true)));
            exit;               
        }
        else
            $this->render('create',array('model'=>$model));
	}

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

		if(isset($_POST['Report']))
		{
			$model->attributes=$_POST['Report'];
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
		$dataProvider=new CActiveDataProvider('Report');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Report('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Report']))
			$model->attributes=$_GET['Report'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	/**
	* Creates a new model.
	* If creation is successful, the browser will be redirected to the 'view' page.
	*/
	public function actionCreateReport()
	{
		$model=new Report;
	
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if(isset($_POST['Report']))
		{
			$model->attributes=$_POST['Report'];
			if($model->save())
			{
				echo CJSON::encode(array(
			                        'status'=>'success', 
			                        'div'=>"Report successfully added",
			                        'reportid'=>$model->id
					));
					exit;

			}
			//$this->actionReport();
			exit;
		}
		
		// Populate known fields
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$event= Event::model()->find('id=?', array($user->selectedevent));
		$model->name=str_replace(" ","",$event->name)."Report";
		$model->showtwitterdailybreakdown=true;
		$model->showtwittertopten=true;
		$model->option1=true;
		$model->option2=true;
		$model->option3=true;
		
		echo CJSON::encode(array(
	                'status'=>'failure', 
	                'div'=>$this->renderPartial('_form', array('model'=>$model), true)));
		exit;

	}
	
	function actionGenerateReport()
	{
		$model=new Report;
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		if(isset($_GET['reportid']))
		{
			$before = microtime(TRUE);
			//$model->attributes=$_POST['Report'];
			$report= Report::model()->find('id=?', array($_GET['reportid']));			
		
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
		$event= Event::model()->find('id=?', array($user->selectedevent));
	
		$filename = $report->name;
	
		// All the database work needs to be done before the YiiBase is unregistered
		$sql = "select count(*) from tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=:currentevent";
		$params=array(':currentevent'=>$user->selectedevent);
	
		$eventTwitterNum = Tweets::model()->countBySql($sql, $params);
		
		$connection = Yii::app()->db;
		
		$sql = "select filter from filter where eventid=".$user->selectedevent;
		$command = $connection->createCommand($sql);
		$filters = $command->queryAll();
		
		$filterString="";
		$first=true;
		foreach ($filters as $filter) {
			if ($first) {
				$filterString=$filterString.$filter['filter'];
				$first=false;
			}
			else {
				$filterString=$filterString.", ".$filter['filter'];
			}
				
		}

		$disclaimer="Twitter messages for the ".$event->name." Event do not represent all of the Twitter traffic around this event. ".
			"These messages were collected using the search term(s): ".$filterString;
		
		$userid=Yii::app()->user->id;
		$elapsedtime111;
		$elapsedtime121;
		$elapsedtime211;
		$elapsedtime221;
		$elapsedtime311;
		$elapsedtime321;
		$elapsedtime411;
		$elapsedtime421;
		$elapsedtime431;
		$elapsedtime511;
		$elapsedtime521;
		$elapsedtime531;
		$elapsedtime611;
		$elapsedtime621;
		$elapsedtime631;
		
		// If user selected to show the top ten Twitter Users perform the query that gets the needed data.
		if ($report->showtwittertopten)
		{
			$start311 = microtime(TRUE);
			// can use max() in this query
			$sql = "select fromuser, count(*) as tweetcount from tweets a INNER JOIN tweetevent b USING(tweetid) 
			        where b.eventid=".$user->selectedevent." group by a.fromuser order by count(*) DESC limit 10";
			$command = $connection->createCommand($sql);
			$topTwitterUsers = $command->queryAll();
			global $elapsedtime311;
			$elapsedtime311 = microtime(TRUE)-$start111;
			
			
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (27, '$elapsedtime311', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();
		}
	
		// If user selected to show the Daily Twitter Breakdown perform the query that gets the needed data.
		if ($report->showtwitterdailybreakdown)
		{
			$start211 = microtime(TRUE);
			//quick hack for a demo
			//$sql="select created from tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=".$user->selectedevent." order by created DESC limit 1";
			$sql="select created from tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=".$user->selectedevent.
			" order by created ASC limit 1";
			$command = $connection->createCommand($sql);
			$oldestTweetDate = $command->queryAll();
		
			$sql="select created from tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=".$user->selectedevent." order by created DESC limit 1";
			$command = $connection->createCommand($sql);
			$newestTweetDate = $command->queryAll();
		
			$sql="SELECT DATE(created) as date, COUNT(*) as tweetcount FROM tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=".$user->selectedevent." AND DATE(created) >= DATE('".$oldestTweetDate[0]['created']."') AND DATE(created) <= DATE('".$newestTweetDate[0]['created']."') GROUP BY DATE(created)";
			$command = $connection->createCommand($sql);
			$tweetCountByDate = $command->queryAll();
			global $elapsedtime211;
			$elapsedtime211 = microtime(TRUE)-$start211;
			
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (24, '$elapsedtime211', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();
		}
		
		
		// If user selected to show the Category Twitter Breakdown perform the query that gets the needed data.
		if ($report->option1)
		{
			$start111 = microtime(TRUE);
			$categories=Category::model()->findAll('root=? order by lft',array($event->rootcategoryid));
		
			$categoryCounts = array();
			
			foreach($categories as $n=>$category)
			{
				$sql = "select count(*) from coding where coding = ".$category->id;
				$params=array();
				$categoryTweetCount = Tweets::model()->countBySql($sql, $params);
				array_push($categoryCounts, array($category->name,$categoryTweetCount));
			}
			$categoryCounts[0][1]=$eventTwitterNum;
			global $elapsedtime111;
			$elapsedtime111 = microtime(TRUE)-$start111;
			
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (21, '$elapsedtime111', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();
			
		}
		
		// If user selected to show the Top Twenty Hashtags perform the query that gets the needed data.
		if ($report->option2)
		{
			$start411 = microtime(TRUE);
			$hashtagCounts=array();
			$tablename = "maxhash".$user->selectedevent;
			//$sql="select text from tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=".$user->selectedevent;
			$sql = "select hashtag, count from ".$tablename." order by count desc limit 20";
			$command = $connection->createCommand($sql);
			$eventTweets = $command->queryAll();
			
			foreach($eventTweets as $eventTweet)
			{
				$hashtagCounts[$eventTweet['hashtag']] = $eventTweet['count'];
			}
			//arsort($hashtagCounts);
			$hashtagCounts=array_slice($hashtagCounts,0,20);
			global $elapsedtime411;
			$elapsedtime411 = microtime(TRUE)-$start411;
			
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (30, '$elapsedtime411', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();			
		}
		
		// If user selected to show the Top Retweets perform the query that gets the needed data.
		if ($report->option3)
		{
			$start511 = microtime(TRUE);
			$tablename = "maxrt".$user->selectedevent;
			$alternatesql = "select * from ".$tablename;//."order by retweetcount DESC  limit 20";
			//$toprt = mysql_query($alternatesql);
			$command = $connection->createCommand($alternatesql);
			$topRetweets = $command->queryAll();
			//$hashtagCounts=array();
			//$sql="select retweetid, max(retweetcount) as MaxTweetCount from tweets  where retweetid group by retweetid order by MaxTweetCount DESC limit 20;";
			$sql="select tweetid, text, fromuser, max(retweetcount) as MaxTweetCount 
			from tweets a INNER JOIN tweetevent b USING(tweetid) where b.eventid=".$user->selectedevent." 
			group by tweetid order by MaxTweetCount DESC  limit 20";
			/*$sql="select t.retweetid, rt.text, rt.fromuser, t.MaxTweetCount from 
			 *(select retweetid, max(retweetcount) as MaxTweetCount from tweets a INNER JOIN tweetevent b USING(tweetid) 
			 * where retweetid and b.eventid=".$user->selectedevent." 
			 * group by retweetid order by MaxTweetCount DESC limit 20) t, tweets rt where t.retweetid=rt.tweetid 
			 * order by MaxTweetCount DESC";
			 * */
			//$command = $connection->createCommand($sql);
			//$topRetweets = $command->queryAll();
			global $elapsedtime511;
			$elapsedtime511 = microtime(TRUE)-$start511;
			
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (33, '$elapsedtime511', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();
		}
		
		// Unregister the Yii autoload so that phpocx autoload will work properly
		spl_autoload_unregister(array('YiiBase','autoload'));
	
		Yii::import('application.vendors.*');
		require_once 'phpdocx/classes/CreateDocx.inc';
		require_once 'phpdocx/classes/TransformDoc.inc';
		$docx = new CreateDocx();
	
		// Title Page
		$paramsText = array(
			    'b' => 'single',
			    'font' => 'Arial',
			    'sz'=>'24',
			    'jc'=>'center'
		);
		
		$disclaimerText = array(
					    'font' => 'Arial',
					    'color'=> FF0000,
		);
		
		$docx->addText($event->name, $paramsText);
	
		$text="Twitter Communication Report";
		$docx->addText($text, $paramsText);
	
		$docx->addBreak('page');
		//$docx->addTableContents('Arial');
		//$docx->addBreak('page');
	
		$paramsTitle = array(
			    'val' => 1,
		//'u' => 'single',
			    'font' => 'Arial',
			    'sz' => 22
		);
	
		$paramsTitle2 = array(
			    'val' => 2,
			    'font' => 'Arial',
			    'sz' => 16
		);
	
		$docx->addTitle('Twitter Report', $paramsTitle);
		$docx->addTitle('Overview', $paramsTitle2);
	
	
		$paramsText = array(
			    'font' => 'Arial'
		);
	
	
		$text = "The total number of Twitter messages collected for the ".$event->name." Event is ".$eventTwitterNum.".";
		$docx->addText($text, $paramsText);
	
		// If user selected to show the top ten Twitter Users add it to the report
		if ($report->showtwittertopten)
		{
			$start321 = microtime(TRUE);
			$docx->addTitle('Top Twitter Users', $paramsTitle2);
	
			$docx->addText($disclaimer, $disclaimerText);
			$text = "The following table shows the ten Twitter users that sent the most tweets in this dataset for the ".$event->name." Event:";
			$docx->addText($text, $paramsText);
			
			$valuesTable = array( array("User", "Number of Tweets"));
		
			foreach($topTwitterUsers as $twitterUser)
			{
				array_push($valuesTable, array($twitterUser['fromuser'],$twitterUser['tweetcount']));
			}
		
			$paramsTable = array(
				    'border' => 'single',
				    'border_sz' => 20,
			);
		
			$docx->addTable($valuesTable, $paramsTable);
			global $elapsedtime321;
			$elapsedtime321 = microtime(TRUE)-$start121;
			
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (22, '$elapsedtime121', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();
		}
	
		// If user selected to show the Daily Twitter Breakdown add it to the report
		if ($report->showtwitterdailybreakdown)
		{
			$start221 = microtime(TRUE);
			$docx->addTitle('Daily Twitter Count', $paramsTitle2);
			
			$docx->addText($disclaimer, $disclaimerText);
			$text = "The following table and graph shows the number of Twitter messages sent by date in the tweet dataset for the ".$event->name." Event:";
			$docx->addText($text, $paramsText);
				
			$valuesTable = array( array("Date", "Number of Tweets"));
			
			foreach($tweetCountByDate as $tweetCount)
			{
				//hack for demo
				//array_push($valuesTable, array($tweetCount['date'],$eventTwitterNum));
				array_push($valuesTable, array($tweetCount['date'],$tweetCount['tweetcount']));
			}
			
			$paramsTable = array(
							    'border' => 'single',
							    'border_sz' => 20,
			);
			
			$docx->addTable($valuesTable, $paramsTable);
			
			$legends = array(
				    'legend' => array('Num Tweets'),
			);
			foreach($tweetCountByDate as $tweetCount)
			{
				$legends[$tweetCount['date']]=array($tweetCount['tweetcount']);
			}
			$args = array(
				    'data' => $legends,
				    'type' => 'colChart',
				    'title' => 'Number of Tweets by Day',
				    'color' => 2,
				    'textWrap' => 0,
				    'sizeX' => 17, 'sizeY' => 7,
				    'jc' => 'center',
				    'font' => 'Arial'
			);
			$docx->addGraphic($args);
			global $elapsedtime221;
			$elapsedtime221 = microtime(TRUE)-$start221;
			
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (25, '$elapsedtime221', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();
		}
	
	
		// If user selected to show the Category Twitter Breakdown add it to the report
		if ($report->option1)
		{
			$start121 = microtime(TRUE);
			$docx->addTitle('Twitter Count by Category', $paramsTitle2);
			
			$docx->addText($disclaimer, $disclaimerText);
			$text = "The following table and graph shows the number of Twitter messages in each category in this dataset for the ".$event->name." Event:";
			$docx->addText($text, $paramsText);
			
			$valuesTable = array( array("Category", "Number of Tweets"));
			foreach($categoryCounts as $tweetCount)
			{
				array_push($valuesTable, array($tweetCount[0],$tweetCount[1]));
			}
				
			$paramsTable = array(
										    'border' => 'single',
										    'border_sz' => 20,
			);
				
			$docx->addTable($valuesTable, $paramsTable);
		
			$legends = array(
							    'legend' => array('Num Tweets'),
			);
			foreach($categoryCounts as $tweetCount)
			{
				$legends[$tweetCount[0]]=array($tweetCount[1]);
			}
			$args = array(
							    'data' => $legends,
							    'type' => 'colChart',
							    'title' => 'Number of Tweets by Category',
							    'color' => 2,
							    'textWrap' => 0,
							    'sizeX' => 17, 'sizeY' => 7,
							    'jc' => 'center',
							    'font' => 'Arial'
			);
			$docx->addGraphic($args);
			global $elapsedtime121;
			$elapsedtime121 = microtime(TRUE)-$start121;
			
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (22, '$elapsedtime121', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();
		}
	
	
	
		// If user selected to show the Top Twenty Hashtags perform the query that gets the needed data.
		if ($report->option2)
		{
			$start421 = microtime(TRUE);
			$docx->addTitle('Top Twenty Hashtags', $paramsTitle2);
			
			$docx->addText($disclaimer, $disclaimerText);
			$text = "The following table shows the twenty hashtags that appeared in the most tweets for the ".$event->name." Event dataset:";
			$docx->addText($text, $paramsText);
			
			$valuesTable = array( array("Hashtag", "Number of Tweets","Percentage of Tweets"));
				
			
			foreach($hashtagCounts as $key => $value)
			{
				$percentTweets=round($value/$eventTwitterNum * 100) . '%';
				array_push($valuesTable, array("#".$key, $value, $percentTweets));
			}
				
			$paramsTable = array(
												    'border' => 'single',
												    'border_sz' => 20,
			);
				
			$docx->addTable($valuesTable, $paramsTable);
			global $elapsedtime421;
			$elapsedtime421 = microtime(TRUE) - $start421;
			
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (31, '$elapsedtime421', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();
		}
		
		
		
		if ($report->option3)
		{
			$start521 = microtime(TRUE);
			$docx->addTitle('Top Twenty Retweets', $paramsTitle2);
				
			$docx->addText($disclaimer, $disclaimerText);
			$text = "The following table shows the twenty tweets with the most retweets for the ".$event->name." Event dataset:";
			$docx->addText($text, $paramsText);
				
			$valuesTable = array( array("Tweet", "Number of Retweets"));
		
			//t.retweetid, rt.text, rt.fromuser, t.MaxTweetCount
			foreach($topRetweets as $topRetweet)
			{
				array_push($valuesTable, array($topRetweet['text'], $topRetweet['MaxTweetCount']));
			}
			
					
			$paramsTable = array(
				'border' => 'single',
				'border_sz' => 20,
			);
		
			$docx->addTable($valuesTable, $paramsTable);
			global $elapsedtime521;
			global $elapsedtime521;
			$elapsedtime521 = microtime(TRUE) - $start521;
						
			$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (34, '$elapsedtime521', '$eventTwitterNum', '$userid')";
			//$command = $connection->createCommand($sql);
			//$performanceLogging = $command->queryAll();
		}
	
		$docx->createDocx($filename);
	
		// Once the phpdocx work is done register the Yii autoload again
		spl_autoload_register(array('YiiBase','autoload'));
	
		unset($docx);
		$document = new TransformDoc();
		//$document->setStrFile($filename.'.docx');
		//$document->generatePDF();
		//unset($document);
	
		header("Content-Type: application/vnd.ms-word");
		//header("Content-Type: application/pdf");
		header("Content-Length: ".filesize($filename.'.docx'));
		//header("Content-Length: ".filesize($filename.'.pdf'));
		header('Content-Disposition: attachment; filename='.$filename.'.docx');
		header('Content-Transfer-Encoding: binary');
		ob_clean();
		flush();
		//readfile($filename.'.pdf');
		//unlink($filename.'.pdf');
		readfile($filename.'.docx');
		unlink($filename.'.docx');
		
		//$myfile = fopen("performanceloggingtest.txt", "w") or die("Unable to open file!");
		//$txt = "the elapsed time is ".number_format(( microtime(true) - $before), 4) . " Seconds\n";
		//fwrite($myfile, $txt);
		//fclose($myfile);
		//print "the elapsed time is ".microtime(TRUE) - $before;
		$elapsedtime = microtime(TRUE) - $before;
		
		
		$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (2, '$elapsedtime', '$eventTwitterNum', '$userid')";
		$command = $connection->createCommand($sql);
		$performanceLogging = $command->queryAll();
		
		global $elapsedtime111 ; 
		global $elapsedtime211 ; 
		global $elapsedtime311 ; 
		global $elapsedtime411 ;
		global $elapsedtime511 ; 
		
		global $elapsedtime121 ; 
		global $elapsedtime221 ; 
		global $elapsedtime321 ; 
		global $elapsedtime421 ;
		global $elapsedtime521 ; 
		
		$totaltime611 = $elapsedtime111 + $elapsedtime211 + $elapsedtime311 + $elapsedtime411 + $elapsedtime511; 
		$totaltime621 = $elapsedtime121 + $elapsedtime221 + $elapsedtime321 + $elapsedtime421 + $elapsedtime521; 
		
		$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (36, '$elapsedtime611', '$eventTwitterNum', '$userid')";
		//$command = $connection->createCommand($sql);
		//$performanceLogging = $command->queryAll();
		
		$sql = "INSERT INTO performanceaudits (taskid, timeinsec, totalnumberoftweets, fromuserid) 
		         VALUES (37, '$elapsedtime621', '$eventTwitterNum', '$userid')";
		//$command = $connection->createCommand($sql);
		//$performanceLogging = $command->queryAll();
		exit;
		}
	
	}
	

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Report::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='report-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	protected function extraStuff()
	{
		
		
		
		$legends = array(
		'legend1' => array(10, 11, 12),
		'legend2' => array(0, 1, 2),
		'legend3' => array(40, 41, 42)
		);
		$args = array(
		'data' => $legends,
		'type' => 'pie3DChart',
		'title' => 'Title first chart',
		'cornerX' => 20, 'cornerY' => 20, 'cornerP' => 30,
		'color' => 2,
		'textWrap' => 0,
		'sizeX' => 10, 'sizeY' => 10,
		'jc' => 'left',
		'showPercent' => 1,
		'font' => 'Times New Roman'
				);
				$docx->addGraphic($args);
			
		
		$paramsHeader = array(
			    'name' => '../files/img/image.png',
		'jc' => 'right',
		'textWrap' => 5,
		'font' => 'Arial'
				);
			
				$docx->addHeader('Header Arial', $paramsHeader);
			
		$paramsHeader = array(
			    'font' => 'Times New Roman'
				);
			
				$docx->addHeader('Header Times New Roman', $paramsHeader);
			
		$paramsFooter = array(
		'pager' => 'true',
		'pagerAlignment' => 'center',
		'font' => 'Arial'
		);
		
		$docx->addFooter('Footer Arial', $paramsFooter);
			
				/*$paramsImg = array(
		'name' => '../files/img/image.png',
		'scaling' => 50,
				'spacingTop' => 100,
		'spacingBottom' => 0,
		'spacingLeft' => 100,
		'spacingRight' => 0,
		'textWrap' => 1,
		'border' => 1,
		'borderDiscontinuous' => 1
		);
		
		$docx->addImage($paramsImg);*/
		
		
		
		
		$text = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, ' .
		'sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut ' .
			    'enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut' .
			    'aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit ' .
			    'in voluptate velit esse cillum dolore eu fugiat nulla pariatur. ' .
			    'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui ' .
			    'officia deserunt mollit anim id est laborum.';
			
				$docx->addTitle('A different Title', $paramsTitle2);
			
				$paramsText = array(
			    'b' => 'single',
			    'font' => 'Arial'
				);
			
				$docx->addText($text, $paramsText);
			
				$valuesList = array(
		'Line 1',
		'Line 2',
		'Line 3',
			    'Line 4',
		'Line 5'
				);
			
		$paramsList = array(
		'val' => 1
		);
		
		$docx->addList($valuesList, $paramsList);
		
		$docx->addBreak('line');
				//$docx->addBreak('page');
			
		$valuesTable = array(
		array(
		11,
		12
		),
		array(
		21,
				22
		),
		);
		
		$paramsTable = array(
		'border' => 'single',
		'border_sz' => 20
		);
		
		
		$docx->addTable($valuesTable, $paramsTable);
		
		$docx->addLink('Link to Google', 'http://www.google.es', 'Arial');
	}
}
