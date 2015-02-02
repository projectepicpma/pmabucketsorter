<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'event-form',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'location'); ?>
		<?php echo $form->textField($model,'location',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'location'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'startdate'); 
		// setting to mountain time zone, otherwise the default time zone is UTC
		date_default_timezone_set('America/Denver');
		$defaultdate =  date("Y-m-d");?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model'=>$model,
        		'attribute'=>'startdate',
				'options'=>array(
					'dateFormat'=>'yy-m-d',
					'defaultDate' => $defaultdate,
					'showAnim'=>'fold',
					
			),
				'htmlOptions'=>array(
					'style'=>'height:20px;',
					'value'=>$defaultdate,
					
			),
		));?>
		<?php echo $form->error($model,'startdate'); ?>
		
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enddate'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model'=>$model,
        		'attribute'=>'enddate',
				'options'=>array(
					'dateFormat'=>'yy-m-d',
					'showAnim'=>'fold',
			),
				'htmlOptions'=>array(
					'style'=>'height:20px;'
			),
		));?>
		<?php echo $form->error($model,'enddate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'summary'); ?>
		<?php echo $form->textArea($model,'summary',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'summary'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->