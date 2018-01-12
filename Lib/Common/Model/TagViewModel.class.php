<?php
//用于视图查询
namespace Common\Model;
use Think\Model\ViewModel;
class TagViewModel extends ViewModel {
	//视图定义
	protected $viewFields = array (
		 'Tag'=>array('*','tag_id'=>'vod_tag_id','tag_name'=>'vod_tag_name'),
		 'Vod'=>array('*', '_on'=>'Tag.tag_id = Vod.vod_id'),
	);
}
?>