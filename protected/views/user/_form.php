<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fullname'); ?>
		<?php echo $form->textField($model,'fullname',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'fullname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'profile'); ?>
		<?php echo $form->textArea($model,'profile',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'profile'); ?>
	</div>
	<div class="row">
		<?php 
		/*$check = Roletypes::model()->findAll();
		$usersArr = CHtml::listData( $check, 'id' , 'description');
		print_r( $usersArr );*/
		echo $form->labelEx($model,'role'); 
		$list=CHtml::listData(Roletypes::model()->findAll(), 'id', 'description');
		echo CHtml::dropDownList('pick_role', $model->role,$list, array('empty' => '(Select a state)')); 
		?>
		
	
	
	
	
	<?php /*echo CHtml::dropDownList('pick_role',null,Roletypes::loadRoles(),
				array(
				'ajax' => array(
					'type'=>'POST', //request type
					'url'=>CController::createUrl('user/selectRole'), 
					'data'=>array('role_id'=>'js:$(\'#pick_role\').val()'),
					
					),
				'empty' => 'Select the role...')
				);*/?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->