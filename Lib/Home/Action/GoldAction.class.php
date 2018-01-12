<?php
namespace Home\Action;
use Common\Action\HomeAction;
class GoldAction extends HomeAction{
  	public function vod(){
		$id = intval(I('get.id','','strip_tags,htmlspecialchars'));
		$type=trim(I('get.type','','strip_tags,htmlspecialchars'));
		if ($id < 1) {
			$data['data']="数据非法";
			$data['info']="-1";
			$data['status']="-1";	
			$this->ajaxReturn($data);
		}
		$this->show($id,$type,'vod');
    }
    public function news(){
		$id = intval(I('get.id','','strip_tags,htmlspecialchars'));
		$type=trim(I('get.type','','strip_tags,htmlspecialchars'));
		if ($id < 1) {
			$data['data']="数据非法";
			$data['info']="-1";
			$data['status']="-1";	
			$this->ajaxReturn($data);
		}
		$this->show($id,$type,'news');
    }
/*
alter table `ff_vod` add vod_gold_1 float not null default '0';
alter table `ff_vod` add vod_gold_2 float not null default '0';
alter table `ff_vod` add vod_gold_3 float not null default '0';
alter table `ff_vod` add vod_gold_4 float not null default '0';
alter table `ff_vod` add vod_gold_5 float not null default '0';
*/
	public function show($id,$type,$model='vod'){
		$rs = D(ucfirst($model));
		$array = $rs->field(''.$model.'_gold,'.$model.'_golder,'.$model.'_gold,'.$model.'_gold_1,'.$model.'_gold_2,'.$model.'_gold_3,'.$model.'_gold_4,'.$model.'_gold_5,'.$model.'_golder')->find($id);
		
		if ($array) {
			if($type){
				$cookie = $model.'-gold-'.$id;
				if(isset($_COOKIE[$cookie])){
					$data['data']="您已评分！";
			        $data['info']=0;
			        $data['status']=0;	
					$this->ajaxReturn($data);
				}
				$type = is_numeric($type)?$type:1;
				$array[$model.'_gold_'.$type] = $array[$model.'_gold_'.$type]+1;
				$array[$model.'_golder'] = $array[$model.'_gold_1']+$array[$model.'_gold_2']+$array[$model.'_gold_3']+$array[$model.'_gold_4']+$array[$model.'_gold_5'];
				$array[$model.'_gold'] = number_format(($array[$model.'_gold_1']*1+$array[$model.'_gold_2']*2+$array[$model.'_gold_3']*3+$array[$model.'_gold_4']*4+$array[$model.'_gold_5']*5)/$array[$model.'_golder']*2,1);
				$rs->where($model.'_id = '.$id)->save($array);
				setcookie($cookie,'t',time()+intval(C('user_second')));
			}else{
				$array= $array;
			}			
		}else{
			$array[$model.'_gold'] = 0.0;
			$array[$model.'_gold_1'] = 0;
			$array[$model.'_gold_2'] = 0;
			$array[$model.'_gold_3'] = 0;
			$array[$model.'_gold_4'] = 0;
			$array[$model.'_gold_5'] = 0;
			$array[$model.'_golder'] = 0;
		}
		$array['status']=1;	
		$array['info']="感谢您的参与，评分成功！";
		$array['data'] = $array;
		$this->ajaxReturn($array);
	}
}
?>