<?php
$this->breadcrumbs=array(
	'Rules'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Rule', 'url'=>array('index')),
	array('label'=>'Create Rule', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('rule-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Rules</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'rule-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'categoryid',
		'movetocategoryid',
		'ruletype',
		'rulestring',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
