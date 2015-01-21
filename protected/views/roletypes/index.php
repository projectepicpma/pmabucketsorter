<?php
/* @var $this RoletypesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Roletypes',
);

$this->menu=array(
	array('label'=>'Create Roletypes', 'url'=>array('create')),
	array('label'=>'Manage Roletypes', 'url'=>array('admin')),
);
?>

<h1>Roletypes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
