<?php
$this->breadcrumbs=array(
	'Buckets'=>array('index'),
	'Create',
);

$this->menu=array(
array('label'=>'List Buckets', 'url'=>array('index')),
array('label'=>'Manage Buckets', 'url'=>array('admin')),
);
?>

<h1>Create Buckets</h1>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>