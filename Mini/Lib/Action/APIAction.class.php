<?php

class APIAction extends Action
{

    public function UpdateProductsWithColor()
    {
        $returnProductValue = array(
            'code' => -1,
            'msg' => array(
                'success' => '',
                'error' => ''
            )
        );

        //$callback=$_GET["callback"];

        $app = $_GET['app'];
        $key = $_GET['key'];
        $products = $_GET['products'];
        $batchid = $_GET['batchid'];

        //$products1 = unserialize(base64_decode());

        //$strRequest = file_get_contents('php://input');
        /*$strRequest = $this->_param('data');
        $Request = json_decode($strRequest,true);*/

        /*	$app = $Request['app'];
            $key = $Request['key'];
            $products =  $Request['products'];
            $batchid = $Request['batchid'];*/

        if (!isset($products) || !isset($key) || !isset($app) || !isset($batchid) ||
            empty($products) || empty($key) || empty($app) || empty($batchid)
        ) {
            $returnProductValue['msg']['error'] = '1. 传递参数错误';
        } else {
            $productSyn = D('ProductSyn');
            $returnProductValue = $productSyn->UpdateUQProduct($app, $key, $products, $batchid);
        }

        $this->ajaxReturn($returnProductValue, 'JSONP');
        //echo $callback."($json)";
//        echo $callback.'('.$json.')';

    }

    public function GetProductColorByID()
    {
        $id = $_GET['id'];

        $returnValue = array();

        //$id = '17141542788';

        if (isset($id) &&  ! empty($id) )
        {
            $productSyn = D('ProductSyn');
            $returnOjb =  $productSyn->GetProductColorByID($id);
            if (isset($returnOjb))
            {
                for($i = 0; $i < count($returnOjb); $i++)
                {
                    $returnValue['color'][$i]['id'] = $returnOjb[$i]['colorid'];
                    $returnValue['color'][$i]['code'] = $returnOjb[$i]['colorcode'];
                    $returnValue['color'][$i]['name'] = $returnOjb[$i]['colorname'];
                    $returnValue['gender'] = $returnOjb[$i]['gender'];
                    $returnValue['uq'] = $returnOjb[$i]['uq'];
                }
            }
        }

        $this->ajaxReturn($returnValue, 'JSON');
    }

    public function GetGoodInfoByUQ()
    {
        $id = $_GET['id'];
        $u_id = $_SESSION['u_id'];
        $goodInfo = array();

        if(isset($id) && ! empty($id))
        {
            $collection = M('Collection');
            $goods = M('beubeu_goods');
            $goodtag = M('Goodtag');
            $love = M('Love');
            $buy = M('Buy');

            $mapgoods['left(u_beubeu_goods.item_bn,8)'] = substr($id,0,8);

            //取出收藏数据
            $clothes = $goods
                ->field('u_beubeu_goods.id as gid,u_beubeu_goods.isud as isud,u_beubeu_goods.num_iid,u_beubeu_goods.type,u_beubeu_goods.title,u_beubeu_goods.num,u_beubeu_goods.price,u_beubeu_goods.pic_url,u_beubeu_goods.detail_url')
                ->where($mapgoods)
                ->select();
            foreach($clothes as $k=>$v){
                switch($v['type']){
                    case '1' :
                        $sexname = '女装';
                        break;
                    case '2' :
                        $sexname = '男装';
                        break;
                    case '3' :
                        $sexname = '童装';
                        break;
                    case '4' :
                        $sexname = '婴幼儿';//todo
                        break;
                }
                $clothes[$k]['csex'] = $sexname;
                $gtag = $goodtag->join('u_tag on u_tag.id=u_goodtag.ftag_id')
                    ->field('u_tag.name,u_goodtag.ccateid')
                    ->where(array('u_goodtag.good_id'=>$v['gid'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>2))
                    ->find();
                $clothes[$k]['tagname1'] = $gtag['name'];
                $clothes[$k]['fg'] = $gtag['ccateid'];
                //场合
                $gtag2 = $goodtag->join('u_tag on u_tag.id=u_goodtag.tag_id')
                    ->field('u_tag.name')
                    ->where(array('u_goodtag.good_id'=>$v['gid'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>1))
                    ->find();
                $clothes[$k]['tagname2'] = $gtag2['name'];

                if(isset($u_id))
                {
                    $islove = $love->field('id')->where(array('num_iid'=>$v['num_iid'],'uid'=>$u_id))->find();
                    if(!empty($islove)){
                        $clothes[$k]['love'] = 1;
                    }
                    $isbuy = $buy->field('id')->where(array('num_iid'=>$v['num_iid'],'uid'=>$u_id))->find();
                    if(!empty($isbuy)){
                        $clothes[$k]['buy'] = 1;
                    }
                }
                //add color,uq,gender, added by David
                $productSyn = D('ProductSyn');
                $returnOjb =  $productSyn->GetProductColorByID($clothes[$k]['num_iid']);
                if (isset($returnOjb))
                {
                    $uq_color = array();
                    for($i = 0; $i < count($returnOjb); $i++)
                    {
                        $uq_color[$i]['id'] = $returnOjb[$i]['colorid'];
                        $uq_color[$i]['code'] = $returnOjb[$i]['colorcode'];
                        $uq_color[$i]['name'] = $returnOjb[$i]['colorname'];
                        $clothes[$k]['color'] = $uq_color;
                        $clothes[$k]['gender'] = $returnOjb[$i]['gender'];
                        $clothes[$k]['uq'] = $returnOjb[$i]['uq'];
                    }
                }
            }

            if(count($clothes) > 0)
            {
                $taobaoInfo['id'] = $clothes[0]['num_iid'];
                $taobaoInfo['src'] = __ROOT__.'/'.$clothes[0]['pic_url'];
                $taobaoInfo['sex'] = $clothes[0]['type'];
                $taobaoInfo['csex'] = $clothes[0]['csex'];
                $taobaoInfo['tag'] = $clothes[0]['tagname1'];
                $taobaoInfo['url'] = $clothes[0]['detail_url'];
                $taobaoInfo['place'] = $clothes[0]['tagname2'];
                $taobaoInfo['price'] = $clothes[0]['price'];
                $taobaoInfo['rest'] = $clothes[0]['num'];
                $taobaoInfo['fg'] = $clothes[0]['fg'];
                $taobaoInfo['alt'] = $clothes[0]['title'];

                $taobaoInfo['data-like'] =  $clothes[0]['love'];
                $taobaoInfo['data-had'] =  $clothes[0]['buy'];
                $taobaoInfo['isud'] = $clothes[0]['isud'];

                $taobaoInfo['color'] =  $clothes[0]['color'];
                $taobaoInfo['gender'] =  $clothes[0]['gender'];
                $taobaoInfo['uq'] =  $clothes[0]['uq'];
            }
        }

        $this->ajaxReturn($taobaoInfo, 'JSON');
    }

    //根据城市ID获取天气信息
    public function GetWeatherByCityID()
    {
        $id = $_POST['id'];
        $subindex = trim($this->_request('subindex'));//表示首页点击切换的动作
        $subid = trim($this->_request('subid'));//点击百度里边店铺的标记
        $shopid = trim($this->_request('shopid'));//点击百度地图里的店铺id
        $baiduerjiid = trim($this->_request('baiduerjiid'));//百度地图上的select二级(直辖市)

        $Weather = D('Weather');
        $returnObj =  $Weather->GetWeatherInfoByID($id);
        //取得城市拼音
        $cbn = $Weather->getarea('',$id);//当前城市对应的城市信息
        //如果点了百度地图里的店铺
        if($subid && $shopid){
            $aidresult = M('Shop')->field('pid,aid')->where(array('id'=>$shopid))->find();
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
        $this->ajaxReturn($weatherInfo, 'JSON');
    }
    //获取店铺信息
    public function getshopinfo(){
      if(S('shopinfo')){
          $shopinfo = unserialize(S('shopinfo'));
      }else{
        $Weather = D('Weather');
        $shopinfo = $Weather->getshopinfo();
        S('shopinfo',serialize($shopinfo),array('type'=>'file'));
        }
        $this->ajaxReturn($shopinfo, 'JSON');
    }
/*leon 3.13 新增
通过uq号，获取色号和图片地址
*/
    public function GetProductColorByUqID()
    {
        $id = $_GET['id'];
        $preStr = "http://uniqlo.bigodata.com.cn/";
        $returnValue = array();

        //$id = '17141542788';

        if (isset($id) &&  ! empty($id) )
        {
            $productSyn = D('ProductSyn');
            $returnOjb =  $productSyn->GetProductColorByUqID($id);
            if (isset($returnOjb))
            {
                for($i = 0; $i < count($returnOjb); $i++)
                {
                    $returnValue['color'][$i]['uq'] = $returnOjb[$i]['uq'].$returnOjb[$i]['colorid'];
                    $returnValue['color'][$i]['url'] = $preStr.$returnOjb[$i]['url'];
                }
            }
        }

        $this->ajaxReturn($returnValue, 'JSONP');
    }

    public function getcity(){
        $pid = trim($this->_request('pid'));//省id
        $baiduid = trim($this->_request('baiduid'));
        $shopid = trim($this->_request('shopid'));//店铺id
        $scid = trim($this->_request('cid'));
        $baiduerjiid = trim($this->_request('baiduerjiid'));//百度地图上的select二级(直辖市)
        $area = M('Areas');
        $Weather = D('Weather');
        $shop = M('Shop');
        if(!empty($baiduid)){
            $pid = $Weather->getId($baiduid,$pid,$scid);
        }
        if($baiduid!=2){
            $list = $area->cache(true)->field('region_id,local_name')->where(array('p_region_id'=>$pid,'disabled'=>'false'))->select();
            if(empty($baiduid) && count($list)==1){
                $list[0]['sel'] = 1;
            }
        }else{
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
        }
        $arr['clist'] = $list;
        $arr['baiduerjiid'] = $baiduerjiid;
        $arr['shopid'] = $shopid;
        $this->ajaxReturn($arr, 'JSON');
    }


//dean 同步标签接口
    public function GetTagsByUqID()
    {
        error_log(print_r($_REQUEST,1),3,'1.txt');
        $returnValue = array(
            'code' => -1,
            'msg' => array(
                'success' => array(),
                'error' => ''
            )
        );
        $app = $_GET['app'];
        $key = $_GET['key'];
        $data = $_GET['data'];
        if (!isset($data) || !isset($key) || !isset($app) ||
            empty($data) || empty($key) || empty($app)
        ) {
            $returnValue['msg']['error'] = '1. 传递参数错误';
        }else{

            $products = $data["products"];
            if (!isset($products)|| empty($products)){
                $returnValue['msg']['error'] = '1. 传递参数错误';
            }else{
                $suitSyn = D('SuitsSyn');
                $returnValue = $suitSyn->GetTagsByUQID($app, $key, $products);
            }
        }
        $this->ajaxReturn($returnValue, 'JSONP');
    }

    //dean 同步搭配接口
    public function UpdatebeubeuSuits()
    {

        $returnValue = array(
            'code' => 1,
            'msg' => array(
                'success' => '',
                'error' => ''
            )
        );
        $app = $_GET['app'];
        $key = $_GET['key'];
        $data = $_GET['data'];
        $batchid = $_GET['batchid'];

        if (!isset($data) || !isset($key) || !isset($app) || !isset($batchid) ||
            empty($data) || empty($key) || empty($app) || empty($batchid)
        ) {
            $returnValue['msg']['error'] = '1. 传递参数错误';
        } else {

            $suits = $data["suits"];

            if (!isset($suits)|| empty($suits)){
                $returnValue['msg']['error'] = '1. 传递参数错误';
            }else{
                $suitSyn = D('SuitsSyn');
                $returnValue = $suitSyn->UpdatebeubeuSuits($app, $key, $suits, $batchid);
            }
        }

        $this->ajaxReturn($returnValue, 'JSONP');

    }
}