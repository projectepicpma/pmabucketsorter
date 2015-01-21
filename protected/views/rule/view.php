<?php
$this->breadcrumbs=array(
	'Rules'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Rule', 'url'=>array('index')),
	array('label'=>'Create Rule', 'url'=>array('create')),
	array('label'=>'Update Rule', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Rule', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Rule', 'url'=>array('admin')),
);
?>

<h1>View Rule #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'categoryid',
		'movetocategoryid',
		'ruletype',
		'rulestring',
	),
)); ?>
