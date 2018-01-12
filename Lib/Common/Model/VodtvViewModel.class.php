<?php
namespace Common\Model;
use Think\Model\ViewModel;
class VodtvViewModel extends ViewModel {
	//视图定义
	protected $viewFields = array (
		 'Vodtv'=>array('*','Vodtv_name'=>'vod_tv_name'),
		 'Vod'=>array('*', '_on'=>'Vodtv.vodtv_id  = Vod.vod_id'),
	);
}
?>