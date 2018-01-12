<?php 
namespace Common\Model;
use Think\Model\RelationModel;
class VodModel extends RelationModel {
	private $vod_id;
	//自动验证
	protected $_validate = array(
	    array('vod_cid','number','请选择分类！',1,'',3),
		array('vod_cid','getlistson','请选择当前子类栏目！',1,'function',3),
		array('vod_name','require','影片名称必须填写！',1,'',3),
	);
	//自动完成
	protected $_auto = array(
		array('vod_letter','vod_letter',3,'callback'),
		array('vod_gold','vod_gold',3,'callback'),
		array('vod_pic','vod_pic',3,'callback'),
		array('vod_addtime','vod_addtime',3,'callback'),
		array('vod_filmtime','vod_filmtime',3,'callback'),
		array('vod_year','vod_year',3,'callback'),
	);
	//关联定义
	protected $_link = array(
		'Tag'=>array(
			'mapping_type' => self::HAS_MANY,
			'class_name'=> 'Tag',
			'mapping_name'=>'Tag',//数据对像映射名称
			'foreign_key' => 'tag_id',
			'parent_key' => 'vod_id',
			'mapping_fields' => 'tag_id,tag_sid,tag_name',
			'condition' => 'tag_sid = 1',
			//'mapping_limit' => 5,
			//'mapping_order' => 'create_time desc',
		)
	);
	//字母处理
	public function vod_letter(){
		return ff_letter_first(trim($_POST["vod_name"]));
	}
	//图片处理
	public function vod_pic(){
		$img = D('Img');
		return $img->down_load(trim($_POST["vod_pic"]));
	}		
	//积分处理
	public function vod_gold(){
		if($_POST["vod_gold"] > 10){
			$_POST["vod_gold"] = 10;
		}	
		return 	$_POST["vod_gold"];
	}			
	//是否更新时间
	public function vod_addtime(){
		if ($_POST['checktime']) {
			return time();
		}else{
			return strtotime($_POST['vod_addtime']);
		}
	}
	//处理上映日期
	public function vod_filmtime(){
		if ($_POST['vod_filmtime']) {
			return strtotime($_POST['vod_filmtime'].' 0:0:0');
		}else{
			return '';
		}
	}
	//处理年代
	public function vod_year(){
		if ($_POST['vod_filmtime']) {
			return date('Y',strtotime($_POST['vod_filmtime'].' 0:0:0'));
		}else{
			return $_POST['vod_year'];
		}
	}		
}
?>