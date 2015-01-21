<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'rule-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'categoryid'); ?>
		<?php echo $form->textField($model,'categoryid'); ?>
		<?php echo $form->error($model,'categoryid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'movetocategoryid'); ?>
		<?php echo $form->textField($model,'movetocategoryid'); ?>
		<?php echo $form->error($model,'movetocategoryid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ruletype'); ?>
		<?php echo $form->textField($model,'ruletype'); ?>
		<?php echo $form->error($model,'ruletype'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'rulestring'); ?>
		<?php echo $form->textField($model,'rulestring',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'rulestring'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->