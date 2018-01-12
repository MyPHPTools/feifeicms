<?php
namespace Common\Model;
use Think\Model\AdvModel;
class WeekdayModel extends AdvModel {
	public function weekday_update($id,$weekday){
		$rs = M("Weekday");
		$data['weekday_id'] = $id;
		$rs->where($data)->delete();
		if($weekday){
		$weekday_arr = explode(',',$weekday);
		$weekday_arr = array_unique($weekday_arr);
		foreach($weekday_arr as $key=>$val){
			$data['weekday_cid'] = $val;
			$rs->data($data)->add();
		   }
		}
		}	
}
?>