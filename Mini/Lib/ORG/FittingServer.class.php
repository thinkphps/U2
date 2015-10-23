<?php
class FittingServer{
    public function GetCates($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $arr = array();
        $arr = $d3model->GetSellercats();
        return json_encode($arr);
    }
    public function GetCateGoods($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $page = trim(htmlspecialchars($parmas['page']));
        $page = $page>0?$page:1;
        $arr = $d3model->GetCateGoodData($page);
        return json_encode($arr);
    }
    public function GetGoods($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $page = trim(htmlspecialchars($parmas['page']));
        $modified = trim(htmlspecialchars($parmas['modified']));
        $page = $page>0?$page:1;
        $modified = $modified?$modified:'';
        if(!empty($modified)){
            if(!preg_match("/^\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$/s",$modified)){
                $login_arr = array('code'=>0,'msg'=>'时间格式不对');
                return json_encode($login_arr);
            }
        }
        $where['page'] = $page;
        $where['modified'] = $modified;
        $arr = $d3model->GetGoodsData($where);
        return json_encode($arr);
    }
    public function GetSku($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $page = trim(htmlspecialchars($parmas['page']));
        $page = $page>0?$page:1;
        $arr = $d3model->GetSkuData($page);
        return json_encode($arr);
    }
    public function GetStatusValue($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        $type = trim(htmlspecialchars($parmas['type']));
        $arrType = array('d3good','d3sku','d3cg');
        if(!in_array($type,$arrType)){
            $login_arr = array('code'=>0,'msg'=>'类型不对');
            return json_encode($login_arr);
        }
        $arr = $d3model->GetRedisValue($type);
        return json_encode($arr);
    }
    public function AddSyLog($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        if(!empty($parmas['data'])){
            $re = $d3model->Add3dlog($parmas['data']);
            unset($parmas);
            if($re==0){
               return json_encode(array('code'=>1,'msg'=>'完成'));
            }else{
               return json_encode(array('code'=>0,'msg'=>'参数错误'));
            }
        }else{
            return json_encode(array('code'=>0,'msg'=>'没数据'));
        }
    }
    public function TotalDayLog($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $flag = $d3model->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return json_encode($login_arr);
        }
        if(!empty($parmas['data'])){
            $re = $d3model->AddTotalLog($parmas['data']);
            unset($parmas);
            if($re==0){
                return json_encode(array('code'=>1,'msg'=>'完成'));
            }else{
                return json_encode(array('code'=>0,'msg'=>'参数错误'));
            }
        }else{
            return json_encode(array('code'=>0,'msg'=>'没数据'));
        }
    }
    public function CheckMobileExists($mobile){
        $mob = json_decode($mobile,true);
        $mac = D('Fitting');
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
            $sms_str = sms_send('2062343','66801','66801',$phone,$msg);
            if($sms_str){
                $login_arr = array('code'=>1,'mobileCode'=>$mobileCode);
            }else{
                $login_arr = array('code'=>0,'msg'=>'验证码发送失败');
            }
            return json_encode($login_arr);
        }
    }
    public function Register($user){
        $userinfo = json_decode($user,true);
        $mac = D('Fitting');
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
            'login_type'=>'4d'
        );
        $res = M('user')->add($data);
        if($res){
            if($mobile){
                $user_name = $mobile;
            }
            if($taobao_name){
                $user_name = $taobao_name;
            }
            $login_arr = array('code'=>1,'msg'=>'注册成功','uniq_user_id'=>$res,'uniq_user_name'=>$user_name);
            return json_encode($login_arr);
        }else{
            $login_arr = array('code'=>0,'msg'=>'注册失败');
            return json_encode($login_arr);
        }
    }
    public function login($log){
        $login = json_decode($log,true);
        $mac = D('Fitting');
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
        return json_encode($login_arr);
    }
    public function change_pwd($cpwd){
        $chpwd = json_decode($cpwd,true);
        $mac = D('Fitting');
        $Isper = $this->IsPermissions($mac,$chpwd);
        if(!empty($Isper)){
            return json_encode($Isper);
        }
        $old_password	 = trim(htmlspecialchars($chpwd['old_password']));
        $new_password    = trim(htmlspecialchars($chpwd['new_password']));
        $passlength = strlen($new_password);
        if($passlength<6 || $passlength>16){
            $login_arr = array('code'=>0,'msg'=>'密码格式错误');
            return json_encode($login_arr);
        }
        $where_str = array('id'=>$chpwd['uniq_user_id'],'mobile'=>$chpwd['uniq_user_name'],'password'=>md5($old_password));
        $user = M('user')->where($where_str)->find();
        if($user){
            if($user['login_type']=='app' || $user['login_type']=='normal'){
                $data['password'] = md5($new_password);
                $flag = M('user')->where(array('id'=>$chpwd['uniq_user_id']))->save($data);
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
        return json_encode($login_arr);
    }
    public function GetUserInfo($udata){
        $use = json_decode($udata,true);
        $mac = D('Fitting');
        $Isper = $this->IsPermissions($mac,$use);
        if(!empty($Isper)){
            return json_encode($Isper);
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
        return json_encode($login_arr);
    }
    public function changeTaoName($udata){
        $use = json_decode($udata,true);
        $mac = D('Fitting');
        $Isper = $this->IsPermissions($mac,$use);
        if(!empty($Isper)){
            return json_encode($Isper);
        }
        $uid = $use["uniq_user_id"];
        if($uid){
            $user = M('User');
            $tname = trim(htmlspecialchars($use['taobao_name']));
            $result = $user->field('mobile,taobao_name,login_type')->where(array('id'=>$uid))->find();
            if(!empty($result)){
                $map = array('taobao_name'=>$tname);
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
        return json_encode($arr);
    }
    public function App3dCollection($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $Isper = $this->IsPermissions($d3model,$parmas);
        if(!empty($Isper)){
            return json_encode($Isper);
        }
        $type = intval(trim(htmlspecialchars($parmas['type'])));
        if($type==1){
            $re = $d3model->AddAppCollection($parmas['data'],$parmas["uniq_user_id"]);
        }else if($type==2){
            $re = $d3model->GetAppCollection($parmas['uniq_user_id']);
        }
        return json_encode($re);
    }
    public function App3dFigure($data){
        $parmas = json_decode($data,true);
        $d3model = D('Fitting');
        $Isper = $this->IsPermissions($d3model,$parmas);
        if(!empty($Isper)){
            return json_encode($Isper);
        }
        $type = intval(trim(htmlspecialchars($parmas['type'])));
        if($type==1){
            $re = $d3model->AddAppFigure($parmas['data'],$parmas["uniq_user_id"]);
        }else if($type==2){
            $re = $d3model->GetAppFigure($parmas['uniq_user_id']);
        }
        return json_encode($re);
    }
    public function IsPermissions($mac,&$parmas){
        $flag = $mac->CkeckApp($parmas['uname'],$parmas['upass']);
        if(!$flag){
            $login_arr = array('code'=>0,'msg'=>'无权访问');
            return $login_arr;
        }
        $parmas["uniq_user_id"] = trim(htmlspecialchars($parmas["uniq_user_id"]));
        $parmas["uniq_user_name"] = trim(htmlspecialchars($parmas["uniq_user_name"]));
        $islo = $mac->IsLogin($parmas["uniq_user_id"],$parmas["uniq_user_name"]);
        if(!$islo){
            return array('code'=>0,'msg'=>'此用户不存在');
        }
    }
}