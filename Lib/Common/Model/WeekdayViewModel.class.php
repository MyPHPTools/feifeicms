<?php
namespace Common\Model;
use Think\Model\ViewModel;
class WeekdayViewModel extends ViewModel {
	//视图定义
	protected $viewFields = array (
		 'Weekday'=>array('*','weekday_cid'=>'vod_weekday_id'),
		 'Vod'=>array('*', '_on'=>'Weekday.weekday_id  = Vod.vod_id'),
	);
}
?>