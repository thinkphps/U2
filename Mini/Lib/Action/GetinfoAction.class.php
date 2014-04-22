<?php
class GetinfoAction extends Action{
        public function sexToStyle(){
            $sid = trim($this->_post('sid'));
            $fid = trim($this->_post('fid'));
            $tem = trim($this->_post('tem'));
            $sid = $sid?$sid:0;
            $fid = $fid?$fid:0;
            if(S('sid'.$tem.$sid.$fid)){
             $arr = unserialize(S('sid'.$tem.$sid.$fid));
            }else{
                $recomodel = D('Reco');
                if(!empty($fid)){
                    $defaultwhere['suitStyleID'] = $fid;
                }
            switch($sid){
                case 3 :
                //取出默认数据
                $defaultwhere['suitGenderID'] = array('exp','IN(3,4)');
                $defaultResult = $recomodel->getBeubeu($defaultwhere);
                break;
                case 4 :
                  //mini婴幼儿还是从以前商品取数据
                 $goodtag = M('Goodtag');
                 $windex = D('Windex');
                 $where = '';
                 if(isset($tem)){
                        $widvalue = $windex->getwindex($tem);
                        $where.="and g.wid in (".$widvalue['str'].")";
                 }
                 if(!empty($sid)){
                        $where.=" and g.gtype='".$sid."'";
                }
                $where.=" and bg.approve_status='onsale' and bg.num>=15";
                    $sql = "select distinct g.good_id,case when g.wid=".$widvalue['wid']." then 0 end wo, bg.num_iid,bg.type,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join `u_goods` as bg on bg.id=g.good_id where 1 ".$where." order by wo asc,uptime desc";
                    $defaultResult = $goodtag->query($sql);
                    /*$defaultResult = '';
                    foreach($childResult as $k=>$v){
                        if($v){
                            //$defaultResult.='<li><img  data-original="'.__ROOT__.'/'.$v['pic_url'].'" id="'.$v['num_iid'].'" place="'.$gtag2['name'].'" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'"></li>';
                            $defaultResult.='<li><img  data-original="http://uniqlo.bigodata.com.cn/'.$v['pic_url'].'" id="'.$v['num_iid'].'" place="'.$gtag2['name'].'" url=http://uniqlo.bigodata.com.cn/'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'"></li>';
                        }
                    }*/
                break;
                case 1 :
                case 2 :
                $defaultwhere['suitGenderID'] = $sid;
                $defaultResult = $recomodel->getBeubeu($defaultwhere);
                break;
            }
            $arr['def'] = $defaultResult;
            S('sid'.$tem.$sid.$fid,serialize($arr),array('type'=>'file'));
          }
            $this->ajaxReturn($arr, 'JSON');
        }
    public function getGS($where){
           $list = M('SettingsGenderStyle')->cache(true)->join('inner join u_settings_suit_style as sss on u_settings_gender_style.styleID=sss.ID')->distinct(true)->field('sss.ID')->where($where)->select();
           return $list;
    }

    //中间点击分类获取所对应得风格和自定义分类
    public function midsexToStyle(){
        $sid = trim($this->_request('sid'));
        $sid = $sid?$sid:0;
        if(S('midsid'.$sid)){
         $arr = unserialize(S('midsid'.$sid));
        }else{
        $recomodel = D('Reco');
        $ucuslist  = $recomodel->getCusData(array('gtype'=>$sid,'isud'=>'1'));//上装
        $dcuslist  = $recomodel->getCusData(array('gtype'=>$sid,'isud'=>'2'));//下装

        $arr['u'] = $ucuslist;
        $arr['d'] = $dcuslist;
        S('midsid'.$sid,serialize($arr),array('type'=>'file'));
        }
        $this->ajaxReturn($arr, 'JSON');
    }


    //给jack返回款号和色号
    public function getCidItembn(){
        $suitid = trim($this->_post('suitid'));
        if($suitid>0){
            if(S('ciditembn'.$suitid)){
                $result = unserialize(S('ciditembn'.$suitid));
            }else{
                $suites = M('Suits');
                $sql = "SELECT concat(left(u_beubeu_goods.item_bn,8) , u_suits_goodsdetail.cid) AS barcode
 from u_suits INNER JOIN u_suits_goodsdetail on u_suits.suitID = u_suits_goodsdetail.suitID
INNER JOIN u_beubeu_goods on u_suits_goodsdetail.num_iid = u_beubeu_goods.num_iid
where u_suits.suitID = ".$suitid;
                $result = $suites->query($sql);
                S('ciditembn'.$suitid,serialize($result),array('type'=>'file'));
            }
            if(!empty($result)){
            $returnArr = array('code'=>1,'data'=>$result);
            }else{
           $returnArr = array('code'=>1,'msg'=>'没有数据');
            }
        }else{
            $returnArr = array('code'=>0,'msg'=>'参数错误');
        }
        $this->ajaxReturn($returnArr, 'JSON');
    }

    //给jack返回num_iid
    public function getJackNumiid(){
        $item_bn = trim($this->_post('item_bn'));
        if(!empty($item_bn)){

           $goods = M('Goods');
           $sql = "select num_iid,title,detail_url from u_beubeu_goods where left(item_bn,8)='".$item_bn."'";
           $result = $goods->query($sql);
           if(!empty($result[0])){
            $returnArr = array('code'=>1,'data'=>$result[0]);
           }else{
            $returnArr = array('code'=>0,'msg'=>'没有数据');
           }
        }else{
            $returnArr = array('code'=>0,'msg'=>'参数错误');
        }
        $this->ajaxReturn($returnArr, 'JSON');
    }
}