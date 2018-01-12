<?php
namespace Admin\Action;
use Common\Action\BaseAction;
class UserAction extends BaseAction{	
	// 后台用户管理
    public function show(){
	    $rs = D("Admin.User");
		$join = C('db_prefix').'group on '.C('db_prefix').'user.user_gid = '.C('db_prefix').'group.group_id';
		$list = $rs->join($join)->order('user_id desc')->limit(C('url_num_admin'))->page(1)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
        $this->display('./Public/system/admin_show.html');
    }
	// 用户添加与编辑表单
    public function add(){
		$rs = D("Admin.User");
		$user_id = $_GET['id'];
		if ($user_id>0) {
            $where['user_id'] = $user_id;
			$array = $rs->where($where)->find();
			$array['user_duetime'] = date('Y-m-d H:i:s',$array['user_duetime']);
			$array['user_jointime'] = date('Y-m-d H:i:s',$array['user_jointime']);
			$array['user_logtime'] = date('Y-m-d H:i:s',$array['user_logintime']);
		}else{
			$array['user_id']=0;
			$array['user_money']=0;
			$array['user_duetime'] = date('Y-m-d H:i:s',time());
		}
		$rsg = D("Admin.Group");
		$listgroup = $rsg->order('group_id desc')->select();
		$this->assign($array);
		$this->assign('listgroup',$listgroup);
        $this->display(APP_PATH.'/Public/admin/user/add.html');
    }
	// 用户添加入库
	public function insert() {
		$rs = D("Admin.User");
		if($rs->create()) {
			if($rs->add()) {
    			$this->success('用户添加成功！');
            }else{
                $this->error('用户添加失败！');
            }
		}else{
			$this->error($rs->getError());
		}
	}
	// 更新用户
	public function update(){
		$rs = D("Admin.User");
		if ($rs->create()) {
			if (false !==  $rs->save()) {
			    $this->assign("jumpUrl",'?s=Admin-User-Show');
				$this->success('更新用户信息成功！');
			}else{
				$this->error("更新用户信息失败！");
			}
		}else{
			$this->error($rs->getError());
		}
	}
	// 删除用户
	public function del(){
		$rs = D("Admin.User");
		$rs->where('user_id = '.$_GET['id'])->delete();
		$this->success('删除用户成功！');
	}			
	// 用户组列表
    public function showgroup(){
	    $rs = D("Admin.Group");
		$list = $rs->order('group_id asc')->select();
		$this->assign('list',$list);
        $this->display(APP_PATH.'/Public/admin/user/showgroup.html');
    }	
	// 用户组添加与编辑表单
    public function addgroup(){
		$group_id = $_GET['id'];
		if ($group_id>0) {
			$rs = D("Admin.Group");
            $where['group_id'] = $group_id;
			$array = $rs->where($where)->find();
			$this->assign($array);
		}
        $this->display(APP_PATH.'/Public/admin/user/addgroup.html');
    }
	// 用户组添加入库
	public function insertgroup() {
		$rs = D("Admin.Group");
		if($rs->create()) {
			if ($rs->add()) {
    			$this->success('用户组添加成功！');
            }else{
                $this->error('用户组添加失败！');
            }
		}else{
			exit($rs->getError());
		}
	}		
}
?>