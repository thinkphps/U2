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
    public function getBeubeu2($where,$page,$page_num,$start){
        $where['approve_status'] = 0;
        $beubeu_suits = M('BeubeuSuits');
        $where['tag'] = array('neq',0);
        $count = $beubeu_suits->distinct(true)->field('tag')->where($where)->select();
        $count = count($count);
        $num = ceil($count/$page_num);
        if($page>$num){
           $page = 1;
           $start = 0;
        }
        $sql_where = '';
        if(!empty($where['suitStyleID'])){
            $sql_where.= ' and bs.suitStyleID='.$where['suitStyleID'];
        }
        if(is_array($where['suitGenderID'])){
            $sql_where.= ' and bs.suitGenderID '.$where['suitGenderID'][1];
        }else if(!is_array($where['suitGenderID']) && !empty($where['suitGenderID'])){
            $sql_where.= ' and bs.suitGenderID='.$where['suitGenderID'];
        }
        $sql_where.= ' and bs.approve_status=0';
        $sql = "SELECT suitID,suitGenderID,suitImageUrl FROM `u_beubeu_suits` WHERE tag = (SELECT tag FROM `u_beubeu_suits` as bs WHERE 1 {$sql_where} group by bs.tag order by bs.tag desc limit {$start},{$page_num}) and tag!=0 limit 0,4";
        //$beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl,suitImageUrlHead,suitImageUrlBody,suitImageUrlShose,suitImageUrlMatch')->where($where)->order('suitID desc')->limit($start.','.$page_num)->select();
        $beubeu_suits_list = $beubeu_suits->query($sql);
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
         }
        $arr['page'] = $page+1;
        $arr['result'] = $beubeu_suits_list;
        $arr['count'] = $count;
        return $arr;
    }
    public function getBeubeu($where,$page,$page_num,$start){
        $where['suitImageUrl'] = array('exp','IS NOT NULL');
        //$beubeu_suits = M('BeubeuSuits');
        $beubeu_suits = M('SuitOrder');
        $count = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->count();
        $num = ceil($count/$page_num);
        if($page>$num){
            $page = 1;
            $start = 0;
        }
        if(isset($where['suitStyleID']) && !empty($where['suitStyleID'])){
        $beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->order('id desc')->limit($start.','.$page_num)->select();
        }else{
            $beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->order('all_order asc')->limit($start.','.$page_num)->select();
        }
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
        }
        $arr['page'] = $page+1;
        $arr['result'] = $beubeu_suits_list;
        $arr['count'] = $count;
        return $arr;
    }
    //取出自定义分类
    public function getCusData($where){
     return M('Customcate')->field('id,name')->where($where)->select();
    }
    public function getCusData2($where){
        $sell = M('Sellercats');
        $result = $sell->field('ID as id,shortName as name')->where($where)->group('shortName')->order('sort_order asc')->select();
        foreach($result as $k=>$v){
			if(!empty($where['gender'])){
            $arr2['gender'] = $where['gender'];
			}
            $arr2['selected'] = 1;
            $arr2['shortName'] = $v['name'];
            $arr2['isshow'] = 0;
            $idlist = $sell->field('ID as id')->where($arr2)->select();
            $idstr = '';
            foreach($idlist as $k1=>$v1){
                if($v1){
                    $idstr.=$v1['id'].'_';
                }
            }
            $idstr = rtrim($idstr,'_');
            $ucuslist[] = array('id'=>$idstr,'name'=>$v['name']);
        }
        unset($result);
        return $ucuslist;
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
    public function getCateList2($where){
        $customcate = M('Sellercats');
        $custom = $customcate->field('ID as id,shortName as name')->where($where)->group('shortName')->order('sort_order asc')->select();
        foreach($custom as $k=>$v){
            $idlist = $customcate->field('ID as id')->where(array('selected'=>1,'shortName'=>$v['name'],'isshow'=>0))->select();
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
  public function getBenebnColl($where,$page,$page_num,$start){
      $beubeu_coll = M('BeubeuCollection');
      $count = $beubeu_coll->field('id')->where($where)->count();
      $num = ceil($count/$page_num);
      if($page>$num){
          $page = 1;
          $start = 0;
      }
      $result = $beubeu_coll->field('id,gender,suitID,pic_head,pic_body,pic_shoes,pic_clothes')->where($where)->order('id desc')->limit($start.','.$page_num)->select();
      $str = '';
      foreach($result as $k=>$v){
      $str.=$v['id'].',';
      }
      $str = rtrim($str,',');
      //$sql = "select t1.bcid,bg.`num_iid`,bg.`approve_status`,bg.`title`,bg.`num`,bg.`pic_url`,IF(bg.num>0 and bg.approve_status='onsale',bg.detail_url,'') as detail_url from (select `bcid`,`num_iid` from `u_beubeu_coll_goods` as bc where bc.bcid in ({$str})) as t1 inner join `u_beubeu_goods` as bg on bg.num_iid=t1.num_iid";
      $sql = "select t1.bcid,t1.uq,bg.`num_iid`,bg.`approve_status`,bg.`title`,bg.`num`,bg.`pic_url`,IF(bg.num>0 and bg.approve_status='onsale',bg.detail_url,'') as detail_url,li.loveid,li.buyid from (select `bcid`,`num_iid`,`uq` from `u_beubeu_coll_goods` as bc where bc.bcid in ({$str})) as t1 inner join `u_beubeu_goods` as bg on bg.num_iid=t1.num_iid left join (SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid=".$where['uid']."
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid=".$where['uid']."
) bl group by bl.num_iid) as li on li.num_iid=bg.num_iid";
      $detail = $beubeu_coll->query($sql);
      foreach($result as $k1=>$v1){
          $detailArr = array();
		  $karr = array();$karr2 = array();
          foreach($detail as $k2=>$v2){
             if($v1['id']==$v2['bcid']){
                 if($v2['num']<=0 || $v2['approve_status']=='instock'){
                     $v2['title'] = '【已售罄】'.$v2['title'];
                 }
                 $orid = $this->collGoodsOrder($v2['title']);
                 if($orid!=-1){
                     $karr2[$orid] = $v2;
					 $karr[] = $orid;
                 }else{
                     array_push($detailArr,$v2);
                 }
                 //$detailArr[] = $v2;
             }
          }
          arsort($karr);      
          foreach($karr as $k3=>$v3){
			  if(empty($detailArr)){
                 $detailArr[] = $karr2[$v3];
			  }else{		  
			  array_unshift($detailArr,$karr2[$v3]);
			  }
         }
          foreach($detailArr as $kd=>$vd){
              $sql = "select `url` from `u_products` where `num_iid`=".$vd['num_iid']." and `cid`=right('".$vd['uq']."',2) limit 0,1";
              $pic = $beubeu_coll->query($sql);
              $detailArr[$kd]['pic_url'] = $pic[0]['url'];
          }
          $result[$k1]['detail'] = $detailArr;
      }
      $arr['page'] = $page+1;
      $arr['result'] = $result;
      return $arr;
  }

public function getUserInfo(){
    $uid = session("uniq_user_id");
    $user = M('User')->field('user_name,mobile,taobao_name,collflag,login_type')->where(array('id'=>$uid))->find();
    $collcount = M('BeubeuCollection')->field('id')->where(array('uid'=>$uid))->count();
    if($user['login_type']=='normal'){
         if(!empty($user['mobile'])){
             $uname = $user['mobile'];
         }
        if(!empty($user['user_name'])){
            $uname = $user['user_name'];
        }
        if(!empty($user['taobao_name'])){
            $uname = $user['taobao_name'];
        }
    }else{
        $uname = $user['user_name'];
    }
    $arr[] = $uname;
    $arr[] = $user['collflag'];
    $arr[] = $collcount;
    $arr[] = $user['taobao_name'];
    return $arr;
}
   public function collGoodsOrder($title){
       $arr = array('羽绒服','大衣','外套','卫衣','马甲','毛衣','针织衫','衬衫','衫','薄衫','茄克','家居服','套装（连身装）','T恤','背心','内衣','裙子','裤','帽子','坎肩围巾','包','袜子','鞋子','配饰','首饰','其他');
       $orid = -1;
    foreach($arr as $k=>$v){
       if(is_int(strpos($title,$v))){
           $orid = $k;
          break;
       }
    }
       return $orid;
   }
}