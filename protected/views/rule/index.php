<?php
$this->breadcrumbs=array(
	'Rules',
);

$this->menu=array(
	array('label'=>'Create Rule', 'url'=>array('create')),
	array('label'=>'Manage Rule', 'url'=>array('admin')),
);
?>

<h1>Rules</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
