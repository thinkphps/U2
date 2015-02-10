<?php
class MacappModel extends Model{
    public function CkeckApp($uname,$upass){
        if($uname==C('IOSNMAE') && $upass==C('IOSPASS')){
            $flag = 1;
        }else{
            $flag = 0;
        }
        return $flag;
    }
    public function getCollNumm_iid($uq){
        if(!empty($uq)){
            $goods = M('Goods');
            $is_g = is_int(strpos($uq,'_'));
            if(!$is_g){
                $uq = $uq.'_';
            }
            $arr_uq = explode('_',$uq);
            foreach($arr_uq as $k=>$v){
                if($v){
                    $uv = substr($v,0,8);
                    $sql = "select `num_iid` from `u_goods` where item_bn like '".$uv."%' order by num desc";
                    $result = $goods->query($sql);$uqArr = array();
                    $uqArr[] = $result[0]['num_iid'];$uqArr[] = $v;
                    $arr[] = $uqArr;
                }
            }
            return $arr;
        }
    }
public function getsstyle($sid){
    $sql = "select s.ID,s.description,gs.goodnum from u_settings_suit_style as s inner join u_settings_gender_style as gs on gs.styleID=s.ID where gs.genderID=".$sid;
    $list = M('')->query($sql);
    return $list;
}
public function getUqNum($item_bn){
    if(!empty($item_bn)){
        $goods = M('Goods');
        $sql = "select num_iid,title,approve_status,IF(num>0 and approve_status='onsale',detail_url,'') as detail_url,num from u_beubeu_goods where item_bn like '".$item_bn."%' order by num desc";
        $result = $goods->query($sql);
        if(!empty($result[0])){
            $returnArr = array('code'=>1,'data'=>$result[0]);
        }else{
            $returnArr = array('code'=>0,'msg'=>'没有数据');
        }
    }else{
            $returnArr = array('code'=>0,'msg'=>'参数错误');
    }
    return $returnArr;
}
    public function getCusData2($where){
        $sell = M('Sellercats');
        $result = $sell->field('ID as id,shortName as name')->where($where)->group('shortName')->order('sort_order asc')->select();
        foreach($result as $k=>$v){
            if(!empty($where['gender'])){
                $arr2['gender'] = $where['gender'];
            }
            $arr2['selected'] = 1;
            $arr2['shortName'] = $v['name'];
            $arr2['isshow'] = 0;
            $idlist = $sell->field('ID as id,goodnum')->where($arr2)->select();
            $idstr = '';$sum = 0;
            foreach($idlist as $k1=>$v1){
                if($v1){
                    $idstr.=$v1['id'].'_';
                    $sum+=$v1['goodnum'];
                }
            }
            $idstr = rtrim($idstr,'_');
            $ucuslist[] = array('id'=>$idstr,'name'=>$v['name'],'sum'=>$sum);
        }
        unset($result);
        return $ucuslist;
    }
public function SqlCount(&$sql,$goodtag,$page_num){
    $gcount = $goodtag->query($sql);
    $count = ceil($gcount[0]['co']/$page_num);
    return $count;
}
    public function getBeubeu($where,$page,$page_num,$start,$unihost,$root_dir=''){
        $where['suitImageUrl'] = array('neq','');
        $beubeu_suits = M('BeubeuSuits');
        $detail = M('BeubeuSuitsGoodsdetail');
        $count = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->count();
        $num = ceil($count/$page_num);
        if($page>$num){
            $page = 1;
            $start = 0;
        }
        $beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->order('suitID desc')->limit($start.','.$page_num)->select();
        foreach($beubeu_suits_list as $k=>$v){
            /*$before = substr($v['suitImageUrl'],0,-4);
            $v['suitImageUrl'] = $before.'a.png';*/
            $beubeu_suits_list[$k]['suitImageUrl'] = $unihost.$v['suitImageUrl'];
            $beubeu_suits_list[$k]['detail'] = $this->getSuitsDetail($detail,$v['suitID'],$unihost,$root_dir);
            switch($v['suitGenderID']){
                case 1 :
                    $sex = 15474;
                    break;
                case 2 :
                    $sex = 15478;
                    break;
                case 3 :
                    $sex = 15583;
                    break;
                case 4 :
                    $sex = 15581;
                    break;
            }
            $beubeu_suits_list[$k]['sex'] = $sex;
        }
        $arr['page'] = $page+1;
        $arr['result'] = $beubeu_suits_list;
        $arr['count'] = $num;
        return $arr;
    }
    public function getSuitsDetail($detail,$suitID,$unihost,$root_dir){
       $sql = "select `item_bn` as uq from `u_beubeu_suits_goodsdetail` where `suitID`={$suitID} and left(`item_bn`,2)='UQ'";
       $detailSuit = $detail->query($sql);unset($sql);
       foreach($detailSuit as $k=>$v){
            $cid = substr($v['uq'],-2,2);
            $uq =  substr($v['uq'],0,-2);
            $sql = "select p.`url` as url from `u_beubeu_goods` as g inner join `u_products` as p on p.goods_id=g.id where g.`item_bn` like '".$uq."%' and p.`cid`='{$cid}' limit 0,1";
            $result = $detail->query($sql);
            if(!empty($result)){
                $before = dirname($result[0]['url']);
                $filename = pathinfo($result[0]['url'],PATHINFO_FILENAME);
                $newfilepath = $root_dir.'/'.$before.'/mac100/'.$filename.'.png';
                if(file_exists($newfilepath)){
                    $detailSuit[$k]['url'] = $unihost.$before.'/mac100/'.$filename.'.png';
                }else{
                    $detailSuit[$k]['url'] = $unihost.$result[0]['url'];
                }
            }
       }
        return $detailSuit;
    }
    public function getBenebnColl($where,$unihost,$page,$page_num,$start){
        $beubeu_coll = M('BeubeuCollection');
        $count = $beubeu_coll->field('id')->where($where)->count();
        $num = ceil($count/$page_num);
        if($page>$num){
            $page = 1;
            $start = 0;
        }
        $result = $beubeu_coll->field('id,gender,suitID,pic_clothes')->where($where)->order('id desc')->limit($start.','.$page_num)->select();
        $str = '';
        foreach($result as $k=>$v){
            $str.=$v['id'].',';
        }
        $str = rtrim($str,',');
        $sql = "select t1.bcid,t1.uq,bg.`num_iid`,bg.`approve_status`,bg.`title`,bg.`num`,bg.`pic_url`,IF(bg.num>0 and bg.approve_status='onsale',bg.detail_url,'') as detail_url from (select `bcid`,`num_iid`,`uq` from `u_beubeu_coll_goods` as bc where bc.bcid in ({$str})) as t1 inner join `u_beubeu_goods` as bg on bg.num_iid=t1.num_iid";
        $detail = $beubeu_coll->query($sql);
        foreach($result as $k1=>$v1){
            $detailArr = array();
            $karr = array();$karr2 = array();
            foreach($detail as $k2=>$v2){
                $v2['pic_url'] = $unihost.$v2['pic_url'];
                if($v1['id']==$v2['bcid']){
                    if($v2['num']<=0 || $v2['approve_status']=='instock'){
                        $v2['title'] = '【已售罄】'.$v2['title'];
                    }
                    $orid = $this->collGoodsOrder($v2['title']);
                    if($orid!=-1){
                        $karr2[$orid] = $v2;
                        $karr[] = $orid;
                    }else{
                        array_push($detailArr,$v2);
                    }
                    //$detailArr[] = $v2;
                }
            }
            arsort($karr);
            foreach($karr as $k3=>$v3){
                if(empty($detailArr)){
                    $detailArr[] = $karr2[$v3];
                }else{
                    array_unshift($detailArr,$karr2[$v3]);
                }
            }
            $result[$k1]['detail'] = $detailArr;
        }
        $arr['page'] = $page+1;
        $arr['count'] = $num;
        $arr['result'] = $result;
        unset($result);
        return $arr;
    }
    public function collGoodsOrder($title){
        $arr = array('羽绒服','大衣','外套','卫衣','马甲','毛衣','针织衫','衬衫','开衫','薄衫','POLO衫','茄克','家居服','套装（连身装）','T恤','背心','内衣','裙子','裤','帽子','坎肩围巾','包','袜子','鞋子','配饰','首饰','其他');
        $orid = -1;
        foreach($arr as $k=>$v){
            if(is_int(strpos($title,$v))){
                $orid = $k;
                break;
            }
        }
        return $orid;
    }
    public function AppShare($bbody,$bshose,$bclose,$bhead,$root_dir){
        $md5b = md5($bbody);$md5s = md5($bshose);$md5c = md5($bclose);$md5h = md5($bhead);$md5share = md5($bbody.$bshose.$bclose.$bhead);
        $share = M('Share');
        $result = $share->field('pic_url')->where(array('id'=>$md5share))->find();
        if(!empty($result)){
            $url = 'http://'.$_SERVER['HTTP_HOST'].$result['pic_url'];
        }else{
            $down = M('ShareDown');
            $shoseexten = pathinfo($bshose,PATHINFO_EXTENSION);
            $downb = $down->field('pic_url')->where(array('id'=>$md5b))->find();
            if(!empty($downb)){
                $bimage[0] = $downb['pic_url'];
            }else{
                $bimage = $this->createdir($md5b,$root_dir.'/Upload/sharedownpic/Body/','Upload/sharedownpic/Body/',$bbody,2);//身体
                @file_put_contents($bimage[0],file_get_contents($bbody));
                $down->add(array('id'=>$md5b,'pic_url'=>$bimage[0]));
            }
            $downs = $down->field('pic_url')->where(array('id'=>$md5s))->find();
            if(!empty($downs)){
                $simage[0] = $downs['pic_url'];
            }else{
                if($shoseexten=='png'){
                    $simage = $this->createdir($md5s,$root_dir.'/Upload/sharedownpic/Shose/','Upload/sharedownpic/Shose/',$bshose,2);//鞋子
                    @file_put_contents($simage[0],$bshose);
                    $down->add(array('id'=>$md5s,'pic_url'=>$simage[0]));
                }
            }
            $downc = $down->field('pic_url')->where(array('id'=>$md5c))->find();
            if(!empty($downc)){
                $cimage[0] = $downc['pic_url'];
            }else{
                $cimage = $this->createdir($md5c,$root_dir.'/Upload/sharedownpic/Match/','Upload/sharedownpic/Match/',$bclose,2);//衣服
                @file_put_contents($cimage[0],file_get_contents($bclose));
                $down->add(array('id'=>$md5c,'pic_url'=>$cimage[0]));
            }
            $downh = $down->field('pic_url')->where(array('id'=>$md5h))->find();
            if(!empty($downh)){
                $himage[0] = $downh['pic_url'];
            }else{
                $himage = $this->createdir($md5h,$root_dir.'/Upload/sharedownpic/Head/','Upload/sharedownpic/Head/',$bhead,2);//头
                @file_put_contents($himage[0],file_get_contents($bhead));
                $down->add(array('id'=>$md5h,'pic_url'=>$himage[0]));
            }
            $white=new Imagick($bimage[0]);//身体
            if($shoseexten=='png'){
                $im4=new Imagick($simage[0]);//鞋子
            }
            $im2=new Imagick($cimage[0]);//衣服
            $im3=new Imagick($himage[0]);//头
            if($shoseexten=='png'){
                $white->compositeimage($im4, Imagick::COMPOSITE_OVER, 0, 0);
            }
            $white->compositeimage($im2, Imagick::COMPOSITE_OVER, 0, 0);
            $white->compositeimage($im3, Imagick::COMPOSITE_OVER, 0, 0);
            $white->thumbnailImage( 400, 533);
            $white->setImageFormat('png');
            $image = $this->createdir($md5share,$root_dir.'/Upload/sharepic/','/Upload/sharepic/',$bclose,2);
            $white->writeImage($image[0]);
            $white->clear();
            $white->destroy();
            $share->add(array('id'=>$md5share,'pic_url'=>$image[1]));
            $url = 'http://'.$_SERVER['HTTP_HOST'].$image[1];
        }
        return $url;
    }
    public function createdir($filename,$dir='',$path='',$img='',$flag = 1){
        $timenow = time();
        $year = date('Y',$timenow);
        $date = date('Y-m',$timenow);
        $time = date('Y-m-d',$timenow);
        $hou = date('Y-m-d-H',$timenow);
        $dir1 = $dir.$year;
        $path.=$year;
        if(!file_exists($dir1)&&!is_dir($dir1)){
            mkdir($dir1,0777);
        }
        $dir1 = $dir1.'/'.$date;
        $path.='/'.$date;
        if(!file_exists($dir1)&&!is_dir($dir1)){
            mkdir($dir1,0777);
        }
        $dir1 = $dir1.'/'.$time;
        $path.='/'.$time;
        if(!file_exists($dir1)&&!is_dir($dir1)){
            mkdir($dir1,0777);
        }

        /*$dir1 = $dir1.'/'.$hou;
        $path.='/'.$hou;
        if(!file_exists($dir1)&&!is_dir($dir1)){
            mkdir($dir1,0777);
        }*/
        $extension = pathinfo($img, PATHINFO_EXTENSION);
        $save_image = $dir1.'/'.$filename.'.'.$extension;
        if($flag==1){
            $path.='/'.$filename.'.png';
        }else if($flag==2){
            $path.='/'.$filename.'.'.$extension;
        }
        $arr[] = $save_image;
        $arr[] = $path;
        return $arr;
    }
public function IsLogin($uid,$user_name){
    $where_str = " id='{$uid}' and ( taobao_name = '{$user_name}' OR mobile = '{$user_name}' )";
    $user = M('User')->where($where_str)->find();
    if(!empty($user)){
      $flag = 1;
    }else{
      $flag = 0;
    }
    return $flag;
}
public function GetProductColorByID($id,$unihost)
    {
        $goods = M('Goods');

        //由于客户需要展示图片，所以将所有的颜色改成图片地址。
        return $goods
            ->join('INNER JOIN u_products_beubeu on left(u_goods.item_bn,8) = u_products_beubeu.uq')
            ->join('INNER JOIN u_settings on u_settings.key = u_goods.gender')
            ->join('INNER JOIN u_color on u_color.id = u_products_beubeu.color')
            ->join('INNER JOIN u_products on u_products.num_iid=u_goods.num_iid and u_products.cid=u_products_beubeu.color')
            ->field("
                    distinct u_products_beubeu.color as colorid,
                    u_products.num_iid as num_iid,
                    concat('{$unihost}',u_products.url)  as colorcode,
                    u_color.color_name as colorname,
                    left(u_goods.item_bn,8) as uq ,
                    u_settings.value as gender
                    ")
            ->where(array('u_goods.num_iid'=>$id,'u_products_beubeu.status'=>'1'))
            ->group('uq,colorid')
            ->order('u_products_beubeu.id')
            ->select();
    }
public function Qqlogin($user){
    $userm = M('User');
    $result = $userm->field('id,user_name,mobile')->where(array('mobile'=>$user['openid']))->find();
    if(!empty($result)){
        $data = array('user_name'=>$user['nickname'],'token'=>$user['access_token']);
        $userm->where(array('mobile'=>$user['openid']))->save($data);
        $arr = array('code'=>1,'uniq_user_name'=>$user['openid'],'uniq_user_id'=>$result['id']);
    }else{
        $pass = md5(123456);$time = date('Y-m-d H:i:s');$ip = 'ios';
        $data = array('user_name'=>$user['nickname'],'mobile'=>$user['openid'],'password'=>$pass,'token'=>$user['access_token'],'createtime'=>$time,'ip'=>$ip,'is_active'=>'1','login_type'=>'iosqq');
        $reid = $userm->add($data);
        if($reid && $reid>0){
          $arr = array('code'=>1,'uniq_user_name'=>$user['openid'],'uniq_user_id'=>$reid);
        }else{
          $arr = array('code'=>0,'msg'=>'登录失败');
        }
    }
    return $arr;
}
public function Sinalogin($user){
    $userm = M('User');
    $result = $userm->field('id,user_name,mobile')->where(array('mobile'=>$user['uid']))->find();
    if(!empty($result)){
        $data = array('user_name'=>$user['screen_name'],'token'=>$user['access_token']);
        $userm->where(array('mobile'=>$user['uid']))->save($data);
        $arr = array('code'=>1,'uniq_user_name'=>$user['openid'],'uniq_user_id'=>$result['id']);
    }else{
        $pass = md5(123456);$time = date('Y-m-d H:i:s');$ip = 'ios';
        $data = array('user_name'=>$user['screen_name'],'mobile'=>$user['uid'],'password'=>$pass,'token'=>$user['access_token'],'createtime'=>$time,'ip'=>$ip,'is_active'=>'1','login_type'=>'sina');
        $reid = $userm->add($data);
        if($reid && $reid>0){
            $arr = array('code'=>1,'uniq_user_name'=>$user['openid'],'uniq_user_id'=>$reid);
        }else{
            $arr = array('code'=>0,'msg'=>'登录失败');
        }
    }
}
    static public function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
        if(function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
        }else{
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice.'...' : $slice;
    }
public function very_code(){
    $str = '';
    $chars= str_repeat('0123456789',3);
    for($i=0;$i<4;$i++){
        $str.= $this->msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1,'utf-8',false);
    }
    return $str;
}
public function App_forget_pwd($mobile){
    $use = M('user');
    $mobileRow = $use->where("mobile = '{$mobile}'")->find();
    if($mobileRow){
                    //调用手机短信接口
                    $new_passwrod = randStr(6,'NUMBER');
                    $msg="我们为您重置了密码，您的新密码为：{$new_passwrod}【优衣库 虚拟试衣间】";
                    $sms_str = sms_send('2062343','66801','66801',$mobile,$msg);
                    if($sms_str){
                        $data['password'] = md5($new_passwrod);
                        $flag = $use->where("mobile='{$mobile}'")->save($data);
                        if($flag){
                            $login_arr = array('code'=>1,'msg'=>'找回成功');
                        }else{
                            $login_arr = array('code'=>0,'msg'=>'密码找回失败');
                        }
                    }else{
                        $login_arr = array('code'=>0,'msg'=>'对不起，短信发送失败');
                    }
    }else{
        $login_arr = array('code'=>0,'msg'=>'请输入正确的手机号码');
    }
    return $login_arr;
}
    public function GetTuijian($item_bn,$num_iid,$unihost,$root_dir=''){
        if($tui = S('tui'.$item_bn)){
        $result = unserialize($tui);
        }else{
        $item_bn = substr($item_bn,0,8);
        $sql = "select su.suitID,case when su.suitGenderID = 1 then 15474 when su.suitGenderID =2 then 15478 when su.suitGenderID = 3 then 15583 when su.suitGenderID = 4 then 15581 end as sex,concat(su.suitImageUrlMatch,'.400x533.png') as suitImageUrl from `u_beubeu_suits` as su left join `u_beubeu_suits_goodsdetail` as sg on sg.suitID=su.suitID where sg.item_bn like '".$item_bn."%' and su.approve_status=0 order by su.suitID desc";
        $result = M('Suits')->query($sql);
        unset($sql);
        $detail = M('BeubeuSuitsGoodsdetail');
        foreach($result as $k=>$v){
            $result[$k]['detail'] = $this->getSuitsDetail($detail,$v['suitID'],$unihost,$root_dir);
        }
        S('tui'.$item_bn,serialize($result),array('type'=>'file'));
       }
        return $result;
    }
public function addLockData($parmas){
    $uq = trim(htmlspecialchars($parmas['uq']));
    $gender = trim(htmlspecialchars($parmas['gender']));
    $pic = trim(htmlspecialchars($parmas['picurl']));
    $lockData = array('uid'=>$parmas["uniq_user_id"],'uq'=>$uq,'gender'=>$gender,'picurl'=>$pic,'createtime'=>date('Y-m-d H:i:s'));
    $re = M('AppLock')->add($lockData);
    return $re;
}
public function GetLockData($uid){
   return M('AppLock')->field('id,uq,gender,picurl')->where(array('uid'=>$uid))->select();
}
public function addFillterData($parmas,$unihost,$root_dir){
    $str = trim(htmlspecialchars($parmas['uqstr']));
    $appfitt = M('AppFitting');
    $result = $appfitt->field('id')->where(array('uid'=>$parmas["uniq_user_id"]))->find();
    if(empty($result)){
        $this->GetUrlIsud($str,$unihost,$appfitt,$root_dir);
       $fittingData = array('uid'=>$parmas["uniq_user_id"],'uqstr'=>$str,'createtime'=>date('Y-m-d H:i:s'));
       $re = $appfitt->add($fittingData);
    }else{
        $this->GetUrlIsud($str,$unihost,$appfitt,$root_dir);
       $re = $appfitt->where(array('uid'=>$parmas["uniq_user_id"]))->save(array('uqstr'=>$str,'createtime'=>date('Y-m-d H:i:s')));
    }
	return $re;
}
public function GetUrlIsud(&$str,$unihost,$appfitt,$root_dir){
    $Arrstr = explode(',',$str);
    foreach($Arrstr as $k=>$v){
        $ProductArr = explode('_',$v);
        $cid = substr($ProductArr[0],-2,2);
        $uq =  substr($ProductArr[0],0,-2);
        $sql = "select g.`isud`,p.`url` from (select `id`,`isud` from `u_goods` where `item_bn` like 'UQ136819%') as g inner join `u_products` as p on p.`goods_id`=g.`id` and p.`cid` ='02' limit 0,1";
        $result = $appfitt->query($sql);
        $before = dirname($result[0]['url']);
        $filename = pathinfo($result[0]['url'],PATHINFO_FILENAME);
        $newfilepath = $root_dir.'/'.$before.'/mac100/'.$filename.'.png';
        if(file_exists($newfilepath)){
            $result[0]['url'] = $unihost.$before.'/mac100/'.$filename.'.png';
        }else{
            $result[0]['url'] = $unihost.$result[0]['url'];
        }
        $Arrstr[$k].='_'.$result[0]['url'].'_'.$result[0]['isud'];
        unset($result);
    }
    $str = implode(',',$Arrstr);
}
public function GetFillterData($parmas){
   $find = M('AppFitting')->field('uqstr')->where(array('uid'=>$parmas['uniq_user_id']))->find();
   $arr = array();
   if(!empty($find)){
       $Arrstr = explode(',',$find['uqstr']);
       foreach($Arrstr as $k=>$v){
       $arr[] = explode('_',$v);
      }
  }
  unset($find);
  return $arr;
}
    //获取100x100的sku小图
    public function Get32Pic(&$productsValue,$root_dir,$unihost){
        foreach($productsValue as $k=>$v){
            $productsValue[$k]['uq'] = $v['uq'].$v['colorid'];
            $before = dirname($v['colorcode']);
            $filename = pathinfo($v['colorcode'],PATHINFO_FILENAME);
            $ext = pathinfo($v['colorcode'], PATHINFO_EXTENSION);
            $newfilepath = $root_dir.'/'.$before.'/mac100/'.$filename.'.png';
            if(file_exists($newfilepath)){
                $productsValue[$k]['colorcode'] = $unihost.$before.'/mac100/'.$filename.'.png';
            }else{
                $productsValue[$k]['colorcode'] = $unihost.$v['colorcode'];
            }
        }
    }
}