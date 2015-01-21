<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'report-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model,'showtwittertopten'); ?>
		<?php echo $form->labelEx($model,'showtwittertopten'); ?>
		<?php echo $form->error($model,'showtwittertopten'); ?>
		<br>
		
		<?php echo $form->checkBox($model,'showtwitterdailybreakdown'); ?>
		<?php echo $form->labelEx($model,'showtwitterdailybreakdown'); ?>
		<?php echo $form->error($model,'showtwitterdailybreakdown'); ?>
		<br>

		<?php echo $form->checkBox($model,'option1'); ?>
		<?php echo $form->labelEx($model,'option1'); ?>
		<?php echo $form->error($model,'option1'); ?>
		<br>

		<?php echo $form->checkBox($model,'option2'); ?>
		<?php echo $form->labelEx($model,'option2'); ?>
		<?php echo $form->error($model,'option2'); ?>
		<br>
		
		<?php echo $form->checkBox($model,'option3'); ?>
		<?php echo $form->labelEx($model,'option3'); ?>
		<?php echo $form->error($model,'option3'); ?>
		
		
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->