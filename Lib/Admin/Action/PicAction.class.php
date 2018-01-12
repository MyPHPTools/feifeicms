<?php
namespace Admin\Action;
use Common\Action\BaseAction;
class PicAction extends BaseAction{
	//下载远程图片
    public function down(){
		C('upload_http',1);
		$img = D('Img');
		$rs = D("Vod");
		if('fail'==trim($_GET['id'])){
			$rs->execute('update '.C('db_prefix').'vod set vod_pic=REPLACE(vod_pic,"httpf://", "http://")');
		}
		$count = $rs->where('Left(vod_pic,7)="http://"')->count('vod_id');
		$list = $rs->where('Left(vod_pic,7)="http://"')->order('vod_addtime desc')->limit(C('upload_http_down'))->select();
		if($list){
			echo '<style type="text/css">div{font-size:13px;color: #333333;line-height:21px;}span{font-weight:bold;color:#FF0000}</style>';
			echo'<div>共有<span>'.$count.'</span>张远程图片，每次批量下载<span>'.C('upload_http_down').'</span>张，<span>'.C('play_collect_time').'</span>秒后执行下一次操作。<br />';
			foreach($list as $key=>$value){
				$imgnew = $img->down_load($value['vod_pic'],'vod');
				if($value['vod_pic'] == $imgnew){
					$rs->where('vod_id='.$value['vod_id'])->setField('vod_pic',str_replace("http://","httpf://",$value['vod_pic']));
					echo(($key+1).' <a href="'.$value['vod_pic'].'" target="_blank">'.$value['vod_pic'].'</a> <font color=red>下载失败!</font><br/>');
				}else{
					$rs->where('vod_id = '.$value['vod_id'])->setField('vod_pic',$imgnew);
					echo(($key+1).' <a href="'.$value['vod_pic'].'" target="_blank">'.$value['vod_pic'].'</a> 下载成功！<br />');					
				}
				ob_flush();flush();
			}
			echo'请稍等一会，正在释放服务器资源...<meta http-equiv="refresh" content='.C('play_collect_time').';url=?s=Admin-Pic-Down>';
			echo'</div>';
		}else{
			$count = $rs->where('Left(vod_pic,8)="httpf://"')->count('vod_id');
			if($count){
				echo'<div style="font-size:14px;">共有<span>'.$count.'</span>张远程图片保存失败,如果需要重新下载,请点击<a href="?s=Admin-Pic-Down-id-fail">[这里]</a>!</div>';
			}else{
				$this->assign("jumpUrl","?s=Admin-Vod-Show");
				$this->success('恭喜您,所有远程图片已经下载完成！');
			}
		}
    }
	// 本地附件展示
    public function show(){
		$id = trim($_GET['id']);
		if ($id) {
			$dirpath = admin_ff_url_repalce(str_replace('*','-',$id));
		}else{
			$dirpath = './'.C('upload_path');
		}
		if (!strpos($dirpath,C('upload_path'))) {
			$this->error('不在上传文件夹范围内！');
		}		
		$dirlast = $this->dirlast();
		$dir = new \Org\Net\Dir($dirpath);
		$list_dir = $dir->toArray();
		foreach($list_dir as $key=>$value){
			$list_dir[$key]['pathfile'] = admin_ff_url_repalce($value['path'],'desc').'|'.str_replace('-','*',$value['filename']);
		}	
		if (empty($list_dir)){
			$this->error('还没有上传任何附件,无需管理！');
		}	
		if($dirlast && $dirlast != '.'){
			$this->assign('dirlast',admin_ff_url_repalce($dirlast,'desc'));
		}
		$this->assign('dirpath',$dirpath);
		$this->assign('list_dir',$list_dir);
		$this->display('./Public/system/pic_show.html');
    }
	//获取上一层路径
	public function dirlast(){
		$id = admin_ff_url_repalce(trim($_GET['id']));
		if ($id) {
			return substr($id,0,strrpos($id, '/'));
		}else{
			return false;
		}
	}	
	// 删除单个本地附件
    public function del(){
		$path = trim(str_replace('*','-',$_GET['id']));
		@unlink($path);
		@unlink(str_replace(C('upload_path').'/',C('upload_path').'-s/',$path));
		$this->success('删除附件成功！');
    }
	// 清理无效图片
	public function ajaxpic(){
		$path = trim(str_replace('*','-',$_GET['id']));
		//根据参数组合生成当前目录下的图片数组
		$list = glob($path.'/*');
		if(empty($list)){
			exit('无图片');
		}
		foreach ($list as $i=>$file){
			$dir[] = str_replace('./'.C('upload_path').'/','',$path.'/'.basename($file));
		}
		//根据条件查询数据库并将图片保存为数组
		if(stristr($path,'/vod')){
			$rs = M("Vod");
			$array = $rs->field('vod_pic')->where('Left(vod_pic,4)!="http"')->order('vod_addtime desc')->select();
			foreach ($array as $value){
				$dir2[] = $value['vod_pic'];
			}			
		}elseif(stristr($path,'/news')){
			$rs = M("News");
			$array = $rs->field('news_pic')->where('Left(news_pic,4)!="http"')->order('news_addtime desc')->select();
			foreach ($array as $value){
				$dir2[] = $value['news_pic'];
			}			
		}elseif(stristr($path,'/slide')){
			$rs = D("Slide");
			$array = $rs->field('slide_pic')->where('Left(slide_pic,4)!="http"')->order('slide_id desc')->select();
			foreach ($array as $value){
				$dir2[] = $value['slide_pic'];
			}			
		}elseif(stristr($path,'/link')){
			$rs = M("Link");
			$array = $rs->field('link_logo')->where('Left(link_logo,4)!="http"')->order('link_id desc')->select();
			foreach ($array as $value){
				$dir2[] = $value['link_logo'];
			}			
		}
		//筛选出当前目录下的无效图片
		$del = array_diff($dir,$dir2);
		foreach ($del as $key=>$value){
			@unlink('./'.C('upload_path').'/'.$value);
		};
		exit('清理完成');
    }						
}
?>