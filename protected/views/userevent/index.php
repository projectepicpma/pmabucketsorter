<?php
$this->breadcrumbs=array(
	'Userevents',
);

$this->menu=array(
	array('label'=>'Create Userevent', 'url'=>array('create')),
	array('label'=>'Manage Userevent', 'url'=>array('admin')),
);
?>

<h1>Userevents</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
