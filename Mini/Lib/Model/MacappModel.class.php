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
}