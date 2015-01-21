<?php
$this->pageTitle=Yii::app()->name;

?>

	<script type="text/javascript">
	function ReloadPage()
	{
		var locationUrl=document.location.href;
		// If the reloadpage function is called then the selected event has changed.
		locationUrl=locationUrl.substring(0,locationUrl.indexOf("splash"));
		document.location=locationUrl+"streaming";
	}
	</script>
<body>	
<br>
<center><h1>What would you like to do?</h1></center>
<br><br>
<center><h3>


	<?php echo CHtml::dropDownList('pick_event',null,Event::loadEventsByUser(),
				array(
				'ajax' => array(
					'type'=>'POST', //request type
					'url'=>CController::createUrl('user/selectEvent'), 
					'data'=>array('event_id'=>'js:$(\'#pick_event\').val()'),
					'success'=>'js:ReloadPage()',
					),
				'empty' => 'Select an Event...')
				);?>
	</h3></center>
	<br>
	<center><h2>OR</h2></center>
<br>
				<center><?php echo CHtml::button('Create New Event ', array('submit' => array('event/create'))); ?>
				</center>
</body>
