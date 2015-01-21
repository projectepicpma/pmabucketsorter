<?php
$this->breadcrumbs=array(
	'Filters'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Filter', 'url'=>array('index')),
	array('label'=>'Create Filter', 'url'=>array('create')),
	array('label'=>'View Filter', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Filter', 'url'=>array('admin')),
);
?>

<h1>Update Filter <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>