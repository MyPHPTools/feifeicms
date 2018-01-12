<?php
namespace Common\Model;
use Think\Model\AdvModel;
class VodtvModel extends AdvModel {
	public function vodtv_update($id,$tv){
		$rs = M("Vodtv");
		$data['vodtv_id'] = $id;
		$rs->where($data)->delete();
		$tv_arr = explode(',',str_replace(array('/','|',' ','，','、'),',',$tv));
		$tv_arr = array_filter(array_unique($tv_arr));
		foreach($tv_arr as $key=>$val){
			$data['vodtv_name'] = $val;
			$rs->data($data)->add();
		   }
		}	
}
?>