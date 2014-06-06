<?php
// 优衣库mini站,author:kimi
class IndexAction extends Action {
    public $uniq_user_name;
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
            //$is_allow_register = 1;
        }

        if($is_allow_register > 0 || $is_phone > 0 || $is_active > 0){
            $is_show = 1;
        }
        //echo $is_allow_register.$is_phone.$is_active;
        $this->assign('is_show',$is_show);
        $collection = M('Collection');
        $goods = M('Goods');
        $goodtag = M('Goodtag');
        $customcate = M('Customcate');
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
        //放到收藏里去
        $num_iid = $_SESSION['num_iid'];
        if(!empty($num_iid) && !empty($u_id)){
            $arr = explode(",",$num_iid);
            foreach($arr as $numid){
                $cresult = $collection->field('id')->where(array('num_iid'=>$numid,'uid'=>$u_id))->find();
                if(empty($cresult)){
                    if(session("uniq_user_id")){
                        $collection->add(array('num_iid'=>$numid,'uid'=>$u_id,'cratetime'=>$time));
                    }
                }
            }
        }
        //kimi 优衣库二期
        if(S('styledata')){
            $beubeu_suits_list = unserialize(S('styledata'));
        }else{
            //默认模特图
            $beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where(array('suitGenderID'=>1))->order('suitID desc')->limit('0,4')->select();
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
            S('styledata',serialize($beubeu_suits_list),array('type'=>'file'));
        }
        //默认上下装自定义分类
        if(S('cust1')){
            $ucuslist = unserialize(S('cust1'));
        }else{
            $where = array('gender'=>array('neq',5),'isud'=>1,'selected'=>1);
            $ucuslist  = $recomodel->getCateList2($where);//上装
            S('cust11',serialize($ucuslist),array('type'=>'file'));
        }
        if(S('cust12')){
            $dcuslist = unserialize(S('cust12'));
        }else{
            $where = array('gender'=>array('neq',5),'isud'=>2,'selected'=>1);
            $dcuslist  = $recomodel->getCateList2($where);//下装
            S('cust12',serialize($dcuslist),array('type'=>'file'));
        }
        $this->assign('beubeu_suits_list',$beubeu_suits_list);
        $this->assign('ucuslist',$ucuslist);
        $this->assign('dcuslist',$dcuslist);
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
        //}
    }


    public function loginout(){
        $user = M('user')->where(array('id'=>session("uniq_user_id")))->find();
        if($user['is_active']){
            M('Collection')->where(array('uid'=>session("uniq_user_id")))->save(array('is_delete'=>1));
        }else{
            M('Collection')->where(array('uid'=>session("uniq_user_id")))->delete();
        }
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
           $this->redirect('Index/index');
        }
    }

//删除
    public function delg(){
        $id = trim($this->_post('id'));//Collectio表的num_iid
        if($id>0){
            M('Collection')->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->delete();
        }
    }
//喜欢
    public function addlove(){
        $id = trim($this->_post('id'));//Collectio表的num_iid
        $flag = trim($this->_post('flag'));
        $isdel = trim($this->_post('isdel'));
        if($id>0){
            $uid = session("uniq_user_id");
            if(!empty($uid)){
                if($flag==1){
                    $love = M('Love');
                    $time = date('Y-m-d H:i:s');
                    if($isdel==1){
                        $cresult = $love->field('id')->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->find();
                        if(empty($cresult)){
                            $love->add(array('num_iid'=>$id,'uid'=>session("uniq_user_id"),'cratetime'=>$time));
                        }
                    }else if($isdel==0){
                        $love->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->delete();
                    }
                }else if($flag==2){
                    $buy = M('Buy');
                    $time = date('Y-m-d H:i:s');
                    if($isdel==1){
                        $cresult = $buy->field('id')->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->find();
                        if(empty($cresult)){
                            $buy->add(array('num_iid'=>$id,'uid'=>session("uniq_user_id"),'cratetime'=>$time));
                        }
                    }else if($isdel==0){
                        $buy->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->delete();
                    }
                }
            }else{
                $returnArr = array('code'=>0,'msg'=>'没有登录');
                $this->ajaxReturn($returnArr, 'JSON');
            }
        }
    }

//点击按钮取数据
    public function getgood(){
        if($this->_request('tem')){
            $tem = trim($this->_request('tem'));//平均温度
        }
        $sid = trim($this->_request('sid'));//性别id形如1,2,3 all为0
        $lid = trim($this->_request('lid'));//收藏id
        $bid = trim($this->_request('bid'));//购买id
        $fid = trim($this->_request('fid'));//风格id
        $zid = trim($this->_request('zid'));//自定义分类
        $kid = trim($this->_request('kid'));//快速搜索标记
        $page = trim($this->_request('page'));
        $keyword = trim($this->_request('keyword'));
        $timestamp = trim($this->_request('timestamp'));
        $oid = trim($this->_request('oid'));//排序id
        if($this->_request('tem')<=-10){
            $tem = -10;
        }
        $lid = $lid?$lid:0;
        $sid = $sid?$sid:0;
        $bid = $bid?$bid:0;
        $fid = $fid?$fid:0;
        $zid = $zid?$zid:0;
        $kid = $kid?$kid:0;
        $page = $page?$page:1;
        $oid = $oid?$oid:1;
        $goodtag = M('Goodtag');
        $windex = D('Windex');
        $page_num = 25;
        $start = ($page-1)*$page_num;
        if(isset($tem)){
            $widvalue = $windex->getwindex($tem);
        }
        $ostr = $windex->getOrderStr($oid);
        $productSyn = D('ProductSyn');
        $uid = session("uniq_user_id");
        $love = M('Love');
        $buy = M('Buy');
        if($lid==1 && $bid==1){
            $sql = "
select bg.num_iid,li.loveid,li.buyid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
) bl group by bl.num_iid) li
where bg.num_iid = li.num_iid order by ".$ostr." limit ".$start.",".$page_num;
            $result = M('BeubeuGoods')->query($sql);
            if(!empty($result)){
                foreach($result as $k1=>$v1){
                    $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                    $result[$k1]['products'] = $productSyn->GetProductColorByID($v1['num_iid']);
                }
            }else{
                $result = array();
            }
            if($page==1){
                if(is_mobile()){
                    $parmas = array('oid'=>$oid);
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    $result = $this->waterdata($result,$lid,$bid,$keyword);
                  }
            }
        }else if($lid==1 && $bid!=1){
            $sql = "
select bg.num_iid,li.loveid,li.buyid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
) bl group by bl.num_iid) li
where bg.num_iid = li.num_iid and li.loveid is not null order by ".$ostr." limit ".$start.",".$page_num;
            $result = M('BeubeuGoods')->query($sql);
            if(!empty($result)){
                foreach($result as $k1=>$v1){
                    $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                    $result[$k1]['products'] = $productSyn->GetProductColorByID($v1['num_iid']);
                }
            }else{
                $result = array();
            }
            if($page==1){
                if(is_mobile()){
                    $parmas = array('oid'=>$oid);
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    $result = $this->waterdata($result,$lid,$bid,$keyword);
                }
            }
        }else if($bid==1 && $lid!=1){
            $sql = "
select bg.num_iid,li.loveid,li.buyid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
)bl group by bl.num_iid)li
where bg.num_iid = li.num_iid and li.buyid is not null order by ".$ostr." limit ".$start.",".$page_num;
            $result = M('BeubeuGoods')->query($sql);
            if(!empty($result)){
                foreach($result as $k1=>$v1){
                    $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                    $result[$k1]['products'] = $productSyn->GetProductColorByID($v1['num_iid']);
                }
            }else{
                $result = array();
            }
            if($page==1){
                if(is_mobile()){
                    $parmas = array('oid'=>$oid);
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    $result = $this->waterdata($result,$lid,$bid,$keyword);
               }
            }
        }else if($kid>0){
            //快速搜索走这里
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
                    $wherelb = " left join (select id,num_iid from `u_love` where uid={$uid}) as lo on lo.num_iid=bg.num_iid left join (select id,num_iid from `u_buy` where uid={$uid}) bu on bu.num_iid=bg.num_iid";
                    $fieldlb = ",lo.id as loveid,bu.id as buyid";
                }
                $sql = "select bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url{$fieldlb} from `u_beubeu_goods` as bg {$wherelb} where  bg.istag='2' {$liketitle} {$ostr} limit {$start},{$page_num}";
                $result = M('BeubeuGoods')->query($sql);
            if(!empty($result)){
                foreach($result as $k1=>$v1){
                    $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                    $result[$k1]['products'] = $productSyn->GetProductColorByID($v1['num_iid']);
                }
            }else{
                $result = array();
            }
            if($page==1){
                if(is_mobile()){
                    $parmas = array('oid'=>$oid);
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    $result = $this->waterdata($result,$lid,$bid,$keyword);
              }
            }
        }else{
            //普通走这里
            /*if(S('good'.$sid.$fid.$zid.$tem.$page)){
             $result = unserialize(S('good'.$sid.$fid.$zid.$tem.$page));
            }else{*/
            $where = '';
            if(isset($tem)){
                $where.="and g.wid in (".$widvalue['str'].")";
            }
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
                foreach($ccid as $k=>$v){
                    if($v){
                        $cstr.=$v.',';
                    }
                }
                $cstr = rtrim($cstr,',');
                //$catewhere = " and cg.cateID in (".$cstr.")";
                $where.= " and g.wid in (".$cstr.")";
            }
            //$where.=" and bg.approve_status='onsale'";
            //$where.=" and bg.isdel=0";
            if(isset($tem)){
                $case = "case when g.wid=".$widvalue['wid']." then 0 else g.wid end wo";
                $ordr = "order by wo asc";
            }else{
                $case = '';
                $ordr = "";
            }
            if(!empty($uid)){
                $wherelb = " left join (select id,num_iid from `u_love` where uid={$uid}) as lo on lo.num_iid=al.num_iid left join (select id,num_iid from `u_buy` where uid={$uid}) bu on bu.num_iid=al.num_iid";
                $fieldlb = ",lo.id as loveid,bu.id as buyid";
            }
            if($sid!=4){
                $goodstable = '`u_beubeu_goods`';
            }else{
                $goodstable = '`u_goods`';
            }
                if(!empty($uid)){
                    $sql = "select al.*{$fieldlb} from (select bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from {$goodstable} as bg inner join (select g.num_iid,{$case} from `u_goodtag` as g where 1 {$where} group by g.num_iid {$ordr}) t1 on t1.num_iid=bg.num_iid {$ostr} limit {$start},{$page_num}) as al ".$wherelb;
                }else{
                    $sql = "select bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from {$goodstable} as bg inner join (select g.num_iid,{$case} from `u_goodtag` as g  where 1 {$where}  group by g.num_iid {$ordr}) t1 on t1.num_iid=bg.num_iid  {$ostr} limit {$start},{$page_num}";
                }
                $colorsql = "select bg.num_iid from {$goodstable} as bg inner join (select g.num_iid,{$case} from `u_goodtag` as g  where 1 {$where}  group by g.num_iid {$ordr}) t1 on t1.num_iid=bg.num_iid {$ostr} limit {$start},{$page_num}";
                $colorData = $windex->colorDetail($colorsql);
            /*if($zid && !empty($zid)){
                 if(!isset($tem) && empty($sid) && empty($fid)){
                  //如果只有自定义分类
                    if(!empty($uid)){
                      $sql = "select al.*{$fieldlb} from (select bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from {$goodstable} as bg inner join `u_catesgoods` as cg on cg.num_iid=bg.num_iid where bg.istag='2' ".$catewhere ." order by {$ostr} limit ".$start.",".$page_num.") as al ".$wherelb;
                    }else{
                     $sql = "select bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from {$goodstable} as bg inner join `u_catesgoods` as cg on cg.num_iid=bg.num_iid where bg.istag='2".$catewhere ." order by {$ostr} limit ".$start.",".$page_num;
                    }
                 }else{
                    //有自定义分类也有其他的条件
                    if(!empty($uid)){
                      $sql = "select al.*{$fieldlb} from (select g.good_id{$case},bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join {$goodstable} as bg on bg.id=g.good_id inner join `u_catesgoods` as cg on cg.num_iid=bg.num_iid where 1 ".$where." ".$catewhere." group by g.good_id ".$ordr."{$ostr} limit ".$start.",".$page_num.")  as al ".$wherelb;
                    }else{
                        $sql = "select g.good_id{$case},bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join {$goodstable} as bg on bg.id=g.good_id inner join `u_catesgoods` as cg on cg.num_iid=bg.num_iid where 1 ".$where." ".$catewhere." group by g.good_id ".$ordr."{$ostr} limit ".$start.",".$page_num;
                    }
                 }
            }else{
            if(!empty($uid)){
                $sql = "select al.*{$fieldlb} from (select g.good_id{$case},bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join {$goodstable} as bg on bg.id=g.good_id where 1 ".$where." group by g.good_id ".$ordr."{$ostr} limit ".$start.",".$page_num.") as al ".$wherelb;
            }else{
                $sql = "select g.good_id".$case.", bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join {$goodstable} as bg on bg.id=g.good_id  where 1 ".$where." group by g.good_id ".$ordr."{$ostr} limit ".$start.",".$page_num;
            }
            }*/
            $result = $goodtag->query($sql);
            if(!empty($result)){
                $windex->doColor($result,$colorData);
            }else{
                $result = array();
            }
            if($page==1){
                if(is_mobile()){
                    $parmas = array('oid'=>$oid);
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    //$result = $this->waterdata($result,$lid,$bid,$keyword);
                    $parmas = array('oid'=>$oid);
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }
            }
            /*S('good'.$sid.$fid.$zid.$tem.$page,serialize($result),array('type'=>'file'));
           }*/
        }
        if(!empty($result)){
            $returnArr = array('code'=>1,'da'=>$result,'timestamp'=>$timestamp);
        }else{
            $returnArr = array('code'=>0,'msg'=>'没有数据');
        }
        $this->ajaxReturn($returnArr, 'JSON');
    }
    public function waterdata($result,$lid,$bid,$keyword){
        $ad = "<div class='productinfo'><div class='wrapper_box banner_box'><a href='http://uniqlo.tmall.com/search.htm?scid=138188209&kid=11727_51912_165828_211546' target='__blank'><img src='".C('UNIQLOURL')."Upload/ad/T2sdBwXY4aXXXXXXXX_!!196993935.jpg' width='228' height='471' alt='' /></a></div></div>";
        $ad2 = '<div class="productinfo"><div class="wrapper_box banner_box"><a href="http://www.uniqlo.com/cn/styledictionary/?kid=11727_51912_165830_211548" target="__blank"><img src="'.C('UNIQLOURL').'Upload/ad/T2w_QOXKdXXXXXXXXX-196993935.jpg" width="228" height="228" alt="" /></a></div></div>';

        if($lid == 1 && $bid == 0){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><input name="search" type="text" value="'.$keyword.'" placeholder="输入您想要的款式或名称" autocomplete="off" id="keywordid"><a href="javascript:;" id="keybutton"></a></div></div></div>';
        }
        else if($lid == 0 && $bid == 1){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn select" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><input name="search" type="text" value="'.$keyword.'" placeholder="输入您想要的款式或名称" autocomplete="off" id="keywordid"><a href="javascript:;" id="keybutton"></a></div></div></div>';

        }else if($lid == 1 && $bid == 1){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn select" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><input name="search" type="text" value="'.$keyword.'" placeholder="输入您想要的款式或名称" autocomplete="off" id="keywordid"><a href="javascript:;" id="keybutton"></a></div></div></div>';
        }else if($lid == 0 && $bid == 0 ){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><input name="search" type="text" value="'.$keyword.'" placeholder="输入您想要的款式或名称" autocomplete="off" id="keywordid"><a href="javascript:;" id="keybutton"></a></div></div></div>';
        }
        else{
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><input name="search" type="text" value="'.$keyword.'" placeholder="输入您想要的款式或名称" autocomplete="off" id="keywordid"><a href="javascript:;" id="keybutton"></a></div></div></div>';
        }

        array_unshift($result,array('first'=>1,'ad'=>$ad));
        $arr_count = count($result);
        if($arr_count>=4){
            array_splice($result,2,0,array(array('first'=>2,'ad'=>$ad2)));
        }else if($arr_count == 3){
            array_splice($result,($arr_count-1),0,array(array('first'=>2,'ad'=>$ad2)));
        }else{
            array_splice($result,($arr_count),0,array(array('first'=>2,'ad'=>$ad2)));
        }
        $arr_count = count($result);
        if($arr_count>=4){
            array_splice($result,3,0,array(array('first'=>3,'cb'=>$str)));
        }else{
            array_splice($result,($arr_count),0,array(array('first'=>3,'cb'=>$str)));
        }
        return $result;
    }
    //手机版广告
    public function waterdataMobile($result,$lid,$bid,$keyword,$arr){
        switch($arr['oid']){
            case 1 :
                $sel1 = 'selected="selected"';
            break;
            case 2 :
                $sel2 = 'selected="selected"';
            break;
            case 3 :
                $sel3 = 'selected="selected"';
            break;
            case 4 :
                $sel4 = 'selected="selected"';
            break;
            case 5 :
                $sel5 = 'selected="selected"';
            break;
        }
        if($lid == 1 && $bid == 0){
            $str = '<div class="productinfo"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div><div class="wrapper_box wrapper_box_btn_group2"><div class="sequence"><select name="sequence" id="gorder"><option value="1" '.$sel1.'>默认排序</option><option value="2" '.$sel2.'>热度</option><option value="3" '.$sel3.'>新品</option><option value="4" '.$sel4.'>价格升序</option><option value="5" '.$sel5.'>价格降序</option></select></div></div></div>';
        }
        else if($lid == 0 && $bid == 1){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn select" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div><div class="wrapper_box wrapper_box_btn_group2"><div class="sequence"><select name="sequence" id="gorder"><option value="1" '.$sel1.'>默认排序</option><option value="2" '.$sel2.'>热度</option><option value="3" '.$sel3.'>新品</option><option value="4" '.$sel4.'>价格升序</option><option value="5" '.$sel5.'>价格降序</option></select></div></div></div>';

        }else if($lid == 1 && $bid == 1){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn select" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div><div class="wrapper_box wrapper_box_btn_group2"><div class="sequence"><select name="sequence" id="gorder"><option value="1" '.$sel1.'>默认排序</option><option value="2" '.$sel2.'>热度</option><option value="3" '.$sel3.'>新品</option><option value="4" '.$sel4.'>价格升序</option><option value="5" '.$sel5.'>价格降序</option></select></div></div></div>';
        }else if($lid == 0 && $bid == 0 ){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div><div class="wrapper_box wrapper_box_btn_group2"><div class="sequence"><select name="sequence" id="gorder"><option value="1" '.$sel1.'>默认排序</option><option value="2" '.$sel2.'>热度</option><option value="3" '.$sel3.'>新品</option><option value="4" '.$sel4.'>价格升序</option><option value="5" '.$sel5.'>价格降序</option></select></div></div></div></div>';
        }
        else{
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div><div class="wrapper_box wrapper_box_btn_group2"><div class="sequence"><select name="sequence" id="gorder"><option value="1" '.$sel1.'>默认排序</option><option value="2" '.$sel2.'>热度</option><option value="3" '.$sel3.'>新品</option><option value="4" '.$sel4.'>价格升序</option><option value="5" '.$sel5.'>价格降序</option></select></div></div></div></div>';
        }
        $arr_count = count($result);
        if($arr_count==0){
            array_splice($result,0,0,array(array('first'=>1,'ad'=>$str)));
        }else{
            array_splice($result,1,0,array(array('first'=>1,'ad'=>$str)));
        }

        return $result;
    }
//收入衣柜
    public function addwar(){
        if(session("uniq_user_name")){
            $id = trim($this->_post('id'));
            if($id>0){
                $uid = session("uniq_user_id");
                $user = M('user')->where("id={$uid}")->find();
                if(empty($user['mobile'])){
                    $returnArr = array('code'=>-3,'msg'=>'');
                }else{
                    //if($user['is_active']){
                    $collection = M('Collection');
                    $time = date('Y-m-d H:i:s');
                    $cresult = $collection->field('id')->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->find();
                    if(empty($cresult)){
                        $flag = $collection->add(array('num_iid'=>$id,'uid'=>session("uniq_user_id"),'cratetime'=>$time));
                        if($flag){
                            $returnArr = array('code'=>1,'msg'=>'');
                        }else{
                            $returnArr = array('code'=>-2,'msg'=>'收入衣柜失败');
                        }
                    }else{
                        $returnArr = array('code'=>-2,'msg'=>'您已经收入过衣柜，不要重复收入');
                    }
                    //}else{
                    //$returnArr = array('code'=>-4,'msg'=>$user['mobile']);
                    //}
                }
            }
        }else{
            $returnArr = array('code'=>-1,'msg'=>'');
        }
        $this->ajaxReturn($returnArr,'JSON');
    }
}