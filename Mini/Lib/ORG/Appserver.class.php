<?php
class Appserver{
 public function DoData($rt){
   $rt = json_decode($rt);
   $arr[] = M('Suits')->field('*')->limit('0,5')->select();
   $arr[] = $rt;
   return json_encode($arr);
   }
  public function CheckMobileExists($mobile){
      $mob = json_decode($mobile,true);
      $mac = D('Macapp');
      $flag = $mac->CkeckApp($mob['uname'],$mob['upass']);
      if(!$flag){
          $login_arr = array('code'=>0,'msg'=>'无权访问');
          return json_encode($login_arr);
      }
      $phone = trim(htmlspecialchars($mob['mobile']));
      if(!preg_match('/^(130|131|132|133|134|135|136|137|138|139|150|151|152|153|155|156|157|158|159|180|186|187|188|189)\d{8}$/',$phone)){
          $login_arr = array('code'=>0,'msg'=>'手机号格式不对');
          return json_encode($login_arr);exit;
      }
      $user = M('user')->where(array('mobile'=>$phone))->find();
      if(!empty($user)){
          $login_arr = array('code'=>0,'msg'=>'该手机号码已被注册');
          return json_encode($login_arr);exit;
      }else{
          $mobileCode = randStr(4,'NUMBER');
          $msg="您的验证码为：{$mobileCode}，请登录优衣库虚拟试衣间网站验证您的手机号码【优衣库 虚拟试衣间】";
          $sms_str = sms_send('2062343','66801','66801',$mobile,$msg);
          if($sms_str){
              session("mobileCode",$mobileCode);
              $login_arr = array('code'=>1,'mobileCode'=>$mobileCode);
          }else{
              $login_arr = array('code'=>0,'msg'=>'验证码发送失败');
          }
          return json_encode($login_arr);
      }
  }
  public function Register($user){
      $userinfo = json_decode($user,true);
      $mac = D('Macapp');
      $flag = $mac->CkeckApp($userinfo['uname'],$userinfo['upass']);
      if(!$flag){
          $login_arr = array('code'=>0,'msg'=>'无权访问');
          return json_encode($login_arr);
      }
      $mobileCode = trim(htmlspecialchars($userinfo['mobileCode']));
      $verifying_code = trim(htmlspecialchars($userinfo['very_code']));
      if($mobileCode!=$verifying_code){
             $login_arr = array('code'=>0,'msg'=>'验证码错误');
             return json_encode($login_arr);exit;
      }
      $password = trim(htmlspecialchars($userinfo['pass']));
      $repassword = trim(htmlspecialchars($userinfo['repass']));
      $passlength = strlen($password);
      if($passlength<6 || $passlength>16){
          $login_arr = array('code'=>0,'msg'=>'密码格式错误');
          return json_encode($login_arr);exit;
      }
      if($password!=$repassword){
          $login_arr = array('code'=>0,'msg'=>'两次密码不相等');
          return json_encode($login_arr);exit;
      }
      $taobao_name = trim(htmlspecialchars($userinfo['taobao_name']));
      $mobile = trim(htmlspecialchars($userinfo['mobile']));
      $data = array(
          'user_name'  =>	$taobao_name,
          'mobile'	   =>	$mobile,
          'taobao_name'=>	$taobao_name,
          'password'   =>	md5($password),
          'createtime' =>	date('Y-m-d H:i:s'),
          'is_active'  =>	1,
          'login_type'=>'app'
      );
      $res = M('user')->add($data);
      if($res){
          if($mobile){
              $user_name = $mobile;
          }
          if($taobao_name){
              $user_name = $taobao_name;
          }
          session("uniq_user_name",$user_name);
          session("uniq_user_id",$res);
          $login_arr = array('code'=>1,'msg'=>'注册成功','uniq_user_id'=>$res,'uniq_user_name'=>$user_name);
          return json_encode($login_arr);
      }else{
          $login_arr = array('code'=>0,'msg'=>'注册失败');
          return json_encode($login_arr);
      }
  }
 public function login($log){
     $login = json_decode($log,true);
     $mac = D('Macapp');
     $flag = $mac->CkeckApp($login['uname'],$login['upass']);
     if(!$flag){
         $login_arr = array('code'=>0,'msg'=>'无权访问');
         return json_encode($login_arr);
     }
     $user_name	 = trim(htmlspecialchars($login['user_name']));
     $password    = trim(htmlspecialchars($login['password']));
     $password = md5($password);
     $where_str = " ( taobao_name = '{$user_name}' OR mobile = '{$user_name}' ) AND password = '{$password}' ";
     $user = M('user')->where($where_str)->find();
     if($user){
         if(!empty($user['mobile'])){
             $user_name = $user['mobile'];
         }
         if(!empty($user['taobao_name'])){
             $user_name = $user['taobao_name'];
         }
         $login_arr = array('code'=>1,'msg'=>'登录成功！','uniq_user_id'=>$user['id'],'uniq_user_name'=>$user_name);
     }else{
         $login_arr = array('code'=>0,'msg'=>'请输入正确的用户名或密码');
     }
        return json_encode($login_arr);exit;
 }
    public function CreateVery($cpwd){
        $chpwd = json_decode($cpwd,true);
        $mac = D('Macapp');
        $flag = $mac->CkeckApp($chpwd['uname'],$chpwd['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $very = $mac->very_code();
        $arr = array('very'=>$very,'verycode'=>md5($very));
        return json_encode($arr);
    }
    public function forget_pwd($cpwd){
        $chpwd = json_decode($cpwd,true);
        $mac = D('Macapp');
        $flag = $mac->CkeckApp($chpwd['uname'],$chpwd['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $verify = trim(htmlspecialchars($cpwd['verycode']));
        $uverify = trim(htmlspecialchars($cpwd['uverify']));
        if($verify!=md5($uverify)){
            $login_arr = array('code'=>0,'msg'=>'请填写正确的验证码');
            return json_encode($login_arr);
        }
        $mobile = trim(htmlspecialchars($cpwd['mobile']));
        $arr = $mac->App_forget_pwd($mobile);
        return json_encode($arr);
    }
    public function change_pwd($cpwd){
        $chpwd = json_decode($cpwd,true);
        $mac = D('Macapp');
        $flag = $mac->CkeckApp($chpwd['uname'],$chpwd['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $chpwd["uniq_user_id"] = trim(htmlspecialchars($chpwd["uniq_user_id"]));
        $chpwd["uniq_user_name"] = trim(htmlspecialchars($chpwd["uniq_user_name"]));
        $islo = $mac->IsLogin($chpwd["uniq_user_id"],$chpwd["uniq_user_name"]);
        if(!$islo){
            return json_encode(array('code'=>0,'msg'=>'此用户不存在'));
        }
        $old_password	 = trim(htmlspecialchars($chpwd['old_password']));
        $new_password    = trim(htmlspecialchars($chpwd['new_password']));
        $passlength = strlen($new_password);
        if($passlength<6 || $passlength>16){
            $login_arr = array('code'=>0,'msg'=>'密码格式错误');
            return json_encode($login_arr);
        }
        $user_name = session("uniq_user_name");
        $where_str = " ( taobao_name = '{$user_name}' OR mobile = '{$user_name}' ) AND password = '{$old_password}' ";
        $user = M('user')->where($where_str)->find();
        if($user){
            if($user['login_type']=='app'){
                $data['password'] = md5($new_password);
                $flag = M('user')->where("taobao_name='{$user_name}' OR mobile = '{$user_name}' ")->save($data);
                if($flag){
                    $login_arr = array('code'=>1,'msg'=>'修改成功');
                }else{
                    $login_arr = array('code'=>0,'msg'=>'请输入正确的旧密码');
                }
            }else{
                $login_arr = array('code'=>0,'msg'=>'第三方用户不能修改密码');
            }
        }else{
            $login_arr = array('code'=>0,'msg'=>'请输入正确的旧密码');
        }
        return json_encode($login_arr);exit;
    }
public function GetUserInfo($udata){
    $use = json_decode($udata,true);
    $mac = D('Macapp');
    $flag = $mac->CkeckApp($use['uname'],$use['upass']);
    if(!$flag){
        $login_arr = array('code'=>0,'msg'=>'无权访问');
        return json_encode($login_arr);
    }
    $use["uniq_user_id"] = trim(htmlspecialchars($use["uniq_user_id"]));
    $use["uniq_user_name"] = trim(htmlspecialchars($use["uniq_user_name"]));
    $islo = $mac->IsLogin($use["uniq_user_id"],$use["uniq_user_name"]);
    if(!$islo){
        return json_encode(array('code'=>0,'msg'=>'此用户不存在'));
    }
    $uid = $use["uniq_user_id"];
    if($uid){
        $result = M('User')->field('user_name,mobile,taobao_name,login_type')->where(array('id'=>$uid))->find();
        if(!empty($result)){
            $login_arr['code'] = 1;
            $login_arr['result'] = $result;
        }else{
            $login_arr['code'] = 0;
            $login_arr['msg'] = '没有此用户';
        }
    }else{
        $login_arr['code'] = 0;
        $login_arr['msg'] = '没有登录';
    }
    return json_encode($login_arr);exit;
}
    public function changeTaoName($udata){
        $use = json_decode($udata,true);
        $mac = D('Macapp');
        $flag = $mac->CkeckApp($use['uname'],$use['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $use["uniq_user_id"] = trim(htmlspecialchars($use["uniq_user_id"]));
        $use["uniq_user_name"] = trim(htmlspecialchars($use["uniq_user_name"]));
        $islo = $mac->IsLogin($use["uniq_user_id"],$use["uniq_user_name"]);
        if(!$islo){
            return json_encode(array('code'=>0,'msg'=>'此用户不存在'));
        }
        $uid = $use["uniq_user_id"];
        if($uid){
            $user = M('User');
            $tname = trim(htmlspecialchars($use['taobao_name']));
            $result = $user->field('mobile,taobao_name,login_type')->where(array('id'=>$uid))->find();
            if(!empty($result)){
                if($result['login_type']=='app'){
                    $map = array('user_name'=>$tname,'taobao_name'=>$tname);
                }
                $re = $user->where(array('id'=>$uid))->save($map);
                if($re){
                    $arr['code'] = 1;
                    $arr['uniq_user_name'] = $tname;
                    $arr['msg'] = '编辑成功';
                }else{
                    $arr['code'] = 0;
                    $arr['msg'] = '关联淘宝登录名没有变化';
                }
            }else{
                $arr['code'] = 0;
                $arr['msg'] = '用户信息不匹配';
            }
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '没有登录';
        }
        return json_encode($arr);exit;
    }
    public function Addlea($udata){
        $use = json_decode($udata,true);
        $mac = D('Macapp');
        $flag = $mac->CkeckApp($use['uname'],$use['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $ip = 'app';
        $catid = trim(htmlspecialchars($use['cate']));
        $content = trim(htmlspecialchars($use['con']));
        $data = array('catid'=>$catid,
            'content'=>$content,
            'ip'=>$ip,
            'createtime'=>date('Y-m-d H:i:s'));
        $res = M('Leave')->add($data);
        if($res){
            $arr['code'] = 0;
            $arr['msg'] = '感谢您的反馈';
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '添加失败';
        }
        return json_encode($arr);exit;
    }
    public function GetCollData($coll){
        $ucoll = json_decode($coll,true);
        $mac = D('Macapp');
        $flag = $mac->CkeckApp($ucoll['uname'],$ucoll['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $unihost = 'http://'.$_SERVER['HTTP_HOST'].'/';
        $ucoll["uniq_user_id"] = trim(htmlspecialchars($ucoll["uniq_user_id"]));
        $ucoll["uniq_user_name"] = trim(htmlspecialchars($ucoll["uniq_user_name"]));
        $islo = $mac->IsLogin($ucoll["uniq_user_id"],$ucoll["uniq_user_name"]);
        if(!$islo){
           return json_encode(array('code'=>0,'msg'=>'此用户不存在'));
        }
        $where['uid'] = $ucoll["uniq_user_id"];
        if(empty($where['uid'])){
            return json_encode(array('code'=>0,'msg'=>'没有登录'));
        }
        $defaultResult = $mac->getBenebnColl($where,$unihost);
        /*if($page==1){
            //获取用户信息
            $userinfo = $recomodel->getUserInfo();
            $arr['uname'] = $userinfo[0];
            $arr['collflag'] = $userinfo[1];
            $arr['collcount'] = $userinfo[2];
        }*/
        return json_encode($defaultResult);exit;
    }
    public function SexToStyle($data){
        $suit = json_decode($data,true);
        $mac = D('Macapp');
        $flag = $mac->CkeckApp($suit['uname'],$suit['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $unihost = 'http://'.$_SERVER['HTTP_HOST'].'/';
        $sid = trim(htmlspecialchars($suit['sid']));
        $fid = trim(htmlspecialchars($suit['fid']));
        $page = trim(htmlspecialchars($suit['page']));
        $sid = $sid?$sid:1;
        $fid = $fid?$fid:0;
        $page = $page?$page:1;
        $page_num = 4;
        $start = ($page-1)*$page_num;
        if(!empty($fid)){
            $defaultwhere['suitStyleID'] = $fid;
        }
        switch($sid){
            case 3 :
                $defaultwhere['suitGenderID'] = array('exp','IN(3,4)');
                $defaultwhere['approve_status'] = 0;
                $defaultResult = $mac->getBeubeu($defaultwhere,$page,$page_num,$start,$unihost);
                break;
            case 1 :
            case 2 :
                $defaultwhere['suitGenderID'] = $sid;
                $defaultwhere['approve_status'] = 0;
                $defaultResult = $mac->getBeubeu($defaultwhere,$page,$page_num,$start,$unihost);
                break;
        }
        $arr['page'] = $defaultResult['page'];
        $arr['count'] = $defaultResult['count'];
        $arr['def'] = $defaultResult['result'];
        return json_encode($arr);exit;
   }
   public function GetCusCate($data){
       $cus = json_decode($data,true);
       $mac = D('Macapp');
       $flag = $mac->CkeckApp($cus['uname'],$cus['upass']);
       if(!$flag){
           $login_arr = array('code'=>0,'msg'=>'无权访问');
           return json_encode($login_arr);
       }
       $sid = trim(htmlspecialchars($cus['sid']));
       $sid = $sid?$sid:0;
       if(S('midsid'.$sid)){
           $arr = unserialize(S('midsid'.$sid));
       }else{
           if($sid!=4 && $sid!=0){
               $where['gender'] = $sid;
               $where['selected'] = 1;
               $where['shortName'] = array('neq','');
               $where['isshow']= 0;
               $ucuslist  = $mac->getCusData2($where);//上装
               $arr['u'] = $ucuslist;
               $arr['style'] = $mac->getsstyle($sid);
           }else if($sid==4){
               $where['gender'] = array('exp','in(4,5)');
               $where['selected'] = 1;
               $where['shortName'] = array('neq','');
               $where['isshow']= 0;
               $babylist  = $mac->getCusData2($where);//上装
               $arr['u'] = $babylist;
           }else if($sid==0){
               $where = array('selected'=>1,'shortName'=>array('neq',''),'isshow'=>0);
               $ucuslist = $mac->getCateList2($where);
               $arr['u'] = $ucuslist;
           }
           S('midsid'.$sid,serialize($arr),array('type'=>'file'));
       }
       return json_encode($arr);
   }
   public function GetGoods($data){
    $parmas = json_decode($data,true);
    $mac = D('Macapp');
    $flag = $mac->CkeckApp($parmas['uname'],$parmas['upass']);
    if(!$flag){
           $login_arr = array('code'=>0,'msg'=>'无权访问');
           return json_encode($login_arr);
    }
    $unihost = 'http://'.$_SERVER['HTTP_HOST'].'/';
    $sid = trim(htmlspecialchars($parmas['sid']));
    $fid = trim(htmlspecialchars($parmas['fid']));
    $zid = trim(htmlspecialchars($parmas['zid']));
    $page = trim(htmlspecialchars($parmas['page']));
    $lid = trim(htmlspecialchars($parmas['lid']));
    $bid = trim(htmlspecialchars($parmas['bid']));
    $kid = trim(htmlspecialchars($parmas['kid']));
    $keyword = trim(htmlspecialchars($parmas['keyword']));
    $oid = trim(htmlspecialchars($parmas['oid']));
    $lid = $lid?$lid:0;
    $sid = $sid?$sid:0;
    $bid = $bid?$bid:0;
    $fid = $fid?$fid:0;
    $zid = $zid?$zid:0;
    $kid = $kid?$kid:0;
    $page = $page?$page:1;
    $oid = $oid?$oid:2;
    $goodtag = M();
    $windex = D('Windex');
    $page_num = 25;
    $start = ($page-1)*$page_num;
    $ostr = $windex->getOrderStr($oid);
    $productSyn = D('ProductSyn');
    $uid = trim(htmlspecialchars($parmas["uniq_user_id"]));
    $love = M('Love');
    $buy = M('Buy');
    if($lid==1 && $bid==1){
        $sql = "select count(bg.num_iid) as co
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
) bl group by bl.num_iid) li
where bg.num_iid = li.num_iid";
        $gcount = $mac->SqlCount($sql,$goodtag,$page_num);
        unset($sql);
        $sql = "
select bg.num_iid,li.loveid,li.buyid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url,bg.list_time
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
) bl group by bl.num_iid) li
where bg.num_iid = li.num_iid order by ".$ostr." limit ".$start.",".$page_num;
        $result = $goodtag->query($sql);
        if(!empty($result)){
            foreach($result as $k1=>$v1){
                $result[$k1]['pic_url'] = $unihost.$v1['pic_url'];
                $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                $result[$k1]['products'] = $mac->GetProductColorByID($v1['num_iid'],$unihost);
                $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
            }
        }else{
            $result = array();
        }
    }else if($lid==1 && $bid!=1){
        $sql = "select count(bg.num_iid) as co
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
) bl group by bl.num_iid) li
where bg.num_iid = li.num_iid and li.loveid is not null";
        $gcount = $mac->SqlCount($sql,$goodtag,$page_num);
        unset($sql);
        $sql = "
select bg.num_iid,li.loveid,li.buyid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url,bg.list_time
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
) bl group by bl.num_iid) li
where bg.num_iid = li.num_iid and li.loveid is not null order by ".$ostr." limit ".$start.",".$page_num;
        $result = $goodtag->query($sql);
        if(!empty($result)){
            foreach($result as $k1=>$v1){
                $result[$k1]['pic_url'] = $unihost.$v1['pic_url'];
                $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                $result[$k1]['products'] = $mac->GetProductColorByID($v1['num_iid'],$unihost);
                $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
            }
        }else{
            $result = array();
        }
    }else if($bid==1 && $lid!=1){
        $sql = "select count(bg.num_iid) as co
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
)bl group by bl.num_iid)li
where bg.num_iid = li.num_iid and li.buyid is not null";
        $gcount = $mac->SqlCount($sql,$goodtag,$page_num);
        unset($sql);
        $sql = "
select bg.num_iid,li.loveid,li.buyid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url,bg.list_time
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
)bl group by bl.num_iid)li
where bg.num_iid = li.num_iid and li.buyid is not null order by ".$ostr." limit ".$start.",".$page_num;
        $result = $goodtag->query($sql);
        if(!empty($result)){
            foreach($result as $k1=>$v1){
                $result[$k1]['pic_url'] = $unihost.$v1['pic_url'];
                $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                $result[$k1]['products'] = $mac->GetProductColorByID($v1['num_iid'],$unihost);
                $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
            }
        }else{
            $result = array();
        }
    }else if($kid>0){
        if($keyword){
            $isair = is_int(strpos($keyword,' '));
            if($isair){
                $keywordArr = explode(' ',$keyword);
                $newkeyword = implode('%',$keywordArr);
            }else{
                $newkeyword =  $keyword;
            }
            $liketitle = "and bg.title like '%{$newkeyword}%'";
        }else{
            $liketitle = '';
        }
        if(!empty($uid)){
            $wherelb = " left join (select id,num_iid from `u_love` where uid={$uid}) as lo on lo.num_iid=ol.num_iid left join (select id,num_iid from `u_buy` where uid={$uid}) bu on bu.num_iid=ol.num_iid";
            $fieldlb = ",lo.id as loveid,bu.id as buyid";
        }
        $sql = "select count(bg.num_iid) as co from `u_beubeu_goods` as bg inner join `u_goodtag` as g on g.num_iid=bg.num_iid  where  1 {$liketitle} group by g.num_iid";
        $gcount = $mac->SqlCount($sql,$goodtag,$page_num);
        unset($sql);
        $sql = "select ol.*{$fieldlb} from (select bg.num_iid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url,bg.list_time from `u_beubeu_goods` as bg inner join `u_goodtag` as g on g.num_iid=bg.num_iid  where  1 {$liketitle} group by g.num_iid order by {$ostr} limit {$start},{$page_num}) as ol{$wherelb}";
        $result = M('BeubeuGoods')->query($sql);
        if(!empty($result)){
            foreach($result as $k1=>$v1){
                $result[$k1]['pic_url'] = $unihost.$v1['pic_url'];
                $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                $result[$k1]['products'] = $mac->GetProductColorByID($v1['num_iid'],$unihost);
                $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
            }
        }else{
            $result = array();
        }
    }else{
        $where = '';
        switch($sid){
            case 1 :
            case 2 :
                $where.=" and g.gtype='".$sid."'";
                break;
            case 3 :
                $where.=" and g.gtype in ('3','4')";
                break;
            case 4 :
                $where.=" and g.gtype='5'";
                break;
        }
        if(!empty($fid) && $fid!='all'){
            $where.=" and g.ftag_id='".$fid."'";
        }
        if($zid && !empty($zid)){
            $is_g = is_int(strpos($zid,'_'));
            if(!$is_g){
                $zid = $zid.'_';
            }
            $cstr = '';
            $ccid = explode('_',$zid);
            $ccid = array_unique($ccid);
            $gzid = $ccid[0];
            foreach($ccid as $k=>$v){
                if($v){
                    $cstr.=$v.',';
                }
            }
            $cstr = rtrim($cstr,',');
            $catewhere = " and cg.cateID in (".$cstr.")";
        }
            $ordr = "order by ";
        if(!empty($uid)){
            $wherelb = " left join (select id,num_iid from `u_love` where uid={$uid}) as lo on lo.num_iid=al.num_iid left join (select id,num_iid from `u_buy` where uid={$uid}) bu on bu.num_iid=al.num_iid";
            $fieldlb = ",lo.id as loveid,bu.id as buyid";
        }
        if($sid!=4){
            $goodstable = '`u_beubeu_goods`';
            if(!empty($zid)){
                $sellsex = $windex->getSellCateSex($gzid);
                if($sellsex['gender']==4 || $sellsex['gender']==5){
                    $goodstable = '`u_goods`';
                }
            }
        }else{
            $goodstable = '`u_goods`';
        }
        if($oid==1){
            $mwhere = " and bg.approve_status='onsale'";
        }
        $wheret = '';
        if(!empty($sid)){
            switch($sid){
                case 1 :
                case 2 :
                    $wheret.=" and g.gtype='".$sid."'";
                    break;
                case 3 :
                    $wheret.=" and g.gtype in ('3','4')";
                    break;
                case 4 :
                    $wheret.=" and g.gtype='5'";
                    break;
            }
        }
        if(!empty($fid) && $fid!='all'){
            $wheret.=" and g.ftag_id='".$fid."'";
        }
        $sql = "select count(bg.num_iid) as co from {$goodstable} as bg where EXISTS(select 1 from `u_goodtag` as g where bg.id=g.good_id{$wheret}) and exists(select 1 from `u_catesgoods` as cg where cg.num_iid=bg.num_iid{$catewhere}) {$mwhere}";
        $gcount = $mac->SqlCount($sql,$goodtag,$page_num);
        unset($sql);
        if(!empty($uid)){
            $sql = "select al.*{$fieldlb} from (select bg.num_iid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url,bg.list_time from {$goodstable} as bg where EXISTS(select 1 from `u_goodtag` as g where bg.id=g.good_id{$wheret}) and exists(select 1 from `u_catesgoods` as cg where cg.num_iid=bg.num_iid{$catewhere}) {$mwhere} {$ordr}{$ostr},bg.id desc limit ".$start.",".$page_num.") as al ".$wherelb;
        }else{
            $sql = "select bg.num_iid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url,bg.list_time from {$goodstable} as bg where EXISTS(select 1 from `u_goodtag` as g where bg.id=g.good_id{$wheret}) and exists(select 1 from `u_catesgoods` as cg where cg.num_iid=bg.num_iid{$catewhere}) {$mwhere} {$ordr}{$ostr},bg.id desc
limit ".$start.",".$page_num;
        }
        $result = $goodtag->query($sql);
        if(!empty($result)){
            foreach($result as $k1=>$v1){
                $result[$k1]['pic_url'] = $unihost.$v1['pic_url'];
                $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                $result[$k1]['products'] = $mac->GetProductColorByID($v1['num_iid'],$unihost);
                $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
            }
        }else{
            $result = array();
        }
    }
       if(!empty($result)){
           $returnArr = array('code'=>1,'page'=>$page+1,'count'=>$gcount,'da'=>$result);
       }else{
           $returnArr = array('code'=>0,'msg'=>'没有数据');
       }
       return json_encode($returnArr);
   }
   public function AddBeuColl($data){
       $parmas = json_decode($data,true);
       $mac = D('Macapp');
       $flag = $mac->CkeckApp($parmas['uname'],$parmas['upass']);
       if(!$flag){
           $login_arr = array('code'=>0,'msg'=>'无权访问');
           return json_encode($login_arr);
       }
       $parmas["uniq_user_id"] = trim(htmlspecialchars($parmas["uniq_user_id"]));
       $parmas["uniq_user_name"] = trim(htmlspecialchars($parmas["uniq_user_name"]));
       $islo = $mac->IsLogin($parmas["uniq_user_id"],$parmas["uniq_user_name"]);
       if(!$islo){
           return json_encode(array('code'=>0,'msg'=>'此用户不存在'));
       }
       $uid = $parmas["uniq_user_id"];
       if($uid){
           $headpic = trim(htmlspecialchars($parmas['headpic']));
           $bodypic = trim(htmlspecialchars($parmas['bodypic']));
           $shoespic = trim(htmlspecialchars($parmas['shoespic']));
           $clothespic = trim(htmlspecialchars($parmas['pic_match']));
           $gender = trim(htmlspecialchars($parmas['gender']));
           $suitid = trim(htmlspecialchars($parmas['suitid']));
           $uq = trim(htmlspecialchars($parmas['uq']));
           $num_iid = $mac->getCollNumm_iid($uq);
           $beuben = M('BeubeuCollection');
           if(!empty($suitid) && !empty($clothespic)){
               $count = $beuben->field('id')->where(array('uid'=>$uid))->count();
               if($count<50){
                   $time = date('Y-m-d H:i:s');
                   $data = array('uid'=>$uid,
                       'gender'=>$gender,
                       'suitID'=>$suitid,
                       'pic_head'=>$headpic,
                       'pic_body'=>$bodypic,
                       'pic_shoes'=>$shoespic,
                       'pic_clothes'=>$clothespic,
                       'createtime'=>$time);
                   $res = $beuben->add($data);
                   if($res>0){
                       $insql = "insert into `u_beubeu_coll_goods` (`bcid`,`num_iid`) values ";
                       $str = '';
                       if(!empty($num_iid)){
                           foreach($num_iid as $k=>$v){
                               if($v){
                                   $str.="('".$res."','".$v."'),";
                               }
                           }
                           $str = rtrim($str,',');
                           $insql.=$str;
                           $beuben->query($insql);
                       }
                       $arr['code'] = 1;
                       $arr['msg'] = '已收藏至您的个人衣柜';
                   }else{
                       $arr['code'] = 0;
                       $arr['msg'] = '收藏失败';
                   }
               }else{
                   $arr['code'] = 0;
                   $arr['msg'] = '一个用户最多只能收藏50套';
               }
           }else{
               $arr['code'] = 0;
               $arr['msg'] = '请先搭配';
           }
       }else{
           $arr['code'] = 0;
           $arr['msg'] = '登录之后即可收藏此套搭配';
       }
       return json_encode($arr);
   }
    public function Addlove($data){
        $parmas = json_decode($data,true);
        $mac = D('Macapp');
        $flag = $mac->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $parmas["uniq_user_id"] = trim(htmlspecialchars($parmas["uniq_user_id"]));
        $parmas["uniq_user_name"] = trim(htmlspecialchars($parmas["uniq_user_name"]));
        $islo = $mac->IsLogin($parmas["uniq_user_id"],$parmas["uniq_user_name"]);
        if(!$islo){
            return json_encode(array('code'=>0,'msg'=>'此用户不存在'));
        }
        $id = trim(htmlspecialchars($parmas['id']));
        $flag = trim(htmlspecialchars($parmas['flag']));
        $isdel = trim(htmlspecialchars($parmas['isdel']));
        if($id>0){
            $uid = $parmas["uniq_user_id"];
            if(!empty($uid)){
                if($flag==1){
                    $love = M('Love');
                    $time = date('Y-m-d H:i:s');
                    if($isdel==1){
                        $cresult = $love->field('id')->where(array('num_iid'=>$id,'uid'=>$uid))->find();
                        if(empty($cresult)){
                            $love->add(array('num_iid'=>$id,'uid'=>$uid,'cratetime'=>$time));
                        }
                    }else if($isdel==0){
                        $love->where(array('num_iid'=>$id,'uid'=>$uid))->delete();
                    }
                }else if($flag==2){
                    $buy = M('Buy');
                    $time = date('Y-m-d H:i:s');
                    if($isdel==1){
                        $cresult = $buy->field('id')->where(array('num_iid'=>$id,'uid'=>$uid))->find();
                        if(empty($cresult)){
                            $buy->add(array('num_iid'=>$id,'uid'=>$uid,'cratetime'=>$time));
                        }
                    }else if($isdel==0){
                        $buy->where(array('num_iid'=>$id,'uid'=>$uid))->delete();
                    }
                }
            }else{
                $returnArr = array('code'=>0,'msg'=>'没有登录');
                return json_encode($returnArr);
            }
        }
    }
    public function loginout(){
        $_SESSION=array();
        if(isset($_COOKIE[session_name()])){
            setCookie(session_name(), '', time()-100, '/');
        }
        cookie('uniq_user_name',null);
        cookie('uniq_user_id',null);
        session_destroy();
        $arr = array('code'=>1);
        return json_encode($arr);
    }
    public function DelBeuColl($data){
        $parmas = json_decode($data,true);
        $mac = D('Macapp');
        $flag = $mac->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $parmas["uniq_user_id"] = trim(htmlspecialchars($parmas["uniq_user_id"]));
        $parmas["uniq_user_name"] = trim(htmlspecialchars($parmas["uniq_user_name"]));
        $islo = $mac->IsLogin($parmas["uniq_user_id"],$parmas["uniq_user_name"]);
        if(!$islo){
            return json_encode(array('code'=>0,'msg'=>'此用户不存在'));
        }
        $id = trim(htmlspecialchars($parmas['id']));//百衣收藏id
        $uid = $parmas["uniq_user_id"];
        if(!empty($uid)){
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
        return json_encode($arr);
    }
public function GetSexStyle($data){
    $cus = json_decode($data,true);
    $mac = D('Macapp');
    $flag = $mac->CkeckApp($cus['uname'],$cus['upass']);
    if(!$flag){
        $login_arr = array('code'=>0,'msg'=>'无权访问');
        return json_encode($login_arr);
    }
    $sid = trim(htmlspecialchars($cus['sid']));
    $sid = $sid?$sid:0;
    $res = $mac->getsstyle($sid);
    return json_encode($res);
}
public function getJackNumiid($data){
    $cus = json_decode($data,true);
    $mac = D('Macapp');
    $flag = $mac->CkeckApp($cus['uname'],$cus['upass']);
    if(!$flag){
        $login_arr = array('code'=>0,'msg'=>'无权访问');
        return json_encode($login_arr);
    }
    $uq = trim(htmlspecialchars($cus['uq']));
    $uq = substr($uq,0,8);
    $res = $mac->getUqNum($uq);
   return json_encode($res);
}
public function SharePic($data){
    $cus = json_decode($data,true);
    $mac = D('Macapp');
    $flag = $mac->CkeckApp($cus['uname'],$cus['upass']);
    if(!$flag){
        $login_arr = array('code'=>0,'msg'=>'无权访问');
        return json_encode($login_arr);
    }
    $bbody = trim(htmlspecialchars($cus['bbody']));//身体
    $bshose = trim(htmlspecialchars($cus['bshose']));
    $bclose = trim(htmlspecialchars($cus['bclose']));
    $bhead = trim(htmlspecialchars($cus['bhead']));
    $root_dir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
    $url = $mac->AppShare($bbody,$bshose,$bclose,$bhead,$root_dir);
    if($url){
      $arr['url'] = $url;
    }else{
      $arr['url'] = '';
    }
    return json_encode($arr);
}
public function SinaQq($data){
    $cus = json_decode($data,true);
    $mac = D('Macapp');
    $flag = $mac->CkeckApp($cus['uname'],$cus['upass']);
    if(!$flag){
        $login_arr = array('code'=>0,'msg'=>'无权访问');
        return json_encode($login_arr);
    }
    $cus['type'] = trim(htmlspecialchars($cus['type']));
    if($cus['type']==1){
        $cus['openid'] = trim(htmlspecialchars($cus['openid']));
        $cus['nickname'] = trim(htmlspecialchars($cus['nickname']));
        $cus['access_token'] = trim(htmlspecialchars($cus['access_token']));
        $arr = $mac->Qqlogin($cus);
        unset($cus);
    }else if($cus['type']==2){
        $cus['uid'] = trim(htmlspecialchars($cus['uid']));
        $cus['screen_name'] = trim(htmlspecialchars($cus['screen_name']));
        $cus['access_token'] = trim(htmlspecialchars($cus['access_token']));
        $arr = $mac->Sinalogin($cus);
        unset($cus);
    }
    return json_encode($arr);
}
}