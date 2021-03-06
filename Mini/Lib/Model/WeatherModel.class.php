<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 14-2-25
 * Time: 上午11:19
 */

class WeatherModel extends Model{
    //根据城市ID获取天气信息
    public function GetWeatherInfoByID($id)
    {
        $weathers = M('weather w');
        $weatherInfo =  $weathers
            ->join('INNER JOIN u_weathercity wct on w.cityid = wct.commoncityid')
            ->field('w.cityid,wct.commoncityname,w.weather1,w.weather2 ,w.weather3 ,w.weather4 ,w.weather5 ,w.weather6')
            ->where(array('w.cityid'=>$id))
            ->limit('0,1')
            ->select();

        return $weatherInfo;

    }

    public function getarea($city='',$citybn=''){
        $area = M('Areas');
        if(!empty($city)){
            $cbn = $area->field('region_id,local_name,p_region_id,citybn,pinying')->where(array('local_name'=>$city))->find();
        }else if(!empty($citybn)){
            $cbn = $area->field('region_id,local_name,p_region_id,citybn,pinying')->where(array('citybn'=>$citybn,'region_grade'=>2))->find();
        }
        return $cbn;
    }

    //取得店铺信息
    public function shopinfo($id){
        $result = M('Shop')->field('id,sname,tradetime')->where(array('cityid'=>$id))->order('showtag desc')->limit('0,1')->find();
        return $result;
    }

    //取得省
    public function getpca(){
        $area = M('Areas');
        $prolist = $area->cache(true)->field('region_id,local_name,disabled')->where(array('p_region_id'=>array('exp','IS NULL'),'region_id'=>array('not in',array(3235,3239,3242))))->select();
        return $prolist;
    }

    public function getCityList($cid,$pid){
        switch($pid){
            case 1 :
                $pid = $cid;
                break;
            case 21 :
                $pid = $cid;
                break;
            case 42 :
                $pid = $cid;
                break;
            case 62 :
                $pid = $cid;
                break;
        }
        $clist = M('Areas')->field('region_id,local_name,disabled')->where(array('p_region_id'=>$pid))->select();
        return $clist;
    }

    public function getId($bid=0,$pid=0,$cid=0){
        $area = M('Areas');
        if($bid==1){
            switch($pid){
                case 1 :
                    $cid = $area->cache(true)->field('region_id')->where(array('p_region_id'=>$pid))->find();
                    $pid = $cid['region_id'];
                    break;
                case 21 :
                    $cid = $area->cache(true)->field('region_id')->where(array('p_region_id'=>$pid))->find();
                    $pid = $cid['region_id'];
                    break;
                case 42 :
                    $cid = $area->cache(true)->field('region_id')->where(array('p_region_id'=>$pid))->find();
                    $pid = $cid['region_id'];
                    break;
                case 62 :
                    $cid = $area->cache(true)->field('region_id')->where(array('p_region_id'=>$pid))->find();
                    $pid = $cid['region_id'];
                    break;
            }
        }
        return $pid;
    }
    //取得店铺数据
    public function getshopinfo(){
        $shop = M('Shop');
        $list = $shop->cache(true)->join('inner join u_areas on u_shop.cityid=u_areas.region_id')->field('u_shop.id,u_shop.longitude,u_shop.latitude,u_shop.sname,u_shop.saddress,u_shop.tradetime,u_shop.scall,u_shop.sange,u_areas.local_name,u_areas.p_region_id,u_areas.citybn')->select();
        $area = M('Areas');
        $arr = array();
        foreach($list as $k=>$v){
            $aname = $area->cache(true)->field('local_name')->where(array('region_id'=>$v['p_region_id']))->find();
            $arr[$k] = array($v['sname'],$v['saddress'],$v['longitude'],$v['latitude'],$v['sange'],$v['tradetime'],$v['scall'],$v['local_name'],$aname['local_name'],$v['id']);
        }
        unset($list);
        return $arr;
    }
} 