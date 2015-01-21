
<h1>Manage users associated to this event</h1>
<script type="text/javascript">
function addUser() {
	
	$.ajax({
		type: "POST",
		url: '<?php echo Yii::app()->createUrl("user/returnForm"); ?>',
		data:{
"YII_CSRF_TOKEN":"<?php echo Yii::app()->request->csrfToken;?>"
		},
		beforeSend : function(){
		},
		complete : function(){
		},
		success: function(data){

			$.fancybox(data,
			{
							"transitionIn"	:	"elastic",
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
function create_username($data_id,$data_userid)
{
	// create the HTMl for the category links
	$users=User::loadUsers();
	return $users[$data_userid];
}
function sendToDeleteAsPost($id)
{
	$userEventModel = new Userevent;
	Userevent::model()->deleteByPk($id);
}
$eventModel = Event::model()->findByPk($model->eventid);
//$eventName = $eventModel->name;
// for debugging
//echo "$eventName";
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'userevent-grid',
	'dataProvider'=>$model->search(),
	'columns'=>array(
		'userid' => array(
			                'type'=>'html',
			                'value' => 'create_username($data->id,$data->userid)',
			                'header' => 'User Name'
		),
		// not required as it does not make sense in the current context
		//'eventid',
		array(
			'class'=>'CButtonColumn',
			'template' => '{delete}',
      'buttons'=>array
      (      
          'delete' => array
          (          
          	 
             'url' => 'Yii::app()->createUrl("Userevent/delete", array("id"=>$data->id))',
              'options' => array( 'ajax' => array('type' => 'post', 'url'=>'js:$(this).attr("href")', 'success' => 'js:function(data) { $.fn.yiiGridView.update("userevent-grid")}') )
          
		   ),
      ),
		),
	),
)); ?>

<button type="button" onclick=addUser()>Add User</button>