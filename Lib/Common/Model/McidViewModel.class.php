<?php
namespace Common\Model;
use Think\Model\ViewModel;
class McidViewModel extends ViewModel {
	//视图定义
	protected $viewFields = array (
		 'Mcid'=>array('*','mcid_id'=>'vod_mcid_id','mcid_mid'=>'vod_mcid_mid'),
		 'Vod'=>array('*', '_on'=>'Mcid.mcid_id  = Vod.vod_id'),
	);
}
?>