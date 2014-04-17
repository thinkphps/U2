<?php
class GetinfoAction extends Action{
        public function sexToStyle(){
            $sid = trim($this->_request('sid'));
            $sid = $sid?$sid:0;
            if(S('sid'.$sid)){
             $arr = unserialize(S('sid'.$sid));
            }else{
                $recomodel = D('Reco');
            switch($sid){
                case 3 :
                $where['u_settings_gender_style.genderID'] = array('exp','IN(3,4)');
                $result = $this->getGS($where);
                //取出默认数据
                $defaultwhere['suitStyleID'] = $result[0]['ID'];
                $defaultwhere['suitGenderID'] = array('exp','IN(3,4)');
                $defaultResult = $recomodel->getBeubeu($defaultwhere);
                break;
                case 4 :
                  //mini婴幼儿还是从以前商品取数据
                  $result = M('BeubeuGoods')->cache(true)->field('pic_url')->where(array('type'=>$sid,'approve_status'=>'onsale','num'=>array('egt',15)))->order('uptime desc')->select();
                  $arr['def'] = $result;
                break;
                default :
                $where['u_settings_gender_style.genderID'] = $sid;
                $result = $this->getGS($where);
                $defaultwhere['suitStyleID'] = $result[0]['ID'];
                $defaultwhere['suitGenderID'] = $sid;
                $defaultResult = $recomodel->getBeubeu($defaultwhere);
                break;
            }
            if($sid!=4){
            foreach($result as $k=>$v){
            $result[$k]['pid'] = $recomodel->pageToDataStyle($v['ID']);
            }
            $arr['sty'] = $result;
            $arr['def'] = $defaultResult;
            }
            S('sid'.$sid,serialize($arr),array('type'=>'file'));
          }
            $this->ajaxReturn($arr, 'JSON');
        }
    public function getGS($where){
           $list = M('SettingsGenderStyle')->cache(true)->join('inner join u_settings_suit_style as sss on u_settings_gender_style.styleID=sss.ID')->distinct(true)->field('sss.ID')->where($where)->select();
           return $list;
    }
    //mini左侧点击风格获取数据
    public function styleToData(){
            $sid = trim($this->_post('sid'));
            $fid = trim($this->_post('fid'));
            if(S('sf'.$sid.$fid)){
               $result = unserialize(S('sf'.$sid.$fid));
            }else{
               $recomodel = D('Reco');
            switch($sid){
                case 3 :
                    $where['suitStyleID'] = $fid;
                    $where['suitGenderID'] = array('exp','IN(3,4)');
                    $result = $recomodel->getBeubeu($where);
                    break;
                case 4 :

                    break;
                default :
                    $where['suitStyleID'] = $fid;
                    $where['suitGenderID'] = $sid;
                    $result = $recomodel->getBeubeu($where);
                    break;
            }
          S('sf'.$sid.$fid,serialize($result),array('type'=>'file'));
          }
          $this->ajaxReturn($result, 'JSON');
    }

    //中间点击分类获取所对应得风格和自定义分类
    public function midsexToStyle(){
        $sid = trim($this->_request('sid'));
        $sid = $sid?$sid:0;
        if(S('midsid'.$sid)){
         $arr = unserialize(S('midsid'.$sid));
        }else{
        $recomodel = D('Reco');
        switch($sid){
            case 3 :
                $where['u_settings_gender_style.genderID'] = array('exp','IN(3,4)');
                $result = $this->getGS($where);
                //取出自定义分类
                $ucuslist  = $recomodel->getCusData(array('gtype'=>$sid,'isud'=>'1'));//上装
                $dcuslist  = $recomodel->getCusData(array('gtype'=>$sid,'isud'=>'2'));//下装
            break;
            case 4 :
                $ucuslist  = $recomodel->getCusData(array('gtype'=>$sid,'isud'=>'1'));//上装
                $dcuslist  = $recomodel->getCusData(array('gtype'=>$sid,'isud'=>'2'));//下装
            break;
            default :
                $where['u_settings_gender_style.genderID'] = $sid;
                $result = $this->getGS($where);
                $ucuslist  = $recomodel->getCusData(array('gtype'=>$sid,'isud'=>'1'));//上装
                $dcuslist  = $recomodel->getCusData(array('gtype'=>$sid,'isud'=>'2'));//下装
            break;
        }
        if($sid!=4){
            foreach($result as $k=>$v){
                $result[$k]['pid2'] = $recomodel->pageToDataStyle2($v['ID']);
            }

        }
        $arr['sty'] = $result;
        $arr['count'] = ceil(count($result)/2);
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
            $returnArr = array('code'=>1,'data'=>$result[0]['num_iid']);
           }else{
            $returnArr = array('code'=>0,'msg'=>'没有数据');
           }
        }else{
            $returnArr = array('code'=>0,'msg'=>'参数错误');
        }
        $this->ajaxReturn($returnArr, 'JSON');
    }
}