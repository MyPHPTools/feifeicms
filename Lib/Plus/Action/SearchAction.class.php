<?php
namespace Plus\Action;
use Common\Action\HomeAction;
class SearchAction extends HomeAction{
	public function vod(){
		$wd = trim($_GET['q']);
		$where = array();
		$where['vod_name'] = array('like',$wd.'%');
		$rs = D('Vod');
		$list = $rs->field('vod_name,vod_id,vod_cid,vod_jumpurl,vod_title')->where($where)->limit(10)->order('vod_hits_month desc')->select();
		if($list){
			$lists['info']   =   "ok";
            $lists['status'] =   1;
          foreach($list as $key=>$val){
			   $lists['data'][$key]['vod_name'] =$val['vod_name'] ;
	           $lists['data'][$key]['vod_url'] = ff_data_url('vod',$val['vod_id'],$val['vod_cid'],$val['vod_name'],1,$val['vod_jumpurl']);
			   }
			$this->ajaxReturn($lists);
		}else{
			$lists['info']   =   "ok";
            $lists['status'] =   0;
			$lists['data']="";
			$this->ajaxReturn($lists);
		}
	}
}
?>