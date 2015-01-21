<?php
$this->breadcrumbs=array(
	'Buckets'=>array('index'),
$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
array('label'=>'List Buckets', 'url'=>array('index')),
array('label'=>'Create Buckets', 'url'=>array('create')),
array('label'=>'View Buckets', 'url'=>array('view', 'id'=>$model->id)),
array('label'=>'Manage Buckets', 'url'=>array('admin')),
);
?>

<h1>
	Update Buckets
	<?php echo $model->id; ?>
</h1>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>