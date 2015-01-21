<html>
<head>

<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/prototype.js');?>

<script type="text/javascript">
function addNewFilter() {
		 $.ajax({
                    type: "POST",
                    url: '<?php echo Yii::app()->createUrl("filter/returnForm"); ?>',
                      data:{ "YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
                                                          },
                           beforeSend : function(){
                                                     },
                           complete : function(){
                                                       },
                          success: function(data){

                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //success
                });//ajax
}

</script>
<?php 
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.jeditable.mini.js');

Yii::app()->clientScript->registerScript('updateFilterActive','
	$("a[class^=editablefilteractive-]").live("click", function () {
		$(this).editable("'.$this->createUrl('filter/updateActive').'", {
		submitdata : function (value,settings){
                        return {"Filter[id]":$(this).attr("class").substr("21"),};
                    },
        indicator : "Saving...",
        tooltip   : "Click to edit...",
        type : "select",
        data   : " {\'0\':\'No\',\'1\':\'Yes\'}",
        submit   : "OK",
        name : "Filter[active]"
     });
 });
',CClientScript::POS_READY);

Yii::app()->clientScript->registerScript('updateFilterString','
	$("a[class^=editablefilterstring-]").live("click", function () {
		$(this).editable("'.$this->createUrl('filter/updateFilterString').'", {
		submitdata : function (value,settings){
                        return {"Filter[id]":$(this).attr("class").substr("21"),};
                    },
        indicator : "Saving...",
        tooltip   : "Click to edit...",
        type : "textarea",
        width: "200",
        submit   : "OK",
        name : "Filter[filter]"
     });
 });
',CClientScript::POS_READY);
?>
</head>
<body>
<h1>Manage Search Terms </h1>

To change a value in the table, click on the value you want to edit.
<?php 

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'filter-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'active'=> 	array(
	                'type'=>'html',
	                'value' => function ($data){
	                	$label=$data->active? "Yes":"No";
                        return "<a class=editablefilteractive-".$data->id.">".$label."</a>";
                    },
	                'header' => 'Active'
		),
		'filter'=> array(
	                'type'=>'html',
	                'value' => '"<a class=\'editablefilterstring-".$data->id."\'>".$data->filter."</a>"',
	                'header' => 'Keyword(s)'
		),
		array(
			'class'=>'CButtonColumn',
	        'template' => '{delete}',    
			'buttons'=>array(
			        'delete' => array(
			            'url'=>'Yii::app()->createUrl("filter/delete", array("id"=>$data->id))',
						),

					),
		),
	),
));
?>
<button type="button" onclick=addNewFilter()>Add New Term</button>
</body>
</html>
