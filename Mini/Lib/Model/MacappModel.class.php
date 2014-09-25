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
}