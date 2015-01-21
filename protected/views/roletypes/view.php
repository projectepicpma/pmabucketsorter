<?php
/* @var $this RoletypesController */
/* @var $model Roletypes */

$this->breadcrumbs=array(
	'Roletypes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Roletypes', 'url'=>array('index')),
	array('label'=>'Create Roletypes', 'url'=>array('create')),
	array('label'=>'Update Roletypes', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Roletypes', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Roletypes', 'url'=>array('admin')),
);
?>

<h1>View Roletypes #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'description',
	),
)); ?>
