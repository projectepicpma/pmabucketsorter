<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('text')); ?>:</b>
	<?php echo CHtml::encode($data->text); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tweetid')); ?>:</b>
	<?php echo CHtml::encode($data->tweetid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('touserid')); ?>:</b>
	<?php echo CHtml::encode($data->touserid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('touser')); ?>:</b>
	<?php echo CHtml::encode($data->touser); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('fromuserid')); ?>:</b>
	<?php echo CHtml::encode($data->fromuserid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('created')); ?>:</b>
	<?php echo CHtml::encode($data->created); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('fromuser')); ?>:</b>
	<?php echo CHtml::encode($data->fromuser); ?>
	<br />

	*/ ?>

</div>