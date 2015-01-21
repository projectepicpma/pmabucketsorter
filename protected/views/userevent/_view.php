<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('User Event id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('User Name')); ?>:</b>
	<?php echo CHtml::encode($this->getCorrespondingUserName($data->id)); ?>
	<br />
	
	
	<b><?php
	
	echo CHtml::encode($data->getAttributeLabel('Event Name')); ?>:</b>
	<?php echo CHtml::encode($this->getCorrespondingEventName($data->id)); ?>
	<br />


</div>