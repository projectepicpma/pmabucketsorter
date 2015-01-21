<?php
$this->breadcrumbs=array(
	'Filters'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Filter', 'url'=>array('index')),
	array('label'=>'Create Filter', 'url'=>array('create')),
	array('label'=>'Update Filter', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Filter', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Filter', 'url'=>array('admin')),
);
?>

<h1>View Filter #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'filter',
		'eventid',
	),
)); ?>
