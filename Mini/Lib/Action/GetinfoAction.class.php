<?php
class GetinfoAction extends Action{
        public function sexToStyle(){
            $sid = trim($this->_post('sid'));
            $fid = trim($this->_post('fid'));
            $tem = trim($this->_post('tem'));
            $page = trim($this->_request('page'));
            $sid = $sid?$sid:0;
            $fid = $fid?$fid:0;
            $page = $page?$page:1;
            if(is_mobile()){
            $page_num = 2;
            }else{
            $page_num = 4;
            }
            $start = ($page-1)*$page_num;
            /*if(S('sid'.$tem.$sid.$fid)){
             $arr = unserialize(S('sid'.$tem.$sid.$fid));
            }else{*/
                $recomodel = D('Reco');
                if(!empty($fid)){
                    $defaultwhere['suitStyleID'] = $fid;
                }
            switch($sid){
                case 3 :
                //取出默认数据
                $defaultwhere['suitGenderID'] = array('exp','IN(3,4)');
                $defaultwhere['approve_status'] = 0;
                $defaultResult = $recomodel->getBeubeu($defaultwhere,$page,$page_num,$start);
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
                $defaultwhere['approve_status'] = 0;
                $defaultResult = $recomodel->getBeubeu($defaultwhere,$page,$page_num,$start);
                break;
            }
            $arr['page'] = $defaultResult['page'];
            $arr['prepage'] = $page;
            $arr['count'] = $defaultResult['count'];
            $arr['def'] = $defaultResult['result'];
          //S('sid'.$tem.$sid.$fid,serialize($arr),array('type'=>'file'));
          //}
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
        if($sid!=4 && $sid!=0){
            $where['gender'] = $sid;
            $where['selected'] = 1;
            $where['shortName'] = array('neq','');
            $where['isshow']= 0;
            $ucuslist  = $recomodel->getCusData2($where);//上装
            //$ucuslist  = $recomodel->getCusData($where);//上装
            $arr['u'] = $ucuslist;
        }else if($sid==4){
            $where['gender'] = array('exp','in(4,5)');
            $where['selected'] = 1;
            $where['shortName'] = array('neq','');
            $where['isshow']= 0;
            $babylist  = $recomodel->getCusData2($where);//上装
            $arr['u'] = $babylist;
        }else if($sid==0){
            $where = array('selected'=>1,'shortName'=>array('neq',''),'isshow'=>0);
            $ucuslist = $recomodel->getCateList2($where);
            $arr['u'] = $ucuslist;
        }
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
where u_suits.approve_status=0 and u_suits.suitID = ".$suitid;
                $result = $suites->query($sql);
                S('ciditembn'.$suitid,serialize($result),array('type'=>'file'));
            }
            if(!empty($result)){
            $returnArr = array('code'=>1,'data'=>$result);
            }else{
           $returnArr = array('code'=>0,'msg'=>'没有数据');
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
           $sql = "select num_iid,title,IF(num>0,detail_url,'') as detail_url,num from u_beubeu_goods where left(item_bn,8)='".$item_bn."' order by num desc";
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
//个人中心
public function getCollData(){
    $page = trim($this->_request('page'));
    $page = ($page>0) ? $page : 1;
    $page_num = 4;
    $start = ($page-1)*$page_num;
    $recomodel = D('Reco');
    $where['uid'] = session("uniq_user_id");
    $defaultResult = $recomodel->getBenebnColl($where,$page,$page_num,$start);
    if($page==1){
       //获取用户信息
        $userinfo = $recomodel->getUserInfo();
        $arr['uname'] = $userinfo[0];
        $arr['collflag'] = $userinfo[1];
        $arr['collcount'] = $userinfo[2];
    }
    $arr['page'] = $defaultResult['page'];
    $arr['def'] = $defaultResult['result'];
    $this->ajaxReturn($arr, 'JSON');
}
public function delBeubenColl(){
   $id = trim($this->_request('id'));//百衣收藏id
   if(!empty(session("uniq_user_id"))){
       $beubeu_coll = M('BeubeuCollection');
       $res = $beubeu_coll->where(array('id'=>$id))->delete();
       if($res){
          M('BeubeuCollGoods')->where(array('bcid'=>$id))->delete();
           $arr['code'] = 1;
           $arr['msg'] = '删除成功';
       }else{
           $arr['code'] = 0;
           $arr['msg'] = '参数错误';
       }
   }else{
    $arr['code'] = 0;
    $arr['msg'] = '没有登录';
   }
    $this->ajaxReturn($arr, 'JSON');
}
public function setCollFlag(){
    $uid = session("uniq_user_id");
    if($uid){
    $user = M('User');
    $result = $user->field('id,collflag')->where(array('id'=>$uid))->find();
    if(!empty($result)){
        if($result['collflag']==0){
        $re = $user->where(array('id'=>$uid))->save(array('collflag'=>1));
        if($re){
        $arr['code'] = 1;
        $arr['msg'] = '领取成功';
        }else{
        $arr['code'] = 0;
        $arr['msg'] = '领取失败';
        }
       }else{
            $arr['code'] = 0;
            $arr['msg'] = '已经领取';
        }
    }else{
        $arr['code'] = 0;
        $arr['msg'] = '此用户不存在';
    }
    }else{
        $arr['code'] = 0;
        $arr['msg'] = '没有登录';
    }
    $this->ajaxReturn($arr, 'JSON');
}
    //添加beubeu收藏
public function addBeubenColl(){
    $uid = session("uniq_user_id");
    if($uid){
        $fuid = trim($this->_request('uid'));

    }else{
        $arr['code'] = 0;
        $arr['msg'] = '没有登录';
    }
}
public function getUserInfo(){
      $uid = session("uniq_user_id");
      if($uid){
        $result = M('User')->field('user_name,mobile,taobao_name')->where(array('id'=>$uid))->find();
        if(!empty($result)){
            $arr['code'] = 1;
            $arr['result'] = $result;
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '没有此用户';
        }
      }else{
           $arr['code'] = 0;
           $arr['msg'] = '没有登录';
      }
    $this->ajaxReturn($arr, 'JSON');
}
public function changeName(){
    //修改user_name
    $uid = session("uniq_user_id");
    if($uid){
        $user = M('User');
        $uname = trim($this->_post('uname'));
        $result = $user->field('mobile,taobao_name')->where(array('id'=>$uid))->find();
        $user_name = session("uniq_user_name");
        if($result['mobile']==$user_name || $result['taobao_name']==$user_name){
           $re = $user->where(array('id'=>$uid))->save(array('user_name'=>$uname));
           if($re){
               $arr['code'] = 1;
               $arr['msg'] = '编辑成功';
           }else{
               $arr['code'] = 0;
               $arr['msg'] = '编辑失败';
           }
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '用户信息不匹配';
        }
    }else{
      $arr['code'] = 0;
      $arr['msg'] = '没有登录';
    }
    $this->ajaxReturn($arr, 'JSON');
}
public function changeTaoName(){
        //修改淘宝账号
        $uid = session("uniq_user_id");
        if($uid){
            $user = M('User');
            $tname = trim($this->_post('taobao_name'));
            $result = $user->field('mobile,taobao_name')->where(array('id'=>$uid))->find();
            $user_name = session("uniq_user_name");
            if($result['mobile']==$user_name || $result['taobao_name']==$user_name){
                $re = $user->where(array('id'=>$uid))->save(array('taobao_name'=>$tname));
                if($re){
                    $arr['code'] = 1;
                    $arr['msg'] = '编辑成功';
                }else{
                    $arr['code'] = 0;
                    $arr['msg'] = '编辑失败';
                }
            }else{
                $arr['code'] = 0;
                $arr['msg'] = '用户信息不匹配';
            }
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '没有登录';
        }
    }
$this->ajaxReturn($arr, 'JSON');
}