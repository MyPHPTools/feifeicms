<?php
/*项目入口根模块*/
namespace Common\Action;
use Think\Action;
class AllAction extends Action{
    //构造函数
    public function _initialize(){
        header("Content-Type:text/html; charset=utf-8");
    }
	// 影视多分类筛选变量定义
	public function Lable_Vod_Type($param){
		$array['sid'] = 1;
		$array['type_id'] = $param['id'];
		$array['type_year'] = $param['year'];
		$array['type_langaue'] = $param['langaue'];
		$array['type_area'] = $param['area'];
		$array['type_letter'] = $param['letter'];
		$array['type_actor'] = $param['actor'];
		$array['type_director'] = $param['director'];
		//
		$array['type_wd'] = $param['wd'];
		$array['type_page'] = $param['page'];
		$array['type_order'] = $param['order'];
		$array['type_skin'] = getlistname($param['id'],'list_skin_type');
		$array['type_pid'] = getlistname($param['id'],'list_pid');
		if (empty($array['type_skin'])) {
			$array['type_skin'] = 'pp_vodtype';
		}
		return $array;
    }
	// 影视栏目页变量定义
	public function Lable_Vod_List($param,$array_list){
		$array_list['sid'] = 1;
		$array_list['list_page'] = $param['page'];
		if ($param['page'] > 1) {
			$array_list['title'] = $array_list['list_name'].'-第'.$param['page'].'页'.'-'.C('site_name');
		}else{
			$array_list['title'] = $array_list['list_name'].'-'.C('site_name');
		}
		if (empty($array_list['list_skin'])) {
			$array_list['list_skin'] = 'pp_vodlist';
		}
		return $array_list;
    }
	/*****************影视内容,播放页公用变量定义******************************
	* @$array/具体的内容信息
	* @$array_play 为解析播放页
	* @返回赋值后的arrays 多维数组*/
	public function Lable_Vod_Read($array,$array_play=false){
		$array_list = list_search(F('_ppvod/list'),'list_id='.$array['vod_cid']);
		$array['sid'] = 1;
		$array['title'] = $array['vod_name'].'-'.C('site_name');		
		$array['vod_readurl'] = ff_data_url('vod',$array['vod_id'],$array['vod_cid'],$array['vod_name'],1,$array['vod_jumpurl']);
		$array['vod_playurl'] = ff_play_url($array['vod_id'],0,1,$array['vod_cid'],$array['vod_name']);
		$array['vod_picurl'] = ff_img_url($array['vod_pic'],$array['vod_content']);
		$array['vod_picurl_small'] = ff_img_url_small($array['vod_pic'],$array['vod_content']);
		$array['vod_rssurl'] = UU('Home-map/rss',array('id'=>$array['vod_id']),true,false);
		$array['vod_hits_insert'] = ff_get_hits('vod','insert',$array);
		$array['vod_hits_month'] = ff_get_hits('vod','vod_hits_month',$array);
		$array['vod_hits_week'] = ff_get_hits('vod','vod_hits_week',$array);
		$array['vod_hits_day'] = ff_get_hits('vod','vod_hits_day',$array);
		if($array['vod_skin']){
			$array['vod_skin_detail'] = trim($array['vod_skin']);
			$array['vod_skin_play'] = trim($array['vod_skin']).'_play';
		}else{
			$array['vod_skin_detail'] = !empty($array_list['list_skin_detail']) ? $array_list['list_skin_detail'] : 'pp_vod';
			$array['vod_skin_play'] = !empty($array_list['list_skin_play']) ? $array_list['list_skin_play'] : 'pp_play';
		}
		//播放列表解析
		$array['vod_playlist'] = $this->ff_playlist_all($array);
		$array['vod_playcount'] = count($array['vod_playlist']);
		$array['vod_player'] = 'var ff_urls=\''.$this->ff_playlist_json(array($array['vod_name'],$array_list[0]['list_name'],$array_list[0]['list_url']),$array['vod_playlist']).'\';';
		//按顺序排列
		ksort($array['vod_playlist']);
		$arrays['show'] = $array_list[0];
		$arrays['read'] = $array;
		return $arrays;
	}
	/*****************影视播放页变量定义 适用于动态与合集为一个播放页******************************<br />
	* @$array 内容页解析好后的内容页变量 arrays['read']
	* @$array_play 为播放页URL参数 array('id'=>558,'sid'=>1,'pid'=>1)
	* @返回$array 内容页重新组装的数组*/
	public function Lable_Vod_Play($array,$array_play,$createplayone){
		$player = C('play_player');
		$player_here = explode('$$$',$array['vod_play']);
		$array['vod_sid'] = $array_play['sid'];
		$array['vod_pid'] = $array_play['pid'];
		$array['vod_playname'] = $player_here[$array_play['sid']];
		$array['vod_playername'] = $player[$array['vod_playname']][1];
		$array['vod_playerkey'] = $player[$array['vod_playname']][0];
		$array['vod_jiname'] = $array['vod_playlist'][$array['vod_playerkey'].'-'.$array_play['sid']]['son'][$array_play['pid']-1]['playname'];
		$array['vod_playpath']= $array['vod_playlist'][$array['vod_playerkey'].'-'.$array_play['sid']]['son'][$array_play['pid']-1]['playpath'];
		$array['vod_nextpath']= $array['vod_playlist'][$array['vod_playerkey'].'-'.$array_play['sid']]['son'][$array_play['pid']]['playpath'];
		$array['vod_count'] = $array['vod_playlist'][$array['vod_playerkey'].'-'.$array_play['sid']]['son'][0]['playcount'];
		$array['title'] = '正在播放 '.$array['vod_name'].'-'.$array['vod_jiname'].'-'.C('site_name');
		//播放器调用
		if($createplayone){
			//适用于静态网页生成每一集
			$player_jsdir = C('site_path').ff_play_url_dir($array['vod_id'],0,1,$array['vod_cid'],$array['vod_name']).'.js?'.uniqid();
			$array['vod_player'] = '<script charset="utf-8" src="'.$player_jsdir.'"></script><script charset="utf-8" src="'.C('site_path').'Runtime/Player/play.js"></script><script charset="utf-8" src="'.C('site_path').'Public/player5.0/play.js"></script>'."\n";
		}else{
			$array['vod_player'] = '<script language="javascript">'.$array['vod_player'].'</script><script charset="utf-8" src="'.C('site_path').'Runtime/Player/play.js"></script><script charset="utf-8" src="'.C('site_path').'Public/player5.0/play.js?"></script>'."\n";
		}
		//点击数调用
		$array['vod_hits_month'] = ff_get_hits('vod','vod_hits_month',$array,C('url_html_play'));
		$array['vod_hits_week'] = ff_get_hits('vod','vod_hits_week',$array,C('url_html_play'));
		$array['vod_hits_day'] = ff_get_hits('vod','vod_hits_day',$array,C('url_html_play'));
		return $array;
	}
	//组合播放地址组列表为二维数组
	public function ff_playlist_all($array){
		if(empty($array['vod_url'])){return false;}
		$playlist = array();
		$array_server = explode('$$$',$array['vod_server']);
		$array_player = explode('$$$',$array['vod_play']);
		$array_urllist = explode('$$$',$array['vod_url']);
		$player = C('play_player');
		$server = C('play_server');
		foreach($array_player as $sid=>$val){
			$playlist[$player[$val][0].'-'.$sid] = array('servername' => $array_server[$sid],'serverurl' => $server[$array_server[$sid]],'playername'=>$player[$val][1],'playname'=>$val,'son' => $this->ff_playlist_one($array_urllist[$sid],$array['vod_id'],$sid,$array['vod_cid'],$array['vod_name']));
		}
		//ksort($playlist);
	    return $playlist;
	}
	//分解单组播放地址链接
	public function ff_playlist_one($urlone,$id,$sid,$cid,$name){
		$urllist = array();
	    $array_url = explode(chr(13),str_replace(array("\r\n", "\n", "\r"),chr(13),$urlone));
		foreach($array_url as $key=>$val){
		    if (strpos($val,'$') > 0) {
		        $ji = explode('$',$val);
			    $urllist[$key]['playname'] = trim($ji[0]);
			    $urllist[$key]['playpath'] = trim($ji[1]);
			}else{
			    $urllist[$key]['playname'] = '第'.($key+1).'集';
			    $urllist[$key]['playpath'] = trim($val);
			}
			$urllist[$key]['playurl'] = ff_play_url($id,$sid,$key+1,$cid,$name);
			$urllist[$key]['playcount'] = count($array_url);
		}
	    return $urllist;
	}
	//生成播放列表字符串
	public function ff_playlist_json($vod_info_array,$vod_url_array){
		//json数组创建
		$key = 0;
		foreach($vod_url_array as $val){
			$array_urls[$key]['servername'] = $val['servername'];
			$array_urls[$key]['playname'] = $val['playname'];
			foreach($val['son'] as $keysub=>$valsub){
				$array_urls[$key]['playurls'][$keysub] = array($valsub['playname'], $valsub['playpath'], $valsub['playurl']);
			}
			$key++;
		}
		return json_encode(array('Vod'=>$vod_info_array,'Data'=>$array_urls));
	}
	//资讯栏目页变量定义
	public function Lable_News_List($param,$array_list){
		$array_list['sid'] = 2;
		$array_list['list_wd'] = $param['wd'];	
		$array_list['list_page'] = $param['page'];
		$array_list['list_letter'] = $param['letter'];
		$array_list['list_order'] = $param['order'];
		if ($param['page'] > 1) {
			$array_list['title'] = $array_list['list_name'].'-第'.$param['page'].'页';
		}else{
			$array_list['title'] = $array_list['list_name'];
		}
		$array_list['title'] = $array_list['title'].'-'.C('site_name');
		if (empty($array_list['list_skin'])) {
			$array_list['list_skin'] = 'pp_newslist';
		}
		return $array_list;
    }	
	//资讯内容页变量定义
	public function Lable_News_Read($array,$array_play = false){
		$array_list = list_search(F('_ppvod/list'),'list_id='.$array['news_cid']);
		$array['sid'] = 2;
		$array['title'] = $array['news_name'].'-'.C('site_name');
		$array['news_readurl'] = ff_data_url('news',$array['news_id'],$array['news_cid'],$array['news_name'],1,$array['news_jumpurl']);
		$array['news_picurl'] = ff_img_url($array['news_pic'],$array['news_content']);
		$array['news_picurl_small'] = ff_img_url_small($array['news_pic'],$array['news_content']);
		$array['news_hits_insert'] = ff_get_hits('news','insert',$array);
		$array['news_hits_month'] = ff_get_hits('news','news_hits_month',$array);
		$array['news_hits_week'] = ff_get_hits('news','news_hits_week',$array);
		$array['news_hits_day'] = ff_get_hits('news','news_hits_day',$array);
		if($array['news_skin']){
			$array['news_skin_detail'] = trim($array['news_skin']);
		}else{
			$array['news_skin_detail'] = !empty($array_list['list_skin_detail']) ? $array_list['list_skin_detail'] : 'pp_news';
		}		
		$arrays['show'] = $array_list[0];
		$arrays['read'] = $array;		
		return $arrays;
	}
	//专题列表页变量定义
	public function Lable_Special_List($param){
		$array_list = array();
		$array_list['sid'] = 3;
		$array_list['special_skin'] = 'pp_speciallist';
		$array_list['special_page'] = $param['page'];
		$array_list['special_order'] = 'special_'.$param['order'];
		if ($param['page'] > 1) {
			$array_list['title'] = '专题列表-第'.$param['page'].'页'.'-'.C('site_name');
		}else{
			$array_list['title'] = '专题列表'.'-'.C('site_name');
		}
		return $array_list;
    }
	//专题内容页变量定义
	public function Lable_Special_Read($array,$array_play = false){
		$array_ids = array();$where = array();
		$array['special_readurl'] = ff_data_url('special',$array['special_id'],0,$array['special_name'],1,0);
		$array['special_logo'] = ff_img_url($array['special_logo'],$array['special_content']);
		$array['special_banner'] = ff_img_url($array['special_banner'],$array['special_content']);
		$array['special_hits_insert'] = ff_get_hits('special','insert',$array);
		$array['special_hits_month'] = ff_get_hits('special','special_hits_month',$array);
		$array['special_hits_week'] = ff_get_hits('special','special_hits_week',$array);
		$array['special_hits_day'] = ff_get_hits('special','special_hits_day',$array);
		$array['special_skin'] = !empty($array['special_skin']) ? $array['special_skin'] : 'pp_special';
		$array['title'] = $array['special_name'].'-专题-'.C('site_name');
		$array['sid'] = 3;
		//收录影视
		$rs = D('TopicvodView');
		$where['topic_sid'] = 1;
		$where['topic_tid'] = $array['special_id'];
		$list_vod = $rs->where($where)->order('topic_oid desc,topic_did desc')->select();
		foreach($list_vod as $key=>$val){
			$list_vod[$key]['list_id'] = $list_vod[$key]['vod_cid'];
			$list_vod[$key]['list_name'] = getlistname($list_vod[$key]['list_id'],'list_name');
			$list_vod[$key]['list_url'] = getlistname($list_vod[$key]['list_id'],'list_url');
			$list_vod[$key]['vod_readurl'] = ff_data_url('vod',$list_vod[$key]['vod_id'],$list_vod[$key]['vod_cid'],$list_vod[$key]['vod_name'],1,$list_vod[$key]['vod_jumpurl']);
			$list_vod[$key]['vod_playurl'] = ff_play_url($list_vod[$key]['vod_id'],0,1,$list_vod[$key]['vod_cid'],$list_vod[$key]['vod_name']);
			$list_vod[$key]['vod_picurl'] = ff_img_url($list_vod[$key]['vod_pic'],$list_vod[$key]['vod_content']);
			$list_vod[$key]['vod_picurl_small'] = ff_img_url_small($list_vod[$key]['vod_pic'],$list_vod[$key]['vod_content']);		
		}
		//收录资讯
		$rs = D('TopicnewsView');
		$where['topic_sid'] = 2;
		$where['topic_tid'] = $array['special_id'];
		$list_news = $rs->where($where)->order('topic_oid desc,topic_did desc')->select();
		foreach($list_news as $key=>$val){
			$list_news[$key]['list_id'] = $list_news[$key]['news_cid'];
			$list_news[$key]['list_name'] = getlistname($list_news[$key]['list_id'],'list_name');
			$list_news[$key]['list_url'] = getlistname($list_news[$key]['list_id'],'list_url');
			$list_news[$key]['news_readurl'] = ff_data_url('news',$list_news[$key]['news_id'],$list_news[$key]['news_cid'],$list_news[$key]['news_name'],1,$list_news[$key]['news_jumpurl']);
			$list_news[$key]['news_picurl'] = ff_img_url($list_news[$key]['news_pic'],$list_news[$key]['news_content']);
			$list_news[$key]['news_picurl_small'] = ff_img_url_small($list_news[$key]['news_pic'],$list_news[$key]['news_content']);		
		}
		$arrays['read'] = $array;
		$arrays['list_vod'] = $list_vod;
		$arrays['list_news'] = $list_news;
		return $arrays;
	}			
	//搜索页变量定义
	public function Lable_Search($param,$sidname = 'vod'){
		$array_search = array();
		if($sidname == 'vod'){
			$array_search['search_actor'] = $param['actor'];
			$array_search['search_director'] = $param['director'];			
			$array_search['search_area'] = $param['area'];
			$array_search['search_langaue'] = $param['langaue'];
			$array_search['search_year'] = $param['year'];
			$array_search['sid'] = 1;
		}else{
			$array_search['sid'] = 2;
		}
		$array_search['search_wd'] = $param['wd'];
		$array_search['search_name'] = $param['name'];
		$array_search['search_title'] = $param['title'];
		$array_search['search_page'] = $param['page'];
		$array_search['search_letter'] = $param['letter'];
		$array_search['search_order'] = $param['order'];
		$array_search['search_skin'] = 'pp_'.$sidname.'search';
		if ($param['page'] > 1) {
			$array_search['title'] = $array_search['search_wd'].'-第'.$param['page'].'页';
		}else{
			$array_search['title'] = $array_search['search_wd'];
		}
		$array_search['title'] = $array_search['search_area'].$array_search['search_langaue'].$array_search['search_actor'].$array_search['search_director'].$array_search['title'].'-'.C('site_name');
		return $array_search;
    }	
	//Tag页变量定义
	public function Lable_Tags($param){
		$array_tag = array();
		$array_tag['tag_name'] = $param['wd'];
		$array_tag['tag_url'] = ff_tag_url($param['wd']);
		$array_tag['tag_page'] = $param['page'];
		if ($param['page'] > 1) {
			$array_tag['title'] = $array_tag['tag_name'].'-第'.$param['page'].'页-'.C('site_name');
		}else{
			$array_tag['title'] = $array_tag['tag_name'].'-'.C('site_name');
		}
		return $array_tag;
    }	
	//首页标签定义
	public function Lable_Index(){
		$array = array();
		$array['title'] = C('site_name');
		$array['model'] = 'index';
		return $array;
	}
	//地图页标签定义
	public function Lable_Maps($mapname,$limit,$page){
		$rs = M("Vod");
		$list = $rs->order('vod_addtime desc')->limit($limit)->page($page)->select();
		if($list){
			foreach($list as $key=>$val){
				$list[$key]['vod_readurl'] = ff_data_url('vod',$list[$key]['vod_id'],$list[$key]['vod_cid'],$list[$key]['vod_name'],1,$list[$key]['vod_jumpurl']);
				$list[$key]['vod_playurl'] = ff_play_url($list[$key]['vod_id'],0,1,$list[$key]['vod_cid'],$list[$key]['vod_name']);
			}
			return $list;			
		}
		return false;
    }
	//全局标签定义
	public function Lable_Style(){
		$array = array();
		$array['model'] = strtolower(MODULE_NAME);
		$array['action'] = strtolower(ACTION_NAME);	
		C('TOKEN_ON',false);//C('TOKEN_NAME','form_'.$array['model']);取消前端的表单令牌
		$array['root'] = __ROOT__.'/';
		$array['tpl'] = $array['root'].str_replace('./','',C('VIEW_PATH').C('default_theme')).'/';
		$array['css'] = '<link rel="stylesheet" type="text/css" href="'.$array['tpl'].'style.css">'."\n";
		$array['sitename'] = C('site_name');
		$array['siteurl'] = C('site_url');
		$array['sitehot'] = ff_hot_key(C('site_hot'));
		$array['keywords'] = C('site_keywords');
		$array['description'] = C('site_description');
		$array['email'] = C('site_email');
		$array['copyright'] = C('site_copyright');
		$array['tongji'] = C('site_tongji');
		$array['icp'] = C('site_icp');
		$array['hotkey']   = ff_hot_key(C('site_hot'));
		//$array['username'] = $_COOKIE['bd_username'];
		//$array['userid'] = intval($_COOKIE['bd_userid']);
		$array['url_tag'] = UU('Home-tag/show','',true,false);		
		$array['url_guestbook'] = UU('Home-gb/show','',true,false);
		$array['url_special'] = ff_special_url(1);
		$array['url_map_rss'] = ff_map_url('rss');
		$array['url_map_baidu'] = ff_map_url('baidu');
		$array['url_map_google'] = ff_map_url('google');
		$array['url_map_soso'] = ff_map_url('soso');
		$array['list_slide'] = F('_ppvod/slide');
		$array['list_link'] = F('_ppvod/link');
		$array['list_menu'] = F('_ppvod/listtree');		
		return $array;		
	}					
}
?>