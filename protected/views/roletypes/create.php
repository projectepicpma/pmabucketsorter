<?php
/* @var $this RoletypesController */
/* @var $model Roletypes */

$this->breadcrumbs=array(
	'Roletypes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Roletypes', 'url'=>array('index')),
	array('label'=>'Manage Roletypes', 'url'=>array('admin')),
);
?>

<h1>Create Roletypes</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>