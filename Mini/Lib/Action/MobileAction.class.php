<?php
class MobileAction extends Action{
    public function _initialize(){
        /*if(!is_mobile()){
            $this->redirect('Index/index');
            exit;
        }*/
    }
   public function index(){
       if(cookie('uniq_user_name') && cookie('uniq_user_id')){
           session("uniq_user_name",cookie('uniq_user_name'));
           session("uniq_user_id",cookie('uniq_user_id'));
       }
       $this->uniq_user_name =  session("uniq_user_name");
       $user = M('User');
       $num_iid = trim($this->_request('num'));
       $is_show = 0;
       $is_allow_register = 0;
       $is_phone = 0;
       $is_active = 0;
       $is_mobile_active = 0;
       $mobile = '';
       $nick = session("uniq_user_id");
       $result = $user->where(array('id'=>$nick))->find();
       if(!empty($num_iid)){
           $_SESSION['num_iid'] = $num_iid;
           if(empty($this->uniq_user_name)){
               $is_allow_register = 1;
           }else{
               if(!$result['is_active']){
                   $is_active = 1;
               }else{
                   $is_mobile_active = 1;
               }
               if($result['mobile']){
                   $is_phone = 0;
                   $mobile = $result['mobile'];
               }else{
                   $is_active = 0;
                   $is_phone = 1;
               }
           }
       }else{
           $is_mobile_active = $result['is_active'];
       }

       if($is_allow_register > 0 || $is_phone > 0 || $is_active > 0){
           $is_show = 1;
       }
       //kimi 优衣库二期
       $suit_style = M('SettingsSuitStyle');
       $beubeu_suits = M('BeubeuSuits');
       $recomodel = D('Reco');
       if(S('mstyledata')){
           $beubeu_suits_list = unserialize(S('mstyledata'));
       }else{
           //默认模特图

           $beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where(array('suitGenderID'=>1,'suitImageUrl'=>array('neq',''),'approve_status'=>0))->order('suitID desc')->limit('0,2')->select();
           foreach($beubeu_suits_list as $k=>$v){
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
           S('mstyledata',serialize($beubeu_suits_list),array('type'=>'file'));
       }
       //默认女士上下装自定义分类
       if(S('cust12')){
           $ucuslist = unserialize(S('cust12'));
       }else{
           $where = array('selected'=>1,'shortName'=>array('neq',''));
           $ucuslist  = $recomodel->getCateList2($where);//自定义分类
           S('cust12',serialize($ucuslist),array('type'=>'file'));
       }
       $this->assign('beubeu_suits_list',$beubeu_suits_list);
       $this->assign('ucuslist',$ucuslist);
       //优衣库二期
       $this->assign('nick',$nick);
       $this->assign('newstore',C('NEWSRORE'));
       $this->assign('cityn',cookie('cityn'));
       $this->assign('provi',cookie('pro'));
       $this->assign('uniurl',C('UNIQLOURL'));
       $this->assign('uniq_user_name',$this->uniq_user_name);
       $this->assign('is_allow_register',$is_allow_register);
       $this->assign('is_phone',$is_phone);
       $this->assign('is_active',$is_active);
       $this->assign('is_mobile_active',$is_mobile_active);
       $this->assign('mobile',$mobile);
       $this->assign('user',$result);
       $this->assign('basedir',__ROOT__);
     $this->display();
   }
}