<?php
$this->breadcrumbs=array(
	'Buckets'=>array('index'),
$model->name,
);

$this->menu=array(
array('label'=>'List Buckets', 'url'=>array('index')),
array('label'=>'Create Buckets', 'url'=>array('create')),
array('label'=>'Update Buckets', 'url'=>array('update', 'id'=>$model->id)),
array('label'=>'Delete Buckets', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
array('label'=>'Manage Buckets', 'url'=>array('admin')),
);
?>

<h1>
	View Buckets #
	<?php echo $model->id; ?>
</h1>


<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'level',
),
)); ?>
