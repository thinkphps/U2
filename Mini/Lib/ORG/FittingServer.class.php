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
        $page = $page>0?$page:1;
        $arr = $d3model->GetGoodsData($page);
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
}