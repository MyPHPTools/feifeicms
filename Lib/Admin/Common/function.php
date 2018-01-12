<?php
//星级转化数组
function admin_star_arr($stars){
	for ($i=1; $i<=5; $i++) {
		if ($i <= $stars){
			$ss[$i]=1;
		}else{
			$ss[$i]=0;
		}
	}
	return $ss;
}
// 获取模板分页数据大小
function gettplnum($rule,$filename){
	$content = read_file(C('VIEW_PATH').C('default_theme').'/'.trim($filename).'.html');
	preg_match_all('/'.$rule.'/', $content, $data);
	foreach($data[1] as $key=>$val){
		if(strpos($val,'page:true')>0){
			$array = explode(';',str_replace('num','limit',$val));
			foreach ($array as $v){list($key,$val) = explode(':',trim($v));$param[trim($key)]=trim($val);}
			return $param['limit'];break;
		}
	}
	return 0;
}
// 获取相对应的影片或文章
function getcmcid($id,$sid){
	if($sid == 1){
		$rs = D("Vod");
		$array=$rs->field('vod_name')->where('vod_id='.$id)->find();
		if($array){return $array['vod_name'];}
	}else{
		$rs = D("News");
		$array=$rs->field('news_name')->where('news_id='.$id)->find();
		if($array){return $array['news_name'];}
	}
	return '未知数据！';
}
// 安装测试写入文件
function testwrite($d){
	$tfile = '_ppvod.txt';
	$d = ereg_replace('/$','',$d);
	$fp = @fopen($d.'/'.$tfile,'w');
	if(!$fp){
		return false;
	}else{
		fclose($fp);
		$rs = @unlink($d.'/'.$tfile);
		if($rs){
			return true;
		}else{
			return false;
		}
	}
}
//获取模板编辑名称
function gettplname($filename){
	if('pp_footer.html' == $filename){
	    return '底部公用模板';
	}elseif('pp_header.html' == $filename){
	    return '顶部公用模板';
	}elseif('pp_index.html' == $filename){
	    return '网站首页模板';
	}elseif('pp_news.html' == $filename){
	    return '新闻内容模板';
	}elseif('pp_newschannel.html' == $filename){
	    return '新闻频道列表模板';
	}elseif('pp_newslist.html' == $filename){
	    return '新闻栏目列表模板';
	}elseif('pp_newssearch.html' == $filename){
	    return '新闻搜索模板';
	}elseif('pp_newstag.html' == $filename){
	    return '新闻标签模板';
	}elseif('pp_play.html' == $filename){
	    return '播放页模板';
	}elseif('pp_vod.html' == $filename){
	    return '视频内容模板';
	}elseif('pp_vodlist.html' == $filename){
	    return '视频栏目列表模板';
	}elseif('pp_vodchannel.html' == $filename){
	    return '视频频道列表模板';
	}elseif('pp_vodsearch.html' == $filename){
	    return '视频搜索模板';
	}elseif('pp_vodtag.html' == $filename){
	    return '视频标签模板';
	}elseif('pp_comment.html' == $filename){
	    return '评论模板';
	}elseif('pp_guestbook.html' == $filename){
	    return '留言模板';
	}elseif('style.css' == $filename){
	    return '模板主题样式表';
	}elseif('js.css' == $filename){
	    return 'Javascript样式表';
	}elseif('js.js' == $filename){
	    return 'Javascript文件';
	}else{
		if(stristr($filename,'home_')){
			return '自定义标签';
		}elseif(stristr($filename,'my_')){
	    	return '自定义模板';
		}elseif(stristr($filename,'wap_')){
	    	return '移动模块模板';
		}else{
	    	return '未知文件';
		}
	}
}
// 获取数据库表名描述
function gettablename($tablename){
	if (strpos($tablename,'ads')>0){
		return '广告';
	}
	if (strpos($tablename,'news')>0){
		return '文章';
	}
	if (strpos($tablename,'vod')>0){
		return '影视';
	}
	if (strpos($tablename,'list')>0){
		return '栏目';
	}
	if (strpos($tablename,'cm')>0){
		return '评论信息';
	}
	if (strpos($tablename,'gb')>0){
		return '留言本';
	}
	if (strpos($tablename,'admin')>0){
		return '后台用户';
	}
	if (strpos($tablename,'special')>0){
		return '专题';
	}
	if (strpos($tablename,'user')>0){
		return '用户中心';
	}
	if (strpos($tablename,'view')>0){
		return '观看记录';
	}	
	if (strpos($tablename,'slide')>0){
		return '幻灯片';
	}	
	if (strpos($tablename,'link')>0){
		return '友情链接';
	}
	if (strpos($tablename,'collect')>0){
		return '采集';
	}	
	if (strpos($tablename,'tag')>0){
		return '标签';
	}												
}
// 获取文件夹大小
function getdirsize($dir){ 
	$dirlist = opendir($dir);
	while (false !==  ($folderorfile = readdir($dirlist))){ 
		if($folderorfile != "." && $folderorfile != "..") { 
			if (is_dir("$dir/$folderorfile")) { 
				$dirsize += getdirsize("$dir/$folderorfile"); 
			}else{ 
				$dirsize += filesize("$dir/$folderorfile"); 
			}
		}    
	}
	closedir($dirlist);
	return $dirsize;
}
//生成热门关键词JS
function admin_ff_hot_key($string){
	$array_hot = array();
	foreach(explode(chr(13),trim($string)) as $key=>$value){
		$array = explode('|',$value);
		if($array[1]){
			$array_hot[$key] = '<a href="'.$array[1].'" target="_blank">'.trim($array[0]).'</a>';
		}else{
			$array_hot[$key] = '<a href="'.UU('Home-vod/search',array('wd'=>urlencode(trim($value))),true,false).'">'.trim($value).'</a>';
		}
	}
	$hotkey = implode(' ',$array_hot);
	$hotkey = 'document.write(\''.$hotkey.'\');';
	write_file('./Runtime/Js/hotkey.js',$hotkey);
}
//替换采集等通过url参数传值
function admin_ff_url_repalce($xmlurl,$order='asc'){
	if($order=='asc'){
		return str_replace(array('|','@','#','||'),array('/','=','&','//'),$xmlurl);
	}else{
		return str_replace(array('/','=','&','||'),array('|','@','#','//'),$xmlurl);
	}	
}
?>