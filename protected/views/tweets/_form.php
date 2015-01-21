<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'tweets-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
		<?php echo $form->textField($model,'text',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'text'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tweetid'); ?>
		<?php echo $form->textField($model,'tweetid',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'tweetid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'touserid'); ?>
		<?php echo $form->textField($model,'touserid',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'touserid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'touser'); ?>
		<?php echo $form->textField($model,'touser',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'touser'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fromuserid'); ?>
		<?php echo $form->textField($model,'fromuserid',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'fromuserid'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
		<?php echo $form->error($model,'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fromuser'); ?>
		<?php echo $form->textField($model,'fromuser',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'fromuser'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->