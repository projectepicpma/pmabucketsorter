<?php
$this->pageTitle=Yii::app()->name . ' - Create Event';
$this->breadcrumbs=array(
	'Create Event',
);
?>
<h1>Create New Event</h1>
<?php echo CHtml::label('Event Name     ','eventName'); ?>
<?php echo CHtml::textfield('eventName', 'Boulder Four Mile Fire'); ?>
<br><br>
<?php echo CHtml::label('Search Terms   ','searchTerms'); ?>
<?php echo CHtml::textfield('searchTerms',"boulder"); ?>
<br><br>
<?php echo CHtml::button('Create', array('submit' => array('site/coding?index=1'))); ?>
