<?php
namespace Common\Model;
use Think\Model\ViewModel;
class TopicvodViewModel extends ViewModel {
	//视图定义 飞飞cms唯一官方论坛 www.feifeicms.cc
	protected $viewFields = array (
		 'Topic'=>array('*','topic_did'=>'vod_topic_did'),
		 'Vod'=>array('*', '_on'=>'Topic.topic_did = Vod.vod_id'),
	);
}
?>