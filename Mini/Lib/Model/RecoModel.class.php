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

     //优衣库二期
    //页面风格与数据库风格id对应关系
    public function pageToDataStyle($key){
        $arr = array(8,13,9,10,12,5,6,7,11,14);
        return $arr[$key-1];
    }
    public function pageToDataStyle2($key){
        $arr = array('d_1','d_2','e_1','a_2','c_2','a_1','b_1','c_1','b_2','e_2');
        return $arr[$key-1];
    }
    public function getBeubeu($where){
        $where['approve_status'] = 0;
        $beubeu_suits = M('BeubeuSuits');
        $beubeu_suits_list = $beubeu_suits->cache(true)->field('suitID,suitGenderID,suitImageUrl')->where($where)->order('uptime desc')->select();
        //$beubeu_detail = M('BeubeuSuitsGoodsdetail');
        foreach($beubeu_suits_list as $k=>$v){
            switch($v['suitGenderID']){
                case 1 :
                    $sex = 15474;
                    break;
                case 2 :
                    $sex = 15478;
                    break;
                case 3 :
                    $sex = 15583;
                    break;
                case 4 :
                    $sex = 15581;
                    break;
            }
            $beubeu_suits_list[$k]['sex'] = $sex;
        /*$detailResult = $beubeu_detail->cache(true)->field('item_bn')->where(array('suitID'=>$v['suitID']))->select();
            if(!empty($detailResult)){
                foreach($detailResult as $k2=>$v2){
                    $detailResult[$k2]['sex'] = $sex;
                }
                $beubeu_suits_list[$k]['detail'] = json_encode($detailResult);
            }else{
                $beubeu_suits_list[$k]['detail'] = 0;
            }*/
         }
        return $beubeu_suits_list;
    }

    //取出自定义分类
    public function getCusData($where){
     return M('Customcate')->field('id,name')->where($where)->select();
    }
    public function getCateList($isud){
        $customcate = M('Customcate');
        $custom = $customcate->cache(true)->field('id,name')->where(array('gtype'=>array('neq','5'),'isud'=>$isud))->group('name')->select();
        foreach($custom as $k=>$v){
            $idlist = $customcate->cache(true)->field('id')->where(array('name'=>$v['name'],'isud'=>$isud))->select();
            $idstr = '';
            foreach($idlist as $k1=>$v1){
                if($v1){
                    $idstr.=$v1['id'].'_';
                }
            }
            $idstr = rtrim($idstr,'_');
            $ucuslist[] = array('id'=>$idstr,'name'=>$v['name']);
         }
        return $ucuslist;
    }
}
