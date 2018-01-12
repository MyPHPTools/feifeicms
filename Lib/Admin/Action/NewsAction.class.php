<?php
namespace Admin\Action;
use Common\Action\BaseAction;
class NewsAction extends BaseAction{	
	// 新闻管理
    public function show(){
		$admin = array();
		//获取地址栏参数
		$admin['cid']= $_REQUEST['cid'];
		$admin['status'] = intval($_REQUEST['status']);
		$admin['stars'] = intval($_REQUEST['stars']);
		$admin['type'] = !empty($_GET['type'])?$_GET['type']:C('admin_order_type');
		$admin['order'] = !empty($_GET['order'])?$_GET['order']:'desc';
		$admin['orders'] = 'news_'.$admin["type"].' '.$admin['order'];
		$admin['wd'] = urldecode(trim($_REQUEST['wd']));
		$admin['tag'] = urldecode(trim($_REQUEST['tag']));
		$admin['tid'] = $_REQUEST['tid'];//专题ID
		$admin['p'] = '';
		//生成查询参数
		$limit = C('url_num_admin');
		$order = 'news_'.$admin["type"].' '.$admin['order'];
		if ($admin['cid']) {
			$where['news_cid']= getlistsqlin($admin['cid']);
		}	
		if ($admin['status'] == 2) {
			$where['news_status'] = array('neq',1);
		}elseif ($admin['status'] == 1) {
			$where['news_status'] = array('eq',1);
		}
		if($admin['stars']){
			$where['news_stars'] = $admin['stars'];
		}		
		if ($admin['wd']) {
			$where['news_name'] = array('like','%'.$admin['wd'].'%');
			$admin['wd'] = urlencode($admin['wd']);
		}
		//根据不同条件加载模型
		if($admin['tag']){
			$where['tag_sid'] = 2;
			$where['tag_name'] = $admin['tag'];
			$rs = D('TagnewsView');
			$admin['tag'] = urlencode($_REQUEST['tag']);
		}else{
			$rs = D("News");
		}
		//组合分页信息		
		$count = $rs->where($where)->count('news_id');
		$totalpages = ceil($count/$limit);
		$currentpage = !empty($_GET['p'])?intval($_GET['p']):1;
		$currentpage = get_maxpage($currentpage,$totalpages);
		$pageurl = U('Admin-News/Show',$admin,false,false)."-p-".'{!page!}'.C('url_html_suffix');
		$admin['p'] = $currentpage;$_SESSION['news_jumpurl'] = U('Admin-News/Show',$admin).C('url_html_suffix');
		$pages = '共'.$count.'篇文章&nbsp;当前:'.$currentpage.'/'.$totalpages.'页&nbsp;'.getpage($currentpage,$totalpages,8,$pageurl,'pagego(\''.$pageurl.'\','.$totalpages.')');
		$admin['pages'] = $pages;
		//查询数据
		$list = $rs->where($where)->order($order)->limit($limit)->page($currentpage)->select();
		foreach($list as $key=>$val){
		    $list[$key]['list_url'] = '?s=Admin-News-Show-cid-'.$list[$key]['news_cid'];
			$list[$key]['news_url'] = ff_data_url('news',$list[$key]['news_id'],$list[$key]['news_cid'],$list[$key]['news_name'],1,$list[$key]['news_jumpurl']);
			$list[$key]['news_starsarr'] = admin_star_arr($list[$key]['news_stars']);
		}				
		//dump($rs->getLastSql());
		//变量赋值并输出模板
		$this->assign($admin);
		$this->assign('list',$list);
		$this->assign('list_news',F('_ppvod/listnews'));
		if($admin['tid']){
			$this->display('./Public/system/special_news.html');
		}else{
	    	$this->display('./Public/system/news_show.html');
		}
    }
	// 添加编辑
    public function add(){
		$rs = D("News");
		$news_id = intval($_GET['id']);
		if($news_id>0){
            $where['news_id'] = $news_id;
			$array = $rs->where($where)->relation('Tag')->find();
			$array['news_tplname'] = '编辑';
			foreach($array['Tag'] as $key=>$value){
				$tag[$key] = $value['tag_name'];
			}			
			$array['news_starsarr'] = admin_star_arr($array['news_stars']);
			if (C('admin_time_edit')){
				$array['checktime'] = 'checked';
			}
			$array['news_keywords'] = implode(',',$tag);
			$_SESSION['vod_jumpurl'] = $_SERVER['HTTP_REFERER'];
		}else{
		    $array['news_cid'] = cookie('news_cid');
		    $array['news_stars'] = 0;
		    $array['news_del'] = 0;
			$array['news_hits'] = 0;
			$array['news_inputer'] = $_SESSION['admin_name'];
			$array['news_addtime'] = time();
			$array['news_starsarr'] = admin_star_arr(1);
			$array['checktime'] = 'checked';
			$array['news_tplname'] = '添加';
		}
		$this->assign($array);
		$this->assign('list_news',F('_ppvod/listnews'));
		$this->display('./Public/system/news_add.html');
    }
	//数据库写入前置操作
    public function _before_insert(){
		//自动获取关键词tag
		if(empty($_POST["news_keywords"]) && C('rand_tag')){
			$_POST["news_keywords"] = ff_tag_auto($_POST["news_name"],$_POST["news_content"]);
		}
	}	
	// 新增新闻保存到数据库
	public function insert(){
		$rs = D("News");
		if($rs->create()){
			$id = $rs->add();
			if( false !== $id){		
			    $this->assign("jumpUrl",'?s=Admin-News-Add');
			}else{
				$this->error('文章添加失败！');
			}
		}else{
		    $this->error($rs->getError());
		}
	}
	// 新增新闻保存到数据库-后置操作
	public function _after_insert(){
		cookie('news_cid',intval($_POST["news_cid"]));
		$this->success('文章添加成功,继续添加新文章！');
	}
	// 更新数据
	public function update(){
	    $this->_before_insert();
		$tag = D('Tag');$rs = D("News");
		if($rs->create()){
			if(false !==  $rs->save()){
				//手动更新TAG
				if($_POST["news_keywords"]){
					$tag->tag_update($_POST["news_id"],$_POST["news_keywords"],2);
				}
				//后置操作条件
				$rs->$news_id = $_POST["news_id"];
			}else{
				$this->error("修改新闻信息失败！");
			}
		}else{
			$this->error($rs->getError());
		}
	}
	// 后置操作
	public function _after_update(){
		$rs = D("News");
		$news_id = $rs->$news_id;
		if($news_id){
			$this->_after_add_update($news_id);
			$this->assign("jumpUrl",$_SESSION['news_jumpurl']);
			$this->success('修改新闻信息成功！');
		}else{
			$this->error('修改新闻信息失败！');
		}		
	}
	//操作完毕后
	public function _after_add_update($news_id){
		//删除数据缓存
		if(C('data_cache_news')){
			S('data_cache_news_'.$news_id,NULL);
		}	
		//删除静态缓存
		if(C('html_cache_on')){
			$id = md5($news_id).C('html_file_suffix');
			@unlink('./Html/News_read/'.$news_id);
		}
		//生成网页
		if(C('url_html')){
			echo'<iframe scrolling="no" src="?s=Admin-Create-newsid-id-'.$news_id.'" frameborder="0" style="display:none"></iframe>';
		}				
	}		
	// Ajax设置星级
    public function ajaxstars(){
		$where['news_id'] = $_GET['id'];
		$data['news_stars'] = intval($_GET['stars']);
		$rs = D("News");
		$rs->where($where)->save($data);		
		exit('ok');
    }
	// 设置状态
    public function status(){
		$where['news_id'] = $_GET['id'];
		$rs = D("News");
		if($_GET['value']){
			$rs->where($where)->setField('news_status',1);
		}else{
			$rs->where($where)->setField('news_status',0);
		}
		redirect($_SESSION['news_jumpurl']);
    }
	// 删除文章
    public function del(){
		$this->delfile($_GET['id']);
		redirect($_SESSION['news_jumpurl']);
    }
	// 删除文章all
    public function delall(){
		if(empty($_POST['ids'])){
			$this->error('请选择需要删除的文章！');
		}	
		$array = $_POST['ids'];
		foreach($array as $val){
			$this->delfile($val);
		}
		redirect($_SESSION['news_jumpurl']);
    }
	// 删除静态文件与图片
    public function delfile($id){
		//删除专题收录
		$rs = D("Topic");
		$where['topic_sid'] = 1;
		$where['topic_did'] = $id;
		$rs->where($where)->delete();
		unset($where);
		//删除新闻评论
		unset($where);
		$rs = D("Cm");
		$where['cm_cid'] = $id;
		$where['cm_sid'] = 2;
		$rs->where($where)->delete();			
		//删除新闻TAG
		$rs = D("Tag");
		$where['tag_id'] = $id;
		$where['tag_sid'] = 2;
		$rs->where($where)->delete();
		unset($where);
		//删除静态文件与图片
		$rs = D("News");
		$where['news_id'] = $id;
		$array = $rs->field('news_id,news_cid,news_pic,news_name')->where($where)->find();
		@unlink(ff_img_url($arr['news_pic']));
		if(C('url_html')>0){
			@unlink(ff_data_url('news',$array['news_id'],$array['news_cid'],$array['news_name'],1));
		}
		unset($where);				
		//删除新闻ID
		$where['news_id'] = $id;
		$rs = D("News");
		$rs->where($where)->delete();
		unset($where);
    }
	// 批量转移文章
    public function pestcid(){
		if(empty($_POST['ids'])){
			$this->error('请选择需要转移的新闻！');
		}	
		$cid = intval($_POST['pestcid']);
		if (getlistson($cid)) {
			$rs = D("News");
			$data['news_cid'] = $cid;
			$where['news_id'] = array('in',$_POST['ids']);
			$rs->where($where)->save($data);
			redirect($_SESSION['news_jumpurl']);
		}else{
			$this->error('请选择当前大类下面的子分类！');		
		}
    }	
	// 批量生成数据
    public function create(){
		echo'<iframe scrolling="no" src="?s=Admin-Create-newsid-id-'.implode(',',$_POST['ids']).'" frameborder="0" style="display:none"></iframe>';
		$this->assign("jumpUrl",$_SESSION['news_jumpurl']);
		$this->success('批量生成新闻成功！');
    }				
}
?>