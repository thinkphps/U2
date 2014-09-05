<?php
/*
 * 作者:kimi,时间:2014-08-18,第三方登录文件
 */
class OpenapiAction extends Action{

    public function _initialize(){
        import("@.ORG.Sinaapi");
        import("@.ORG.Qqapi");
    }
    public function sinaLogin(){
        //新浪登录
            $uid = session("uniq_user_id");
            if($uid && $uid>0){
                $this->redirect('Index/index');
                exit;
            }else{
                $sina = new Sinaapi(C('SINAAPPID'),C('SINAAPPKEY'));
                $authurl = $sina->fetchAuthUrl(C('SINACALLBACK'));
                header('Location: '.$authurl);
           }
        }
    public function qqLogin(){
        //qq登录
        $qq = new Qqapi(C('QQAPPID'),C('QQAPPKEY'));
        $qqurl = $qq->fetchLoginUrl(C('QQCALLBACK'));
        header('Location: '.$qqurl);
        }

    public function qqCallback(){
        $qq = new Qqapi(C('QQAPPID'),C('QQAPPKEY'));
        $accessToken = $qq->fetchAccessToken($_GET,C('QQCALLBACK'));
        $openid = $qq->setAccessToken($accessToken['access_token']);
        $user = $qq->fetchUserInfo();
        $userm = M('User');
        $result = $userm->field('id,user_name,mobile')->where(array('mobile'=>$openid))->find();
        if(!empty($result)){
            $data = array('user_name'=>$user['nickname'],'token'=>$accessToken['access_token']);
            $userm->where(array('mobile'=>$openid))->save($data);
            session("uniq_user_name",$openid);
            session("uniq_user_id",$result['id']);
            unset($data);unset($accessToken);unset($user);
            $this->redirect('Index/index');
            exit;
        }else{
            $pass = md5(123456);$time = date('Y-m-d H:i:s');$ip = get_client_ip();
            $data = array('user_name'=>$user['nickname'],'mobile'=>$openid,'password'=>$pass,'token'=>$accessToken['access_token'],'createtime'=>$time,'ip'=>$ip,'is_active'=>'1','login_type'=>'qq');
            $reid = $userm->add($data);
            if($reid && $reid>0){
                session("uniq_user_name",$openid);
                session("uniq_user_id",$reid);
            }
            unset($data);unset($accessToken);unset($user);
            $this->redirect('Index/index');
            exit;
        }
    }
    public function sinaCallback(){
        if(empty($_GET['code'])){
            $this->redirect('Index/index');
            exit;
        }else{
            $uid = session("uniq_user_id");
            if($uid && $uid>0){
                $this->redirect('Index/index');
                exit;
            }else{
            $sina = new Sinaapi(C('SINAAPPID'),C('SINAAPPKEY'));
            $accessToken = $sina->fetchAccessToken($_GET,C('SINACALLBACK'));
            $sina->setAccessToken($accessToken);
            $user = $sina->showuser();
            $userm = M('User');
            $result = $userm->field('id,user_name,mobile')->where(array('mobile'=>$accessToken['uid']))->find();
            if(!empty($result)){
             $data = array('user_name'=>$user['screen_name'],'token'=>$accessToken['access_token']);
             $userm->where(array('mobile'=>$accessToken['uid']))->save($data);
                session("uniq_user_name",$result['mobile']);
                session("uniq_user_id",$result['id']);
             unset($data);unset($accessToken);unset($user);
                $this->redirect('Index/index');
                exit;
            }else{
             $pass = md5(123456);$time = date('Y-m-d H:i:s');$ip = get_client_ip();
             $data = array('user_name'=>$user['screen_name'],'mobile'=>$accessToken['uid'],'password'=>$pass,'token'=>$accessToken['access_token'],'createtime'=>$time,'ip'=>$ip,'is_active'=>'1','login_type'=>'sina');
             $reid = $userm->add($data);
             if($reid && $reid>0){
                 session("uniq_user_name",$accessToken['uid']);
                 session("uniq_user_id",$reid);
             }
                unset($data);unset($accessToken);unset($user);
                $this->redirect('Index/index');
                exit;
            }
        }
        }
    }
}