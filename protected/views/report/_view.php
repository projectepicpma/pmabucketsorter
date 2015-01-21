<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('showtwittertopten')); ?>:</b>
	<?php echo CHtml::encode($data->showtwittertopten); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('showtwitterdailybreakdown')); ?>:</b>
	<?php echo CHtml::encode($data->showtwitterdailybreakdown); ?>
	<br />


</div>