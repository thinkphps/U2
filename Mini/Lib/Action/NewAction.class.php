<?php
class NewAction extends Action{
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
        $goods = M('Goods');
        $goodtag = M('Goodtag');
        $time = date('Y-m-d H:i:s');
        $love = M('Love');
        $buy = M('Buy');
        $suit_style = M('SettingsSuitStyle');
        $beubeu_suits = M('BeubeuSuits');
        $recomodel = D('Reco');
        if(empty($result)){
            $u_id = 0;
        }else{
            $u_id = $result['id'];
        }
        $_SESSION['u_id'] = $u_id;
        //kimi 优衣库二期
        //默认女士上下装自定义分类,这个不能删
        if(S('cust11')){
            $ucuslist = unserialize(S('cust11'));
        }else{
            $where = array('selected'=>1,'shortName'=>array('neq',''),'isshow'=>0);
            $ucuslist  = $recomodel->getCateList2($where);//自定义分类
            S('cust11',serialize($ucuslist),array('type'=>'file'));
        }
        $this->assign('ucuslist',$ucuslist);
        //优衣库二期
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
}