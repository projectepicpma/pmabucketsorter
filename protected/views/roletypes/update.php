<?php
/* @var $this RoletypesController */
/* @var $model Roletypes */

$this->breadcrumbs=array(
	'Roletypes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Roletypes', 'url'=>array('index')),
	array('label'=>'Create Roletypes', 'url'=>array('create')),
	array('label'=>'View Roletypes', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Roletypes', 'url'=>array('admin')),
);
?>

<h1>Update Roletypes <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>