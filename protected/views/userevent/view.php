<?php
$this->breadcrumbs=array(
	'Userevents'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Userevent', 'url'=>array('index')),
	array('label'=>'Create Userevent', 'url'=>array('create')),
	array('label'=>'Update Userevent', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Userevent', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Userevent', 'url'=>array('admin')),
);
?>

<h1>View Userevent #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	
	'attributes'=>array(
		'User Name',
		'Event Name',
		'User Event id',
	),
	
)); ?>
