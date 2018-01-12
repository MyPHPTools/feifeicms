<?php
namespace Admin\Action;
use Common\Action\BaseAction;
class TplAction extends BaseAction{	
	// 显示模板管理
    public function show(){
		$dirpath = $this->dirpath();//当前目录
		$dirlast = $this->dirlast();//上一层目录
		$dir = new \Org\Net\Dir($dirpath);
		$list_dir = $dir->toArray();
		if (empty($list_dir)){
			$this->error('该文件夹下面没有文件！');
		}
		foreach($list_dir as $key=>$value){
			$list_dir[$key]['pathfile'] = admin_ff_url_repalce($value['path'],'desc').'|'.$value['filename'];
		}
		$_SESSION['tpl_jumpurl'] = '?s=Admin-Tpl-Show-id-'.admin_ff_url_repalce($dirpath,'desc');
		if($dirlast && $dirlast != '.'){
			$this->assign('dirlast',admin_ff_url_repalce($dirlast,'desc'));
		}		
		$this->assign('dirpath',$dirpath);
		$this->assign('list_dir',list_sort_by($list_dir,'mtime','desc'));
		$this->display('./Public/system/tpl_show.html');
    }
	//获取模板当前路径
	public function dirpath(){
		$id = admin_ff_url_repalce(trim($_GET['id']));
		if ($id) {
			$dirpath = $id;
		}else{
			$dirpath = C('VIEW_PATH').C('default_theme').'/';
		}
		if (!strpos($dirpath,'Tpl')) {
			$this->error('不在模板文件夹范围内！');
		}
		return $dirpath;
	}
	//获取模板上一层路径
	public function dirlast(){
		$id = admin_ff_url_repalce(trim($_GET['id']));
		if ($id) {
			return substr($id,0,strrpos($id, '/'));
		}else{
			return false;
		}
	}		
	// 编辑模板
	public function add(){
		$filename = admin_ff_url_repalce(str_replace('*','.',trim($_GET['id'])));
		if (empty($filename)) {
			$this->error('模板名称不能为空！');
		}
		$content = read_file($filename);
		$this->assign('filename',$filename);
		$this->assign('content',htmlspecialchars($content));
		$this->display('./Public/system/tpl_add.html');
	}
	// 更新模板
	public function update(){
		$filename = trim($_POST['filename']);
		if (empty($filename)) {
			$this->error('模板文件名不能为空！');
		}		
		if( !in_array( strrchr($filename,"."), array('.html','.htm','.shtml','.shtm','.xml','.js','.css')) ){
			$this->error('模板格式错误！');
		}
		//
		$content = stripslashes(htmlspecialchars_decode($_POST['content']));
		if (empty($content)) {
			$this->error('模板内容不能为空！');
		}		
		if (!testwrite(substr($filename,0,strrpos($filename,'/')))){
			$this->error('在线编辑模板需要给'.TEMPLATE_PATH.'添加写入权限！');
		}
		write_file($filename,$content);
		if (!empty($_SESSION['tpl_jumpurl'])) {
			$this->assign("jumpUrl",$_SESSION['tpl_jumpurl']);
		}else{
			$this->assign("jumpUrl",'?s=Admin/Tpl/Show');
		}
		$this->success('恭喜您，模板更新成功！');
	}
	// 删除模板
    public function del(){
		$id = admin_ff_url_repalce(str_replace('*','.',trim($_GET['id'])));
		if (!substr(sprintf("%o",fileperms($id)),-3)){
			$this->error('无删除权限！');
		}
		@unlink($id);
		if (!empty($_SESSION['tpl_jumpurl'])) {
			$this->assign("jumpUrl",$_SESSION['tpl_jumpurl']);
		}else{
			$this->assign("jumpUrl",'?s=Admin/Tpl/Show');
		}
		$this->success('删除文件成功！');
    }				
}
?>