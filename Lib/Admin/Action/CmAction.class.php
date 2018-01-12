<?php
namespace Admin\Action;
use Common\Action\BaseAction;
class CmAction extends BaseAction{
	// 用户评论管理
    public function show(){
		$admin = array();$where = array();
		$admin['status'] = intval($_GET['status']);
		$admin['wd']     = urldecode(trim($_REQUEST['wd']));
		if ($admin['status'] == 2) {
			$where['cm_status'] = array('eq',0);
		}elseif ($admin['status'] == 1) {
			$where['cm_status'] = array('eq',1);
		}
		if (!empty($admin['wd'])) {
			$search['cm_ip']      = array('like','%'.$admin['wd'].'%');
			$search['cm_content'] = array('like','%'.$admin['wd'].'%');
			$search['user_name'] = array('like','%'.$admin['wd'].'%');
			$search['_logic'] = 'or';
			$where['_complex'] = $search;
			$admin['wd'] = urlencode($admin['wd']);
		}
		//
		$admin['p'] = '';
		$rs = D('CmView');
		$count  = $rs->where($where)->count();
		$limit = intval(C('url_num_admin'));
		$currentpage = !empty($_GET['p'])?intval($_GET['p']):1;
		$totalpages = ceil($count/$limit);
		$currentpage = get_maxpage($currentpage,$totalpages);
		$pageurl = U('Admin-Cm/Show',$admin,false,false)."-p-".'{!page!}';
		//
		$admin['p'] = $currentpage;$_SESSION['cm_jumpurl'] = U('Admin-Cm/Show',$admin);
		$admin['pages'] = '共'.$count.'篇评论&nbsp;当前:'.$currentpage.'/'.$totalpages.'页&nbsp;'.getpage($currentpage,$totalpages,8,$pageurl,'pagego(\''.$pageurl.'\','.$totalpages.')');
		$admin['list'] = $rs->where($where)->limit($limit)->page($currentpage)->order('cm_addtime desc')->select();
		$this->assign($admin);
        $this->display('./Public/system/cm_show.html');
    }
	// 用户评论编辑
    public function add(){
		$rs = D('Cm');
		$where['cm_id'] = $_GET['id'];
		$array = $rs->where($where)->find();
		$this->assign($array);	
        $this->display('./Public/system/cm_add.html');
    }
	// 更新用户评论
	public function update(){
		$rs = D('Cm');
		if ($rs->create()) {
			if (false !==  $rs->save()) {
			    $this->assign("jumpUrl",$_SESSION['cm_jumpurl']);
				$this->success('更新评论信息成功！');
			}else{
				$this->error("更新评论信息失败！");
			}
		}
		$this->error($rs->getError());
	}
	// 删除评论BY-ID
    public function del(){
		$rs = D('Cm');
		$where['cm_id'] = $_GET['id'];
		$rs->where($where)->delete();
		redirect($_SERVER['HTTP_REFERER']);
    }
	// 删除评论All
    public function delall($uid){
		if(empty($_POST['ids'])){
			$this->error('请选择需要删除的评论信息！');
		}
		$rs = D('Cm');	
		$where['cm_id'] = array('in',implode(',',$_POST['ids']));
		$rs->where($where)->delete();
		redirect($_SERVER['HTTP_REFERER']);
    }	
	// 清空评论
    public function delclear(){
		$rs = D('Cm');
		if ($_REQUEST['cid']) {
			$rs->where('cm_cid > 0')->delete();
		}else{
			$rs->where('cm_cid = 0')->delete();
		}
		$this->success('所有用户评论或报错信息已经清空！');
    }
	// 隐藏与显示评论
    public function status(){
		$rs = D('Cm');
		$where['cm_id'] = $_GET['id'];
		if(intval($_GET['value'])){
			$rs->where($where)->setField('cm_status',1);
		}else{
			$rs->where($where)->setField('cm_status',0);
		}
		redirect($_SERVER['HTTP_REFERER']);
    }	
   // 批量隐藏与显示评论
    public function statusall(){
		if(empty($_POST['ids'])){
			$this->error('请选择需要操作的评论内容！');
		}
		$rs = D('Cm');
		$where['cm_id'] = array('in',implode(',',$_POST['ids']));
		if(intval($_GET['value'])){
			$rs->where($where)->setField('cm_status',1);
		}else{
			$rs->where($where)->setField('cm_status',0);
		}
		redirect($_SERVER['HTTP_REFERER']);
    }							
}
?>