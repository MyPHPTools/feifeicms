<?php
namespace Home\Action;
use Common\Action\HomeAction;
class MyAction extends HomeAction{
    public function show(){
		$id = !empty($_GET['id'])?$_GET['id']:'new';
		$this->display('my_'.trim($id));
	}					
}
?>