<?php
namespace Admin\Action;
use Common\Action\BaseAction;
class VodAction extends BaseAction{
	// 影片列表
    public function show(){
		$admin = array();
		//获取地址栏参数
		$admin['cid']= $_REQUEST['cid'];
		$admin['continu'] = $_REQUEST['continu'];
		$admin['status'] = intval($_REQUEST['status']);
		$admin['isfilm'] = intval($_REQUEST['isfilm']);
		$admin['player'] = trim($_REQUEST['player']);
		$admin['stars'] = intval($_REQUEST['stars']);
		$admin['url'] = intval($_REQUEST['url']);
		$admin['type'] = !empty($_GET['type'])?$_GET['type']:C('admin_order_type');
		$admin['order'] = !empty($_GET['order'])?$_GET['order']:'desc';
		$admin['orders'] = 'vod_'.$admin["type"].' '.$admin['order'];
		$admin['wd'] = urldecode(trim($_REQUEST['wd']));
		$admin['tag'] = urldecode(trim($_REQUEST['tag']));
		$admin['tid'] = $_REQUEST['tid'];//专题ID
		$admin['p'] = '';
		//生成查询参数
		$limit = C('url_num_admin');
		$order = 'vod_'.$admin["type"].' '.$admin['order'];
		if ($admin['cid']) {
			$where['vod_cid']= getlistsqlin($admin['cid']);
		}
		if($admin["continu"] == 1){
			$where['vod_continu'] = array('neq','0');
		}
		if ($admin['status'] == 2) {
			$where['vod_status'] = array('eq',0);
		}elseif ($admin['status'] == 1) {
			$where['vod_status'] = array('eq',1);
		}elseif ($admin['status'] == 3) {
			$where['vod_status'] = array('eq',-1);
		}
		if($admin["isfilm"]){
			$where['vod_isfilm'] = array('eq',$admin["isfilm"]);
		}				
		if($admin['player']){
			$where['vod_play'] = array('like','%'.trim($admin['player']).'%');
		}
		if($admin['stars']){
			$where['vod_stars'] = $admin['stars'];
		}	
		if($admin["url"]){
			$where['vod_url'] = array('eq','');
		}			
		if ($admin['wd']) {
			$search['vod_name'] = array('like','%'.$admin['wd'].'%');
			$search['vod_title'] = array('like','%'.$admin['wd'].'%');
			$search['vod_actor'] = array('like','%'.$admin['wd'].'%');
			$search['vod_director'] = array('like','%'.$admin['wd'].'%');
			$search['_logic'] = 'or';
			$where['_complex'] = $search;
			$admin['wd'] = urlencode($admin['wd']);
		}
		//根据不同条件加载模型
		if($admin['tag']){
			$where['tag_sid'] = 1;
			$where['tag_name'] = $admin['tag'];
			$rs = D('TagView');
			$admin['tag'] = urlencode($_REQUEST['tag']);
		}else{
			$rs = D("Vod");
		}
		//组合分页信息
		
		$count = $rs->where($where)->count('vod_id');
		$totalpages = ceil($count/$limit);
		$currentpage = !empty($_GET['p'])?intval($_GET['p']):1;
		$currentpage = get_maxpage($currentpage,$totalpages);//$admin['page'] = $currentpage;
		$pageurl = U('Admin-Vod/Show',$admin,false,false)."-p-".'{!page!}'.C('url_html_suffix');
		$admin['p'] = $currentpage;$_SESSION['vod_jumpurl'] = U('Admin-Vod/Show',$admin).C('url_html_suffix');
		$pages = '共'.$count.'部影片&nbsp;当前:'.$currentpage.'/'.$totalpages.'页&nbsp;'.getpage($currentpage,$totalpages,8,$pageurl,'pagego(\''.$pageurl.'\','.$totalpages.')');
		$admin['pages'] = $pages;
		//查询数据
		$list = $rs->where($where)->order($order)->limit($limit)->page($currentpage)->select();
		foreach($list as $key=>$val){
		    $list[$key]['list_url'] = '?s=Admin-Vod-Show-cid-'.$list[$key]['vod_cid'];
			$list[$key]['vod_url'] = ff_data_url('vod',$list[$key]['vod_id'],$list[$key]['vod_cid'],$list[$key]['vod_name'],1,$list[$key]['vod_jumpurl']);
			$list[$key]['vod_starsarr'] = admin_star_arr($list[$key]['vod_stars']);
		}
		//dump($rs->getLastSql());
		//变量赋值并输出模板
		$this->ppvod_play();
		$this->assign($admin);
		$this->assign('list',$list);
		$this->assign('list_vod',F('_ppvod/listvod'));
		if($admin['tid']){
			$this->display('./Public/system/special_vod.html');
		}else{
	    	$this->display('./Public/system/vod_show.html');
		}
    }
	// 添加编辑影片
    public function add(){
		$rs = D("Vod");
		$vod_id = intval($_GET['id']);
		if($vod_id){
			$where = array();
            $where['vod_id'] = $vod_id;
			$array = $rs->where($where)->relation('Tag')->find();
			foreach($array['Tag'] as $key=>$value){
				$tag[$key] = $value['tag_name'];
			}
			foreach(explode('$$$',$array['vod_play']) as $key=>$val){
			    $play[array_search($val,C('play_player'))] = $val;
			}
			$array['vod_play_list'] = C('play_player');
			$array['vod_server_list'] = C('play_server');
			$array['vod_play'] = explode('$$$',$array['vod_play']);
			$array['vod_server'] = explode('$$$',$array['vod_server']);	
			$array['vod_url'] = explode('$$$',$array['vod_url']);
			$array['vod_starsarr'] = admin_star_arr($array['vod_stars']);
			$array['vod_keywords'] = implode(',',$tag);
			if (C('admin_time_edit')){
				$array['checktime'] = 'checked';
			}
			$array['vod_tplname'] = '编辑';
			$_SESSION['vod_jumpurl'] = $_SERVER['HTTP_REFERER'];
		}else{
		    $array['vod_cid'] = cookie('vod_cid');
		    $array['vod_stars'] = 1;
		    $array['vod_status'] = 1;
			$array['vod_hits'] = 0;
			$array['vod_addtime'] = time();
			$array['vod_continu'] = 0;
			$array['vod_inputer'] = $_SESSION['admin_name'];
			$array['vod_play_list'] = C('play_player');
			$array['vod_server_list'] = C('play_server');
			$array['vod_url']=array(0=>'');
			$array['vod_starsarr'] = admin_star_arr(1);
			$array['checktime'] = 'checked';
			$array['vod_tplname'] = '添加';
		}
		if($array['vod_cid']){
			$array['vod_cat_list'] = D('Mcat')->list_cat($array['vod_cid']);
		}
		$array['vod_language_list']=explode(',',C('play_language'));
		$array['vod_area_list']=explode(',',C('play_area'));
		$array['vod_year_list']=explode(',',C('play_year'));
		$this->ppvod_play();
		$this->assign($array);
		$this->assign("jumpUrl",$_SESSION['vod_jumpurl']);
		$this->assign('listvod',F('_ppvod/listvod'));
		$this->display('./Public/system/vod_add.html');
    }
	// 数据库写入前置操作
    public function _before_insert(){
		//自动获取关键词tag
		if(empty($_POST["vod_keywords"]) && C('rand_tag')){
			$_POST["vod_keywords"] = ff_tag_auto($_POST["vod_name"],$_POST["vod_content"]);
		}
		//播放器组与地址组
		$play = $_POST["vod_play"];
		$server = $_POST["vod_server"];
		foreach($_POST["vod_url"] as $key=>$val){
			$val = trim($val);
			if($val){
			    $vod_play[] = $play[$key];
				$vod_server[] = $server[$key];
				$vod_url[] = $val;
			};
		}
		$_POST["vod_play"] = strval(implode('$$$',$vod_play));
		$_POST["vod_server"] = strval(implode('$$$',$vod_server));
		$_POST["vod_url"] = strval(implode('$$$',$vod_url));	
		$_POST['vod_mcid'] = empty($_POST['vod_mcids']) ? 0 : implode(',', $_POST['vod_mcids']);
		$_POST['vod_weekday'] = empty($_POST['vod_weekday']) ? 0 : implode(',', $_POST['vod_weekday']);
	}
	// 新增数据
	public function insert(){
		$tag = D('Tag');$rs = D("Vod");$weekday = D('Weekday');$actors = D('Actors');$vodtv = D('Vodtv');
		if($rs->create()){
			//关联操作>>写入tag
			if($_POST["vod_keywords"]){
				$rs->Tag = $tag->tag_array($_POST["vod_keywords"],1);
				$id = $rs->relation('Tag')->add();
				
			}else{
				$id = $rs->add();
			}
			$rs->$vod_id = $id;
			if($_POST['vod_actor']){
				$actors_array=str_replace(array('/','|',' ','，','、'),',',$_POST['vod_actor']);
				$actors->actors_update($id,$actors_array,1);
				}
			if($_POST['vod_director']){
				$directors_array=str_replace(array('/','|',' ','，','、'),',',$_POST['vod_director']);
				$actors->actors_update($id,$directors_array,2);
				}	
			if($_POST['vod_mcids']){
				D('Mcid')->mcid_update($id,$_POST['vod_mcids']);
				}
			if($_POST['vod_weekday']){
				$weekday->weekday_update($id,$_POST['vod_weekday']);
				}	
			if($_POST['vod_prty']){
				$prty->prty_update($id,$_POST['vod_prty']);
				}
			if($_POST['vod_diantai']){
				$vodtv->vodtv_update($id,$_POST['vod_diantai']);
				}	
		}else{
		    //$this->error($rs->getError());
		}
	}
	// 数据库写入-后置操作
	public function _after_insert(){
		$rs = D("Vod");
		$vod_id = $rs->$vod_id;
		if($vod_id){
			cookie('vod_cid',$vod_id);
			$this->_after_add_update($vod_id);
			$this->assign("jumpUrl",'?s=Admin-Vod-Add');
			$this->success('视频添加成功，继续添加新视频！');
		}else{
			$this->error('视频添加失败。');
		}
	}
	// 更新数据
	public function update(){
	    $this->_before_insert();
		$tag = D('Tag');$rs = D("Vod");$weekday = D('Weekday');$actors = D('Actors');$vodtv = D('Vodtv');
		if($rs->create()){	
			if(false !==  $rs->save()){
				//手动更新TAG
				if($_POST["vod_keywords"]){
					$tag->tag_update($_POST["vod_id"],$_POST["vod_keywords"],1);
				}
				D('Mcid')->mcid_update($_POST["vod_id"],$_POST['vod_mcids']);
				$actors_array=str_replace(array('/','|',' ','，','、'),',',$_POST['vod_actor']);
				$actors->actors_update($_POST["vod_id"],$actors_array,1);
				
				$directors_array=str_replace(array('/','|',' ','，','、'),',',$_POST['vod_director']);
				$actors->actors_update($_POST["vod_id"],$directors_array,2);
				
				$weekday->weekday_update($_POST["vod_id"],$_POST['vod_weekday']);
				$vodtv->vodtv_update($_POST["vod_id"],$_POST['vod_diantai']);
				//后置操作条件
				$rs->$vod_id = $_POST["vod_id"];
			}else{
				$this->error("修改影片信息失败！");
			}
		}else{
			$this->error($rs->getError());
		}
	}
	// 后置操作
	public function _after_update(){
		$rs = D("Vod");
		$vod_id = $rs->$vod_id;
		if($vod_id){
			$this->_after_add_update($vod_id);
			$this->assign("jumpUrl",$_SESSION['vod_jumpurl']);
			$this->success('视频更新成功！');
		}else{
			$this->error('视频更新失败。');
		}		
	}
	//操作完毕后
	public function _after_add_update($vod_id){
		//删除数据缓存
		if(C('data_cache_vod')){
			S('data_cache_vod_'.$vod_id,NULL);
		}
		//删除静态缓存
		if(C('html_cache_on')){
			$id = md5($vod_id).C('html_file_suffix');
			@unlink('./Html/Vod_read/'.$vod_id);
			@unlink('./Html/Vod_play/'.$vod_id);
		}
		//生成网页
		if(C('url_html')){
			echo'<iframe scrolling="no" src="?s=Admin-Create-vodid-id-'.$vod_id.'" frameborder="0" style="display:none"></iframe>';
		}				
	}	
	// 删除影片
    public function del(){
		$this->delfile($_GET['id']);
		redirect($_SESSION['vod_jumpurl']);
    }
	// 删除影片all
    public function delall(){
		if(empty($_POST['ids'])){
			$this->error('请选择需要删除的影片！');
		}	
		$array = $_POST['ids'];
		foreach($array as $val){
			$this->delfile($val);
		}
		redirect($_SESSION['vod_jumpurl']);
    }
	// 删除静态文件与图片
    public function delfile($id){
		$where = array();
		//删除影片观看记录
		//$rs = D("View");
		//$where['did'] = $id;
		//$rs->where($where)->delete();
		//删除专题收录
		$rs = D("Topic");
		$where['topic_sid'] = 1;
		$where['topic_did'] = $id;
		$rs->where($where)->delete();
		unset($where);	
		//删除小分类
		$rs = M("Mcid");
		$where['mcid_id'] = $id;
		$rs->where($where)->delete();
		unset($where);
		//删除周期
		$rs = D('Weekday');
		$where['weekday_id'] = $id;
		$rs->where($where)->delete();
		unset($where);
		//演员导演
		$rs = D('Actors');
		$where['actors_id'] = $id;
		$rs->where($where)->delete();
		unset($where);
		//删除电台
		$rs = D('Vodtv');
		$where['vodtv_id'] = $id;
		$rs->where($where)->delete();
		unset($where);	
		//删除影片评论
		$rs = D("Cm");
		$where['cm_cid'] = $id;
		$where['cm_sid'] = 1;
		$rs->where($where)->delete();
		unset($where);	
		//删除影片TAG
		$rs = D("Tag");
		$where['tag_id'] = $id;
		$where['tag_sid'] = 1;
		$rs->where($where)->delete();
		unset($where);
		//删除静态文件与图片
		$rs = D("Vod");
		$where['vod_id'] = $id;
		$array = $rs->field('vod_id,vod_cid,vod_pic,vod_name')->where($where)->find();
		@unlink(ff_img_url($arr['vod_pic']));
		if(C('url_html') > 0){	
			@unlink(ff_data_url_dir('vod',$array['vod_id'],$array['vod_cid'],$array['vod_name'],1).C('html_file_suffix'));
			@unlink(ff_play_url_dir($array['vod_id'],0,1,$array['vod_cid'],$array['vod_name']).C('html_file_suffix'));
			@unlink(ff_play_url_dir($array['vod_id'],0,1,$array['vod_cid'],$array['vod_name']).'js');
		}
		unset($where);
		//删除影片ID
		$where['vod_id'] = $id;
		$rs->where($where)->delete();
		unset($where);
    }
	// 批量生成数据
    public function create(){
		echo'<iframe scrolling="no" src="?s=Admin-Create-vodid-id-'.implode(',',$_POST['ids']).'" frameborder="0" style="display:none"></iframe>';
		$this->assign("jumpUrl",$_SESSION['vod_jumpurl']);
		$this->success('批量生成数据成功！');
    }	
	// 批量转移影片
    public function pestcid(){
		if(empty($_POST['ids'])){
			$this->error('请选择需要转移的影片！');
		}	
		$cid = intval($_POST['pestcid']);
		if (getlistson($cid)) {
			$rs = D("Vod");
			$data['vod_cid'] = $cid;
			$where['vod_id'] = array('in',$_POST['ids']);
			$rs->where($where)->save($data);
			redirect($_SESSION['vod_jumpurl']);
		}else{
			$this->error('请选择当前大类下面的子分类！');		
		}
    }	
	// 设置状态
    public function status(){
		$where['vod_id'] = $_GET['id'];
		$rs = D("Vod");
		if($_GET['value']){
			$rs->where($where)->setField('vod_status',1);
		}else{
			$rs->where($where)->setField('vod_status',0);
		}
		redirect($_SESSION['vod_jumpurl']);
    }	
	// Ajax设置星级
    public function ajaxstars(){
		$where['vod_id'] = $_GET['id'];
		$data['vod_stars'] = intval($_GET['stars']);
		$rs = D("Vod");
		$rs->where($where)->save($data);		
		echo('ok');
    }	
	// Ajax设置连载
    public function ajaxcontinu(){
		$where['vod_id'] = $_GET['id'];
		$data['vod_continu'] = trim($_GET['continu']);
		$rs = D("Vod");
		$rs->where($where)->save($data);		
		echo('ok');
    }
	 // ajax 获取类型数据 QQ:182377860
	public function ajaxcat(){
		$list_id = intval($_GET['id']);
		$cat_data = D('Mcat')->list_cat($list_id);
		$this->assign('cat_list',$cat_data);
		$this->display('./Public/system/vod_ajaxcat.html');
	}
    public function modify_cat(){
        $id = intval($_GET['id']);
        $vodData = D('Vod')->field('vod_id,vod_cid,vod_mcid')->where(array('vod_id' => $id))->find();
        $data['listvod'] = F('_ppvod/listvod');
        $data['vod_cat_list'] = D('Mcat')->list_cat($vodData['vod_cid']);
        $data['vodData'] = $vodData;
        $this->assign('data',$data);
        $this->display('./Public/system/modify_cat.html');
    }
    public function modify_cat_post(){
        $vod_id = intval($_POST['vod_id']);
        if(isset($_POST['vod_mcids'])) {
            $mcids = $_POST['vod_mcids'];
			$mcid = implode(',', $_POST['vod_mcids']);
        } else {
            $mcids = '';
			$mcid = '';
        }
		D('Vod')->where(array('vod_id'=>$vod_id))->save(array('vod_mcid' => $mcid));
		D('Mcid')->mcid_update($vod_id,$_POST['vod_mcids']);
        echo 1;
    }
	// ajax 获取类型数据 QQ:182377860
}
?>