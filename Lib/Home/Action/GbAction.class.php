<?php
namespace Home\Action;
use Common\Action\HomeAction;
class GbAction extends HomeAction{
    //留言列表
    public function show(){
		$rs = D('GbView');
		$page = !empty($_GET['p']) ? intval($_GET['p']) : 1;
		$limit = intval(C('user_gbnum'));
		if (C('user_check')) {
			$where['gb_status'] = array('eq',1);
		}
		// 组合分页信息
		$count = $rs->where($where)->count('gb_id');
		$totalpages = ceil($count/$limit);
		if($page > $totalpages){
			$page = $totalpages;
		}
		$pageurl = UU('Home-gb/show',array('p'=>'{!page!}'),true,false);
		$pages = '共'.$count.'篇留言&nbsp;当前:'.$page.'/'.$totalpages.'页&nbsp;'.getpage($page,$totalpages,C('home_pagenum'),$pageurl,'pagego(\''.$pageurl.'\','.$totalpages.')');
		// 查询数据
		$list = $rs->where($where)->limit($limit)->order('gb_oid desc,gb_addtime desc')->page($page)->select();
		foreach($list as $key=>$val){
			$list[$key]['gb_floor'] = $count-(($page-1) * $limit + $key);
		}
		// 是否报错	
		$vodid = intval($_GET['id']);
		if($vodid){
			$rs = M("Vod");
			$array = $rs->field('vod_id,vod_name,vod_actor')->where('vod_status = 1 and vod_id='.$vodid)->find();
			if($array){
				$this->assign('gb_content','影片ID'.$array['vod_id'].'点播出现错误！名称：'.$array['vod_name'].' 主演：'.$array['vod_actor']);
			}
		}
		$this->assign('gb_list',$list);	
		$this->assign('gb_count',$count);
		$this->assign('gb_pages',$pages);
		$this->assign('vod_id',$vodid);
		$this->display('pp_guestbook');
    }
	// 添加留言
    public function insert(){
		$rs = D("Gb");
		C('TOKEN_ON',false);//关闭令牌验证
		if($rs->create()){
			if (false !== $rs->add()) {
				$cookie = 'gbook-'.intval($_POST['gb_cid']);
				setcookie($cookie, 'true', time()+intval(C('user_second')));
				if (C('user_check')) {
					$this->success('留言成功，我们会尽快审核您的留言！');
				}else{
					$this->success('恭喜您，留言成功！');
				}
			}else{
				$this->error('留言失败，请重试！');
			}
		}else{
		    $this->error($rs->getError());
		}
    }	
}
?>