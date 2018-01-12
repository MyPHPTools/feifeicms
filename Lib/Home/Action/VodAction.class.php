<?php
namespace Home\Action;
use Common\Action\HomeAction;
class VodAction extends HomeAction{
    // 影视搜索
    public function search(){
		//获取地址栏参数
		$Url = ff_param_url();
		//$JumpUrl传递分页及跳转参数
		$JumpUrl = ff_param_jump($Url);
		$JumpUrl['p'] = '{!page!}';
		C('jumpurl',UU('Home-vod/search',$JumpUrl,true,false));	
		C('currentpage',$Url['page']);
		//变量赋值
		$search = $this->Lable_Search($Url,'vod');
		$this->assign($search);
		$this->display($search['search_skin']);
    }			
    // 影视列表页
    public function show(){
		$Url = ff_param_url();
		$JumpUrl = ff_param_jump($Url);
		$JumpUrl['p'] = '{!page!}';	
		C('jumpurl',UU('Home-vod/show',$JumpUrl,true,false));
		C('currentpage',$Url['page']);
		$List = list_search(F('_ppvod/list'),'list_id='.$Url['id']);
		$channel = $this->Lable_Vod_List($Url,$List[0]);
		$this->assign($channel);
		$this->display($channel['list_skin']);
    }
    // 多分类筛选
    public function type(){
		$Url = ff_param_url();
		$JumpUrl = ff_param_jump($Url);
		$JumpUrl['p'] = '{!page!}';	
		C('jumpurl',UU('Home-vod/type',$JumpUrl,true,false));
		C('currentpage',$Url['page']);
		$List = list_search(F('_ppvod/list'),'list_id='.$Url['id']);
		$channel = $this->Lable_Vod_List($Url,$List[0]);
		$this->assign($channel);
		$this->display($channel['list_skin_type']);
    }	
	// 影片内容页
    public function read(){
		$array_detail = $this->get_cache_detail( intval($_GET['id']) );
		if($array_detail){
			$this->assign($array_detail['show']);
			$this->assign($array_detail['read']);
			$this->display($array_detail['read']['vod_skin_detail']);
		}else{
			$this->assign("jumpUrl",C('site_path'));
			$this->error('此影片已经删除，请选择观看其它节目！');
		}
    }
	// 影片播放页
    public function play(){
		$array_detail = $this->get_cache_detail( intval($_GET['id']) );
		if($array_detail){
			$array_detail['read'] = $this->Lable_Vod_Play($array_detail['read'],array('id'=>intval($_GET['id']), 'sid'=>intval($_GET['sid']), 'pid'=>intval($_GET['pid'])));
			$this->assign($array_detail['show']);
			$this->assign($array_detail['read']);
			$this->display($array_detail['read']['vod_skin_play']);
		}else{
			$this->assign("jumpUrl",C('site_path'));
			$this->error('此影片已经删除，请选择观看其它节目！');
		}
    }
	// 从数据库获取数据
	private function get_cache_detail($vod_id){
		if(!$vod_id){ return false; }
		//优先读取缓存数据
		if(C('data_cache_vod')){
			$array_detail = S('data_cache_vod_'.$vod_id);
			if($array_detail){
				return $array_detail;
			}
		}
		//未中缓存则从数据库读取
		$where = array();
		$where['vod_id'] = $vod_id;
		$where['vod_cid'] = array('gt',0);
		$where['vod_status'] = array('eq',1);
		$rs = D("Vod");
		$array = $rs->where($where)->relation('Tag')->find();
		if($array){
			//解析标签
			$array_detail = $this->Lable_Vod_Read($array);
			if( C('data_cache_vod') ){
				S('data_cache_vod_'.$vod_id, $array_detail, intval(C('data_cache_vod')));
			}
			return $array_detail;
		}
		return false;
	}
}
?>