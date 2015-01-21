<?php
$this->pageTitle=Yii::app()->name . ' - All links';
$this->breadcrumbs=array(
	'All links',
);
?>
<h1>Navigate to:</h1><hr />
<h3>Manage and browse all the users:</h3>
<p><?php echo CHtml::link('Users',array('user/index')); ?></p>
<hr />
<h3>Manage and browse all the userEvents: It will tell you which users are associated with which events</h3>
<p><?php echo CHtml::link('Users associated with events',array('userevent/index')); ?></p>
<hr />
<h3>Manage and browse all the events:</h3>
<p><?php echo CHtml::link('Existing events',array('event/index')); ?></p>
<hr />

