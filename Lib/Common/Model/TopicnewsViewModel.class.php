<?php
namespace Common\Model;
use Think\Model\ViewModel;
class TopicnewsViewModel extends ViewModel {
	//视图定义 飞飞cms唯一官方论坛 www.feifeicms.cc
	protected $viewFields = array (
		 'Topic'=>array('*','topic_did'=>'news_topic_did'),
		 'News'=>array('*', '_on'=>'Topic.topic_did = News.news_id'),
	);
}
?>