<div class="form">
<script>  
function hideOrNot(element){
    var checked = $(element).is(':checked');
    if (checked) {
        $('#hiddenDiv').toggle();
    } else {
        // reset values
        $('#hiddenDiv').toggle();
    }
}
  $(function() {
    $( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  
  
</script>
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
		
		<?php echo $form->checkBox($model,'showtwitterdailybreakdown',array('onchange'=>'hideOrNot(this);')); ?>
		<?php echo $form->labelEx($model,'showtwitterdailybreakdown'); ?>
		<?php echo $form->error($model,'showtwitterdailybreakdown'); ?>	
		
		
		<div id="hiddenDiv" style="display: inline">
			<br>
			&nbsp&nbsp
			<?php
			echo $form->labelEx($model,'breakdownfromd'); 
			echo $form->dateField($model, 'breakdownfromd'); 
			echo $form->error($model,'breakdownfromd'); 
			 ?>
			<br />
			&nbsp&nbsp
			<?php
			echo $form->labelEx($model,'breakdowntod'); 
			echo $form->dateField($model, 'breakdowntod'); 
			echo $form->error($model,'breakdowntd');
			?>
			
			<!--<label for="from">From date</label>
			&nbsp&nbsp<input type="text" id="from" name="from">
			<br>
			&nbsp&nbsp<label for="to">To Date</label>
			&nbsp&nbsp<input type="text" id="to" name="to">-->
        </div>
 	
        
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
