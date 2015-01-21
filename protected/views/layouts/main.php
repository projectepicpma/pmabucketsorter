<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="en" />

<!-- blueprint CSS framework -->
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css"
	media="screen, projection" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css"
	media="print" />
<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
<link rel="stylesheet" type="text/css"
	href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<script type="text/javascript">
	function ReloadPage()
	{
		var locationUrl=document.location.href;

		// If the reloadpage function is called then the selected event has changed.
		// If a level is assigned, unassign it because it will not be a level
		// associated with the new selected event.
		if (locationUrl.indexOf("?level")!=1)
		{
			locationUrl=locationUrl.substring(0,locationUrl.indexOf("?level"));
			document.location=locationUrl;
		}
		else
		{
			document.location.reload();
		}
	}
	</script>
</head>

<body>
<?php date_default_timezone_set("America/Denver");?>
	<div class="container" id="page">

		<div id="header">
			<div id="logo">
			<?php echo CHtml::encode(Yii::app()->name); ?> 
		<?php if(!Yii::app()->user->isGuest) {
				echo "- ";
				echo CHtml::dropDownList('event_id',User::getUserSelectedEvent(),Event::loadEventsByUser(),
				array(
				'ajax' => array(
				'type'=>'POST', //request type
				'url'=>CController::createUrl('user/selectEvent'), 
				'data'=>array('event_id'=>'js:$(\'#event_id\').val()'),
				'success'=>'js:ReloadPage()',
				)));
				echo CHtml::button('Create New Event', array('submit' => array('event/create'))); 
			}?>
	</div>
	</div><!-- header -->
			
		<div id="mainmenu">
			
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
		array('label'=>'Event Details', 'url'=>array('/site/index')),
		array('label'=>'Event Access', 'url'=>array('/site/eventaccess')),
		array('label'=>'Search Terms', 'url'=>array('/site/searchterms')),
		array('label'=>'Streaming', 'url'=>array('/site/streaming')),
		array('label'=>'Archive', 'url'=>array('/site/coding')),
		//array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
		//array('label'=>'Tree', 'url'=>array('/site/tree')),
		array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
		array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
		array('label'=>'Manage the application', 'url'=>array('/site/links'), 'visible'=>Yii::app()->user->checkAccess('admin')),
		),
		)); ?>
		</div>
		<!-- mainmenu -->
		
		
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by <a href="http://epic.cs.colorado.edu"> Project EPIC</a>.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->

</div>
	<!-- page -->

</body>
</html>
