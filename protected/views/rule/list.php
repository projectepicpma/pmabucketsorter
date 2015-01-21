<script type="text/javascript">
function addNewRule() {
	//alert("Inside Add New Rule!"+<?php echo $model->categoryid?>);
	new Ajax.Request('<?php echo Yii::app()->createUrl("rule/create"); ?>' , {
			method: "post",
			parameters: {},
			onSuccess: function(codes) {
				$.fn.yiiGridView.update("rule-grid");
			  }
			});
}

function runRulesNow() {
	new Ajax.Request('<?php echo Yii::app()->createUrl("rule/runRules"); ?>' , {
			method: "post",
			parameters: {},
			onSuccess: function(codes) {
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

<h1>Manage Rules </h1>
To change a value in the table, click on the value you want to edit.
<?php 
function create_category($data_id,$data_categoryid)
{
	// create the HTMl for the category links
	$categories=Category::loadCategories();
	$category_link="<a class=editablecategoryid-".$data_id.">".$categories[$data_categoryid]."</a>";
	return $category_link;
}

function create_movetocategory($data_id,$data_movetocategoryid)
{
	// create the HTMl for the movetocategory links
	$categories=Category::loadCategories();
	$movetocategory_link="<a class=editablemovetocategoryid-".$data_id.">".$categories[$data_movetocategoryid]."</a>";
	return $movetocategory_link;
}

function create_ruletype($data_id,$data_ruletype)
{
	// create the HTMl for the movetocategory links
	//$ruletypes=Rule::loadRuleTypes();
	$ruletypes=array('0'=> 'The Twitter Username', '1'=>'The Twitter Message');
	$ruletype_link="<a class=editableruletype-".$data_id.">".$ruletypes[$data_ruletype]."</a>";
	return $ruletype_link;
}

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'rule-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'categoryid' => array(
			                'type'=>'html',
			                'value' => 'create_category($data->id,$data->categoryid)',
			                'header' => 'Move Tweets in this Category...'
		),
		'movetocategoryid' => array(
	                'type'=>'html',
	                'value' => 'create_movetocategory($data->id,$data->movetocategoryid)',
	                'header' => 'to Category...'
        ),
		'ruletype'=> array(
	                'type'=>'html',
	                'value' => 'create_ruletype($data->id,$data->ruletype)',
	                'header' => 'If...'
        ),
		'rulestring'=> array(
	                'type'=>'html',
	                'value' => '"<a class=\'editablerulestring-".$data->id."\'>".$data->rulestring."</a>"',
	                'header' => 'Contains the String...'
	    ),
		array(
			'class'=>'CButtonColumn',
	        'template' => '{delete}',          
            ),
	),
)); 
?>
<button type="button" onclick=addNewRule()>Add New Rule</button>
<button type="button" onclick=runRulesNow()>Run Rules Now</button>

<script  type="text/javascript">
    
 //Close button:

		$(".close").click(
			function () {
				$(this).parent().fadeTo(400, 0, function () { // Links with the class "close" will close parent
					$(this).slideUp(600);
				});
				return false;
			}
		);


</script>