<?php
/*****************后台公用类库 继承全站公用类库*******************************/
namespace Common\Action;
use Common\Action\AllAction;
class BaseAction extends AllAction{
	//构造
    public function _initialize(){
	    parent::_initialize();
		//检查登录
		if (!$_SESSION[C('USER_AUTH_KEY')]) {
			$this->assign('jumpUrl',C('cms_admin').'?s=Admin-Login');
			$this->error('对不起，您还没有登录，请先登录！');
		}
		//检查权限 不需要验证操作的除外
		if (!in_array(strtolower(ACTION_NAME),explode(',',C('NOT_AUTH_ACTION')))) {
			// 检索当前模块是否需要认证
			$model_id = array_search(MODULE_NAME,explode(',',C('REQUIRE_AUTH_MODULE')));
			if (is_int($model_id)) {
				$usertype = explode(',',$_SESSION['admin_ok']);
				if (!$usertype[$model_id]) {
					$this->error('对不起，您没有管理该模块的权限，请联系超级管理员授权！');
				}
			}
		}
    }		
	//生成播放器列表
    public function ppvod_play(){
	    $this->assign('countplayer',count(C('PP_PLAYER')));
		$this->assign('playtree',C('play_player'));
    }	
	//生成前台分类缓存
    public function ppvod_list(){
		$rs = D("List");
		$where['list_status'] = array('eq',1);
		$list=$rs->where($where)->order('list_oid asc')->select();
		foreach($list as $key=>$val){
			if ($list[$key]['list_sid'] == 9){
				$list[$key]['list_url'] = $list[$key]['list_jumpurl'];
				$list[$key]['list_url_big'] = $list[$key]['list_jumpurl'];
			}else{
				$list[$key]['list_url'] = ff_list_url(getsidname($list[$key]['list_sid']),array('id'=>$list[$key]['list_id']),1);
				$list[$key]['list_url_big'] = ff_list_url(getsidname($list[$key]['list_sid']),array('id'=>$list[$key]['list_pid']),1);
				$list[$key]['list_name_big'] = getlistname($list[$key]['list_pid']);
				if($list[$key]['list_sid'] == 1){
					$list[$key]['list_limit'] = gettplnum('ff_mysql_vod\(\'(.*)\'\)',$list[$key]['list_skin']);
				}else{
					$list[$key]['list_limit'] = gettplnum('ff_mysql_news\(\'(.*)\'\)',$list[$key]['list_skin']);
				}
			}
		}
		//查询缓存小分类
		$condition = array(
			'list_pid' => 0,
			'list_sid' => 1
		);
		$tree = M('List')->where($condition)->field("list_id,list_name,list_oid")->order('list_oid asc')->select();
		foreach ($tree as $k=>$v){
			$tree[$k]['son'] = D('Mcat')->list_cat($v['list_id']);
			$tree[$k]['total'] = $tree[$k]['son'] == null ? 0 : count($tree[$k]['son']);

		}
		$mcat_mcid = M('Mcat')->order('m_cid asc')->select();
		foreach ($tree as $k=>$v){
			$mcat_mcid[$k]['son'] = $mcat_mcid[0]['m_cid'];
		}
	    F('_ppvod/mcat',$tree);
		F('_ppvod/mcid',$mcat_mcid);
		//$list[999]['special'] = get_tpl_limit('<bdlist(.*)limit="([0-9]+)"(.*)>','special_list');//缓存专题分页数据	
		F('_ppvod/list',$list);
		F('_ppvod/listtree',list_to_tree($list,'list_id','list_pid','son',0));
		
		$where['list_sid'] = array('EQ',1);
		$list = $rs->where($where)->order('list_oid asc')->select();
		F('_ppvod/listvod',list_to_tree($list,'list_id','list_pid','son',0));
		
		$where['list_sid']=array('EQ',2);
		$list=$rs->where($where)->order('list_oid asc')->select();
		F('_ppvod/listnews',list_to_tree($list,'list_id','list_pid','son',0));
    }	
}
?>