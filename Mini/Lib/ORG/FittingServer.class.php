<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 15-5-11
 * Time: 下午3:42
 * $D试衣间接口
 */
class FittingServer{
    public function GetCates($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $arr = array();
        $arr = $d3model->GetSellercats();
        return json_encode($arr);
    }
    public function GetCateGoods($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $page = trim(htmlspecialchars($parmas['page']));
        $page = $page>0?$page:1;
        $arr = $d3model->GetCateGoodData($page);
        return json_encode($arr);
    }
    public function GetGoods($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $page = trim(htmlspecialchars($parmas['page']));
        $modified = trim(htmlspecialchars($parmas['modified']));
        $page = $page>0?$page:1;
        $modified = $modified?$modified:'';
        if(!empty($modified)){
            if(!preg_match("/^\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$/s",$modified)){
                $login_arr = array('code'=>0,'msg'=>'时间格式不对');
                return json_encode($login_arr);
            }
        }
        $where['page'] = $page;
        $where['modified'] = $modified;
        $arr = $d3model->GetGoodsData($where);
        return json_encode($arr);
    }
    public function GetSku($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $page = trim(htmlspecialchars($parmas['page']));
        $page = $page>0?$page:1;
        $arr = $d3model->GetSkuData($page);
        return json_encode($arr);
    }
    public function GetStatusValue($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $type = trim(htmlspecialchars($parmas['type']));
        $arrType = array('d3good','d3sku','d3cg');
        if(!in_array($type,$arrType)){
            $login_arr = array('code'=>0,'msg'=>'类型不对');
            return json_encode($login_arr);
        }
        $arr = $d3model->GetRedisValue($type);
        return json_encode($arr);
    }
    public function AddSyLog($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
    }
}