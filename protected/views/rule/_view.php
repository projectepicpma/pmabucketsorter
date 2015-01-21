<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('categoryid')); ?>:</b>
	<?php echo CHtml::encode($data->categoryid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('movetocategoryid')); ?>:</b>
	<?php echo CHtml::encode($data->movetocategoryid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ruletype')); ?>:</b>
	<?php echo CHtml::encode($data->ruletype); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rulestring')); ?>:</b>
	<?php echo CHtml::encode($data->rulestring); ?>
	<br />


</div>