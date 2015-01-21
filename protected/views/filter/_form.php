<div class="form">

<?php 
 $formId='filter-form';
 $ajaxUrl=CController::createUrl('filter/create');
$val_error_msg='Error. Keyword was not saved.';
$val_success_message='Keyword was created successfuly.';


$success='function(data){
    var response= jQuery.parseJSON (data);

     $.fn.yiiGridView.update("filter-grid");
     $.fancybox.close();
     }';

$js_afterValidate="js:function(form,data,hasError) {


        if (!hasError) {                         //if there is no error submit with  ajax
        jQuery.ajax({'type':'POST',
                              'url':'$ajaxUrl',
                         'cache':false,
                         'data':$(\"#$formId\").serialize(),
                         'success':$success
                           });
                         return false; //cancel submission with regular post request,ajax submission performed above.
    } //if has not error submit via ajax

else{
return false;       //if there is validation error don't send anything
    }                    //cancel submission with regular post request,validation has errors.

}";

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'filter-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>true,
    'focus'=>array($model,'filter'),
	'clientOptions'=>array('validateOnSubmit'=>true,
                                        'validateOnType'=>false,
                                        'afterValidate'=>$js_afterValidate,
                                        'errorCssClass' => 'err',
                                        'successCssClass' => 'suc',
),

 
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'filter'); ?>
		<?php echo $form->textField($model,'filter',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'filter'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->