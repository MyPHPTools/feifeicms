<?php
/*-------------------------------------------------老函数兼容开始------------------------------------------------------------------*/
function getfirstchar($s0){
	return ff_letter_first($s0);
}
function getpinyin($str,$ishead=0,$isclose=1){
	return ff_pinyin($str,$ishead=0,$isclose=1);
}
function getsitepath($filename){
	return get_site_path($filename);
}
function getbaseurl($baseurl,$url){
	return get_base_url($baseurl,$url);
}
function getfile($url){
	return ff_file_get_contents($url);
}
function getvoddata($rule,$html){
	return ff_preg_match($rule,$html);
}
function getvodall($rule,$html){
	return ff_preg_match_all($rule,$html);
}
function getreurl($listurl){
   return ff_krsort_url($listurl);
}
function getreplace($arr){
	return ff_implode_rule($arr);
}
function getrole($str){
	return ff_replace_rule($str);
}
function getrandstr($string){
	return  ff_rand_str($string);
}
function getpicurl($file){
	return ff_img_url($file);
}
function getpicurl_s($file){
	return ff_img_url_small($file);	
}
function getplayurl($vodid){
	return ff_play_url($vodid,0,1);
}
function getactorurl($str,$type="actor"){
	return ff_search_url($str,$type);	
}
function ppvodurl($model,$params,$indexphp,$redirect=false,$suffix=false){
	return UU($model,$params,$redirect=false,$suffix=false);
};
function getletter($file='vod',$str=''){
	return ff_letter_url($file='vod',$str='');
}
function ppvodpage($currentPage,$totalPages,$halfPer=5,$url,$pagego){
	return getpage($currentPage,$totalPages,$halfPer=5,$url,$pagego);
}
function getqvodname($jiname){
	return ff_qvod_name($jiname);
}
/*-------------------------------------------------文件夹与文件操作开始------------------------------------------------------------------*/
//读取文件
function read_file($l1){
	return @file_get_contents($l1);
}
//写入文件
function write_file($l1, $l2=''){
	$dir = dirname($l1);
	if(!is_dir($dir)){
		mkdirss($dir);
	}
	return @file_put_contents($l1, $l2);
}
//递归创建文件
function mkdirss($dirs,$mode=0777) {
	if(!is_dir($dirs)){
		mkdirss(dirname($dirs), $mode);
		return @mkdir($dirs, $mode);
	}
	return true;
}
// 数组保存到文件
function arr2file($filename, $arr=''){
	if(is_array($arr)){
		$con = var_export($arr,true);
	} else{
		$con = $arr;
	}
	$con = "<?php\nreturn $con;\n?>";//\n!defined('IN_MP') && die();\nreturn $con;\n
	write_file($filename, $con);
}
/*-------------------------------------------------系统路径相关函数开始------------------------------------------------------------------*/
//获取当前地址栏URL
function get_http_url(){
	return htmlspecialchars("http://".$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]);
}
//获取根目录路径
function get_site_path($filename){
    $basepath = $_SERVER['PHP_SELF'];
    $basepath = substr($basepath,0,strpos($basepath,$filename));
	return $basepath;
}
//相对路径转绝对路径
function get_base_url($baseurl,$url){
	if("#" == $url){
		return "";
	}elseif(FALSE !== stristr($url,"http://")){
		return $url;
	}elseif( "/" == substr($url,0,1) ){
		$tmp = parse_url($baseurl);
		return $tmp["scheme"]."://".$tmp["host"].$url;
	}else{
		$tmp = pathinfo($baseurl);
		return $tmp["dirname"]."/".$url;
	}
}
//获取指定地址的域名
function get_domain($url){
	preg_match("|http://(.*)\/|isU", $url, $arr_domain);
	return $arr_domain[1];
}
/*-------------------------------------------------字符串处理开始------------------------------------------------------------------*/
// UT*转GBK
function u2g($str){
	return iconv("UTF-8","GBK",$str);
}
// GBK转UTF8
function g2u($str){
	return iconv("GBK","UTF-8//ignore",$str);
}
// 转换成JS
function t2js($l1, $l2=1){
    $I1 = str_replace(array("\r", "\n"), array('', '\n'), addslashes($l1));
    return $l2 ? "document.write(\"$I1\");" : $I1;
}
// 去掉换行
function nr($str){
	$str = str_replace(array("<nr/>","<rr/>"),array("\n","\r"),$str);
	return trim($str);
}
//去掉连续空白
function nb($str){
	$str = str_replace("　",' ',str_replace("&nbsp;",' ',$str));
	$str = ereg_replace("[\r\n\t ]{1,}",' ',$str);
	return trim($str);
}
//字符串截取(同时去掉HTML与空白)
function msubstr($str, $start=0, $length, $suffix=false){
	return ff_msubstr(eregi_replace('<[^>]+>','',ereg_replace("[\r\n\t ]{1,}",' ',nb($str))),$start,$length,'utf-8',$suffix);
}
function ff_msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
	$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$length_new = $length;
	for($i=$start; $i<$length; $i++){
		if (ord($match[0][$i]) > 0xa0){
			//中文
		}else{
			$length_new++;
			$length_chi++;
		}
	}
	if($length_chi<$length){
		$length_new = $length+($length_chi/2);
	}
	$slice = join("",array_slice($match[0], $start, $length_new));
    if($suffix && $slice != $str){
		return $slice."…";
	}
    return $slice;
}
// 汉字转拼单
function ff_pinyin($str,$ishead=0,$isclose=1){
	$str = u2g($str);//转成GBK
	global $pinyins;
	$restr = '';
	$str = trim($str);
	$slen = strlen($str);
	if($slen<2){
		return $str;
	}
	if(count($pinyins)==0){
		$fp = fopen('./Lib/Conf/pinyin.dat','r');
		if($fp){
		while(!feof($fp)){
			$line = trim(fgets($fp));
			$pinyins[$line[0].$line[1]] = substr($line,3,strlen($line)-3);
		}
		}
		fclose($fp);
	}
	for($i=0;$i<$slen;$i++){
		if(ord($str[$i])>0x80){
			$c = $str[$i].$str[$i+1];
			$i++;
			if(isset($pinyins[$c])){
				if($ishead==0){
					$restr .= $pinyins[$c];
				}
				else{
					$restr .= $pinyins[$c][0];
				}
			}else{
				//$restr .= "_";
			}
		}else if( eregi("[a-z0-9]",$str[$i]) ){
			$restr .= $str[$i];
		}
		else{
			//$restr .= "_";
		}
	}
	if($isclose==0){
		unset($pinyins);
	}
	return $restr;
}
//生成字母前缀
function ff_letter_first($s0){
	$firstchar_ord=ord(strtoupper($s0{0})); 
	if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return $s0{0}; 
	$s=iconv("UTF-8","gb2312", $s0); 
	$asc=ord($s{0})*256+ord($s{1})-65536; 
	if($asc>=-20319 and $asc<=-20284)return "A";
	if($asc>=-20283 and $asc<=-19776)return "B";
	if($asc>=-19775 and $asc<=-19219)return "C";
	if($asc>=-19218 and $asc<=-18711)return "D";
	if($asc>=-18710 and $asc<=-18527)return "E";
	if($asc>=-18526 and $asc<=-18240)return "F";
	if($asc>=-18239 and $asc<=-17923)return "G";
	if($asc>=-17922 and $asc<=-17418)return "H";
	if($asc>=-17417 and $asc<=-16475)return "J";
	if($asc>=-16474 and $asc<=-16213)return "K";
	if($asc>=-16212 and $asc<=-15641)return "L";
	if($asc>=-15640 and $asc<=-15166)return "M";
	if($asc>=-15165 and $asc<=-14923)return "N";
	if($asc>=-14922 and $asc<=-14915)return "O";
	if($asc>=-14914 and $asc<=-14631)return "P";
	if($asc>=-14630 and $asc<=-14150)return "Q";
	if($asc>=-14149 and $asc<=-14091)return "R";
	if($asc>=-14090 and $asc<=-13319)return "S";
	if($asc>=-13318 and $asc<=-12839)return "T";
	if($asc>=-12838 and $asc<=-12557)return "W";
	if($asc>=-12556 and $asc<=-11848)return "X";
	if($asc>=-11847 and $asc<=-11056)return "Y";
	if($asc>=-11055 and $asc<=-10247)return "Z";
	return 0;//null
}
/*-------------------------------------------------采集函数开始------------------------------------------------------------------*/
// 采集内核
function ff_file_get_contents($url,$timeout=10,$referer){
	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
		if($referer){
			curl_setopt ($ch, CURLOPT_REFERER, $referer);
		}		
		$content = curl_exec($ch);
		curl_close($ch);
		if($content){
			return $content;
		}		
	}
	$ctx = stream_context_create(array('http'=>array('timeout'=>$timeout)));
	$content = @file_get_contents($url, 0, $ctx);
	if($content){
		return $content;
	}
	return false;
}
// 采集-匹配规则结果
function ff_preg_match($rule,$html){
	$arr = explode('$$$',$rule);
	if(count($arr) == 2){
	    preg_match('/'.$arr[1].'/', $html, $data);
		return $data[$arr[0]].'';
	}else{
	    preg_match('/'.$rule.'/', $html, $data);
		return $data[1].'';
	}
}
// 采集-匹配规则结果all
function ff_preg_match_all($rule,$html){
	$arr = explode('$$$',$rule);
	if(count($arr) == 2){
	    preg_match_all('/'.$arr[1].'/', $html, $data);
		return $data[$arr[0]];
	}else{
	    preg_match_all('/'.$rule.'/', $html, $data);
		return $data[1];
	}
}
// 采集-倒序采集
function ff_krsort_url($listurl){
   krsort($listurl);
   foreach($listurl as $val){
       $list[]=$val;
   }
   return $list;
}
// 采集-将所有替换规则保存在一个字段
function ff_implode_rule($arr){
    foreach($arr as $val){
	    $array[] = trim(stripslashes($val));
	}
	return implode('|||',$array);
}
//  采集-规则替换
function ff_replace_rule($str){
	//$str = str_replace(array("\n","\r"),array("<nr/>","<rr/>"),strtolower($str));
	$arr1 = array('?','"','(',')','[',']','.','/',':','*','||');
	$arr2 = array('\?','\"','\(','\)','\[','\]','\.','\/','\:','.*?','(.*?)');
	//$str = str_replace(array("\n","\r"),array("<nr/>","<rr/>"),strtolower($str));
	return str_replace('\[$ppvod\]','([\s\S]*?)',str_replace($arr1,$arr2,$str));
}
//生成随机伪静态简介
function ff_rand_str($string){
    $arr=C('play_collect_content');
    //$all=mb_strlen($string,'utf-8');
	$all=iconv_strlen($string,'utf-8');
    $len=floor(mt_rand(0,$all-1));
    $str=msubstr($string,0,$len);
	$str2=msubstr($string,$len,$all);
	return $str.$arr[array_rand($arr,1)].$str2;
}
//获取绑定分类对应ID值
function ff_bind_id($key){
	$bindcache = F('_xml/bind');
	return $bindcache[$key];
}
//TAG分词自动获取
function ff_tag_auto($title,$content){
	$data = ff_file_get_contents('http://keyword.discuz.com/related_kw.html?ics=utf-8&ocs=utf-8&title='.rawurlencode($title).'&content='.rawurlencode(msubstr($content,0,500)));
	if($data) {
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $data, $values, $index);
		xml_parser_free($parser);
		$kws = array();
		foreach($values as $valuearray) {
			if($valuearray['tag'] == 'kw') {
				if(strlen($valuearray['value']) > 3){
					$kws[] = trim($valuearray['value']);
				}
			}elseif($valuearray['tag'] == 'ekw'){
				$kws[] = trim($valuearray['value']);
			}
		}
		return implode(',',$kws);
	}
	return false;
}
// 格式化采集影片名称
function ff_xml_vodname($vodname){
	$vodname = str_replace(array('【','】','（','）','(',')','{','}'),array('[',']','[',']','[',']','[',']'),$vodname);
	$vodname = preg_replace('/\[([a-z][A-Z])\]|([a-z][A-Z])版/i','',$vodname);
	$vodname = preg_replace('/TS清晰版|枪版|抢先版|HD|BD|TV|DVD|VCD|TS|\/版|\[\]/i','',$vodname);
	return trim($vodname);
}
// 格式化采集影片主演
function ff_xml_vodactor($vodactor){
	return str_replace(array(',','/','，','|','、'),',',$vodactor);	
}
/*-------------------------------------------------飞飞系统栏目相关函数开始------------------------------------------------------------------*/
//通过栏目名名获取对应的栏目ID
function getlistid($str){
    $arr = list_search(F('_ppvod/list'),'list_name='.$str);
	if(empty($arr)){
		return 0;
	}else{
	    return $arr[0]['list_id'];
	}
}
//通过栏目ID获取对应的栏目名称/别名等
function getlistname($cid,$type='list_name'){
    $arr=list_search(F('_ppvod/list'),'list_id='.$cid);
	if(is_array($arr)){
		return $arr[0][$type];
	}else{
	    return '未知ID'.$cid;
	}
}
//通过影片ID栏目ID
function getvodcid($id){
	$rs = M("Vod");
	$where['vod_id'] = $id;
	$list = $rs->where($where)->getField('vod_cid') ;
	if(isset($list)){
		return $list;
	}
	return "";
}
// 检查当前栏目是否没有小类
function getlistson($pid){
	$tree=list_search(F('_ppvod/listtree'),'list_id='.$pid);
	if(!empty($tree[0]['son'])){
		return false;
	}else{
	    return true;
	}
}
function ff_mcat_name($str,$cid){
	if(empty($str)){
		return "未知";
	}
	$mcids = explode(',', $str);
	$html = '';
	foreach($mcids as $v){
		$name = M('Mcat')->where("m_cid = {$v}")->getField('m_name');
		$html .= "{$name} ";
	}
	return $html;
}
function ff_mcat_url($str,$cid){
	if(empty($str)){
		return "未知";
	}
	$mcids = explode(',', $str);
	$html = '';
	foreach($mcids as $v){
		$url = UU('Home-vod/type', array('id'=>$cid,'mcid'=>$v),true,false);
		$arr = list_search(F('_ppvod/mcid'),'m_cid='.$v);
		$html .= "<a href='".$url."' target='_blank'>{$arr[0]['m_name']}</a> ";
	}
	return $html;
}
function ff_mcat_title($str,$cid){
	if(empty($str)){
		return "未知";
	}
	$mcids = explode(',', $str);
	$html = '';
	foreach($mcids as $v){
		$name = M('Mcat')->where("m_cid = {$v}")->getField('m_name');
		$html .= "{$name}";
	}
	return $html;
}
//通过栏目ID返回小分类其它值按数组方式
function getlistmcat($cid){
    $tree = list_search(F('_ppvod/mcat'),'list_id='.$cid);
	if(!empty($tree[0]['son'])){
		foreach($tree[0]['son'] as $val){
			$param[]=$val;
			}
		return $param;
	}else{
		$catlist = D('Mcat')->list_cat($cid);
		return $catlist ;
	}
}
//通过栏目ID获取栏目拼音
function getlistdir($cid){
    $arr = list_search(F('_ppvod/list'),'list_id='.$cid);
	if(empty($arr)){
		return 0;
	}else{
	    return $arr[0]['list_dir'];
	}
}
//通过栏目ID返回其它值按数组方式
function getlistall($cid){
    $tree = list_search(F('_ppvod/listtree'),'list_id='.$cid);
	if(!empty($tree[0]['son'])){
		foreach($tree[0]['son'] as $val){$param[]=$val;}
		return $param;
	}else{
		$vod_type=M('list')->where(array('list_pid'=>2))->select();
		return $vod_type;
	}
}
//生成栏目sql查询语句范围
function getlistsqlin($cid){
	$tree = list_search(F('_ppvod/listtree'),'list_id='.$cid);
	if (!empty($tree[0]['son'])) {
		foreach($tree[0]['son'] as $val){
			$arr['vod_cid'][] = $val['list_id'];
		}
		$channel = implode(',', $arr['vod_cid']);
		return array('IN',''.$channel.'');	
	}
	return $cid;
}
//通过栏目ID返回其它值按数组方式
function getlistarr($cid,$type='list_id'){
    $tree = list_search(F('_ppvod/listtree'),'list_id='.$cid);
	if(!empty($tree[0]['son'])){
		foreach($tree[0]['son'] as $val){$param[]=$val[$type];}
		return $param;
	}else{
		return false;
	}
}
//去重后的模板栏目ID参数 $cids = array(1,2,3,...)
function getlistarr_tag($cids){
	foreach($cids as $key=>$value){
		if(getlistson($value)){
			$cid .= ','.$value;
		}else{
			$cidin = getlistsqlin($value);
			$cid .= ','.$cidin[1];
		}
	}
	$cidarr = explode(',',$cid);
	unset($cidarr[0]);
	$cidarr = array_unique($cidarr);
	return $cidarr;
}
// 获取栏目数据统计
function getcount($cid){
	$where = array();
	if(999 == $cid){
		$rs = M("Vod");
		$where['vod_cid'] = array('gt',0);
		$where['vod_addtime'] = array('gt',getxtime(1));//当天更新的影视
		$count = $rs->where($where)->count('vod_id');
	}elseif(0 == $cid){
		$rs = M("Vod");
		$where['vod_cid'] = array('gt',0);
		$count = $rs->where($where)->count('vod_id');	
	}else{
		$sid = getlistname($cid,'list_sid');
		if ($sid == '2'){
			$where['news_cid'] = getlistsqlin($cid);
			$where['news_status'] = 1;
			$rs = M("News");
			$count = $rs->where($where)->count('news_id');
		}else{
			$where['vod_cid'] = getlistsqlin($cid);	
			$where['vod_status'] = 1;
			$rs = M("Vod");
			$count = $rs->where($where)->count('vod_id');
		}
	}
	//dump($rs->getLastSql());
	return $count+0;
}
//获取模型名称
function getsidname($sid){
	if ($sid==1){
		return 'vod';
	}elseif ($sid==2){
		return 'news';
	}elseif ($sid==3){
		return 'special';
	}else{
		return 'url';
	}
}
//获取模型ID
function ff_sid($sidname){
	if ($sidname == 'vod'){
		return 1;
	}elseif ($sidname == 'news'){
		return 2;
	}elseif($sidname == 'special'){
		return 3;
	}else{
		return 9;
	}
}
/*-------------------------------------------------模板常用函数-----------------------------------------------------------------*/
//获得某天前的时间戳
function getxtime($day){
	$day = intval($day);
	return mktime(23,59,59,date("m"),date("d")-$day,date("y"));
}
// 获取标题颜色
function getcolor($str,$color){
	if(empty($color)){
	    return $str;
	}else{
	    return '<font color="'.$color.'">'.$str.'</font>';
	}
}
// 获取时间颜色
function getcolordate($type='Y-m-d H:i:s',$time,$color='red'){
	if((time()-$time)>86400){
	    return date($type,$time);
	}else{
	    return '<font color="'.$color.'">'.date($type,$time).'</font>';
	}
}
// 读出qvod地址的文件名
function ff_qvod_name($jiname){
	$jiname = ff_preg_match('3$$$(.*)\|(.*)\|(.*)\.(.*)\|',$jiname);
	return $jiname;
}
// 处理积分样式
function getjifen($fen){
	$array = explode('.',$fen);
	return '<strong>'.$array[0].'</strong>.'.$array[1];
}
//分页样式
function getpage($currentPage,$totalPages,$halfPer=5,$url,$pagego){
    $linkPage .= ( $currentPage > 1 )
        ? '<a href="'.str_replace('{!page!}',1,$url).'" class="prev disabled">首页</a>&nbsp;<a href="'.str_replace('{!page!}',($currentPage-1),$url).'" class="prev disabled">上一页</a>&nbsp;' 
        : '<span class="prev disabled">首页</span>&nbsp;<span class="prev disabled">上一页</span>&nbsp;';
    for($i=$currentPage-$halfPer,$i>1||$i=1,$j=$currentPage+$halfPer,$j<$totalPages||$j=$totalPages;$i<$j+1;$i++){
        $linkPage .= ($i==$currentPage)?'<span class="current">'.$i.'</span>&nbsp;':'<a href="'.str_replace('{!page!}',$i,$url).'">'.$i.'</a>&nbsp;'; 
    }
    $linkPage .= ( $currentPage < $totalPages )
        ? '<a href="'.str_replace('{!page!}',($currentPage+1),$url).'" class="next pagegbk">下一页</a>&nbsp;<a href="'.str_replace('{!page!}',$totalPages,$url).'" class="next pagegbk">尾页</a>'
         : '<span class="prev disabled">下一页</span>&nbsp;<span class="prev disabled">尾页</span>';
	if(!empty($pagego)){
		$linkPage .='';
	}
	//
	if(C('url_html') && C('url_html_list')){
    	return str_replace('-1'.C('html_file_suffix'),C('html_file_suffix'),str_replace('index1'.C('html_file_suffix'),'',$linkPage));
	}else{
		return $linkPage;
	}
}
//分页样式
//顶部分页样式
function getpagetop($currentPage,$totalPages,$halfPer=5,$url,$pagego){
    $linkPage .= ( $currentPage > 1 )
        ? '<a href="'.str_replace('{!page!}',($currentPage-1),$url).'" class="prev pagegbk" data="p-'.($currentPage-1).'">上一页</a>&nbsp;'
        : '<span class="prev disabled">上一页</span>';
    $linkPage .= ( $currentPage < $totalPages )
        ? '<a href="'.str_replace('{!page!}',($currentPage+1),$url).'" class="next pagegbk" data="p-'.($currentPage+1).'">下一页</a>&nbsp;'
        : '<span class="prev disabled">下一页</span>';
    return str_replace('-1'.C('html_file_suffix'),C('html_file_suffix'),str_replace('index-1'.C('html_file_suffix'),'',$linkPage));
}
//处理最大分页参数
function get_maxpage($currentpage,$totalpages){
	if ($currentpage > $totalpages){
		$currentpage = $totalpages;
	}
	return $currentpage;
}
// 获取热门关键词
function ff_hot_key($string){
	if(C('url_html')){
		return '<script type="text/javascript" src="'.C('site_path').'Runtime/Js/hotkey.js" charset="utf-8"></script>';
	}
	$array_hot = array();
	foreach(explode(chr(13),trim($string)) as $key=>$value){
		$array = explode('|',$value);
		if($array[1]){
			$array_hot[$key] = '<a href="'.$array[1].'" target="_blank">'.trim($array[0]).'</a>';
		}else{
			$array_hot[$key] = '<a href="'.UU('Home-vod/search',array('wd'=>urlencode(trim($value))),true,false).'">'.trim($value).'</a>';
		}
	}
	return implode(' ',$array_hot);
}
// 获取与处理人气值
function ff_get_hits($sidname,$type='hits',$array,$js=true){
	if((C('url_html') && $js) || $type=='insert'){
		return '<script type="text/javascript" src="'.C('site_path').'index.php?s=hits-show-id-'.$array[$sidname.'_id'].'-type-'.$type.'-sid-'.$sidname.'" charset="utf-8"></script>';
	}else{
		return $array[$type];
	}
}
// 递归多维数组转为一级数组
function ff_arrays2array($array){
	static $result_array=array();
	foreach($array as $value){
		if(is_array($value)){
			ff_arrays2array($value);
		}else{ 
			$result_array[]=$value;
		}
	}
	return $result_array;
}
// 返回下一篇或上一篇的内容的信息
function ff_detail_array($sid='vod', $type='next', $id, $cid, $field='vod_id,vod_cid,vod_status,vod_name,vod_jumpurl'){
	//优先读取缓存数据
	$cache_key = 'cache_'.$sid.'_'.$type.'_'.$cid.'_'.$id;
	if(C('data_cache_vod')){
		$array = S($cache_key);
		if($array){
			return $array;
		}
	}
	$where = array();
	$where[$sid.'_cid'] = $cid;
	$where[$sid.'_status'] = 1;
	if($type == 'next'){
		$where[$sid.'_id'] = array('gt', $id);
		$order = $sid.'_id asc';
	}else{
		$where[$sid.'_id'] = array('lt', $id);
		$order = $sid.'_id desc';
	}
	if($sid != 'vod'){
		$field = str_replace('vod_',$sid.'_',$field);
	}
	$rs = M(ucfirst($sid));
	$array = $rs->field($field)->where($where)->limit(1)->order($order)->find();
	// 是否写入缓存
	if( C('data_cache_vod') ){
		S($cache_key, $array, intval(C('data_cache_vod')));
	}
	return $array;
}
// 迅雷
function ThunderEncode($url) 
{
$thunderPrefix="AA";
$thunderPosix="ZZ";
$thunderTitle="thunder://";
$thunderUrl=$thunderTitle.base64_encode($thunderPrefix.$url.$thunderPosix);
if (strpos($url,'http://') !== false || strpos($url,'ftp://') !== false){
return $thunderUrl;
}
else{
return $url;
 }

}
/*-------------------------------------------------飞飞系统访问路径函数开始------------------------------------------------------------------*/
// 重写动态路径
function UU($model,$params,$redirect=true,$suffix=false){
	//rewrite重写
	if(C('url_rewrite')){
		if($params['p'] != '{!page!}'){
			$params['p'] = 1;
		}
		if($model == 'Home-vod/show'){
			$reurl = str_replace(array('$id','$page'),array($params['id'],$params['p']),C('rewrite_vodlist'));
		}elseif($model == 'Home-vod/read'){
			$reurl = str_replace('$id',$params['id'],C('rewrite_voddetail'));
		}elseif($model == 'Home-vod/play'){
			$reurl = str_replace(array('$id','$sid','$pid'),array($params['id'],$params['sid'],$params['pid']),C('rewrite_vodplay'));
		}elseif($model == 'Home-vod/search'){
			$reurl = str_replace(array('$wd','$page','$actor','$director','$order'),array($params['wd'],$params['p'],$params['actor'],$params['director'],$params['order']),C('rewrite_vodsearch'));
		}elseif($model == 'Home-vod/type'){
			$reurl = str_replace(array('$id','$page','$mcid','$wd','$area','$language','$actor','$director','$year','$letter','$lz','$order'),array($params['id'],$params['p'],$params['mcid'],$params['wd'],$params['area'],$params['language'],$params['actor'],$params['director'],$params['year'],$params['letter'],$params['lz'],$params['order']),C('rewrite_vodtype'));
		}elseif($model == 'Home-tag/vod'){
			$reurl = str_replace(array('$wd','$page'),array($params['wd'],$params['p']),C('rewrite_vodtag'));
		}elseif($model == 'Home-news/show'){
			$reurl = str_replace(array('$id','$page'),array($params['id'],$params['p']),C('rewrite_newslist'));
		}elseif($model == 'Home-news/read'){
			$reurl = str_replace('$id',$params['id'],C('rewrite_newsdetail'));
		}elseif($model == 'Home-news/search'){
			$reurl = str_replace(array('$wd','$page'),array($params['wd'],$params['p']),C('rewrite_newssearch'));
		}elseif($model == 'Home-tag/news'){
			$reurl = str_replace(array('$wd','$page'),array($params['wd'],$params['p']),C('rewrite_newstag'));
		}elseif($model == 'Home-special/show'){
			$reurl = str_replace('$page',$params['p'],C('rewrite_specialshow'));
		}elseif($model == 'Home-special/read'){
			$reurl = str_replace('$id',$params['id'],C('rewrite_specialdetail'));
		}elseif($model == 'Home-gb/show'){
			$reurl = str_replace(array('$id','$page'),array($params['id'],$params['p']),C('rewrite_guestbook'));
		}elseif($model == 'Home-my/show'){
			$reurl = str_replace('$id',$params['id'],C('rewrite_mytpl'));
		}elseif($model == 'Home-map/show'){
			$reurl = str_replace(array('$id','$limit'),array($params['id'],$params['limit']),C('rewrite_map'));
		}
		//伪静态规则设置正确
		if($reurl){
			return $reurl.C('url_html_suffix');
		}else{
			return str_replace('index.php?s=/Home-','',U($model,$params,$redirect,$suffix));
		}
	}
	//TP框架默认生成的路径
	$reurl = str_replace('home-','',str_replace('index.php?s=/','index.php?s=',U($model,$params,$redirect,$suffix)));
	//不是静态模式则去掉index.php
	if(!C('url_html')){
		return str_replace('index.php','',urldecode($reurl));
	}
	return $reurl;
};
// 获取广告调用地址
function getadsurl($str,$charset="utf-8"){
	return '<script type="text/javascript" src="'.C('site_path').C('admin_ads_file').'/'.$str.'.js" charset="'.$charset.'"></script>';
}
/*******************替换自定义路径的变量****************************/
function str_replace_dir($urldir,$id,$cid,$name){
	$old = array('{listid}','{listdir}','{pinyin}','{id}','{md5}',C('html_file_suffix'));
	$new = array($cid,getlistname($cid,'list_dir'),ff_pinyin($name),$id,md5($id),'');
	return str_replace($old,$new,$urldir);
}
/*******************获取栏目页路径****************************
* $sid 模型名称'movie/article/special'
* $arrurl为数组参数传入参考U函数(方便动态模式直接生成)
* $page 分页数字,大于1时返回的URL带有分页跳转参数变量{!page!}
* 只有一个栏目ID参数时 $arrurl['id'] = $cid;
*/
function ff_list_url($sid,$arrurl,$page){
	//静态模式
	if(C('url_html') && C('url_html_list') && in_array($sid,array('vod','news'))){
		$showurl = C('site_path').str_replace('index'.C('html_file_suffix'),'',ff_list_url_dir($sid,$arrurl['id'],$page).C('html_file_suffix'));
		return $showurl;
	}else{
		if($page > 1){ $arrurl['p'] = '{!page!}'; }
		$showurl = UU('Home-'.$sid.'/show',$arrurl,true,false);
	}
	return $showurl;
}
/**********************静态生成的栏目结构******************************
* (返回的值为buildHtml的文件名,生成静态时需要将{!page!}换成对应的page值)
* $mid 模型名称'movie/article/special'
* $cid 当前分类的栏目ID值
* $page 分页数字,大于1时返回的URL带有分页跳转参数变量{!page!}
*/
function ff_list_url_dir($sid,$cid,$page){
	//影视或文章
	if('vod' == $sid){
		$listdir = str_replace_dir(C('url_vodlist'),$id,$cid,$name);
	}else{
		$listdir = str_replace_dir(C('url_newslist'),$id,$cid,$name);
	}
	if($page > 1){
		$listdir .= '-{!page!}';
	}
	return $listdir;
}
/*************************获取内容页路径****************************************
* $sid 模型名称'vod/news/special'
* $id 影片ID/文章ID/专题ID值
* $cid 当前影片/文章/专题/对应的栏目ID值
* $name 影片/文章/专题/的名称
* $page 分页数字,大于1时返回的URL带有分页跳转参数变量{!page!}
* $jumpurl 跳转地址
*/
function ff_data_url($sid,$id,$cid,$name,$page,$jumpurl){
	//有跳转地址
	if ($jumpurl) {
		return $jumpurl;
	}
	//静态
	if(C('url_html')){
		$readurl = C('site_path').str_replace('index'.C('html_file_suffix'),'',ff_data_url_dir($sid,$id,$cid,$name,$page).C('html_file_suffix'));
		return $readurl;
	}
	//动态模式
	$arrurl['id'] = $id;
	if($page > 1){
		$arrurl['p'] = '{!page!}';
	}
	return UU('Home-'.$sid.'/read',$arrurl,true,false);
}
/*************************获取详情页目录****************************************
* 返回的值为buildHtml的文件名
* $sid 模型名称'vod/news/special'
* $id 影片ID/文章ID/专题ID值
* $cid 当前影片/文章/专题/对应的栏目ID值
* $name 影片/文章/专题/的名称
* $page 分页数字,大于1时返回的URL带有分页跳转参数变量{!page!}
*/
function ff_data_url_dir($sid,$id,$cid,$name,$page){
	//专题直接返回
	if('special' == $sid){
		return trim(C('url_special').$id);
	}
	//影视或文章
	if('vod' == $sid){
		$datadir = str_replace_dir(C('url_voddata'),$id,$cid,$name);
	}else{
		$datadir = str_replace_dir(C('url_newsdata'),$id,$cid,$name);
	}
	if($page > 1){
		$datadir .= '-{!page!}';
	}
	return $datadir;
}
//获取播放页链接
function ff_play_url($id,$sid,$pid,$cid,$name){
	//静态模式
	if(C('url_html') && C('url_html_play')){
		$playurl = C('site_path').str_replace('index'.C('html_file_suffix'),'',ff_play_url_dir($id,$sid,$pid,$cid,$name).C('html_file_suffix'));
		if(C('url_html_play') == 1){
			$playurl .= '?'.$id.'-'.$sid.'-'.$pid;
		}
	}else{//动态
		$playurl = UU('Home-vod/play',array('id'=>$id,'sid'=>$sid,'pid'=>$pid),true,false);
	}
	return $playurl;
}
//播放页静态生成结构
function ff_play_url_dir($id,$sid,$pid,$cid,$name){
	$playdir = str_replace_dir(C('url_play'),$id,$cid,$name);
	if(C('url_html_play') == 2){
		$playdir .= '-'.$id.'-'.$sid.'-'.$pid;
	}	
	return $playdir;
}
//获取专题URL
function ff_special_url($page){
	if(C('url_html')){
		return C('site_path').str_replace('index'.C('html_file_suffix'),'',ff_special_url_dir($page).C('html_file_suffix'));
	}else{
		return UU('Home-special/show','',true,false);
	}
}
//获取专题静态模式目录结构URL
function ff_special_url_dir($page){
	$listdir = trim(C('url_special')).'index';
	if($page > 1){
		$listdir .= '-{!page!}';
	}
	return $listdir;
}
// 获取某图片的访问地址
function ff_img_url($file,$content,$number=1){
	if(!$file){
		return ff_img_url_preg($file,$content,$number);
	}
	if(strpos($file,'http://') !== false){
		return $file;
	}
	$prefix = C('upload_http_prefix');
	if(!empty($prefix)){
		return $prefix.$file;
	}else{
		return C('site_path').C('upload_path').'/'.$file;
	}
}
// 获取某图片的缩略图地址
function ff_img_url_small($file,$content,$number=1){
	if(!$file){
		return ff_img_url_preg($file,$content,$number);
	}
	if(strpos($file,'http://') !== false){
		return $file;
	}	
	$prefix = C('upload_http_prefix');
	if(!empty($prefix)){
		return $prefix.$file;
	}else{
		return C('site_path').C('upload_path').'-s/'.$file;
	}	
}
//正则提取正文里指定的第几张图片地址
function ff_img_url_preg($file,$content,$number=1){
	preg_match_all('/<img(.*?)src="(.*?)(?=")/si',$content,$imgarr);///(?<=img.src=").*?(?=")/si
	preg_match_all('/(?<=src=").*?(?=")/si',implode('" ',$imgarr[0]).'" ',$imgarr);
	$countimg = count($imgarr);
	if($number > $countimg){
		$number = $countimg;
	}
	return $imgarr[0][($number-1)];
}
// Tag链接
function ff_tag_url($str,$sid=1){
	if($sid==2){
		return UU('Home-tag/news',array('wd'=>urlencode($str)),true,false);
	}else{
		return UU('Home-tag/vod',array('wd'=>urlencode($str)),true,false);
	}
}
// 内容页Tag链接
function ff_content_url($content,$array_tag='',$sid=''){
	if($array_tag){
		foreach($array_tag as $key=>$value){
			$content = str_replace($value['tag_name'],'<a href="'.ff_tag_url($value['tag_name'],$sid).'">'.$value['tag_name'].'</a>',$content);
		}
	}
	return $content;
}
// 自定义模板链接
function ff_mytpl_url($tplname){
	$tplname = str_replace(array('my_','.html'),'',$tplname);
	if(C('url_html')){
		return C('site_path').C('url_mytpl').$tplname.C('html_file_suffix');
	}
	return UU('Home-my/show',array('id'=>trim($tplname)),true,false);
}
// 地图页链接
function ff_map_url($tplname){
	if(C('url_html')){
		return C('site_path').C('url_map').$tplname.'.xml';
	}
	$limit = 30;
	if($tplname != 'rss'){
		$limit = 100;
	}
	return UU('Home-map/show',array('id'=>$tplname,'limit'=>$limit),true,false);
}
// 获取26个字母链接
function ff_letter_url($file='vod',$str=''){
	if(C('url_html')){
		$index='index.html';
	}else{
		$index='index.php';
	}
    for($i=1;$i<=26;$i++){
	   $url = UU('Home-'.$file.'/search',array('id'=>chr($i+64),'x'=>'letter'),true,false);
	   $str.='<a href="'.$url.'" class="letter_on">'.chr($i+64).'</a>';
	}
	return $str;
}
// 获取搜索带链接
function ff_search_url($str,$type="actor",$sidname='vod',$action='search'){
	$array = array();
    $str = str_replace(array('/','|',',','，'),' ',$str);
	$arr = explode(' ',$str);
	foreach($arr as $key=>$val){
		$array[$key] = '<a href="'.UU('Home-'.$sidname.'/'.$action,array($type=>urlencode($val)),true,false).'" target="_blank">'.$val.'</a>';
	}
	return implode(' ',$array);
}
// 获取影片最后一集array(sid,pid,jiname,jipath)
function ff_play_url_end($vod_url){
	$array = array();
	$arr_server = explode('$$$',trim($vod_url));
	foreach($arr_server as $key=>$value){
		$array[$key] = array(count(explode(chr(13),str_replace(array("\r\n", "\n", "\r"),chr(13),$value))),$key);
	}
	$max_key = max($array);
	$array = explode(chr(13),str_replace(array("\r\n", "\n", "\r"),chr(13),$arr_server[$max_key[1]]));
	$arr_url = explode('$',end($array));
	if($arr_url[1]){
		return array($max_key[1],$max_key[0],$arr_url[0],$arr_url[1]);
	}else{
		return array($max_key[1],$max_key[0],'第'.$max_key[0].'集',$arr_url[0]);
	}
}
/*---------------------------------------标签解析函数开始------------------------------------------------------------------*/
//路径参数处理函数
function ff_param_url(){
	$where = array();
	$where['sid'] = intval($_REQUEST['sid']);
	$where['id'] = intval($_REQUEST['id']);
	$where['year'] = intval($_REQUEST['year']);
	$where['language'] = htmlspecialchars(urldecode(trim($_REQUEST['language'])));
	$where['area'] = htmlspecialchars(urldecode(trim($_REQUEST['area'])));
	$where['letter'] = htmlspecialchars($_REQUEST['letter']);
	$where['actor'] = htmlspecialchars(urldecode(trim($_REQUEST['actor'])));
	$where['director'] = htmlspecialchars(urldecode(trim($_REQUEST['director'])));
	$where['wd'] = htmlspecialchars(urldecode(trim($_REQUEST['wd'])));
	//
	$where['mcid'] = intval($_REQUEST['mcid']);
	$where['limit'] = !empty($_GET['limit']) ? intval($_GET['limit']) : 10;
	$where['page'] = !empty($_GET['p']) ? intval($_GET['p']) : 1;
	$where['order'] = ff_order_by($_GET['order']);
	return $where;
}
//分页跳转参数处理
function ff_param_jump($where){
	if($where['sid']){
		$jumpurl['sid'] = $where['sid'];
	}
	if($where['id']){
		$jumpurl['id'] = $where['id'];
	}
	if($where['year']){
		$jumpurl['year'] = $where['year'];
	}		
	if($where['language']){
		$jumpurl['language'] = urlencode($where['language']);
	}
	if($where['area']){
		$jumpurl['area'] = urlencode($where['area']);
	}
	if($where['letter']){
		$jumpurl['letter'] = $where['letter'];
	}	
	if($where['actor']){
		$jumpurl['actor'] = urlencode($where['actor']);
	}
	if($where['director']){
		$jumpurl['director'] = urlencode($where['director']);
	}
	if($where['wd']){
		$jumpurl['wd'] = urlencode($where['wd']);
	}
	if($where['lz']){
		$jumpurl['lz'] = $where['lz'];
	}
	if($where['mcid']) {
		$jumpurl['mcid'] = $where['mcid']; 
	}
	if($where['order'] != 'addtime' && $where['order']){
		$jumpurl['order'] = $where['order'];
	}
	$jumpurl['p'] = '';
	return $jumpurl;
}
//返回安全的orderby
function ff_order_by($order = 'addtime'){
	if(empty($order)){
		return 'addtime';
	}
	$array = array();
	$array['addtime'] = 'addtime';
	$array['id'] = 'id';
	$array['hits'] = 'hits';
	$array['hits_month'] = 'hits_month';
	$array['hits_week'] = 'hits_week';
	$array['stars'] = 'stars';
	$array['up'] = 'up';
	$array['down'] = 'down';
	$array['gold'] = 'gold';
	$array['golder'] = 'golder';
	$array['year'] = 'year';
	$array['letter'] = 'letter';
	$array['filmtime'] = 'filmtime';
	return $array[trim($order)];
}
//生成参数列表,以数组形式返回
function ff_param_lable($tag = ''){
	$param = array();
	$array = explode(';',str_replace('num:','limit:',$tag));
	foreach ($array as $v){
		list($key,$val) = explode(':',trim($v));
		$param[trim($key)] = trim($val);
	}
	return $param;
}
/******************************************
* @处理影视标签函数
* @以字符串方式传入,通过ff_param_lable函数解析为以下变量
* name:vod 必须(vod/news/special/guestbook/common/user)
* ids:调用指定ID的一个或多个数据,如 1,2,3
* cid:数据所在分类,可调出一个或多个分类数据,如 1,2,3 默认值为全部,在当前分类为:'.$cid.'
* field:调用影视类的指定字段,如(id,title,actor) 默认全部
* limit:数据条数,默认值为10,可以指定从第几条开始,如3,8(表示共调用8条,从第3条开始)
* order:推荐方式(id/addtime/hits/year/up/down) (desc/asc/rand())
* wd:'关键字' 用于调用自定义关键字(搜索/标签)结果
* serial:调用连载信息(all/数字) 全部连载值为all 其它数字为大于该数字的连载影片
* time: 指定上传时间内,如(1/7/30) 分别表示(当天/本周/本月)------未做好
* stars:推荐星级数,可调出一个或多个星级数据,如 1,2,3 默认值为全部
* hits:大于指定人气值的数据(如:888)或某段之间的(如:888,999)
* up:大于指定支持值的数据(如:888)或某段之间的(如:888,999)
* down:大于指定反对值的数据(如:888)或某段之间的(如:888,999)
* gold:大于指定评分平均值的数据(如:6)或某段之间的(如:1,8)/范围:0-10
* golder:大于指定评分人的数据(如:888)或某段之间的(如:888,999)
*/
function ff_mysql_vod($tag){
	$search = array();$where = array();
	$tag = ff_param_lable($tag);
	$field = !empty($tag['field']) ? $tag['field'] : '*';
	$limit = !empty($tag['limit']) ? $tag['limit'] : '10';
	$order = !empty($tag['order']) ? $tag['order'] : 'vod_addtime';
	//优先从缓存调用
	if(C('data_cache_vodforeach') && C('currentpage') < 2 ){
		$data_cache_name = md5(C('data_cache_foreach').implode(',',$tag));
		$data_cache_content = S($data_cache_name);
		if($data_cache_content){
			return $data_cache_content;
		}
	}
	//根据参数生成查询条件
	$where['vod_status'] = array('eq',1);	
	if ($tag['ids']) {
		$where['vod_id'] = array('in',$tag['ids']);
	}	
	if ($tag['cid']) {
		$cids = explode(',',trim($tag['cid']));
		if (count($cids)>1) {
			$where['vod_cid'] = array('in',getlistarr_tag($cids));
		}else{
			$where['vod_cid'] = getlistsqlin($tag['cid']);
		}
	}
	if ($tag['weishi']) {
		$where['vod_diantai'] = array('neq','');
	}
	if ($tag['day']) {
		$where['vod_addtime'] = array('gt',getxtime($tag['day']));
	}
	if ($tag['filmtime']) {
		if($tag['filmtime']==1){
		$where['vod_filmtime'] = array('elt', time());
		//正在热映的视频desc
		}
		else{
		$where['vod_filmtime'] = array('gt', time());
		//即将上映的视频asc
		}
	}	
	if ($tag['stars']) {
		$where['vod_stars'] = array('in',$tag['stars']);
	}
	if ($tag['letter']) {
		$where['vod_letter'] = array('in',$tag['letter']);
	}
	if($tag['isfiml']){
		$where['vod_isfiml'] = array('eq',$tag['isfiml']);
	}	
	if ($tag['area']) {
		$where['vod_area'] = array('eq',''.$tag['area'].'');
	}
	if ($tag['language']) {
		$where['vod_language'] = array('eq',''.$tag['language'].'');
	}
	if($tag['lz'] == 1){
		$where['vod_continu'] = array('neq','0');
	}elseif($tag['lz'] == 2){
		$where['vod_continu'] = 0;
	}	
	if ($tag['year']) {
		$year = explode(',',$tag['year']);
		if (count($year) > 1) {
			$where['vod_year'] = array('between',$year[0].','.$year[1]);
		}else{
			$where['vod_year'] = array('eq',$tag['year']);
		}
	}
	if ($tag['hits']) {
		$hits = explode(',',$tag['hits']);
		if (count($hits) > 1) {
			$where['vod_hits'] = array('between',$hits[0].','.$hits[1]);
		}else{
			$where['vod_hits'] = array('gt',$hits[0]);
		}
	}
	if ($tag['up']) {
		$up = explode(',',$tag['up']);
		if (count($up)>1) {
			$where['vod_up'] = array('between',$up[0].','.$up[1]);
		}else{
			$where['vod_up'] = array('gt',$up[0]);
		}
	}
	if ($tag['down']) {
		$down = explode(',',$tag['down']);
		if (count($down)>1) {
			$where['vod_down'] = array('between',$down[0].','.$down[1]);
		}else{
			$where['vod_down'] = array('gt',$down[0]);
		}
	}
	if ($tag['gold']) {
		$gold = explode(',',$tag['gold']);
		if (count($gold)>1) {
			$where['vod_gold'] = array('between',$gold[0].','.$gold[1]);
		}else{
			$where['vod_gold'] = array('gt',$gold[0]);
		}
	}
	if ($tag['golder']) {
		$golder = explode(',',$tag['golder']);
		if (count($golder)>1) {
			$where['vod_golder'] = array('between',$golder[0].','.$golder[1]);
		}else{
			$where['vod_golder'] = array('gt',$golder[0]);
		}
	}
	if ($tag['name']) {
		$where['vod_name'] = array('like','%'.$tag['name'].'%');
	}
	if ($tag['title']) {
		$where['vod_title'] = array('like','%'.$tag['title'].'%');
	}
	if ($tag['actor']) {
		$where['vod_actor'] = array('like','%'.$tag['actor'].'%');
	}
	if ($tag['director']) {
		$where['vod_director'] = array('like','%'.$tag['director'].'%');
	}
	if ($tag['play']) {
		$where['vod_play'] = array('eq',$tag['play']);
	}
	if ($tag['inputer']) {
		$where['vod_inputer'] = array('eq',$tag['inputer']);
	}	
	if ($tag['wd']) {
		$search['vod_name'] = array('like','%'.$tag['wd'].'%');
		$search['vod_title'] = array('like','%'.$tag['wd'].'%');
		$search['vod_actor'] = array('like','%'.$tag['wd'].'%');
		$search['vod_director'] = array('like','%'.$tag['wd'].'%');
		$search['_logic'] = 'or';
		$where['_complex'] = $search;		
	}
	if ($tag['yanyuan']) {
		$search['vod_actor'] = array('like','%'.$tag['yanyuan'].'%');
		$search['vod_director'] = array('like','%'.$tag['yanyuan'].'%');
		$search['_logic'] = 'or';
		$where['_complex'] = $search;		
	}
	//查询数据开始
	if($tag['tag']){//视图模型查询
		$where['tag_sid'] = 1;
		$where['tag_name'] = $tag['tag'];
		$rs = D('TagView');
	}elseif($tag['mcid']){
		$mcid=explode(',',$tag['mcid']);
		if(count($mcid)>1){
			foreach($mcid as $akey =>$avalue){
				$mcidarr[]=array('eq',$avalue);
			}
			$mcidarr[]='or';
			$where['mcid_mid']=$mcidarr;
			$group= "vod_id";
		}else{
		$where['mcid_mid'] = array('eq',$tag['mcid']);
		}
		$rs = D('McidView');	
		}
	elseif($tag['weekday']){
		$weekday=explode(',',$tag['weekday']);
		if(count($weekday)>1){
			foreach($weekday as $akey =>$avalue){
				$weekdayarr[]=array('eq',$avalue);
			}
			$weekdayarr[]='or';
			$where['weekday_cid']=$weekdayarr;
			$group= "vod_id";
		}else{
		$where['weekday_cid'] = array('eq',$tag['weekday']);
		}
		$rs = D('WeekdayView');	
		}elseif($tag['actor']){
        $actor=explode(',',$tag['actor']);
		if(count($actor)>1){
			foreach($actor as $akey =>$avalue){
				if(!empty($avalue)){
				$actorarr[]=array('eq',$avalue);
				}
			}
			$actorarr[]='or';
			$where['actors_name']=$actorarr;
			$where['actors_type']=1;
			$group= "vod_id"; 
		}else{
			$where['actors_name'] = array('eq',$tag['actor']);
			$where['actors_type']=1;
		}
		$rs = D('ActorsView');	
		}
	elseif ($tag['diantai']) {
		$diantai=array_filter(explode(',',$tag['diantai']));
	    if(count($diantai)>1){
			foreach($diantai as $akey =>$avalue){
				$diantaiarr[]=array('eq',$avalue);
			}
			$diantaiarr[]='or';
			$where['vodtv_name']=$diantaiarr;
			$group= "vod_id"; 
		}else{
		$where['vodtv_name'] = array('eq',$tag['diantai']);
		}
		$rs = D('VodtvView');
	  }	
	elseif($tag['director']){
		$director=array_filter(explode(',',$tag['director']));
		if(count($director)>1){
			foreach($director as $dkey =>$dvalue){
				if(!empty($dvalue)){
				$directorarr[]=array('eq',$dvalue);
				}
			}
			$directorarr[]='or';
			$where['actors_name']= $directorarr;
			$where['actors_type']=2;
			$group= "vod_id"; 
		}else{
			$where['actors_name'] = array('eq',$tag['director']);
			$where['actors_type']=2;
			}	
		$rs = D('ActorsView');	
		}
		else{
		$rs = M('Vod');
	}
	if($tag['page']){
		//组合分页信息
		$count = $rs->where($where)->count('vod_id');if(!$count){return false;}
		$totalpages = ceil($count/$limit);
		$currentpage = get_maxpage(C('currentpage'),$totalpages);
		//生成分页列表
		//$pageurl = ff_list_url('vod',C('jumpurl'),9999);
		$pageurl = urldecode(C('jumpurl'));
		$pages = '共'.$count.'部影片&nbsp;当前:'.$currentpage.'/'.$totalpages.'页&nbsp;'.getpage($currentpage,$totalpages,C('home_pagenum'),$pageurl,'pagego(\''.$pageurl.'\','.$totalpages.')');
		$pagestop = '<span>'.$currentpage.'/'.$totalpages.'</span>'.getpagetop($currentpage,$totalpages,C('home_pagenum'),$pageurl,'pagego(\''.$pageurl.'\','.$totalpages.')');	
		//数据列表
		$list = $rs->field($field)->where($where)->order($order)->limit($limit)->page($currentpage)->select();
		$list[0]['count'] = count($list);
		$list[0]['page'] = $pages;
		$list[0]['pagetop'] = $pagestop;
	}else{
		$list = $rs->field($field)->where($where)->order($order)->limit($limit)->select();
	}
	//dump($rs->getLastSql());
	//循环赋值
	foreach($list as $key=>$val){
		$list[$key]['list_id'] = $list[$key]['vod_cid'];
		$list[$key]['list_name'] = getlistname($list[$key]['list_id'],'list_name');
		$list[$key]['list_url'] = getlistname($list[$key]['list_id'],'list_url');
		$list[$key]['vod_readurl'] = ff_data_url('vod',$list[$key]['vod_id'],$list[$key]['vod_cid'],$list[$key]['vod_name'],1,$list[$key]['vod_jumpurl']);
		$list[$key]['vod_playurl'] = ff_play_url($list[$key]['vod_id'],0,1,$list[$key]['vod_cid'],$list[$key]['vod_name']);
		$list[$key]['vod_picurl'] = ff_img_url($list[$key]['vod_pic'],$list[$key]['vod_content']);
		$list[$key]['vod_picurl_small'] = ff_img_url_small($list[$key]['vod_pic'],$list[$key]['vod_content']);		
	}
	//是否写入数据缓存
	if(C('data_cache_vodforeach') && C('currentpage') < 2 ){
		S($data_cache_name,$list,intval(C('data_cache_vodforeach')));
	}
	return $list;
}
/*数据调用-新闻资讯标签*/
function ff_mysql_news($tag){
	$search = array();$where = array();
	$tag = ff_param_lable($tag);
	$field = !empty($tag['field']) ? $tag['field'] : '*';
	$limit = !empty($tag['limit']) ? $tag['limit'] : '10';
	$order = !empty($tag['order']) ? $tag['order'] : 'news_addtime';
	//优先从缓存调用
	if(C('data_cache_newsforeach') && C('currentpage') < 2 ){
		$data_cache_name = md5(C('data_cache_foreach').implode(',',$tag));
		$data_cache_content = S($data_cache_name);
		if($data_cache_content){
			return $data_cache_content;
		}
	}	
	//根据参数生成查询条件
	$where['news_status'] = array('eq',1);	
	if ($tag['ids']) {
		$where['news_id'] = array('in',$tag['ids']);
	}
	if ($tag['day']) {
		$where['news_addtime'] = array('gt',getxtime($tag['day']));
	}	
	if ($tag['hits']) {
		$hits = explode(',',$tag['hits']);
		if (count($hits) > 1) {
			$where['news_hits'] = array('between',$hits[0].','.$hits[1]);
		}else{
			$where['news_hits'] = array('gt',$hits[0]);
		}
	}		
	if ($tag['cid']) {
		$cids = explode(',',trim($tag['cid']));
		if (count($cids)>1) {
			$where['news_cid'] = array('in',getlistarr_tag($cids));
		}else{
			$where['news_cid'] = getlistsqlin($tag['cid']);
		}
	}
	if ($tag['stars']) {
		$where['news_stars'] = array('in',$tag['stars']);
	}
	if ($tag['letter']) {
		$where['news_letter'] = array('in',$tag['letter']);
	}
	if ($tag['name']) {
		$where['news_name'] = array('like','%'.$tag['name'].'%');
	}	
	if ($tag['title']) {
		$where['news_title'] = array('like','%'.$tag['title'].'%');
	}			
	if ($tag['wd']) {
		$search['news_name'] = array('like','%'.$tag['wd'].'%');
		$search['news_inputer'] = array('like','%'.$tag['wd'].'%');
		$search['_logic'] = 'or';
		$where['_complex'] = $search;
	}
	if ($tag['up']) {
		$up = explode(',',$tag['up']);
		if (count($up)>1) {
			$where['news_up'] = array('between',$up[0].','.$up[1]);
		}else{
			$where['news_up'] = array('gt',$up[0]);
		}
	}
	if ($tag['down']) {
		$down = explode(',',$tag['down']);
		if (count($down)>1) {
			$where['news_down'] = array('between',$down[0].','.$down[1]);
		}else{
			$where['news_down'] = array('gt',$down[0]);
		}
	}
	if ($tag['gold']) {
		$gold = explode(',',$tag['gold']);
		if (count($gold)>1) {
			$where['news_gold'] = array('between',$gold[0].','.$gold[1]);
		}else{
			$where['news_gold'] = array('gt',$gold[0]);
		}
	}
	if ($tag['golder']) {
		$golder = explode(',',$tag['golder']);
		if (count($golder)>1) {
			$where['news_golder'] = array('between',$golder[0].','.$golder[1]);
		}else{
			$where['news_golder'] = array('gt',$golder[0]);
		}
	}
	//查询数据开始
	if($tag['tag']){//视图模型查询
		$where['tag_sid'] = 2;
		$where['tag_name'] = $tag['tag'];
		$rs = D('TagnewsView');
	}else{
		$rs = M('News');
	}
	if($tag['page']){
		//组合分页信息
		$count = $rs->where($where)->count('news_id');if(!$count){return false;}
		$totalpages = ceil($count/$limit);
		$currentpage = get_maxpage(C('currentpage'),$totalpages);
		//生成分页列表
		$pageurl = urldecode(C('jumpurl'));
		$pages = '共'.$count.'篇资讯&nbsp;当前:'.$currentpage.'/'.$totalpages.'页&nbsp;'.getpage($currentpage,$totalpages,C('home_pagenum'),$pageurl,'pagego(\''.$pageurl.'\','.$totalpages.')');
		//数据列表
		$list = $rs->field($field)->where($where)->order($order)->limit($limit)->page($currentpage)->select();
		$list[0]['count'] = count($list);
		$list[0]['page'] = $pages;						
	}else{
		$list = $rs->field($field)->where($where)->order($order)->limit($limit)->select();
	}
	//循环赋值
	foreach($list as $key=>$val){
		$list[$key]['list_id'] = $list[$key]['news_cid'];
		$list[$key]['list_name'] = getlistname($list[$key]['list_id'],'list_name');
		$list[$key]['list_url'] = getlistname($list[$key]['list_id'],'list_url');
		$list[$key]['news_readurl'] = ff_data_url('news',$list[$key]['news_id'],$list[$key]['news_cid'],$list[$key]['news_name'],1,$list[$key]['news_jumpurl']);
		$list[$key]['news_picurl'] = ff_img_url($list[$key]['news_pic'],$list[$key]['news_content']);
		$list[$key]['news_picurl_small'] = ff_img_url_small($list[$key]['news_pic'],$list[$key]['news_content']);		
	}
	//是否写入数据缓存
	if(C('data_cache_newsforeach') && C('currentpage') < 2 ){
		S($data_cache_name,$list,intval(C('data_cache_newsforeach')));
	}	
	return $list;
}
/*数据调用-专题循环标签*/
function ff_mysql_special($tag){
	$search = array();$where = array();
	$tag = ff_param_lable($tag);
	$field = !empty($tag['field']) ? $tag['field'] : '*';
	$limit = !empty($tag['limit']) ? $tag['limit'] : '10';
	$order = !empty($tag['order']) ? $tag['order'] : 'special_addtime';
	//优先从缓存调用
	if(C('data_cache_specialforeach') && C('currentpage') < 2 ){
		$data_cache_name = md5(C('data_cache_foreach').implode(',',$tag));
		$data_cache_content = S($data_cache_name);
		if($data_cache_content){
			return $data_cache_content;
		}
	}	
	//根据参数生成查询条件
	$where['special_status'] = array('eq',1);	
	if ($tag['ids']) {
		$where['special_id'] = array('in',$tag['ids']);
	}
	if ($tag['hits']) {
		$hits = explode(',',$tag['hits']);
		if (count($hits) > 1) {
			$where['special_hits'] = array('between',$hits[0].','.$hits[1]);
		}else{
			$where['special_hits'] = array('gt',$hits[0]);
		}
	}
	if ($tag['name']) {
		$where['special_name'] = array('like','%'.$tag['name'].'%');
	}
	//查询数据开始
	$rs = M('Special');
	if($tag['page']){
		//组合分页信息
		$count = $rs->where($where)->count('special_id');if(!$count){return false;}
		$totalpages = ceil($count/$limit);
		$currentpage = get_maxpage(C('currentpage'),$totalpages);
		//生成分页列表
		$pageurl = urldecode(C('jumpurl'));
		$pages = '共'.$count.'篇专题&nbsp;当前:'.$currentpage.'/'.$totalpages.'页&nbsp;'.getpage($currentpage,$totalpages,C('home_pagenum'),$pageurl,'pagego(\''.$pageurl.'\','.$totalpages.')');
		//数据列表
		$list = $rs->field($field)->where($where)->order($order)->limit($limit)->page($currentpage)->select();
		$list[0]['count'] = count($list);
		$list[0]['page'] = $pages;						
	}else{
		$list = $rs->field($field)->where($where)->order($order)->limit($limit)->select();
	}
	//dump($rs->getLastSql());
	//循环赋值
	foreach($list as $key=>$val){
		$list[$key]['special_readurl'] = ff_data_url('special',$list[$key]['special_id'],0,$list[$key]['special_name'],1,'');
		$list[$key]['special_logo'] = ff_img_url($list[$key]['special_logo'],$list[$key]['special_content']);
		$list[$key]['special_banner'] = ff_img_url_small($list[$key]['special_banner'],$list[$key]['special_content']);		
	}
	//是否写入数据缓存
	if(C('data_cache_specialforeach') && C('currentpage') < 2 ){
		S($data_cache_name,$list,intval(C('data_cache_specialforeach')));
	}	
	return $list;
}





/*---------------------------------------ThinkPhp扩展函数库开始------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>*/
//输出安全的html
function h($text, $tags = null){
	$text	=	trim($text);
	//完全过滤注释
	$text	=	preg_replace('/<!--?.*-->/','',$text);
	//完全过滤动态代码
	$text	=	preg_replace('/<\?|\?'.'>/','',$text);
	//完全过滤js
	$text	=	preg_replace('/<script?.*\/script>/','',$text);

	$text	=	str_replace('[','&#091;',$text);
	$text	=	str_replace(']','&#093;',$text);
	$text	=	str_replace('|','&#124;',$text);
	//过滤换行符
	$text	=	preg_replace('/\r?\n/','',$text);
	//br
	$text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
	$text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
	//过滤危险的属性，如：过滤on事件lang js
	while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	if(empty($tags)) {
		$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
	}
	//允许的HTML标签
	$text	=	preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
	//过滤多余html
	$text	=	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
	//过滤合法的html标签
	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
	}
	//转换引号
	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
	}
	//过滤错误的单个引号
	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
	}
	//转换其它所有不合法的 < >
	$text	=	str_replace('<','&lt;',$text);
	$text	=	str_replace('>','&gt;',$text);
	$text	=	str_replace('"','&quot;',$text);
	 //反转换
	$text	=	str_replace('[','<',$text);
	$text	=	str_replace(']','>',$text);
	$text	=	str_replace('|','"',$text);
	//过滤多余空格
	$text	=	str_replace('  ',' ',$text);
	return $text;
}
// 随机生成一组字符串
function build_count_rand ($number,$length=4,$mode=1) {
    if($mode==1 && $length<strlen($number) ) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand   =  array();
    for($i=0; $i<$number; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $unqiue = array_unique($rand);
    if(count($unqiue)==count($rand)) {
        return $rand;
    }
    $count   = count($rand)-count($unqiue);
    for($i=0; $i<$count*3; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $rand = array_slice(array_unique ($rand),0,$number);
    return $rand;
}
//XSS漏洞过滤
function remove_xss($val) {
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }
   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);
   $found = true; // keep replacing as long as the previous round replaced something
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
         if ($val_before == $val) {
            // no replacements were made, so exit the loop
            $found = false;
         }
      }
   }
   return $val;
}
/*** 把返回的数据集转换成Tree
 +----------------------------------------------------------
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array 
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0)
{
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}
/**----------------------------------------------------------
 * 在数据列表中搜索
 +----------------------------------------------------------
 * @param array $list 数据列表
 * @param mixed $condition 查询条件
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
function list_search($list,$condition) {
    if(is_string($condition))
        parse_str($condition,$condition);
    // 返回的结果集合
    $resultSet = array();
    foreach ($list as $key=>$data){
        $find   =   false;
        foreach ($condition as $field=>$value){
            if(isset($data[$field])) {
                if(0 === strpos($value,'/')) {
                    $find   =   preg_match($value,$data[$field]);
                }elseif($data[$field]==$value){
                    $find = true;
                }
            }
        }
        if($find)
            $resultSet[]     =   &$list[$key];
    }
    return $resultSet;
}
/**
 +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function byte_format($size, $dec=2)
{
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		 $size /= 1024;
		   $pos++;
	}
	return round($size,$dec)." ".$a[$pos];
}
/**
 +----------------------------------------------------------
 * 对查询结果集进行排序
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

?>