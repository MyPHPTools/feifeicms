<?php
namespace Common\Model;
use Think\Model\ViewModel;
class CmViewModel extends ViewModel {
	protected $viewFields = array (
		 'Cm'=>array('*'),
		 'User'=>array('user_id,user_name,user_face','_on'=>'Cm.cm_uid = User.user_id'),
	);
}
?>