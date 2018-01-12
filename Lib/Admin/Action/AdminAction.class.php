<?php
namespace Admin\Action;
use Common\Action\BaseAction;
class AdminAction extends BaseAction{
	// 用户管理
    public function show(){
	    $rs = D("Admin");
		$list = $rs->order('admin_logintime desc')->select();
		$this->assign('url_html_suffix',C('url_html_suffix'));
		$this->assign('html_file_suffix',C('html_file_suffix'));
		$this->assign('list',$list);
        $this->display('./Public/system/admin_show.html');
    }	
	// 用户添加
    public function add(){
		$array = array();
		$where['admin_id'] = $_GET['id'];
		$rs = D("Admin");
		$array = $rs->where($where)->find();
		$type = explode(',',$array['admin_ok']);
		$this->assign('admin',$type);
		$this->assign($array);
        $this->display('./Public/system/admin_add.html');
    }	
	//处理权限入库
	public function _before_insert(){
		$ok = $_POST['ids'];
		for($i=0;$i<20;$i++){
			if($ok[$i]){
				$rs[$i]=$ok[$i];
			}else{
				$rs[$i]=0;
			}
		}
		$_POST['admin_ok'] = implode(',',$rs);
	}   	
	// 写入数据
	public function insert(){
		$rs = D("Admin");
		if($rs->create()){
			if(false !==  $rs->add()){
			    $this->assign("jumpUrl",'?s=Admin-Admin-Show');
				$this->success('添加后台管理员成功！');
			}else{
				$this->error('添加后台管理员失败');
			}
		}else{
		    $this->error($rs->getError());
		}		
	}
	// 更新数据
	public function update(){
	    $this->_before_insert();
		$rs = D("Admin");
		if ($rs->create()) {
			if(false !==  $rs->save()){
			    $this->assign("jumpUrl",'?s=Admin-Admin-Show');
				$this->success('更新管理员信息成功！');
			}else{
				$this->error("更新管理员信息失败！");
			}
		}else{
			$this->error($rs->getError());
		}
	}
	// 删除用户
    public function del(){
		$rs = D("Admin");
		$rs->where('admin_id='.$_GET['id'])->delete();
		$this->success('删除后台管理员成功！');
    }
	// 批量删除
    public function delall(){
		$where['admin_id'] = array('in',implode(',',$_POST['ids']));
		$rs = D("Admin");
		$rs->where($where)->delete();
		$this->success('批量删除后台管理员成功！');
    }
	// 配置信息
    public function config(){
		$tpl = C('VIEW_PATH').'*';
		$list = glob($tpl);
		foreach ($list as $i=>$file){
			$dir[$i]['filename'] = basename($file);
		}
		$this->assign('dir',$dir);
		$config = require './Runtime/Conf/config.php';
		$this->assign($config);
		$this->ppvod_list();//更新导航菜单
        $this->display('./Public/system/admin_conf.html');
    }
	// 配置信息保存
    public function configsave(){
		$config = $_POST["config"];
		$config['site_tongji'] = stripslashes($config['site_tongji']);
		$config['play_collect_content'] = stripslashes($config['play_collect_content']);
		$config['admin_time_edit'] = (bool) $config['admin_time_edit'];
		$config['url_voddata'] = trim($config['url_voddata']);
		$config['url_newsdata'] = trim($config['url_newsdata']);
		$config['upload_thumb'] = (bool) $config['upload_thumb'];
		$config['upload_water'] = (bool) $config['upload_water'];
		$config['upload_http'] = (bool) $config['upload_http'];
		$config['upload_ftp'] = (bool) $config['upload_ftp'];
		$config['play_collect'] = (bool) $config['play_collect'];
		$config['play_second'] = intval($config['play_second']);
		$config['tmpl_cache_on'] = (bool) $config['tmpl_cache_on'];
		$config['html_cache_on'] = (bool) $config['html_cache_on'];
		$config['user_gbcm'] = (bool) $config['user_gbcm'];
		//播放地址前缀
		foreach(explode(chr(13),trim($config["play_server"])) as $v){
			list($key,$val) = explode('$$$',trim($v));
			$arrserver[trim($key)] = trim($val);
		}
		$config["play_server"] = $arrserver;
		//采集伪原创内容
		foreach(explode(chr(13),trim($config["play_collect_content"])) as $v){
			$arrcollect[] = trim($v);
		}
		$config["play_collect_content"] = $arrcollect;
		//静态缓存
		$config['html_cache_time'] = $config['html_cache_time']*3600;//其它页缓存
		if($config['html_cache_index']>0){
			$config['html_cache_rules']['index:index'] = array('{:action}',$config['html_cache_index']*3600);
		}else{
			$config['html_cache_rules']['index:index'] = NULL;
		}
		if($config['html_cache_list']>0){
		    $config['html_cache_rules']['vod:show'] = array('{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',$config['html_cache_list']*3600);
			$config['html_cache_rules']['news:show'] = array('{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',$config['html_cache_list']*3600);
		}else{
		    $config['html_cache_rules']['vod:show'] = NULL;
			$config['html_cache_rules']['news:show'] = NULL;
		}
		if($config['html_cache_content']>0){
		    $config['html_cache_rules']['vod:read'] = array('{:module}_{:action}/{id|md5}',$config['html_cache_content']*3600);
			$config['html_cache_rules']['news:read'] = array('{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',$config['html_cache_content']*3600);
		}else{
		    $config['html_cache_rules']['vod:read'] = NULL;
			$config['html_cache_rules']['news:read'] = NULL;
		}
		if($config['html_cache_play']>0){
		    $config['html_cache_rules']['vod:play'] = array('{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',$config['html_cache_play']*3600);
		}else{
		    $config['html_cache_rules']['vod:play'] = NULL;
		}						
		if($config['html_cache_ajax']>0){
		    $config['html_cache_rules']['my:show'] = array('{:module}_{:action}/{$_SERVER.REQUEST_URI|md5}',$config['html_cache_ajax']*3600);
		}else{
		    $config['html_cache_rules']['my:show'] = NULL;
		}
		if(0==$config['url_html']){
			@unlink('./index'.C('html_file_suffix'));//动态模式则删除首页静态文件
		}else{
			$config['html_home_suffix'] = $config['html_file_suffix'];//将静态后缀写入配置供前台生成的路径的时候调用
		}
		$config_old = require './Runtime/Conf/config.php';
		$config_new = array_merge($config_old,$config);
		arr2file('./Runtime/Conf/config.php',$config_new);
		@unlink('./Runtime/common~runtime.php');
		//
		$pp_play.= 'var ff_root="'.$config['site_path'].'";';
		$pp_play.= 'var ff_width='.$config['play_width'].';';
		$pp_play.= 'var ff_height='.$config['play_height'].';';
		$pp_play.= 'var ff_showlist='.$config['play_show'].';';
		$pp_play.= 'var ff_second='.intval($config['play_second']).';';
		$pp_play.= 'var ff_qvod="'.$config['play_qvod'].'";';
		$pp_play.= 'var ff_gvod="'.$config['play_gvod'].'";';
		$pp_play.= 'var ff_pvod="'.$config['play_pvod'].'";';
		$pp_play.= 'var ff_web9="'.$config['play_web9'].'";';
		$pp_play.= 'var ff_bdhd="'.$config['play_bdhd'].'";';
		$pp_play.= 'var ff_pplive="";';
		$pp_play.= 'var ff_buffer="'.$config['play_playad'].'";';
		foreach(C('play_server') as $key=>$value){
			$pp_play.= 'var ff_'.$key.'="'.$value.'";';
		}
		foreach(C('play_player') as $key=>$value){
			$pp_play.= 'var play_'.$key.'="'.$value[1].'";';
		}			
		write_file('./Runtime/Player/play.js',$pp_play);
		admin_ff_hot_key(C('site_hot'));
		$this->success('恭喜您，配置信息更新成功！');
    }				
}
?>