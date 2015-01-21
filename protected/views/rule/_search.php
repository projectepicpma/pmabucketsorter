<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'categoryid'); ?>
		<?php echo $form->textField($model,'categoryid'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'movetocategoryid'); ?>
		<?php echo $form->textField($model,'movetocategoryid'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ruletype'); ?>
		<?php echo $form->textField($model,'ruletype'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'rulestring'); ?>
		<?php echo $form->textField($model,'rulestring',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->