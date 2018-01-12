<?php
//生成验证码
namespace Common\Action;
use Common\Action\AllAction;
class VcodeAction extends AllAction{
    public function index(){
	    import("ORG.Util.Image");
		Image::buildImageVerify();//6,0,'png',1,20,'verify'
    }
}
?>