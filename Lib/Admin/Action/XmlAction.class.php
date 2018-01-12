<?php
namespace Admin\Action;
use Common\Action\BaseAction;
class XmlAction extends BaseAction{
	// 资源列表
    public function show(){
		$this->assign('jumpurl',F('_xml/xucai'));
		$this->display('./Public/system/xml_show_list.html');
    }
	// 检测第三方资源分类是否绑定
    public function setbind(){
		$rs = M("List");
		$list = $rs->field('list_id,list_pid,list_sid,list_name')->where('list_sid = 1')->order('list_id asc')->select();
		foreach($list as $key=>$value){
			if(!getlistson($list[$key]['list_id'])){
				unset($list[$key]);
			}
		}
		$array_bind = F('_xml/bind');
		$this->assign('vod_cid',$array_bind[$_GET['bind']]);//绑定后的系统分类
		$this->assign('vod_list',$list);
		$this->display('./Public/system/xml_setbind.html');
	}
	// 存储第三方资源分类绑定
    public function insertbind(){
		$bindcache = F('_xml/bind');
		if (!is_array($bindcache)) {
			$bindcache = array();
			$bindcache['1_1'] = 0;
		}
		$bindkey = trim($_GET['bind']);
		$bindinsert[$bindkey] = intval($_GET['cid']);
		$bindarray = array_merge($bindcache,$bindinsert);
		F('_xml/bind',$bindarray);
		exit('ok');
	}
	// 断点续采
    public function xuncai(){
		$jumpurl = F('_xml/xucai');
		redirect($jumpurl);
    }
	// 空操作 处理资源站接口类型
	public function _empty(){
		$array = array();
		$array['feifei'] = 'feifei';
		$array['caijia'] = 'caijia';
		if( in_array(strtolower(ACTION_NAME), $array) ){
			$this->xml_httpurl(strtolower(ACTION_NAME));
		}
	}
	// 通过远程地址参数抓取需要的数据并处理入库
	public function xml_httpurl($actionname){
		//申明变量
		$array_url = array(); //本地程序跳转参数
		$xml_function_name = 'xml_'.$actionname;//不同资源库程序调用不同的处理函数
		//获取跳转参数
		$array_url['action'] = $_GET['action']; //是否入库(all/day/ids)
		$array_url['fid'] = intval($_GET['fid']); //合作渠道ID
		$array_url['xmlurl'] = $_GET['xmlurl']; //资源库网址
		$array_url['reurl'] = $_GET['reurl']; //来源网址
		$array_url['pic'] = $_GET['pic']; //重采资料
		//
		$array_url['vodids'] = $_GET['vodids']; if($_POST['ids']){$array_url['vodids'] = implode(',',$_POST['ids']);}//指定视频ID	
		$array_url['cid'] = $_GET['cid']; //指定视频分类
		$array_url['wd'] = $_GET['wd']; if($_POST['wd']){$array_url['wd'] = trim($_POST['wd']);} //指定关键字
		$array_url['h'] = intval($_GET['h']); //指定时间
		$array_url['p'] = !empty($_GET['p'])?intval($_GET['p']):1;$array_url['page'] = $array_url['p']; //指定分页		
		//
		$array_url['inputer'] = $_GET['inputer']; //指定资源库频道
		$array_url['play'] = $_GET['play']; //指定播放器组(如不指定则为目标站的全部播放器组)
		// 调用抓取数据处理函数
		$json_data = $this->$xml_function_name($array_url);
		if(!$json_data){
			$this->error("采集失败，请多试几次，如一直出现该错误，通常为网络不稳定或禁用了采集！");
		}
		// 是否采集入库
		if($array_url['action']){
			echo'<style type="text/css">li{font-size:12px;color: #333333;line-height:22px}span{font-weight:bold;color:#FF0000}</style><div id="show"><li>共有<span>'.$json_data['page']['recordcount'].'</span>个数据，需要采集<span>'.$json_data['page']['pagecount'].'</span>次，每一次采集<span>'.$json_data['page']['pagesize'].'</span>个，正在执行第<span color=green>'.$array_url['page'].'</span>次采集任务</li>';
			// 将采集的数据添加到本地数据库
			$rs = D('Xml');
			foreach($json_data['listvod'] as $key=>$vod){
				$array_vod_play = explode('$$$',$vod['vod_play']);
				$array_vod_url = explode('$$$',$vod['vod_url']);
				echo '<li>第<span>'.(($array_url['page']-1)*$json_data['page']['pagesize']+$key+1).'</span>个影片有<span>'.count($array_vod_play).'</span>组播放地址 ['.getlistname($vod['vod_cid']).'] '.$vod['vod_name'].' <font color="green">';
				//有几组播放地址就添加几次
				foreach($array_vod_play as $ii=>$value){
					$vod['vod_inputer'] = 'xml_'.$array_url['fid'];
					$vod['vod_play'] = $value;
					$vod['vod_url'] = trim($array_vod_url[$ii]);
					echo $rs->xml_insert($vod, $array_url['pic']);
				}
				echo '</font></li>';
				ob_flush();flush();
			}
			echo '</div>';
			if('all' == $array_url['action'] || 'day' == $array_url['action']){
				if($array_url['page'] < $json_data['page']['pagecount']){
					//缓存断点续采
					$jumpurl = str_replace('{!page!}',($array_url['page']+1),$json_data['tpl']['pageurl']);
					F('_xml/xucai',$jumpurl);
					//跳转到下一页
					echo C('play_collect_time').'秒后将自动采集下一页!';
					echo '<meta http-equiv="refresh" content='.C('play_collect_time').';url='.$jumpurl.'>';
				}else{
					//清除断点续采
					F('_xml/xucai',NULL);
					echo '<div>恭喜您，所有采集任务已经完成，返回[<a href="?s=Admin-Vod-Show">视频管理中心</a>]，查看[<a href="?s=Admin-Vod-Show-vod_cid-0">需要人工再次审核的数据</a>]!</div>';					
				}
			}
		}else{
			//列表分页的方式展示抓取的数据
			$array_url['vodids'] = '';
			$this->assign($array_url);
			$this->assign($json_data['tpl']);
			$this->assign('list_class',$json_data['listclass']);
			$this->assign('list_vod',$json_data['listvod']);
			$this->display('./Public/system/xml_show.html');
		}
	}
	// 资源站1(feifeicms 3.x api json)
    public function xml_feifei($array_url){
		//组合资源库URL地址并获取XML资源
		$http_url = str_replace('@', '-', admin_ff_url_repalce($array_url['xmlurl']).'index.php?s=plus-api-json-action-'.$array_url['action'].'-vodids-'.$array_url['vodids'].'-cid-'.$array_url['cid'].'-play-'.$array_url['play'].'-inputer-'.$array_url['inputer'].'-wd-'.urlencode($array_url['wd']).'-h-'.$array_url['h'].'-p-'.$array_url['page']);
		$json = ff_file_get_contents($http_url);
		if ($json) {
			$json = json_decode($json);
			// 获取到的远程分页数据
			$array_page = array();
			$array_page['pageindex'] = $json->page->pageindex;
			$array_page['pagecount'] = $json->page->pagecount;
			$array_page['pagesize'] = $json->page->pagesize;
			$array_page['recordcount'] = $json->page->recordcount;
			// 获取到的远程栏目数据
			$array_list = array();
			foreach($json->list as $key=>$value){
				$array_list[$key]['list_id'] = $value->list_id;
				$array_list[$key]['list_name'] = $value->list_name;
				$array_list[$key]['bind_id'] = $array_url['fid'].'_'.$array_list[$key]['list_id'];
			}			
			// 生成本地模板参数
			$array_tpl = array();
			$array_url['p'] = '{!page!}';
			$array_tpl['pageurl'] = urldecode(U('Admin-Xml/Feifei',$array_url));
			$array_tpl['pagelist'] = '共'.$array_page['recordcount'].'条数据&nbsp;页次:'.$array_page['pageindex'].'/'.$array_page['pagecount'].'页&nbsp;'.getpage($array_page['pageindex'],$array_page['pagecount'],5,$array_tpl['pageurl'],'pagego(\''.$array_tpl['pageurl'].'\','.$array_page['pagecount'].')');	
			// 组合入库数据
			$array_vod = array();
			foreach($json->data as $key=>$value){
				$array_vod[$key]['vod_id'] = $value->vod_id;
				$array_vod[$key]['vod_cid'] = intval(ff_bind_id($array_url['fid'].'_'.$value->vod_cid));
				$array_vod[$key]['list_name'] = $value->list_name;
				$array_vod[$key]['vod_name'] = $value->vod_name;
				$array_vod[$key]['vod_title'] = $value->vod_title;
				$array_vod[$key]['vod_continu'] = $value->vod_continu;
				$array_vod[$key]['vod_addtime'] = $value->vod_addtime;
				$array_vod[$key]['vod_pic'] = $value->vod_pic;
				$array_vod[$key]['vod_actor'] = $value->vod_actor;
				$array_vod[$key]['vod_director'] = $value->vod_director;
				$array_vod[$key]['vod_content'] = $value->vod_content;
				$array_vod[$key]['vod_inputer'] = $array_url['fid'].'_'.$array_vod[$key]['vod_id'];
				$array_vod[$key]['vod_reurl'] = str_replace('||','//',$value->vod_reurl);
				if(!$array_vod[$key]['vod_reurl']){
					$array_vod[$key]['vod_reurl'] = $array_url['reurl'].$array_vod[$key]['vod_id'];
				}
				$array_vod[$key]['vod_area'] = $value->vod_area;
				$array_vod[$key]['vod_year'] = $value->vod_year;
				$array_vod[$key]['vod_language'] = $value->vod_language;
				$array_vod[$key]['vod_language'] = $value->vod_language;
				$array_vod[$key]['vod_total'] = $value->vod_total;
				$array_vod[$key]['vod_isend'] = $value->vod_isend;
				$array_vod[$key]['vod_isfilm'] = $value->vod_isfilm;
				$array_vod[$key]['vod_filmtime'] = $value->vod_filmtime;
				$array_vod[$key]['vod_keywords'] = $value->vod_keywords;
				$array_vod[$key]['vod_play'] = $value->vod_play;
				$array_vod[$key]['vod_url'] = $value->vod_url;
			}
			//dump($array_vod);
			//$array['url'] = $array_url; //远程URL变量
			$array['tpl'] =	$array_tpl; //本地模板变量
			$array['page'] = $array_page; //远程分页数据
			$array['listclass'] = $array_list; //远程栏目数据
			$array['listvod'] = $array_vod; //远程内容数据
			return $array;
		}
		return false;
	}
	//资源站为A类型
    public function xml_caijia($array_url){
		//组合资源库URL地址并获取XML资源
		$array_tpl['httpurl'] = '&wd='.urldecode($array_url['wd']).'&t='.$array_url['cid'].'&h='.$array_url['h'].'&ids='.$array_url['vodids'].'&pg='.$array_url['page'];
		if($array_url['action']){
			$array_tpl['httpurl'] = str_replace('?ac=list','?ac=videolist',admin_ff_url_repalce($array_url['xmlurl'])).$array_tpl['httpurl'];
		}else{
			$array_tpl['httpurl'] = admin_ff_url_repalce($array_url['xmlurl']).$array_tpl['httpurl'];
		}
		$xml = ff_file_get_contents($array_tpl['httpurl']);
		if ($xml) {
			//组合分页信息
			preg_match('<list page="([0-9]+)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">',$xml,$page_array);
			$xml_page['recordcount'] = $page_array[4];
			$xml_page['pagecount'] = $page_array[2];
			$xml_page['pagesize'] = $page_array[3];
			$xml_page['pageindex'] = $page_array[1];			
			$array_url['p'] = '{!page!}';
			$array_tpl['pageurl'] =urldecode(U('Admin-Xml/Caijia',$array_url));
			$array_tpl['pagelist'] = '共'.$xml_page['recordcount'].'条数据&nbsp;页次:'.$xml_page['pageindex'].'/'.$xml_page['pagecount'].'页&nbsp;'.getpage($xml_page['pageindex'],$xml_page['pagecount'],5,$array_tpl['pageurl'],'pagego(\''.$array_tpl['pageurl'].'\','.$xml_page['pagecount'].')');
			//组合绑定分类
			preg_match_all('/<ty id="([0-9]+)">([\s\S]*?)<\/ty>/',$xml,$list_array);
			foreach($list_array[1] as $key=>$value){
				$listclass[$key]['list_id'] = $value;
				$listclass[$key]['list_name'] = $list_array[2][$key];
				$listclass[$key]['bind_id'] = $array_url['fid'].'_'.$listclass[$key]['list_id'];
			}
			if($array_url['action']){
				preg_match_all('/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><pic>([\s\S]*?)<\/pic><lang>([\s\S]*?)<\/lang><area>([\s\S]*?)<\/area><year>([\s\S]*?)<\/year><state>([\s\S]*?)<\/state><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><actor><\!\[CDATA\[([\s\S]*?)\]\]><\/actor><director><\!\[CDATA\[([\s\S]*?)\]\]><\/director><dl>([\s\S]*?)<\/dl><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des><\/video>/',$xml,$vod_array);
			}else{
				preg_match_all('/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><dt>([\s\S]*?)<\/dt><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><\/video>/',$xml,$vod_array);
			}
			//组合数据
			foreach($vod_array[1] as $key=>$value){
				$listvod[$key]['vod_addtime'] = $value;
				$listvod[$key]['vod_id'] = $vod_array[2][$key];
				$listvod[$key]['vod_cid'] = intval(ff_bind_id($array_url['fid'].'_'.$vod_array[3][$key]));
				$listvod[$key]['vod_name'] = $vod_array[4][$key];
				$listvod[$key]['list_name'] = $vod_array[5][$key];
				//以下参数只在内容页才有
				$listvod[$key]['vod_pic'] = $vod_array[6][$key];
				$listvod[$key]['vod_language'] = $vod_array[7][$key];
				$listvod[$key]['vod_area'] = $vod_array[8][$key];
				$listvod[$key]['vod_year'] = $vod_array[9][$key];
				$listvod[$key]['vod_continu'] = $vod_array[10][$key];
				$listvod[$key]['vod_actor'] = htmlspecialchars_decode($vod_array[12][$key]);
				$listvod[$key]['vod_director'] = htmlspecialchars_decode($vod_array[13][$key]);
				$listvod[$key]['vod_content'] = htmlspecialchars_decode($vod_array[15][$key]);
				$listvod[$key]['vod_inputer'] = $array_url['fid'].'_'.$vod[$key]['vod_id'];
				$listvod[$key]['vod_reurl'] = admin_ff_url_repalce($array_url['reurl']).$listvod[$key]['vod_id'];
				if($array_url['action']){
					$listvod[$key]['vod_play'] = $vod_array[14][$key];
					$listvod[$key]['vod_title'] = $vod_array[11][$key];
					preg_match_all('/<dd flag="([\s\S]*?)"><\!\[CDATA\[([\s\S]*?)\]\]><\/dd>/',$vod_array[14][$key],$url_arr);
					$listvod[$key]['vod_play'] = str_replace(array('百度影音','youku'),array('bdhd','yuku'),implode('$$$',$url_arr[1]));
					$listvod[$key]['vod_url'] = htmlspecialchars_decode($this->xml_url_replace(implode('$$$',$url_arr[2])));
				}else{
					$listvod[$key]['vod_play'] = $vod_array[6][$key];
					$listvod[$key]['vod_title'] = $vod_array[7][$key];
				}
			}
			//$array['url'] = $array_url; //远程URL变量
			$array['tpl'] =	$array_tpl; //本地模板变量
			$array['page'] = $xml_page; //远程分页信息
			$array['listclass'] = $listclass; //远程分类变量
			$array['listvod'] = $listvod; //远程数据变量
			return $array;
		}
		return false;
	}
	//将资源站A类型XML方式获取到的播放地址转化为飞飞的播放地址格式
	public function xml_url_replace($playurl){
		$array_url = array();
		$arr_ji = explode('#',str_replace('||','//',$playurl));
		foreach($arr_ji as $key=>$value){
			$urlji = explode('$',$value);
			if(count($urlji)==3){
				$array_url[$key] = $urlji[0].'$'.trim($urlji[1]);
			}else{
				$array_url[$key] = trim($urlji[0]);
			}
		}
		return implode(chr(13),$array_url);	
	}							
}
?>