<?php
$this->breadcrumbs=array(
	'Userevents'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Userevent', 'url'=>array('index')),
	array('label'=>'Create Userevent', 'url'=>array('create')),
	array('label'=>'View Userevent', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Userevent', 'url'=>array('admin')),
);
?>

<h1>Update Userevent <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>