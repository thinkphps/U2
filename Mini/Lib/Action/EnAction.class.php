<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 16-3-28
 * Time: 上午10:26
 * To change this template use File | Settings | File Templates.
 */
class EnAction extends Action{
    public function index(){
        $ism = is_mobile();
        if($ism){
            $this->redirect('Mobile/index');
        }
        if(cookie('uniq_user_name') && cookie('uniq_user_id')){
            session("uniq_user_name",cookie('uniq_user_name'));
            session("uniq_user_id",cookie('uniq_user_id'));
        }
        $this->uniq_user_name =  session("uniq_user_name");
        $user = M('User');
        $is_allow_register = 0;
        $is_mobile_active = 0;
        $mobile = '';
        $nick = session("uniq_user_id");
        $result = $user->where(array('id'=>$nick))->find();
        if($result['login_type']!='normal'){
            $this->uniq_user_name = $result['user_name'];
        }
        $is_mobile_active = $result['is_active'];
        $mobile = $result['mobile'];
        if(empty($result)){
            $u_id = 0;
        }else{
            $u_id = $result['id'];
        }
        $_SESSION['u_id'] = $u_id;
        $this->assign('nick',$nick);
        $this->assign('newstore',C('NEWSRORE'));
        $this->assign('cityn',cookie('cityn'));
        $this->assign('provi',cookie('pro'));
        $this->assign('uniurl',C('UNIQLOURL'));
        $this->assign('uniq_user_name',$this->uniq_user_name);
        $this->assign('is_allow_register',$is_allow_register);
        $this->assign('is_mobile_active',$is_mobile_active);
        $this->assign('mobile',$mobile);
        $this->assign('user',$result);
        $this->assign('dsn','http://'.$_SERVER['HTTP_HOST']);
        $this->assign('basedir',__ROOT__);
        $this->display();
    }
    public function loginout(){
        $_SESSION=array();
        if(isset($_COOKIE[session_name()])){
            setCookie(session_name(), '', time()-100, '/');
        }
        cookie('uniq_user_name',null);
        cookie('uniq_user_id',null);
        session_destroy();
        if(is_mobile()){
            $this->redirect('Mobile/index');
        }else{
            $this->redirect('New/index');
        }
    }
}