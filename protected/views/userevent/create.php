<?php
$this->breadcrumbs=array(
	'Userevents'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Userevent', 'url'=>array('index')),
	array('label'=>'Manage Userevent', 'url'=>array('admin')),
);
?>

<h1>Create Userevent</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>