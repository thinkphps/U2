<?php
class RecoModel extends Model{
	public function getrec($tem){
	$goodtag = M('Goodtag');
	$rem = M('Recommend');
	$tm = $rem->field('tm')->find();
	if($tem<=$tm['tm']){
	    $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//上装
	    $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//上装	    		
	}else{
	    $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'1'))->select();//上装
	    $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'2'))->select();//上装	
	    }
    foreach($reulist as $k=>$v){
    $ccateid = 	$goodtag->field('ccateid')->where(array('good_id'=>$v['id']))->find();
	$reulist[$k]['ccateid'] = $ccateid['ccateid'];
    }
    foreach($redlist as $k=>$v){
    $ccateid = 	$goodtag->field('ccateid')->where(array('good_id'=>$v['id']))->find();
	$redlist[$k]['ccateid'] = $ccateid['ccateid'];
    }
    $arr[] = $reulist;
	$arr[] = $redlist;
	return $arr;		
	}
   
   //获取类别所对应的风格和场合
   public function getfc($parent_id,$type,$isshow){
   	      $tag = M('Tag');
		  $wclist = $tag->cache(true)->field('id,name')->where(array('parent_id'=>$parent_id,'type'=>$type,'isshow'=>$isshow))->select();//女性场合
          foreach($wclist as $k=>$v){
   	      $wclist[$k]['c'] = $k+1;
          }
          return $wclist;
   }
}
