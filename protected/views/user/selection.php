<script type="text/javascript">
function addNewRule() {
	new Ajax.Request('<?php echo Yii::app()->createUrl("user/addToEvent"); ?>' , {
			method: "post",
			parameters: {},
			onSuccess: function(codes) {
				$.fn.yiiGridView.update("rule-grid");
			  }
			});
}

</script>
<?php 
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.jeditable.mini.js');
Yii::app()->clientScript->registerScript('updateCategoryID','
	$("a[class^=editablecategoryid-]").live("click", function () {
		$(this).editable("'.$this->createUrl('rule/updateCategoryID').'", {
		submitdata : function (value,settings){
						return {"Rule[id]":$(this).attr("class").substr("19"),};
                    },
        indicator : "Saving...",
        tooltip   : "Click to edit...",
        type : "select",
        data   : "'.str_replace('"','\\\'',json_encode($categories)).'",
        submit   : "OK",
        name : "Rule[categoryid]"
     });
 });
',CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('updateMoveToCategoryID','
	$("a[class^=editablemovetocategoryid-]").live("click", function () {
		$(this).editable("'.$this->createUrl('rule/updateMoveToCategoryID').'", {
		submitdata : function (value,settings){
                        return {"Rule[id]":$(this).attr("class").substr("25"),};
                    },
        indicator : "Saving...",
        tooltip   : "Click to edit...",
        type : "select",
        data   : "'.str_replace('"','\\\'',json_encode($categories)).'",
        submit   : "OK",
        name : "Rule[movetocategoryid]"
     });
 });
',CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('updateRuleType','
	$("a[class^=editableruletype-]").live("click", function () {
		$(this).editable("'.$this->createUrl('rule/updateRuleType').'", {
		submitdata : function (value,settings){
                        return {"Rule[id]":$(this).attr("class").substr("17"),};
                    },
        indicator : "Saving...",
        tooltip   : "Click to edit...",
        type : "select",
        data   : "{\'0\':\'The Twitter Username\',\'1\':\'The Twitter Message\'}",
        submit   : "OK",
        name : "Rule[ruletype]"
     });
 });
',CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('updateRuleString','
	$("a[class^=editablerulestring-]").live("click", function () {
		$(this).editable("'.$this->createUrl('rule/updateRuleString').'", {
		submitdata : function (value,settings){
                        return {"Rule[id]":$(this).attr("class").substr("19"),};
                    },
        indicator : "Saving...",
        tooltip   : "Click to edit...",
        type : "textarea",
        submit   : "OK",
        name : "Rule[rulestring]"
     });
 });
',CClientScript::POS_READY);
?>

<h1>Add Users </h1>
Click the checkbox for the user(s) you want to add, then click the "Add Selected User(s)" button.
<?php 

echo CHtml::beginForm(); 
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'selection-grid',
	'dataProvider'=>$model->searchForAdding(),
	'filter'=>$model,
	'columns'=>array(
		'stopPublish'=>array('class'=>'CCheckBoxColumn','id' => 'selectedIds',),
		'fullname' => array(
			                'type'=>'html',
			                'value' => '$data->fullname',
			                'header' => 'User Name'
		),
	),
	'selectableRows'=>2,
)); 
?><div>
<?php echo CHtml::submitButton('Add Selected User', array('name' => 'Add')); ?>
</div>
<?php echo CHtml::endForm(); ?>


<script  type="text/javascript">
    
		$(".close").click(
			function () {
				$(this).parent().fadeTo(400, 0, function () { // Links with the class "close" will close parent
					$(this).slideUp(600);
				});
				return false;
			}
		);


</script>