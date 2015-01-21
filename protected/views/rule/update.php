<?php
$this->breadcrumbs=array(
	'Rules'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Rule', 'url'=>array('index')),
	array('label'=>'Create Rule', 'url'=>array('create')),
	array('label'=>'View Rule', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Rule', 'url'=>array('admin')),
);
?>

<h1>Update Rule <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>