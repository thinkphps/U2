<?php
class GetinfoModel extends Action{
    public function GetCityInfo(){
        $ip = get_client_ip();

    }
    public function getcity(){
        $url = C('CITYURL1').$ip;
        $data = file_get_contents($url);
        $arrdata = (array)json_decode($data);
        if($arrdata['code']==0){
            $arrdata['data'] = (array)$arrdata->data;
            $arr = array('province'=>$arrdata['data']['region'],'city'=>$arrdata['data']['city']);
        }else{
            $data2 = file_get_contents(C('CITYURL2'));
            $data2 = (array)$data2;
            $arr = array('province'=>$data2['province'],'city'=>$data2['city']);
        }
        return $arr;
    }

    public function getweather($city='',$city_bn=''){
        $weather = M('Weather');
        if(!empty($city)){
            $cbn = $this->getarea($city);
            $weathervalue = $weather->field('weather1,weather2,weather3,weather4,weather5')->where(array('cityid'=>$cbn['citybn']))->find();
            $weathervalue['pinying'] = $cbn['pinying'];
        }else{
            if(!empty($city_bn)){
                $weathervalue = $weather->field('weather1,weather2,weather3,weather4,weather5')->where(array('cityid'=>$city_bn))->find();
            }
        }
        return $weathervalue;
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
    //????
    public function getpca(){
        $area = M('Areas');
        $prolist = $area->cache(true)->field('region_id,local_name')->where(array('p_region_id'=>array('exp','IS NULL')))->select();
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
        $clist = M('Areas')->field('region_id,local_name')->where(array('p_region_id'=>$pid,'disabled'=>'false'))->select();
        return $clist;
    }

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

    public function GetWeatherInfoByID($id)
    {
        $weathers = M('weather w');
        $weatherInfo =  $weathers
            ->join('INNER JOIN u_weathercity wct on w.cityid = wct.commoncityid')
            ->field('w.cityid,wct.commoncityname,w.weather1,w.weather2 ,w.weather3 ,w.weather4 ,w.weather5')
            ->where(array('w.cityid'=>$id))
            ->limit('0,1')
            ->select();
        return $weatherInfo;

    }

    public function shopinfo($id){
        $result = M('Shop')->field('id,sname,tradetime')->where(array('cityid'=>$id))->order('showtag desc')->limit('0,1')->find();
        return $result;
    }

    public function getShopArea($p='',$c=''){
        $area = M('Areas');
        $plist = $area->cache(true)->field('region_id,local_name')->where(array('p_region_id'=>array('exp','IS NULL')))->select();
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

    public function getKeyValue($key){
        $result = M('Settings')->field('value')->where(array('key'=>$key))->find();
        return $result;
    }

    public function getSutsValue($type){
        //$suitSelect = M('SuitsSelect')->cache(true)->join('inner join u_suits suits on u_suits_select.suitID=suits.suitID')->field('suits.suitID,suits.suitImageUrl')->where(array('u_suits_select.selected'=>'1','u_suits_select.type'=>$type))->select();
        $sql = "select suits.`suitID`,suits.suitGenderID,suits.`suitImageUrl`,suits.beubeuSuitID,ustyle.`description`,ustyle.eglishName from  `u_suits_select` uss inner join `u_suits` suits on uss.suitID=suits.suitID inner join u_settings_suit_style ustyle on  suits.`suitStyleID`=ustyle.`ID` where suits.`approve_status`=0 and suits.beubeuSuitID is not null and uss.`selected`='1' and  uss.`type`='".$type."'";
        $suitSelect = M('SuitsSelect')->query($sql);
        $goodsDetail = M('SuitsGoodsdetail');
        foreach($suitSelect as $k=>$v){
            $suitSelect[$k]['detail'] = $goodsDetail->join('inner join u_beubeu_goods ug on u_suits_goodsdetail.num_iid=ug.num_iid')->field('ug.num_iid,ug.pic_url,ug.detail_url,ug.title')->where(array('u_suits_goodsdetail.suitID'=>$v['suitID'],'ug.approve_status'=>'onsale','ug.num'=>array('egt','15')))->select();
        }
        return $suitSelect;
    }

    public function getConSuitsList($where,$page_arr){
        $count =  M('Suits')->join('left join u_settings_suit_style as g on u_suits.suitStyleID=g.ID')->field('u_suits.suitID,u_suits.suitGenderID,u_suits.suitImageUrl,g.description')->where($where)->count();
        $pages = ceil($count/$page_arr[1]);
        if($page_arr[2]>$pages){
            $resultArr = array('code'=>0,'没有数据');
        }else{
        $result = M('Suits')->join('left join u_settings_suit_style as g on u_suits.suitStyleID=g.ID')->field('u_suits.suitID,u_suits.suitGenderID,u_suits.suitImageUrl,u_suits.beubeuSuitID,g.description')->where($where)->limit($page_arr[0].','.$page_arr[1])->order('u_suits.suitID desc')->select();
        $goodsDetail = M('SuitsGoodsdetail');
        foreach($result as $k=>$v){
            $result[$k]['detail'] = $goodsDetail->join('inner join u_beubeu_goods ug on u_suits_goodsdetail.num_iid=ug.num_iid')->field('ug.num_iid,ug.pic_url,ug.detail_url,ug.title')->where(array('u_suits_goodsdetail.suitID'=>$v['suitID'],'ug.approve_status'=>'onsale','ug.num'=>array('egt','15')))->select();
        }
            $resultArr = array('code'=>1,'count'=>$count,'da'=>$result);
       }
        return $resultArr;
    }
}