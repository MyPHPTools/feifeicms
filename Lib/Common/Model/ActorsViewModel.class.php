<?php
namespace Common\Model;
use Think\Model\ViewModel;
class ActorsViewModel extends ViewModel {
	//视图定义
	protected $viewFields = array (
		 'Actors'=>array('*','Actors_name'=>'vod_actors_name'),
		 'Vod'=>array('*', '_on'=>'Actors.actors_id  = Vod.vod_id'),
	);
}
?>