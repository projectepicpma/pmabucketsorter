<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();


	//public $level=1;

	public function createCode($tweetid, $code)
	{
		
		$category= Category::model()->find('id=?',array($code));
		
		$user=User::model()->find('LOWER(username)=?',array(Yii::app()->user->name));
				
		// Follow up the tree coding
		while (!$category->isRoot())
		{
			$model=new Coding;
			
			$model->userid=$user->id;
			$model->level=$user->level;
			$model->tweetid=$tweetid;
			$model->coding=$category->id;
			try {
				$model->save();
			} catch (Exception $e) {
				//do nothing
			}
			$category=$category->getParent();
		}
	}
}