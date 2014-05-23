<?php
class GetinfoModel extends Action{
//取得当前城市信息
    public function getarea($cityid='',$citybn=''){
        $area = M('Areas');
        if(!empty($cityid)){
            $cbn = $area->field('region_id,local_name,p_region_id,pinying')->where(array('local_name'=>$city))->find();
        }else if(!empty($citybn)){
            $cbn = $area->field('region_id,local_name,p_region_id,pinying')->where(array('citybn'=>$citybn,'region_grade'=>2))->find();
        }
        S('h'.$cityid.$citybn,serialize($cbn),array('type'=>'file'));
        return $cbn;
    }

    //获取省
    public function getpca(){
        if(S('phinfo')){
            $prolist = unserialize(S('phinfo'));
        }else{
            $area = M('Areas');
            $prolist = $area->cache(true)->field('region_id,local_name')->where(array('p_region_id'=>array('exp','IS NULL'),'disabled'=>'false'))->select();
            S('phinfo',serialize(),array('type'=>'file'));
       }
        return $prolist;
    }
    //获取当前城市所对应的省
    public function getcity($pid){
        $area = M('Areas');
        $clist = M('Areas')->field('region_id,local_name')->where(array('p_region_id'=>$pid,'disabled'=>'false'))->select();
        return $clist;
    }
    //通过城市编号获取城市拼音
    public function getPinyin($citybn){
        $area = M('Areas');
        $pinyin = $area->field('region_id,pinying')->where(array('citybn'=>$citybn,'region_grade'=>2))->find();
        return $pinyin;
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

    //kimi2014523
    public function getSuitsResult($arr,$where2,$page_arr){
        $where2['u_suits.approve_status'] = 0;
        $where2['u_suits.beubeuSuitID'] = array('exp','IS NOT NULL');
        $suits = M('Suits');
        $count =  $suits->field('u_suits.suitID')->where($where2)->count();
        $pages = ceil($count/$page_arr[1]);
        if($page_arr[2]>$pages){
            $resultArr = array('code'=>0,'没有数据');
        }else{
        $sql = "select suits.*,ug.num_iid,ug.pic_url,ug.detail_url,ug.title from (SELECT u_suits.suitID,u_suits.suitGenderID,u_suits.suitImageUrl,u_suits.beubeuSuitID,
	                         g.description FROM `u_suits`
                            LEFT JOIN u_settings_suit_style AS g ON u_suits.suitStyleID = g.ID where 1".$arr['fid']."
                            ".$arr['sid']." and u_suits.approve_status = 0 and u_suits.beubeuSuitID IS NOT NULL
                            ORDER BY u_suits.suitID DESC limit ".$page_arr[0].",".$page_arr[1].") as suits
                            LEFT join u_suits_goodsdetail as usg1 on suits.suitID = usg1.suitID
                            INNER JOIN u_beubeu_goods ug ON usg1.num_iid = ug.num_iid
                            WHERE ug.approve_status = 'onsale' AND ug.num >= '15'";
        $result = $suits->query($sql);
        $resultArr = array('code'=>1,'count'=>$count,'da'=>$result);
        }
        return $resultArr;
    }

    public function getDefaultSuit($type){
       $sql = "select su.*,ug.num_iid,ug.pic_url,ug.detail_url,ug.title from (select suits.`suitID`,suits.suitGenderID,suits.`suitImageUrl`,suits.beubeuSuitID,ustyle.`description`,ustyle.eglishName from
`u_suits_select` uss inner join `u_suits` suits on uss.suitID=suits.suitID inner join u_settings_suit_style ustyle on
suits.`suitStyleID`=ustyle.`ID` where suits.`approve_status`=0 and suits.beubeuSuitID is not null and uss.`selected`='1' and  uss.`type`='".$type."') as su left JOIN
u_suits_goodsdetail as de on de.suitID=su.suitID inner join u_beubeu_goods ug on
de.num_iid=ug.num_iid WHERE ( ug.approve_status = 'onsale' ) AND ( ug.num >= '15' )";
        return M('Suits')->query($sql);
    }

  public function getData($slav,$result){
      $arr = array();
      $distslav = array_unique($slav);
      $count = count($distslav);
      $j = 0;
      foreach($distslav as $k2=>$v2){
          $chil = array();
          foreach($result as $k=>$v){
              if($v2==$v['suitID']){
                  $arr[$j] = array('suitID'=>$v['suitID'],'suitGenderID'=>$v['suitGenderID'],'suitImageUrl'=>$v['suitImageUrl'],'beubeuSuitID'=>$v['beubeuSuitID'],'description'=>$v['description']);
                  $chil[] = array('num_iid'=>$v['num_iid'],'pic_url'=>$v['pic_url'],'detail_url'=>$v['detail_url'],'title'=>$v['title']);
                  $arr[$j]['detail'] = $chil;
              }
          }
          $j++;
      }
      return $arr;
  }
}