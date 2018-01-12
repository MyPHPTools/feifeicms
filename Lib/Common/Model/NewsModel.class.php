<?php 
namespace Common\Model;
use Think\Model\RelationModel;
class NewsModel extends RelationModel {
	private $news_id;
	// 自动验证
	protected $_validate=array(
	    array('news_cid','number','请选择分类！',1,'',3),
		array('news_cid','getlistson','请选择当前分类下面的子栏目！',1,'function',3),
		array('news_name','require','文章标题必须填写！',1,'',3),
	);
	// 自动完成
	protected $_auto=array(
		array('news_name','trim',3,'function'),
		array('news_remark','get_remark',3,'callback'),
		array('news_letter','a_letter',3,'callback'),
		array('news_addtime','a_addtime',3,'callback'),
		array('news_pic','news_pic',3,'callback'),
	);
	// 关联定义
	protected $_link = array(
		'Tag'=>array(
			'mapping_type' => HAS_MANY,
			'class_name'=> 'Tag',
			'mapping_name'=>'Tag',//数据对像映射名称
			'foreign_key' => 'tag_id',
			'parent_key' => 'news_id',
			'mapping_fields' => 'tag_id,tag_sid,tag_name',
			'condition' => 'tag_sid = 2',
			//'mapping_limit' => 5,
			//'mapping_order' => 'create_time desc',
		),
	);	
	// 自动添加简介
	public function get_remark(){
		if(empty($_POST['news_remark'])){
			return msubstr(trim($_POST['news_content']),0,100,'utf-8',false);
		}else{
			return trim($_POST['news_remark']);
		}
	}
	// 取首字母
	public function a_letter(){
		return ff_letter_first(trim($_POST['news_name']));
	}
	// 更新时间
	public function a_addtime(){
		if ($_POST['checktime']) {
			return time();
		}else{
			return strtotime($_POST['addtime']);
		}
	}
	//图片处理
	public function news_pic(){
		$img = D('Img');
		return $img->down_load(trim($_POST["news_pic"]));
	}			
}
?>