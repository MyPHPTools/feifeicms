<?php
namespace Common\Model;
use Think\Model\AdvModel;
class CmModel extends AdvModel {
	protected $_validate=array(
		//array('vcode','check_vcode','您填写的验证码不正确！',1,'callback'),
		array('cm_cid','number','您没有指定评论ID！',1),
		array('cm_sid','require','您没有指定评论模型！',1),
		array('cm_content','require','您没有填写评论内容！',1),
		//array('cm_content','check_reinput','您已经评论过了!',1,'callback'),
		//array('username','check_login','您输入的用户名或密码错误！',2,'callback'),
	);
	protected $_auto=array(
		array('cm_content','hh_content',1,'callback'),
		array('cm_uid','get_userid',1,'callback'),
		array('cm_sid','intval',1,'function'),
		array('cm_ip','get_client_ip',1,'function'),
		array('cm_addtime','time',3,'function'),
	);
	//检测验证码是否正确
	public function check_vcode(){
		if($_SESSION['verify'] != md5($_POST['vcode'])){
			return false;
		}
	}
	//检测指定时间内重复评论
	public function check_reinput(){
		$cookie = $_COOKIE['comment_'.intval($_POST['cm_sid']).'_'.intval($_POST['cm_cid'])];
		if(isset($cookie)){
			return false;
		}
	}
	//登陆验证
	public function check_login(){
		$userid = intval($_COOKIE['gx_userid']);
		if ($userid) {
			return true;
		}else{
			$rs = M('User');
			$userid = $rs->check_login();
			if ($userid) {
				C('ff_user_id',$userid);
				return true;
			}else{
				return false;
			}
		}
	}
	//自动填充
	public function get_userid(){
		return 1;
		$userid = intval($_COOKIE['ff_user_id']);
		if ($userid) {
			return $userid;
		}
		if (C('ff_user_id')){
			return C('ff_user_id');
		}
		return 1;
	}
	//过滤脏话	
	public function hh_content($str){
		$array = explode('|',C('user_replace'));
		return str_replace($array,'***',msubstr(remove_xss($str),0,200));
	}
}
?>