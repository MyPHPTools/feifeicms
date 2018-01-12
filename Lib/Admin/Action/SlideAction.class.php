<?php
namespace Admin\Action;
use Common\Action\BaseAction; 
class SlideAction extends BaseAction{	
	// 显示幻灯
    public function show(){
	    $rs = D("Slide");
		//更新前台缓存
		$list = $rs->where('slide_status = 1')->order('slide_oid asc')->select();
		if($list){
			foreach($list as $key=>$val){
				$list[$key]['slide_pic'] = ff_img_url($list[$key]['slide_pic']);	
			}		
			F('_ppvod/slide',$list);
		}
		//后台展示
		$list = $rs->order('slide_cid asc,slide_oid asc')->select();
		foreach($list as $key=>$val){
			$list[$key]['slide_pic'] = ff_img_url($list[$key]['slide_pic']);	
		}
		$this->assign('list_slide',$list);
		$this->display('./Public/system/slide_show.html');
    }
	// 添加与编辑幻灯
    public function add(){
		$id = intval($_GET['id']);
	    $rs = D("Slide");
		if ($id>0) {
            $where['slide_id'] = $id;
			$list = $rs->where($where)->find();
			$list['tpltitle'] = '编辑';
		}else{
		    $list['slide_oid'] = $rs->max('slide_oid')+1;
			$list['slide_status'] = 1;
			$list['tpltitle'] = '添加';
		}
		$this->assign($list);
		$this->display('./Public/system/slide_add.html');
    }
	// 添加幻灯片并写入数据库
	public function insert(){
		$rs = D("Slide");
		if ($rs->create()) {
			if ( false !==  $rs->add() ) {
				redirect('?s=Admin-Slide-Show');
			}else{
				$this->error('添加幻灯失败');
			}
		}else{
		    $this->error($rs->getError());
		}		
	}	
	// 更新幻灯片
	public function update(){
		$rs = D("Slide");
		if ($rs->create()) {
			$list = $rs->save();
			if ($list !== false) {
			    redirect('?s=Admin-Slide-Show');
			}else{
				$this->error("更新幻灯失败！");
			}
		}else{
			$this->error($rs->getError());
		}
	}
	// 隐藏与显示幻灯
    public function status(){
		$where['slide_id'] = $_GET['id'];
		$rs = D("Slide");
		if (intval($_GET['sid'])) {
			$rs->where($where)->setField('slide_status',1);
		}else{
			$rs->where($where)->setField('slide_status',0);
		}
		$this->redirect('Admin-Slide/Show');
    }
	// 删除幻灯片
    public function del(){
		$rs = D("Slide");
		$where['slide_id'] = $_GET['id'];
		$rs->where($where)->delete();
		$this->redirect('Admin-Slide/Show');
    }								
}
?>