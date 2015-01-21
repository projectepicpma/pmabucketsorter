<?php
$this->breadcrumbs=array(
	'Filters'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Filter', 'url'=>array('index')),
	array('label'=>'Manage Filter', 'url'=>array('admin')),
);
?>

<h1>Create Filter</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>