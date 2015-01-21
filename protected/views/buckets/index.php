<?php
$this->breadcrumbs=array(
	'Buckets',
);

$this->menu=array(
array('label'=>'Create Buckets', 'url'=>array('create')),
array('label'=>'Manage Buckets', 'url'=>array('admin')),
);
?>

<h1>Buckets</h1>


<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
