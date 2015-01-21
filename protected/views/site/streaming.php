<html>
<head>

<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/prototype.js');?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/scriptaculous.js');?>

<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
<script type="text/javascript">

var pause_mode=false;
var interval;

function linkify(inputText) {
    var replaceText, replacePattern1, replacePattern2, replacePattern3, replacePattern4;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    //Change Twitter usernames to links to Twitter username homepage.
    replacePattern4 = /(@([a-zA-Z_0-9]+))/gim;
    replacedText = replacedText.replace(replacePattern4, '<a href="https://twitter.com/#!/$2" target="_blank">$1</a>');

    return replacedText;
}

function loadTweets(responses) {
	var tblBody = document.getElementById('draggables');
	
	//for(i=0; i<responses.length; i++)
	//responses in the live stream need to be inserted in reverse order
	for(i=responses.length-1; i>=0; i--)
		{	   			
		//alert("Twitter text");
		// Create new table row and insert tweet data
		//var newRow = tblBody.insertRow(-1);
		var newRow = tblBody.insertRow(0);
		newRow.id = responses[i].tweetid;
		newRow.setAttribute("onclick","selectClick(this)");
		newRow.setAttribute("class","rowunselected");
		newRow.setAttribute("onmouseover","this.style.cursor='pointer'");
		newRow.setAttribute("tweetemail","@"+responses[i].fromuser+": "+responses[i].text);
		var newCell0 = newRow.insertCell(0);
		newCell0.innerHTML = "<div class=\"tweet_image\" width=\"48\" height=\"48\"><img width=\"48\" height=\"48\" src=\""+responses[i].profileimg+"\" title=\""+responses[i].fromuser+"\"></div>";

		
		var newCell1 = newRow.insertCell(1);
		newCell1.innerHTML ="<div class=\"tweet_left\"><div class=\"tweet_screen_name\"><span class=\"tweet_name\">"+responses[i].name+"</span> <a target=_new href=\"https://twitter.com/#!/"+responses[i].fromuser+"\">@"+responses[i].fromuser+"</a></div><div class=\"tweet_text\">" + linkify(responses[i].text)+"</div>"+
		"<div class=\"tweet_date\"><a target=_new href=\"http://twitter.com/"+responses[i].fromuser+"/status/"+responses[i].tweetid+"\">"+responses[i].created+"</a></div></div>";

		var newCell2 = newRow.insertCell(2);
		newCell2.innerHTML = "<a class=\"tweet_reply\" target=_new href=\"https://twitter.com/intent/tweet?in_reply_to="+responses[i].tweetid+"\"></a>"+
		"<a class=\"tweet_retweet\" target=_new href=\"https://twitter.com/intent/retweet?tweet_id="+responses[i].tweetid+"\"></a>";
		
		var newCell3 = newRow.insertCell(3);
		newCell3.innerHTML = responses[i].categories;
		
		//<p><a href="https://twitter.com/intent/favorite?tweet_id=51113028241989632">Favorite</a></p>
		// Make the new row draggable
		new Draggable(
			newRow,
	        {
	    	    revert: true,
	          	ghosting: true
	        }
	    );
	}
	while (tblBody.rows.length>20)
	{
		tblBody.deleteRow(-1);
	}
}
function updateQueueCounter(counter) {
	var counterField = document.getElementById('queuedTweetsCounter'); 
	counterField.innerHTML=counter;
    
}

function emptyQueue() {
	// Call the Tweets Controller loadTweets function, pass the response to the loadTweets
	// javascript function. This is called only once. 
	jQuery.getJSON('<?php echo Yii::app()->createUrl("tweets/loadTweets"); ?>', 
		function(responses){
			loadTweets(responses);
	  	});
}
window.onload = function() {
	// Call the Tweets Controller loadTweets function, pass the response to the loadTweets
	// javascript function. This is called only once. 
	jQuery.getJSON('<?php echo Yii::app()->createUrl("tweets/loadTweets"); ?>', 
		function(responses){
			loadTweets(responses);
	  	});
  	updateQueueCounter(0);

	// Call the Tweets Controller loadTweets function, pass the response to the loadTweets
	// javascript function. This is set up to to run again every 5 seconds. 
	interval = setInterval(function () {
	   	jQuery.getJSON('<?php echo Yii::app()->createUrl("tweets/loadTweet"); ?>', 
			function(responses){
		   		loadTweets(responses);
			})
  		}, 2000);       

	setInterval(function () {
	   	new Ajax.Request('<?php echo Yii::app()->createUrl("tweets/getNumQueued"); ?>' , {
		method: "get",  
		onSuccess: function(numQueued) {
		    updateQueueCounter(numQueued.responseText);
		  }
		})
	}, 2000);
}

function selectClick(row){
	// If the clicked row is already selected, set it to be unselected, 
	// otherwise set the row to be selected.
	if (row.getAttribute("class")=="rowselected")
	{
		row.setAttribute("class","rowunselected");
	}
	else
	{
		row.setAttribute("class","rowselected");
	}
}

// Makes all of the draggable rows selected if selected=true.
// If selected=false the draggable rows are all unselected.
function selectAll(selected){
	$$('#draggables tr').each(
		function(row) {
			if (selected) {
				row.setAttribute("class","rowselected");
			}
			else
			{
				row.setAttribute("class","rowunselected");
			}
		});
}

function dropClick(droparea){
	$$('#draggables tr').each(
		function(row) {
			if (row.getAttribute("class")=="rowselected")
			{
				// update the database with a code for the tweet
				new Ajax.Request('<?php echo Yii::app()->createUrl("coding/create"); ?>' , {
					method: "post",
				    parameters: {tweetid:row.id, code:droparea.id},
					onSuccess: function(codes) {
						updateCodes(codes, row);
					  }
					});
			} 
		} 
	);
}

function updateCodes(codes, row) {
	row.cells[3].innerHTML=codes.responseText;
	row.setAttribute("class","rowunselected");
}
function moveItem( draggable,droparea){
	var selectedcategory = droparea.id.replace("node_","");
	//draggable.cells[1].innerHTML="hello";
	
	draggable.setAttribute("class","rowunselected");
	
   	// update the database with a code for the tweet
   	new Ajax.Request('<?php echo Yii::app()->createUrl("coding/create"); ?>' , {
		method: "post",
		parameters: {tweetid:draggable.id, code:selectedcategory},
		onSuccess: function(codes) {
			//draggable.cells[1].innerHTML=codes.responseText;
			//alert(codes.responseText);

			updateCodes(codes, draggable);
		  }
        });

}

function emailForm(){
	var daReferrer = document.referrer;
	var email = "";
	//var email = "IncidentCommander@email.com";
	//var errorMsg = "here here here is the error error error error";
	var subject = "Twitter Message";
	//var body_message = "%0D%0D%0D%0DThank you "+name+" for submitting this error to us. Please tell us in the space above, what you were doing when the error occurred.%0D%0DReferring Page: "+daReferrer+" %0D%0DException Error Message:%0D-------------------------------------------%0D"+errorMsg;
	var body_message = "";
	//alert("email Form");
	
	var rows = document.getElementById('draggables').rows;
	var num_rows = rows.length;
	for (var i = 0; i < num_rows; ++i) {
	    var cells = rows[i].cells;
	    if (rows[i].getAttribute("class") =="rowselected") {
	    	body_message+="-"+rows[i].getAttribute("tweetemail")+"%0A";
	    }
	}		

	var mailto_link = 'mailto:'+email+'?subject='+subject+'&body='+body_message;

	win = window.open(mailto_link,'emailWindow');
	if (win && win.open &&!win.closed) win.close();
	}

 function pauseStream(){
		if (pause_mode) {
			//alert("Resuming the Stream!");
			pause_mode=false;
			document.getElementById("pauseButton").innerHTML="Pause";

			// Call the Tweets Controller loadTweets function, pass the response to the loadTweets
			// javascript function. This is set up to to run again every 5 seconds. 
			interval = setInterval(function () {
			   	jQuery.getJSON('<?php echo Yii::app()->createUrl("tweets/loadTweet"); ?>', 
					function(responses){
				   		loadTweets(responses);
					})
		  		}, 2000);
		}
		else {
			//alert("Pausing the Stream!");
			pause_mode=true;
			document.getElementById("pauseButton").innerHTML="Resume";
			clearInterval(interval);
		}
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
			"plugins" : ["themes","html_data","contextmenu","crrm","dnd","ui"],
			// each plugin you have included can have its own config object
			"core" : { "initially_open" : [ <?php echo $open_nodes?> ],'open_parents':true}
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
	      						//TODO: Hard coded for now
	      						if (itemcategory=="1")
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
		       	$$('#draggables tr').each(
		    			 function(row) {
		    				 if (row.getAttribute("class")=="rowselected")
		    				 {
		    					// update the database with a code for the tweet
		    					new Ajax.Request('<?php echo Yii::app()->createUrl("coding/create"); ?>' , {
		    						method: "post",
		    					    parameters: {tweetid:row.id, code:selectedcategory},
		    						onSuccess: function(codes) {
		    							//row.cells[1].innerHTML=codes.responseText;
		    							//alert(codes.responseText);

		    							updateCodes(codes, row);
		    						  }
		    					});
		    					selectedBehavior=true;
		    					
		    				 } 
		    				} 
		    	);

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
                            alert(response.message);
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
                
                
//BINDING EVENTS FOR THE ADD ROOT AND REFRESH BUTTONS.
   	$("#add_root").click(function () {
		$.ajax({
	    	type: 'POST',
			url:"<?php echo $baseUrl;?>/category/returnForm",
			data:	{
				"create_root" : true,
				"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
	            },
	    	beforeSend : function(){
	        	$("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").addClass("ajax-sending");
	            },
	        complete : function(){
	            $("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").removeClass("ajax-sending");
	            },
	        success:    function(data){
				$.fancybox(data,
	            	{    
	            		"transitionIn"	:	"elastic",
	                	"transitionOut" :   "elastic",
	                	"speedIn"		:	600,
	                	"speedOut"		:	200,
	               		"overlayShow"	:	false,
	                	"hideOnContentClick": false,
	                	"onClosed":    function(){ } //onclosed function
	                })//fancybox
	
	            } //function
	
			});//post
		});//click function
	$("#reload").click(function () {
		jQuery("#<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>").jstree("refresh");
	    });

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

	<table>
		<tr>
			<td>
			</td>
			<td>
				<?php 
					$numActiveSearchTermsString="$numActiveSearchTerms Active Search Term";
					if ($numActiveSearchTerms!=1)
					{
						$numActiveSearchTermsString=$numActiveSearchTermsString."s";
					}
				echo CHtml::link($numActiveSearchTermsString,array('site/searchterms'),$numActiveSearchTerms?array("style"=>"color: green;"):array("style"=>"color: red;")); ?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<button type="button" id="pauseButton" onclick=pauseStream()>Pause</button>
				<button type="button" id="emptyQueueButton" onclick=emptyQueue()>Empty Queue</button>
				<b>Queued: </b>
				<span id="queuedTweetsCounter"></span>
			</td>
		</tr>
		<tr>
			<td id="droppables" width="250px">

	<!--The tree will be rendered in this div-->

	<div id="<?php echo Category::ADMIN_TREE_CONTAINER_ID;?>" style="width: 230px"></div>
				
				
				


				<button type="button" onclick=emailForm()>Email</button>

</td>
			<td>
				<div style="overflow: auto; height: 400px;">
					<table id="myScrollTable">
						<tbody id="draggables">
						</tbody>

					</table>
				</div>
				<button type="button" onclick=selectAll(true)>Select All</button>

				<button type="button" onclick=selectAll(false)>Unselect All</button>
			</td>
		</tr>
	</table>


</body>
</html>
