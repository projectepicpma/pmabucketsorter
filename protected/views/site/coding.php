<?php Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('tweets-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<html>
<head>

<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/prototype.js');?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/scriptaculous.js');?>

<?php date_default_timezone_set($userTimezone); ?>

<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>

<script type="text/javascript">
var pause_mode=false;

function tweetsDraggable()
{
	$$('#tweets-grid table tbody tr').each(
			function(row) {
				//alert("row");
				// Make the row draggable
				new Draggable(
					row,
			        {
			    	    revert: true,
			          	ghosting: true
			        }
			    );
			} 
		);
}

window.onload = function() {
    // Once the document is ready make the Tweets displayed draggable
    tweetsDraggable();
}


// Makes all of the draggable rows selected if selected=true.
// If selected=false the draggable rows are all unselected.
function selectAll(selected){
	if (selected) {
		$.fn.yiiGridView.selectAll('tweets-grid');
	}
	else
	{
		$.fn.yiiGridView.removeSelection('tweets-grid');
	}
}

function moveItem( draggable,droparea){
	var selectedcategory = droparea.id.replace("node_","");
	//alert( "MoveItem selected category="+selectedcategory);
  
   	// update the database with a code for the tweet
   	new Ajax.Request('<?php echo Yii::app()->createUrl("coding/create"); ?>' , {
		method: "post",
		parameters: {tweetid:draggable.id, code:selectedcategory},
		onSuccess: function(codes) {
			$.fn.yiiGridView.update('tweets-grid');
		  }
        });

}

function emailForm(){
	//alert("Inside emailForm!");
	var daReferrer = document.referrer;
	var email = "";
	//var errorMsg = "here here here is the error error error error";
	var subject = "Important Twitter Message!";
	var body_message = "The following Tweets are of interest:";
	//alert("email Form");
	
	//var rows = document.getElementById('draggables').rows;
	//var num_rows = rows.length;
	//f/or (var i = 0; i < num_rows; ++i) {
	//    var cells = rows[i].cells;
	//    if (rows[i].getAttribute("class") =="rowselected") {
	//    	body_message+="-"+cells[1].innerHTML+"\n\n";
	//    }
	//}		
	var rows= $.fn.yiiGridView.getSelection('tweets-grid');
	var num_rows = rows.length;
	//alert("num_rows="+num_rows);
	for (var i = 0; i < num_rows; ++i) {
		// this returns the id of the tweet
		alert("row="+rows[i]);
	    //var cells = rows[i].cells;
	    //alert("cell="+cells[1].innerHTML);
	    //body_message+="-"+cells[1].innerHTML+"\n\n";
	}	

	var mailto_link = 'mailto:'+email+'?subject='+subject+'&body='+body_message;

	win = window.open(mailto_link,'emailWindow');
	if (win && win.open &&!win.closed) win.close();
	}
function deleteNode(obj){
	
   var retVal = confirm((obj).attr('rel')+" and all it\'s subcategories will be deleted.you want to continue?");
   if( retVal == true ){
   	jQuery("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").jstree("remove",obj);    
	  return true;
   }   
	  return false;
   
}

(function($) {
	$(function () {

	$("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>")
		.jstree({
        	"html_data" : {
	        	"ajax" : {
                	"type":"POST",
 	            	"url" : "<?php echo $baseUrl;?>/category/fetchTree",
	            	"data" : function (n) {
	                	return {
                        	id : n.attr ? n.attr("id") : 0,
                            	"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
                            };
	                	}
  	            	}
	       		 },

			"contextmenu":  {
	              "items": function(node) {
	  				//alert("inside Custom Menu!");
	  				   
	  			    // The default set of all items
	  			    var items = {
	  			        "rename" : {
	  			        	"label" : "Rename",
	  			            "action" : function (obj) { this.rename(obj); }
	  			        },
	  			        "rules" : {
	  			        	"label" : "Rules",
	  			            "action" : function (obj) { 
	  			            	//parent_id=data.rslt.parent.attr("id").replace("node_","");
	  			            	$category_id=(obj).attr("id").replace("node_","");
	                            $.ajax({
	                     			type: "POST",
	                     			url: "<?php echo $baseUrl;?>/rule/returnForm",
	                      	 		data:{   'category_id':   $category_id,
	                                  	"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
	                                                           },
	                            	beforeSend : function(){
	                                                      $("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").addClass("ajax-sending");
	                                                              },
	                            	complete : function(){
	                                                        $("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").removeClass("ajax-sending");
	                                                              },
	                           		success: function(data){

	                         						$.fancybox(data,
								                         {    "transitionIn"	:	"elastic",
								                             "transitionOut"    :      "elastic",
								                              "speedIn"		:	600,
								                             "speedOut"		:	200,
								                             "overlayShow"	:	false,
								                             "hideOnContentClick": false,
								                              "onClosed":    function(){} //onclosed function
								                         })//fancybox

	                     			} //success
	                 			});//ajax
	  			            }
                 		},
	  					"remove" : {
	  			        	"label"	: "Delete",
	  			        	"action" : function (obj) {
	  			        		deleteNode(obj);
	  			                } // action
	  						},// remove
	  					"create" : {
	  						"label"	: "Create",
	  						"action" : function (obj) { this.create(obj); },
	  						"separator_after": false
	  						}
	  			    };

	  			    if(node.attr('root') )
	                  {
	  			    	delete items.rename;
	  			    	delete items.remove;
	  			    	delete items.remove;
	                  }
	  			    return items;
	  			    }},//context menu

	        "themes" : {
	        	"theme" : "default",
	        	"dots" : true,
	        	"icons" : false
				},
			// the `plugins` array allows you to configure the active plugins on this instance
			"plugins" : ["themes","html_data","contextmenu","crrm","ui"],
			// each plugin you have included can have its own config object
			"core" : { "initially_open" : [ <?php echo $open_nodes?> ],'userT_parents':true}
			// it makes sense to configure a plugin only if overriding the defaults
			}) //jstree
			
        ///EVENTS
        .bind("filter.jstree", function (e, data) {
			$.ajax({
            	type:"POST",
			   	url:"<?php echo $baseUrl;?>/category/rename",
			   	data:  {
					"id" : data.rslt.obj.attr("id").replace("node_",""),
                    "new_name" : data.rslt.new_name,
			        "YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
                     },
                beforeSend : function(){
                	$("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").addClass("ajax-sending");
                    },
                complete : function(){
                    $("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").removeClass("ajax-sending");
                    },
				success:function (r) {  response= $.parseJSON(r);
					if(!response.success) {
						$.jstree.rollback(data.rlbk);
				    }else{
                    	data.rslt.obj.attr("rel",data.rslt.new_name);
                    }
					}
				}); //ajax
			}) // bind rename

        .bind("rename.jstree", function (e, data) {
			$.ajax({
            	type:"POST",
			   	url:"<?php echo $baseUrl;?>/category/rename",
			   	data:  {
					"id" : data.rslt.obj.attr("id").replace("node_",""),
                    "new_name" : data.rslt.new_name,
			        "YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
                     },
                beforeSend : function(){
                	$("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").addClass("ajax-sending");
                    },
                complete : function(){
                    $("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").removeClass("ajax-sending");
                    },
				success:function (r) {  response= $.parseJSON(r);
					if(!response.success) {
						$.jstree.rollback(data.rlbk);
						if (response.root) {
							alert("Can not rename the root category!");
						}
				    }else{
                    	data.rslt.obj.attr("rel",data.rslt.new_name);
                    }
					}
				}); //ajax
			}) // bind rename

        .bind("remove.jstree", function (e, data) {
			$.ajax({
                type:"POST",
			    url:"<?php echo $baseUrl;?>/category/remove",
			    data:{
					"id" : data.rslt.obj.attr("id").replace("node_",""),
			        "YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
                    },
                beforeSend : function(){
                	$("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").addClass("ajax-sending");
                    },
                complete: function(){
                	$("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").removeClass("ajax-sending");
                    },
			  	success:function (r) {  response= $.parseJSON(r);
					if(!response.success) {
						$.jstree.rollback(data.rlbk);
				        };
			  		}
				});
			})

        .bind("create.jstree", function (e, data) {
        	newname=data.rslt.name;
            //alert("new node name:" + newname);
            parent_id=data.rslt.parent.attr("id").replace("node_","");
            //alert("new node parent id:" + parent_id);
            $.ajax({
            	type: "POST",
            	url: "<?php echo $baseUrl;?>/category/create",
            	data:{   
                	"create_root" : false,
                	'name': newname,
                	'parent_id':   parent_id,
                   	"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
                    },
                beforeSend : function(){
                	$("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").addClass("ajax-sending");
                    },
                complete : function(){
                    $("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").removeClass("ajax-sending");
                    },
               	success: function (r) {  response= $.parseJSON(r);
               		//alert("response:"+response.id);
					if(!response.success) {
						$.jstree.rollback(data.rlbk);
				        } //success
				        else
				        {
				        	data.rslt.obj.attr("id",response.id);
				        	data.rslt.obj.attr("rel",data.rslt.name);
				        	Droppables.add(
	      						response.id,
	      						{
	      							hoverclass: 'hoverActive',
	      						    onDrop: moveItem
	      						});
				        }
			  		}
                });//ajax
			})
	      	.bind("loaded.jstree", function (event, data) {
	      	 //After the tree is loaded, make the tree items drop targets
	      	 $$("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?> li").each(
	      					function(item) {
	      						var itemcategory = item.id.replace("node_","");
	      						if (itemcategory==<?php echo $level;?>)
	      						{
	      							var cells = item.getElementsByTagName("a");   
	      							cells[0].setAttribute("class","categoryselected");
	      						}
	      						
	      						  Droppables.add(
	      						item.id,
	      						{
	      							hoverclass: 'hoverActive',
	      						    onDrop: moveItem
	      						});
	      					} 
	      				);
	      		})
      		.bind("select_node.jstree", function (event, data) {
                var selectedObj = data.rslt.obj;
            	var selectedcategory = selectedObj.attr("id").replace("node_","");
                //alert("select node="+selectedcategory);
                
                var selectedBehavior = false;

                var rows= $.fn.yiiGridView.getSelection('tweets-grid');
            	if (rows.length>0)
            	{
                	selectedBehavior=true;
      
                	$.each(rows, function(index, value) {
                		new Ajax.Request('<?php echo Yii::app()->createUrl("coding/createFromId"); ?>' , {
                			method: "post",
                		    parameters: {id:value, code:selectedcategory},
                			onSuccess: function(codes) {
                				//updateCodes(codes, index);
                				$.fn.yiiGridView.update('tweets-grid');
                			  }
                		});
                	});
            	}
            	$.fn.yiiGridView.removeSelection('tweets-grid');
		       	if (!selectedBehavior)
			       	{
					    window.location.replace("/bucketsorter/site/coding?level="+selectedcategory);	    
			       	}
             })
      		
		.bind("move_node.jstree", function (e, data) {
			data.rslt.o.each(function (i) {
				//jstree provides a whole  bunch of properties for the move_node event
                //not all are needed for this view,but they are there if you need them.
                //Commented out logs  are for debugging and exploration of jstree.

                 next= jQuery.jstree._reference('#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>')._get_next (this, true);
                 previous= jQuery.jstree._reference('#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>')._get_prev(this,true);

				 // can't move the root node
                 if ($(this).attr('root'))
                 {
                 	$.jstree.rollback(data.rlbk);
                 	exit;
                 }
                 pos=data.rslt.cp;
                 moved_node=$(this).attr('id').replace("node_","");
                 next_node=next!=false?$(next).attr('id').replace("node_",""):false;
                 previous_node= previous!=false?$(previous).attr('id').replace("node_",""):false;
                 new_parent=$(data.rslt.np).attr('id').replace("node_","");
                 old_parent=$(data.rslt.op).attr('id').replace("node_","");
                 ref_node=$(data.rslt.r).attr('id').replace("node_","");
                 ot=data.rslt.ot;
                 rt=data.rslt.rt;
                 copy= typeof data.rslt.cy!='undefined'?data.rslt.cy:false;
                 copied_node= (typeof $(data.rslt.oc).attr('id') !='undefined')? $(data.rslt.oc).attr('id').replace("node_",""):'UNDEFINED';
                 new_parent_root=data.rslt.cr!=-1?$(data.rslt.cr).attr('id').replace("node_",""):'root';
                 replaced_node= (typeof $(data.rslt.or).attr('id') !='undefined')? $(data.rslt.or).attr('id').replace("node_",""):'UNDEFINED';

                 //can't move another category to be a root node
                 if (new_parent_root=="root")
                 {
                 	$.jstree.rollback(data.rlbk);
                 	exit;
                 }
                 
  				exit;
				$.ajax({
					async : false,
					type: 'POST',
					url: "<?php echo $baseUrl;?>/category/moveCopy",

					data : {
						"moved_node" : moved_node,
	                    "new_parent":new_parent,
                    	"new_parent_root":new_parent_root,
                    	"old_parent":old_parent,
                    	"pos" : pos,
                    	"previous_node":previous_node,
                    	"next_node":next_node,
                    	"copy" : copy,
                   		"copied_node":copied_node,
                    	"replaced_node":replaced_node,
				        "YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
                        },
                    beforeSend : function(){
                    	$("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").addClass("ajax-sending");
                        },
                    complete : function(){
                        $("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").removeClass("ajax-sending");
                        },
					success : function (r) {
                    	response=$.parseJSON(r);
						if(!response.success) {
							$.jstree.rollback(data.rlbk);
                            //alert(response.message);
						}
						else {
                            //if it's a copy
                            if  (data.rslt.cy){
								$(data.rslt.oc).attr("id", "node_" + response.id);                         
								if(data.rslt.cy && $(data.rslt.oc).children("UL").length) {
									data.inst.refresh(data.inst._get_parent(data.rslt.oc));
								}
                            }
  						}

						}
					}); //ajax
				});//each function
			});   //bind move event
	});
         ;//JSTREE FINALLY ENDS (PHEW!)

} ) ( jQuery );


</script>

<style type="text/css">
div.droparea {
	float: left;
	margin-left: 16px;
	width: 172px;
	border: 3px ridge maroon;
	text-align: center;
	font-size: 24px;
	padding: 9px;
	float: left;
}

.rowselected {
	background: #CCC7A3;
	color: #000;
}

.rowunselected {
	background: #FFFFFF;
	color: #000;
}
</style>
</head>
<body>
<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'dialogReport',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Create a Report',
        'autoOpen'=>false,
		'modal'=>true,
    ),
)); ?>
	<div class="divForForm"></div>

  <?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');

?>

<script type="text/javascript">
// here is the magic
function addReport()
{
    <?php echo CHtml::ajax(array(
            'url'=>array('report/createReport'),
            'data'=> "js:$(this).serialize()",
            'type'=>'post',
            'dataType'=>'json',
            'success'=>"function(data)
            {
                if (data.status == 'failure')
                {
                    $('#dialogReport div.divForForm').html(data.div);
                          // on submit-> call the addReport function again!
                    $('#dialogReport div.divForForm form').submit(addReport);
                }
                else
                {
                	$('#dialogReport').dialog('close');
                	generateReport(data.reportid);
                }
            } ",
            ))?>;
    return false; 
}

function generateReport(reportid)
{
	var reportGenerationUrl= "<?php echo Yii::app()->createUrl('report/generateReport'); ?>";
   	reportGenerationUrl= reportGenerationUrl+"?reportid="+reportid;
   	//alert("reportGenerationUrl: "+reportGenerationUrl);
    window.location.replace(reportGenerationUrl);
    
    return false; 
 
}
 
</script>

	<table>
		<tr>
			<td id="droppables" width="250px">
			Inside the "<?php echo $currentBucket->name?>" Category
	<!--The tree will be rendered in this div-->

	<div id="<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>" style="width: 230px"></div>
				
				
				


				<button type="button" onclick=emailForm()>Email</button>
				<?php echo CHtml::button('Report', array('onclick'=>"{addReport(); $('#dialogReport').dialog('open');}",)); ?>
				<?php echo CHtml::button('Export', array('submit' => array('tweets/export'))); ?>

</td>
			<td>
				<div >

<?php 
function linkify_twitter_status($status_text)
{
	// linkify URLs
	$status_text = preg_replace(
    '/(https?:\/\/\S+)/',
    '<a href="\1" target="_blank">\1</a>',
	$status_text
	);

	// linkify twitter users
	$status_text = preg_replace(
    '/(^|\s)@(\w+)/',
    '\1<a href="http://twitter.com/\2" target="_blank">@\2</a>',
	$status_text
	);

	// linkify tags
	$status_text = preg_replace(
    '/(^|\s)#(\w+)/',
    '\1<a href="http://search.twitter.com/search?q=%23\2" target="_blank">#\2</a>',
	$status_text
	);
	
	return $status_text;
}

function linkify_twitter_user($user_text)
{
	// linkify twitter user
	$user_text = preg_replace(
    '/(\w+)/',
    '<a href="http://twitter.com/\1"  target="_blank">\1</a>',
	$user_text
	);

	return $user_text;
}
function linkify_twitter_profileimg($user_img, $user)
{
	// linkify profile picture link
	$user_img_link="<img class=\"profile\" width=\"48\" height=\"48\" src=\"".$user_img."\" title=\"".$user."\">";

	return $user_img_link;
}

function create_actions($tweet_id)
{
	// create the HTMl for the action links
	$action_link="<a class=\"tweet_reply\" target=_new href=\"https://twitter.com/intent/tweet?in_reply_to=".$tweet_id."\"></a>"
			."<a class=\"tweet_retweet\" target=_new href=\"https://twitter.com/intent/retweet?tweet_id=".$tweet_id."\"></a>";
	return $action_link;
}




$this->widget('ext.TweetGridView', array(
	'id'=>'tweets-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
            'type'=>'raw',
            'value'=>'linkify_twitter_profileimg($data->profileimg, $data->fromuser)',
        ),
		array(
            'name'=>'text',
            'type'=>'raw',
            'header'=>'Text',
            'value'=>'linkify_twitter_status($data->text)',
        ),
		array(
            'name'=>'fromuser',
            'type'=>'raw',
            'header'=>'User',
            'value'=>'linkify_twitter_user($data->fromuser)',
		),
		array(
            'name'=>'created',
            'header'=>'Created',
            'value'=>'date("Y-m-d H:i:s T", strtotime($data->created." GMT"))',
		),
		'categories',
		array(
            'type'=>'raw',
            'value'=>'create_actions($data->tweetid)',
		),
		/* Take out for right now
		 * array(
			'class'=>'CButtonColumn',
					'template' => '{delete}',
                    'deleteButtonUrl'=>'Yii::app()->createUrl("tweets/delete", array("id"=>$data->id))',
		),*/
	),
   'afterAjaxUpdate'=>'function(id,data){tweetsDraggable()}',
   'selectableRows'=>2,
)); ?>
					
					
				</div>
				<button type="button" onclick=selectAll(true)>Select All</button>

				<button type="button" onclick=selectAll(false)>Unselect All</button>
				
			</td>
		</tr>
	</table>



				

</body>
</html>
