<?php
//生成验证码
namespace Home\Action;
use Common\Action\HomeAction;
class VcodeAction extends HomeAction{
    public function index(){
	    $Image=new \Org\Util\Image();
		$Image->buildImageVerify();
    }
}
?>