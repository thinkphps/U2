<?php
class IndexnewAction extends Action{
    //获取店铺信息
    public function getshopinfo(){
        $callback=$_GET['callback'];
        if(S('shopinfohome')){
            $shopinfo = unserialize(S('shopinfohome'));
            $re = json_encode($shopinfo);
        }else{
        $getcity = D('Getinfo');
        $shopinfo = $getcity->getshopinfo();
        S('shopinfohome',serialize($shopinfo),array('type'=>'file'));
        $re = json_encode($shopinfo);
        }
        //$re = iconv('utf8','gbk',$re);
        echo $callback."($re)";
    }
   //传入城市code获取城市所对应的省列表,城市列表
 public function getCityInfo(){
     $id = trim($this->_request('id'));
     $callback=$_GET['callback'];
     if(S('h'.$id)){
         $arr = unserialize(S('h'.$id));
     }else{
         $Weather = D('Getinfo');
         $plist = $Weather->getpca();//省列表
         $nowcity = $Weather->getarea($cityid='',$id);//取得当前城市信息
         $clist = $Weather->getcity($nowcity['p_region_id']);//取得当前城市对应省下的城市列表
         $arr['plist'] = $plist;
         $arr['nowcity'] = $nowcity;
         $arr['clist'] = $clist;
         S('h'.$id,serialize($arr),array('type'=>'file'));
     }
     $re = json_encode($arr);
     echo $callback."($re)";
 }
    //传城市取天气数据
    public function getcitywerther(){
        $id = trim($this->_request('id'));
        $callback=$_GET['callback'];
        $Weather = D('Getinfo');
        $returnObj =  $Weather->GetWeatherInfoByID($id);
        $weatherInfo["cityname"] = $returnObj[0]['commoncityname'];
        $weatherInfo["weather1"] = json_decode($returnObj[0]['weather1'],true);
        $weatherInfo["weather2"] = json_decode($returnObj[0]['weather2'],true);
        $weatherInfo["weather3"] = json_decode($returnObj[0]['weather3'],true);
        $weatherInfo["weather4"] = json_decode($returnObj[0]['weather4'],true);
        $weatherInfo["weather5"] = json_decode($returnObj[0]['weather5'],true);
        if(S('hpin'.$id)){
            $arr = unserialize(S('hpin'.$id));
        }else{
            $pinyin = $Weather->getPinyin($id);
            $shop = $Weather->shopinfo($pinyin['region_id']);
            $arr['pinyin'] = $pinyin['pinying'];
            $arr['shop'] = $shop;
            S('hpin'.$id,serialize($arr),array('type'=>'file'));
            unset($pinyin);
            unset($shop);
        }
        $weatherInfo["pinyin"] = $arr['pinyin'];
        $weatherInfo['tradetime'] = $arr['shop']['tradetime'];
        $weatherInfo['sname'] = $arr['shop']['sname'];
        $weatherInfo['shopid'] = $arr['shop']['id'];
        $weatherInfo['newstore'] = C('NEWSRORE');
        $re = json_encode($weatherInfo);
        echo $callback."($re)";
    }
    //通过城市id或者区域id获取店铺
    public function getCityShop(){
        $id = trim($this->_request('id'));//城市或者区域id
        $callback=$_GET['callback'];
        $levelid= trim($this->_request('levelid'));//省的级别1为直辖市普通为0
        if(S('ca'.$id)){
            $list = unserialize(S('ca'.$id));
        }else{
        $shop = M('Shop');
        if($levelid==0){
            $list = $shop->field('id,sname,tradetime')->where(array('cityid'=>$id))->select();

        }else if($levelid==1){
            $list = $shop->field('id,sname,tradetime')->where(array('aid'=>$id))->select();
        }
            S('ca'.$id,serialize($list),array('type'=>'file'));
        }
        $re = json_encode($list);
        echo $callback."($re)";
    }

    //通过店铺id获取所在城市id
    public function getShopId(){
        $id = trim($this->_request('id'));//店铺id
        $callback=$_GET['callback'];
        $levelid= trim($this->_request('levelid'));//省的级别1为直辖市普通为0
        $shop = M('Shop');
        if($levelid==0){
            $list = $shop->field('cityid')->where(array('id'=>$id))->select();
        }else if($levelid==1){
            $list = $shop->field('aid')->where(array('id'=>$id))->select();
        }
        $re = json_encode($list);
        echo $callback."($re)";
    }
    //get weatherinfo
    public function GetWeatherByCityID()
    {
        $id = trim($this->_request('id'));
        $subindex = trim($this->_request('subindex'));//表示首页点击切换的动作
        $subid = trim($this->_request('subid'));//点击百度里边店铺的标记
        $shopid = trim($this->_request('shopid'));//点击百度地图里的店铺id
        $baiduerjiid = trim($this->_request('baiduerjiid'));//百度地图上的select二级(直辖市)
        $callback=$_GET['callback'];
        $Weather = D('Getinfo');
        $returnObj =  $Weather->GetWeatherInfoByID($id);
        //取得城市拼音
        $cbn = $Weather->getarea('',$id);//当前城市对应的城市信息
        //如果点了百度地图里的店铺
        if($subid && $shopid){
            $aidresult = M('Shop')->field('pid,aid')->where(array('id'=>$shopid))->find();
            /*if(in_array($aidresult['pid'],array(1,21,42,62))){
            $isp = 1;
            }*/
        }
        if(in_array($cbn['p_region_id'],array(1,21,42,62))){
            $isp = 1;
        }
        $shop = $Weather->shopinfo($cbn['region_id']);
        $pro = $Weather->getpca();//省列表
        $clist = $Weather->getCityList($cbn['region_id'],$cbn['p_region_id']);//城市或者区列表

        foreach($pro as $k=>$v){
            if($v['region_id']==$cbn['p_region_id'] && $subindex==1){
                $pro[$k]['sel'] = 1;
                $pro[$k]['baidusel'] = 1;
                break;
            }else if($v['region_id']==$cbn['p_region_id'] && $subid){
                $pro[$k]['baidusel'] = 1;
                $pro[$k]['sel'] = 1;
                break;
            }
        }

        foreach($clist as $k=>$v){
            if($subid && $shopid && $isp){
                if($v['region_id']==$aidresult['aid']){
                    $clist[$k]['sel'] = 1;
                }
            }else if($subid && $baiduerjiid && $isp){
                if($v['region_id']==$baiduerjiid){
                    $clist[$k]['sel'] = 1;
                }
            }else{
                if($v['region_id']==$cbn['region_id']){
                    $clist[$k]['sel'] = 1;
                }
            }
        }
        $weatherInfo["cityname"] = $returnObj[0]['commoncityname'];
        $weatherInfo["weather1"] = json_decode($returnObj[0]['weather1'],true);
        $weatherInfo["weather2"] = json_decode($returnObj[0]['weather2'],true);
        $weatherInfo["weather3"] = json_decode($returnObj[0]['weather3'],true);
        $weatherInfo["weather4"] = json_decode($returnObj[0]['weather4'],true);
        $weatherInfo["weather5"] = json_decode($returnObj[0]['weather5'],true);
        $weatherInfo['cbn'] = $cbn['pinying'];
        $weatherInfo['tradetime'] = $shop['tradetime'];
        $weatherInfo['sname'] = $shop['sname'];
        $weatherInfo['id'] = $shop['id'];
        $weatherInfo['plist'] = $pro;
        $weatherInfo['clist'] = $clist;
        $weatherInfo['indexcity'] = $cbn;
        $weatherInfo['isp'] = $isp;//表示是直辖市
        $weatherInfo['baiduerjiid'] = $baiduerjiid;
        $weatherInfo['shopid'] = $shopid;
        $weatherInfo['newstorre'] = C('NEWSRORE');
        $re = json_encode($weatherInfo);
        //$re = iconv('utf8','gbk',$re);
        echo $callback."($re)";

    }

    public function getcity(){
        $pid = trim($this->_request('pid'));//省id
        $callback=$_GET['callback'];
        $area = M('Areas');
        $levelid= trim($this->_request('levelid'));//省的级别1为直辖市普通为0
        /*$shopid = trim($this->_request('shopid'));//店铺id
        $scid = trim($this->_request('cid'));
        $baiduerjiid = trim($this->_request('baiduerjiid'));//百度地图上的select二级(直辖市)
        $Weather = D('Getinfo');
        $shop = M('Shop');
        if(!empty($baiduid)){
            $pid = $Weather->getId($baiduid,$pid,$scid);
        }
        if($baiduid!=2){*/
            if($levelid==0){
            $list = $area->cache(true)->field('region_id,local_name')->where(array('p_region_id'=>$pid))->select();
            }else if($levelid==1){
            $sql = "select region_id,local_name from u_areas where p_region_id=(select region_id from u_areas where p_region_id={$pid})";
            $list = $area->query($sql);
            }
        /* if(empty($baiduid) && count($list)==1){
             $list[0]['sel'] = 1;
         }
     /*}else{
       switch($pid){
             case 1 :
             case 21 :
             case 42 :
             case 62 :
                 $where = array('aid'=>$scid);
                 break;
             default :
                 $where = array('cityid'=>$scid);
                 break;
         }
         $list = $shop->field('id,longitude,latitude,sname,tradetime')->where($where)->select();
         foreach($list as $k=>$v){
             if($v['id'] == $shopid){
                 $list[$k]['sel'] = 1;
             }
         }
     }*/
        $arr['clist'] = $list;
        $re = json_encode($arr);
        /*$arr['baiduerjiid'] = $baiduerjiid;
        $arr['shopid'] = $shopid;
        ;*/
        //$re = iconv('utf8','gbk',$re);
        echo $callback."($re)";
    }
    //新版获取套图推荐
    public function getSuits(){

        $callback=$_GET['callback'];
        $tem = trim($this->_request('tem'));
        $Weather = D('Getinfo');
        $setingtem = $Weather->getKeyValue('temperature');
        if($tem>=$setingtem['value']){
         $type = 1;
        }else{
         $type = 2;
        }
        $suitSelect = $Weather->getSutsValue($type);
        $re = json_encode($suitSelect);
        echo $callback."($re)";
    }
  //获取有条件传入时的套图
    public function getConSuits(){
        $callback=$_GET['callback'];
        $tem = trim($this->_request('tem'));
        $sid = trim($this->_request('sid'));//性别
        $fid = trim($this->_request('fid'));//风格
        $page = trim($this->_request('page'));
        $sid = $sid?$sid:0;
        $fid = $fid?$fid:0;
        $page = $page?$page:1;
        $page_num = 10;
        $start = ($page-1)*$page_num;
        $where = array();
        /*if(S('homesf'.$sid.$fid.$page)){
        $list = unserialize(S('homesf'.$sid.$fid.$page));
        }else{*/
        $Weather = D('Getinfo');
        if($fid!=0){
           $where['u_suits.suitStyleID'] = intval($fid);
        }
        switch($sid){
            case 3 :
                $where['u_suits.suitGenderID'] = array('exp','IN(3,4)');
            break;
            case 1 :
            case 2 :
               $where['u_suits.suitGenderID'] = intval($sid);
            break;
        }

        if($sid==0 && $fid==0){
         //风格，类别都没有选走这里
            $setingtem = $Weather->getKeyValue('temperature');
            if($tem>$setingtem['value']){
                $type = 1;
            }else{
                $type = 2;
            }

            $listResult = $Weather->getSutsValue($type);
            if(!empty($listResult)){
                $list = array('code'=>1,'da'=>$listResult);
            }else{
                $list = array('code'=>0);
            }

        }else{
            if($sid==4){
             $list = M('BeubeuGoods')->cache(true)->field('pic_url')->where(array('type'=>$sid,'approve_status'=>'onsale','num'=>array('egt',15)))->order('uptime desc')->select();
            }else{
            $page_arr = array($start,$page_num,$page);
            $where['u_suits.approve_status'] = 0;
            $where['u_suits.beubeuSuitID'] = array('exp','IS NOT NULL');
            $listResult = $Weather->getConSuitsList($where,$page_arr);
            if($listResult['code']==1){
                $list = array('code'=>1,'page'=>$page+1,'count'=>$listResult['count'],'da'=>$listResult['da']);
            }else if($listResult['code']==0){
               $list = array('code'=>0,'page'=>$page+1);
            }
            }
        }
        /*S('homesf'.$sid.$fid.$page,serialize($list),array('type'=>'file'));
      }*/
        $re = json_encode($list);
        echo $callback."($re)";
    }
  //对比方法
   public function getConSuits2(){
       /*$callback=$_GET['callback'];
       $page = 1;
               $sql = "(SELECT u_suits.suitID,u_suits.suitGenderID,u_suits.suitImageUrl,u_suits.beubeuSuitID,
	                         g.description,ug.num_iid,ug.pic_url,ug.detail_url,ug.title FROM `u_suits`
                            LEFT JOIN u_settings_suit_style AS g ON u_suits.suitStyleID = g.ID where u_suits.suitStyleID = 7
                            and u_suits.suitGenderID = 1 and u_suits.approve_status = 0 and u_suits.beubeuSuitID IS NOT NULL
                            ORDER BY u_suits.suitID DESC limit 0,10) as suits
                            LEFT join u_suits_goodsdetail as usg1 on suits.suitID = usg1.suitID
                            INNER JOIN u_beubeu_goods ug ON usg1.num_iid = ug.num_iid
                            WHERE ug.approve_status = 'onsale' AND ug.num >= '15'";
               $result = M('Suits')->query($sql);
               $slav = array();
               $arr = array();
               foreach($result as $k=>$v){
                   $slav[] = $v['suitID'];
               }

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
               if(!empty($arr)){
                   $list = array('code'=>1,'page'=>$page+1,'count'=>$count,'da'=>$arr);
               }else if($listResult['code']==0){
                   $list = array('code'=>0,'page'=>$page+1);
               }
       $re = json_encode($list);
       echo $callback."($re)";*/
   }
//点击按钮取数据
    public function getgood(){
        $tem = trim($this->_request('tem'));//平均温度
        $cid = trim($this->_request('cid'));//场合形如1_2_3全部为0
        $sid = trim($this->_request('sid'));//性别id形如1,2,3 all为0
        $tid = trim($this->_request('tid'));//套装id
        $pro = trim($this->_request('pro'));//省
        $callback=$_GET['callback'];
        if($tem<=-10){
            $tem = -10;
        }
        $cid = $cid?$cid:0;
        $sid = $sid?$sid:0;
        $tid = $tid?$tid:0;
        $goodtag = M('Goodtag');
        $windex = D('Windex');
        $recomodel = D('Reco');
        $widvalue = $windex->getwindex($tem);
        $wvalue = $widvalue['str'];
        switch($tid){
            case 0 : //没有选中套装
                if($cid==0 && $sid==0){//场合跟性别都为0
                    //取得官方推荐数据
                    if(!empty($pro)){
                        $recogood = $recomodel->getrec($tem);
                        $uclothes = $recogood[0];
                        $dclothes = $recogood[1];
                    }
                }else if($sid==0 && $cid!='0'){//性别为all
                    $cidarr = explode('_',$cid);
                    $cidstr = '';
                    foreach($cidarr as $k=>$v){
                        if($v){
                            $cidstr.=$v.',';
                        }
                    }
                    $cidstr = rtrim($cidstr,',');
                    //上装
                    $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>array('exp','IN('.$cidstr.')'),'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    $uclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
                    $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>array('exp','IN('.$cidstr.')'),'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    $uclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
                    $windex->saomo($uclothes,$uclothesx);
                    //下装
                    $where2 = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>array('exp','IN('.$cidstr.')'),'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    $dclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
                    $where2x = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>array('exp','IN('.$cidstr.')'),'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    $dclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
                    $windex->saomo($dclothes,$dclothesx);

                }else if($sid!=0 && $cid=='0'){
                    if($sid==4){
                        $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    }else{
                        $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'1','u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    }
                    $uclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
                    $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'1','u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    $uclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
                    $windex->saomo($uclothes,$uclothesx);
                    //下装
                    if($sid!=4){
                        $where2 = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'2','u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                        $dclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
                        $where2x = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'2','u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                        $dclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
                        $windex->saomo($dclothes,$dclothesx);
                    }
                }else if($sid!=0 && $cid!='0'){//性别跟场合都不为0
                    $cidarr = explode('_',$cid);
                    switch($sid){
                        case 1 : //女
                            $ctagid = $cidarr[0];
                            break;
                        case 2 :
                            $ctagid = $cidarr[1];
                            break;
                        case 3 :
                            $ctagid = $cidarr[2];
                            break;
                    }
                    //上装
                    if($sid==4){
                        $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    }else{
                        $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>$ctagid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    }

                    $uclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
                    $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>$ctagid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    $uclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
                    $windex->saomo($uclothes,$uclothesx);
                    //下装
                    if($sid!=4){
                        $where2 = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>$ctagid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                        $dclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
                        $where2x = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>$ctagid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                        $dclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
                        $windex->saomo($dclothes,$dclothesx);
                    }
                }
                break;
            case 1 : //选了套装
                if($sid==0){//性别为all
                    if(!empty($cid)){
                        $cidarr = explode('_',$cid);
                    }
                    $cidstr = '';
                    foreach($cidarr as $k=>$v){
                        if($v){
                            $cidstr.=$v.',';
                        }
                    }
                    $cidstr = rtrim($cidstr,',');
                    //套装
                    $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'4','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    if(!empty($cidstr)){
                        $where['u_goodtag.tag_id'] = array('exp','IN('.$cidstr.')');
                    }
                    $tclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();

                    $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'4','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    if(!empty($cidstr)){
                        $wherex['u_goodtag.tag_id'] = array('exp','IN('.$cidstr.')');
                    }
                    $tclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
                    $windex->saomo($tclothes,$tclothesx);

                }else if($sid>0){//优选性别
                    if(!empty($cid)){
                        $cidarr = explode('_',$cid);
                    }
                    switch($sid){
                        case 1 : //女
                            $ctagid = $cidarr[0];
                            break;
                        case 2 :
                            $ctagid = $cidarr[1];
                            break;
                        case 3 :
                            $ctagid = $cidarr[2];
                            break;
                    }
                    //套装
                    $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'4','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    if($ctagid){
                        $where['u_goodtag.tag_id'] = $ctagid;
                    }
                    $tclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();

                    $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'4','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
                    if($ctagid){
                        $wherex['u_goodtag.tag_id'] = $ctagid;
                    }
                    $tclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
                    $windex->saomo($tclothes,$tclothesx);
                }
                break;
        }
        //如果没有上装跟下装走这里
        if($tid==0){
            if($cid!=0 || $sid!=0){
                if(empty($uclothes) && empty($dclothes)){
                    $recogood = $recomodel->getrec($tem);
                    $uclothes = $recogood[0];
                    $dclothes = $recogood[1];
                    $arr['fl'] = 1;
                }else{
                    $arr['fl'] = 0;
                }
            }
        }
        //上装
        if(!empty($uclothesx)){
            foreach($uclothesx as $k=>$v){
                if(!empty($v)){
                    $uclothes[] = $v;
                }
            }
        }
        $uclothesx = array();
        $ustr = '';
        if(!empty($uclothes)){
            foreach($uclothes as $k=>$v){
                $v['title'] = iconv('utf8','gbk',$v['title']);
                $ustr.='<li><img fg="'.$v['ccateid'].'" data-original="'.C('UNIQLOURL').$v['pic_url'].'" id="1" place="家居1" tag="淑女1" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'" miniUrl="'.C('UNIQLOURL').'mini.php/Index/index/num/'.$v['num_iid'].'">
              </li>';
            }
        }
        $ustr = iconv('gbk','utf8',$ustr);
        $arr['ustr'] = $ustr;
        $arr['flag1'] = 'p';
        //下装
        if(!empty($dclothesx)){
            foreach($dclothesx as $k=>$v){
                if(!empty($v)){
                    $dclothes[] = $v;
                }
            }
        }
        $dclothesx = array();
        $dstr = '';
        if(!empty($dclothes)){
            foreach($dclothes as $k=>$v){
                $v['title'] = iconv('utf8','gbk',$v['title']);
                $dstr.='<li><img fg="'.$v['ccateid'].'" data-original="'.C('UNIQLOURL').$v['pic_url'].'" id="10" place="家居2" tag="淑女10" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'" miniUrl="'.C('UNIQLOURL').'mini.php/Index/index/num/'.$v['num_iid'].'">
              </li>';
            }
        }
        $dstr = iconv('gbk','utf8',$dstr);
        $arr['dstr'] = $dstr;
        $arr['flag1'] = 'p';
        //套装
        if(!empty($tclothesx)){
            foreach($tclothesx as $k=>$v){
                if(!empty($v)){
                    $tclothes[] = $v;
                }
            }
        }
        $tclothesx = array();
        $tstr = '';
        if(!empty($tclothes)){
            foreach($tclothes as $k=>$v){
                $v['title'] = iconv('utf8','gbk',$v['title']);
                $tstr.='<li>
                <img data-original="'.C('UNIQLOURL').$v['pic_url'].'" id="10" place="家居2" tag="淑女10" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'">
              </li>';
            }
            $tstr = iconv('gbk','utf8',$tstr);
        }
        $arr['tstr'] = $tstr;
        if(!empty($tstr)){
            $arr['flag'] = 't';
        }
        $arr['sid'] = $sid;
        $re = json_encode($arr);
        $re = iconv('utf8','gbk',$re);
        echo $callback."($re)";
    }
    public function _empty(){
        header("HTTP/1.1 404 Not Found");
        $this->error('此方法不存在',U('Indexnew/index'));
    }
}