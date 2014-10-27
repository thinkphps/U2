<?php
class MacappModel extends Model{
    public function CkeckApp($uname,$upass){
        if($uname==C('IOSNMAE') && $upass==C('IOSPASS')){
            $flag = 1;
        }else{
            $flag = 0;
        }
        return $flag;
    }
    public function getCollNumm_iid($uq){
        if(!empty($uq)){
            $goods = M('Goods');
            $is_g = is_int(strpos($uq,'_'));
            if(!$is_g){
                $uq = $uq.'_';
            }
            $arr_uq = explode('_',$uq);
            foreach($arr_uq as $k=>$v){
                if($v){
                    $sql = "select `num_iid` from `u_goods` where left(item_bn,8)='".$v."' order by num desc";
                    $result = $goods->query($sql);
                    $arr[] = $result[0]['num_iid'];
                }
            }
            return $arr;
        }
    }
public function getsstyle($sid){
    $sql = "select s.ID,s.description,gs.goodnum from u_settings_suit_style as s inner join u_settings_gender_style as gs on gs.styleID=s.ID where gs.genderID=".$sid;
    $list = M('')->query($sql);
    return $list;
}
public function getUqNum($item_bn){
    if(!empty($item_bn)){
        $goods = M('Goods');
        $sql = "select num_iid,title,approve_status,IF(num>0 and approve_status='onsale',detail_url,'') as detail_url,num from u_beubeu_goods where left(item_bn,8)='".$item_bn."' order by num desc";
        $result = $goods->query($sql);
        if(!empty($result[0])){
            $returnArr = array('code'=>1,'data'=>$result[0]);
        }else{
            $returnArr = array('code'=>0,'msg'=>'没有数据');
        }
    }else{
            $returnArr = array('code'=>0,'msg'=>'参数错误');
    }
    return $returnArr;
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
            $idlist = $sell->field('ID as id,goodnum')->where($arr2)->select();
            $idstr = '';$sum = 0;
            foreach($idlist as $k1=>$v1){
                if($v1){
                    $idstr.=$v1['id'].'_';
                    $sum+=$v1['goodnum'];
                }
            }
            $idstr = rtrim($idstr,'_');
            $ucuslist[] = array('id'=>$idstr,'name'=>$v['name'],'sum'=>$sum);
        }
        unset($result);
        return $ucuslist;
    }
public function SqlCount(&$sql,$goodtag,$page_num){
    $gcount = $goodtag->query($sql);
    $count = ceil($gcount[0]['co']/$page_num);
    return $count;
}
    public function getBeubeu($where,$page,$page_num,$start,$unihost){
        $where['suitImageUrl'] = array('neq','');
        //$beubeu_suits = M('BeubeuSuits');
        $beubeu_suits = M('SuitOrder');
        $count = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->count();
        $num = ceil($count/$page_num);
        if($page>$num){
            $page = 1;
            $start = 0;
        }
        print_r($where);
        if(isset($where['suitStyleID']) && !empty($where['suitStyleID'])){
            $beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->order('id desc')->limit($start.','.$page_num)->select();
        }else{
            $beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->order('all_order asc')->limit($start.','.$page_num)->select();
        }
        foreach($beubeu_suits_list as $k=>$v){
            $beubeu_suits_list[$k]['suitImageUrl'] = $unihost.$v['suitImageUrl'];
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
        $arr['count'] = $num;
        return $arr;
    }
    public function getBenebnColl($where){
        $beubeu_coll = M('BeubeuCollection');
        $result = $beubeu_coll->field('id,gender,suitID,pic_head,pic_body,pic_shoes,pic_clothes')->where($where)->order('id desc')->select();
        $str = '';
        foreach($result as $k=>$v){
            $str.=$v['id'].',';
        }
        $str = rtrim($str,',');
        $sql = "select t1.bcid,bg.`num_iid`,bg.`approve_status`,bg.`title`,bg.`num`,bg.`pic_url`,IF(bg.num>0 and bg.approve_status='onsale',bg.detail_url,'') as detail_url,li.loveid,li.buyid from (select `bcid`,`num_iid` from `u_beubeu_coll_goods` as bc where bc.bcid in ({$str})) as t1 inner join `u_beubeu_goods` as bg on bg.num_iid=t1.num_iid left join (SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
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
            $result[$k1]['detail'] = $detailArr;
        }
        return $result;
    }
}