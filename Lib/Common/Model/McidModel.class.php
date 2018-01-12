<?php
namespace Common\Model;
use Think\Model\AdvModel;
class McidModel extends AdvModel {
	public function mcid_update($id,$mcid){
		$rs = M("Mcid");
		$data['mcid_id'] = $id;
		$rs->where($data)->delete();
		if(is_array($mcid)){
		$mcid_arr = $mcid;
		}
		else{
		$mcid_arr = explode(',',$mcid);
		$mcid_arr = array_filter(array_unique($mcid_arr));	
			}
		foreach($mcid_arr as $key=>$val){
			$data['mcid_mid'] = $val;
			$data['mcid_name'] = ff_mcat_name($val,getvodcid($id));
			$rs->data($data)->add();
		   }
		}	
}
?>