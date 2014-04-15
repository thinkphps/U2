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
    if(S('dstyle')){
        $suit_style_list = unserialize(S('dstyle'));
    }else{
        //取得性别所对应的风格
       $suit_style_list =  $suit_style->cache(true)->join('inner join u_settings_gender_style as g on u_settings_suit_style.ID=g.styleID')->field('u_settings_suit_style.ID')->where(array('g.genderID'=>1))->select();
        foreach($suit_style_list as $k=>$v){
            $suit_style_list[$k]['pid'] = $recomodel->pageToDataStyle($v['ID']);
            $suit_style_list[$k]['pid2'] = $recomodel->pageToDataStyle2($v['ID']);
        }
      S('dstyle',serialize($suit_style_list),array('type'=>'file'));
    }

    if(S('styledata')){
        $beubeu_suits_list = unserialize(S('styledata'));
    }else{
        //默认模特图
       $beubeu_suits_list = $beubeu_suits->cache(true)->field('suitImageUrl')->where(array('suitStyleID'=>1,'suitGenderID'=>1))->order('uptime desc')->select();
      S('styledata',serialize($beubeu_suits_list),array('type'=>'file'));
    }
    //默认女士上下装自定义分类
    $ucuslist  = $recomodel->getCusData(array('gtype'=>'1','isud'=>'1'));//上装
    $dcuslist  = $recomodel->getCusData(array('gtype'=>'1','isud'=>'2'));//下装

    $this->assign('beubeu_suits_list',$beubeu_suits_list);
    $this->assign('stylenum',ceil(count($suit_style_list)/2));
    $this->assign('suit_style_list',$suit_style_list);
    $this->assign('ucuslist',$ucuslist);
    $this->assign('dcuslist',$dcuslist);
    //优衣库二期

   //取出性别所对应的tagid
   $wclist = $recomodel->getfc('1','1','1');//女性场合
   $wflist = $recomodel->getfc('2','1','1');//女性风格
   $mclist = $recomodel->getfc('1','2','1');//男性场合
   $mflist = $recomodel->getfc('2','2','1');//男性风格
   $cclist = $recomodel->getfc('1','3','1');//小孩场合
   $cflist = $recomodel->getfc('2','3','1');//小孩风格
   $bflist = $recomodel->getfc('2','4','1');//baby风格

    $this->assign('nick',$nick);
	//性别所对应的tag
    $this->assign('wclist',json_encode($wclist));//女性场合
	$this->assign('wflist',json_encode($wflist));//女性分割
	$this->assign('mclist',json_encode($mclist));//男性场合
	$this->assign('mflist',json_encode($mflist));//男性风格
	$this->assign('cclist',json_encode($cclist));//小孩场合
	$this->assign('cflist',json_encode($cflist));//小孩风格
	$this->assign('bflist',json_encode($bflist));//baby风格
	$this->assign('mcarr',json_encode($mcarr));
	$this->assign('mfarr',json_encode($mfarr));

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
    $this->assign('babycate',$babycate);
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
	$this->redirect('Index/index');
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
//if(!empty($_SESSION['token'])){
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
//}
}
}

//点击按钮取数据
public function getgood(){
	$tem = trim($this->_request('tem'));//平均温度
	$cid = trim($this->_post('cid'));//场合形如1_2_3全部为0
	$sid = trim($this->_post('sid'));//性别id形如1,2,3 all为0
	$tid = trim($this->_post('tid'));//套装id
	$fid = trim($this->_post('fid'));//风格id
	$zid = trim($this->_post('zid'));//自定义分类
	$pro = trim($this->_post('pro'));//省
	$cid2 = $cid;
	$fid2 = $fid;
	if($tem<=-10){
	$tem = -10;	
	}
	$cid = $cid?$cid:0;
	$sid = $sid?$sid:0;
	$tid = $tid?$tid:0;
    $fid = $fid?$fid:0;
    $zid = $zid?$zid:0;
	$goodtag = M('Goodtag');
	$windex = D('Windex');
	$widvalue = $windex->getwindex($tem);
	$wvalue = $widvalue['str'];
	switch($tid){
		case 0 : //没有选中套装
    if($cid==0 && $sid==0 && $fid==0 && $zid==0){//场合，性别，风格,自定义都为0
		//取得官方推荐数据
	if(!empty($pro)){
	$recogood = $windex->getrecommend2($pro);
	$uclothes = $recogood[0];
	$dclothes = $recogood[1];
	}
	if(isset($tem)){
	//上装
	$wherex = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'1','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$reulistx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
	$where = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'1','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$reulist = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
    $windex->saomo($reulistx,$reulist);

	$where2x = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'2','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$redlistx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
	$where2 = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'2','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$redlist = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
	$windex->saomo($redlistx,$redlist);

	//组织数据
	foreach($reulistx as $k=>$v){
    if(!empty($v)){
	$uclothes[] = $v;//上装
	}
	}
    $reulistx = array();
	foreach($reulist as $k=>$v){
	if(!empty($v)){
	$uclothes[] = $v;//上装
	}
	}
	$reulist = array();
    foreach($redlistx as $k=>$v){
	if(!empty($v)){ 
	$dclothes[] = $v;//下装
	}
	}
	$redlistx = array();
    foreach($redlist as $k=>$v){
	if(!empty($v)){
	$dclothes[] = $v;//下装
	}
	}
    $redlist = array();
	}		
 }else{
	  if(isset($tem)){
	  $where['u_goodtag.wid'] = array('exp','IN('.$wvalue.')');
	  }
	  
      if($sid && !empty($sid)){
      $where['u_goodtag.gtype'] = $sid;
	  }
	  $cidstr = '';
	  if($cid && !empty($cid)){
		 $is_g1 = is_int(strpos($cid,'_'));
		 if(!$is_g1){
          $cid = $cid.'_';
		 }
		 $cidarr = explode('_',$cid);
		 foreach($cidarr as $k=>$v){
		 if($v){
		 $cidstr.=$v.',';
		 }
		 }
	  $cidstr = rtrim($cidstr,',');
	  if(!empty($cidstr)){
	  $wh2['u_goodtag.tag_id'] = array('exp','IN('.$cidstr.')');
      }
	  }

	  //风格
      $fidstr = '';
	  if($fid && !empty($fid)){
		 $wh2['_logic'] = 'OR';
		 $is_g2 = is_int(strpos($fid,'_'));
		 if(!$is_g2){
          $fid = $fid.'_';
		 }
		 $cidarr2 = explode('_',$fid);
		 foreach($cidarr2 as $k=>$v){
		 if($v){
		 $fidstr.=$v.',';
		 }
		 }
	  $fidstr = rtrim($fidstr,',');
	  if(!empty($fidstr)){
	  $wh2['u_goodtag.ftag_id'] = array('exp','IN('.$fidstr.')');
      }
	  }
	  if($wh2){
      $where['_complex'] = $wh2;
	  }
	  if($zid && !empty($zid)){
        $is_g = is_int(strpos($zid,'_'));
		 if(!$is_g){
          $zid = $zid.'_';
		 }
		  $cstr = '';
		 $ccid = explode('_',$zid);
		 foreach($ccid as $k=>$v){
		 if($v){
		 $cstr.=$v.',';
		 }
		 }
		 $cstr = rtrim($cstr,',');
        $where['u_goodtag.ccateid'] = array('exp','IN('.$cstr.')');
	  }

         $where1 = $where;
		 //上装
		 if($sid!=4){//婴幼儿没有上下装
         $where1['u_goodtag.isud'] = '1';
         }
		 $where1['u_goods.approve_status'] = 'onsale';
		 $where1['u_goods.num'] = array('egt','15');
	     $uclothesy = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where1)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
         $where1x = $where;
		 $where1x['u_goodtag.wid'] = $widvalue['wid'];
         $where1x['u_goodtag.isud'] = '1';
		 $where1x['u_goods.approve_status'] = 'onsale';
		 $where1x['u_goods.num'] = array('egt','15');
	     $uclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where1x)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
         $windex->saomo($uclothes,$uclothesy);

		 //下装
		 if($sid!=4){//婴幼儿没有上下装
         $where2x = $where;
		 $where2x['u_goodtag.wid'] = $widvalue['wid'];
         $where2x['u_goodtag.isud'] = '2';
		 $where2x['u_goods.approve_status'] = 'onsale';
		 $where2x['u_goods.num'] = array('egt',15);
	     $dclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
         $where['u_goodtag.isud'] = '2';
		 $where['u_goods.approve_status'] = 'onsale';
		 $where['u_goods.num'] = array('egt',15);
	     $dclothesy = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
         $windex->saomo($dclothes,$dclothesy);
         }

         //吧当天的指数数据放前边
         foreach($uclothesy as $kx=>$vx){
		 if(!empty($vx)){
         $uclothes[] = $vx;
		 }
		 }
         $uclothesy = array();
         if(!empty($dclothesy)){
         foreach($dclothesy as $kx=>$vx){
	     if(!empty($vx)){
         $dclothes[] = $vx;
		 }
		 }
		 }
         $dclothesy = array();
         if(!empty($cid) && !empty($fid)){
		 if(!$is_g1 && !$is_g2){
		 foreach($cidarr2 as $kc=>$vc){
		 if(!empty($vc)){
         $cidarr[] = $vc;
		 }
		 }
		 }
        foreach($uclothes as $kid=>$vid){
         if($is_g1 && $is_g2){
		 $cidarr = array();
		if(!empty($cid2)){
		$cid_arr = explode('_',$cid2);
		switch($vid['type']){
			case 1 : //女
			$ctagid = $cid_arr[0];
			break;
			case 2 :
			$ctagid = $cid_arr[1];
			break;
			case 3 :
			$ctagid = $cid_arr[2];
			break;		
		}
		}
	   if(!empty($fid2)){
		$fid_arr = explode('_',$fid2);
		switch($vid['type']){
			case 1 : //女
			$ftagid= $fid_arr[0];
			break;
			case 2 :
			$ftagid= $fid_arr[1];
			break;
			case 3 :
			$ftagid= $fid_arr[2];
			break;		
		}
		}
		$cidarr[] = $ctagid?$ctagid:-1;
        $cidarr[] = $ftagid?$ftagid:-1;
		}

         $gtlist = $goodtag->field('tag_id,ftag_id')->where(array('good_id'=>$vid['id']))->select();
         $tagarr = array();
		 foreach($gtlist as $kv=>$vv){
         $tagarr[] = $vv['tag_id'];
		 $tagarr[] = $vv['ftag_id'];
		 }
         $gtlist = array();
		 $j = 0;
         foreach($cidarr as $kis=>$vis){
         if(!empty($vis)){
         if(in_array($vis,$tagarr)){
         $j = 1;
		 }else{
         $j = 0;
		 break;
		 }
		 }
		 }
		 if($j==0){
         unset($uclothes[$kid]);
		 }
		 }
		 //下装
         foreach($dclothes as $kid=>$vid){
	     if($is_g1 && $is_g2){
		 $cidarr = array();
		if(!empty($cid2)){
		$cid_arr = explode('_',$cid2);
		switch($vid['type']){
			case 1 : //女
			$ctagid = $cid_arr[0];
			break;
			case 2 :
			$ctagid = $cid_arr[1];
			break;
			case 3 :
			$ctagid = $cid_arr[2];
			break;		
		}
		}
	   if(!empty($fid2)){
		$fid_arr = explode('_',$fid2);
		switch($vid['type']){
			case 1 : //女
			$ftagid= $fid_arr[0];
			break;
			case 2 :
			$ftagid= $fid_arr[1];
			break;
			case 3 :
			$ftagid= $fid_arr[2];
			break;		
		}
		}
		$cidarr[] = $ctagid?$ctagid:-1;
        $cidarr[] = $ftagid?$ftagid:-1;
		}
         $gtlist = $goodtag->field('tag_id,ftag_id')->where(array('good_id'=>$vid['id']))->select();
         $tagarr = array();
		 foreach($gtlist as $kv=>$vv){
         $tagarr[] = $vv['tag_id'];
		 $tagarr[] = $vv['ftag_id'];
		 }
         $gtlist = array();
		 $j = 0;
         foreach($cidarr as $kis=>$vis){
         if(!empty($vis)){
         if(in_array($vis,$tagarr)){
         $j = 1;
		 }else{
         $j = 0;
		 break;
		 }
		 }
		 }
		 if($j==0){
         unset($dclothes[$kid]);
		 }
		 }
		 }
		}
		break;
		case 1 : //选了套装
		if($sid==0){//性别为all
	  if(isset($tem)){
       $where['u_goodtag.wid'] = array('exp','IN('.$wvalue.')');
	  }
	  $where['u_goodtag.isud'] = '4';
	  $cidstr = '';
	  if($cid && !empty($cid)){
		 $is_g1 = is_int(strpos($cid,'_'));
		 if(!$is_g1){
          $cid = $cid.'_';
		 }
		 $cidarr = explode('_',$cid);
		 foreach($cidarr as $k=>$v){
		 if($v){
		 $cidstr.=$v.',';
		 }
		 }
	  $cidstr = rtrim($cidstr,',');
	  if(!empty($cidstr)){
	  $wh2['u_goodtag.tag_id'] = array('exp','IN('.$cidstr.')');
      }
	  }
	  
	  //风格
      $fidstr = '';
	  if($fid && !empty($fid)){
		 $wh2['_logic'] = 'OR';
		 $is_g2 = is_int(strpos($fid,'_'));
		 if(!$is_g2){
          $fid = $fid.'_';
		 }
		 $cidarr2 = explode('_',$fid);
		 foreach($cidarr2 as $k=>$v){
		 if($v){
		 $fidstr.=$v.',';
		 }
		 }
	  $fidstr = rtrim($fidstr,',');
	  if(!empty($fidstr)){
	  $wh2['u_goodtag.ftag_id'] = array('exp','IN('.$fidstr.')');
      }		 
	  }
	  if($wh2){
      $where['_complex'] = $wh2;
		}
	  if($zid && !empty($zid)){
        $is_g = is_int(strpos($zid,'_'));
		 if(!$is_g){
          $zid = $zid.'_';
		 }
		  $cstr = '';
		 $ccid = explode('_',$zid);
		 foreach($ccid as $k=>$v){
		 if($v){
		 $cstr.=$v.',';
		 }
		 }
		 $cstr = rtrim($cstr,',');
        $where['u_goodtag.ccateid'] = array('exp','IN('.$cstr.')');
	  }
		 //套装
		 $where1 = $where;
		 $where1['u_goodtag.wid'] = $widvalue['wid'];
		 $where1['u_goods.approve_status'] = 'onsale';
		 $where1['u_goods.num'] = array('egt','15');
	     $tclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where1)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();

		 $where['u_goods.approve_status'] = 'onsale';
		 $where['u_goods.num'] = array('egt','15');
	     $tclothesy = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
		 $windex->saomo($tclothes,$tclothesy);
         foreach($tclothesy as $tk=>$tv){
		 if(!empty($tv)){
         $tclothes[] = $tv;
		 }
		 }
		 $tclothesy = array();
         if(!empty($cid) && !empty($fid)){
		 if(!$is_g1 && !$is_g2){
		 foreach($cidarr2 as $kc=>$vc){
		 if(!empty($vc)){
         $cidarr[] = $vc;
		 }
		 }
         }
         foreach($tclothes as $kid=>$vid){
		 if($is_g1 && $is_g2){
		 $cidarr = array();
		if(!empty($cid2)){
		$cid_arr = explode('_',$cid2);
		switch($vid['type']){
			case 1 : //女
			$ctagid = $cid_arr[0];
			break;
			case 2 :
			$ctagid = $cid_arr[1];
			break;
			case 3 :
			$ctagid = $cid_arr[2];
			break;		
		}
		}
	   if(!empty($fid2)){
		$fid_arr = explode('_',$fid2);
		switch($vid['type']){
			case 1 : //女
			$ftagid= $fid_arr[0];
			break;
			case 2 :
			$ftagid= $fid_arr[1];
			break;
			case 3 :
			$ftagid= $fid_arr[2];
			break;		
		}
		}
		$cidarr[] = $ctagid?$ctagid:-1;
        $cidarr[] = $ftagid?$ftagid:-1;
		}

         $gtlist = $goodtag->field('tag_id,ftag_id')->where(array('good_id'=>$vid['id']))->select();
         $tagarr = array();
		 foreach($gtlist as $kv=>$vv){
         $tagarr[] = $vv['tag_id'];
		 $tagarr[] = $vv['ftag_id'];
		 }
         $gtlist = array();
		 $j = 0;
         foreach($cidarr as $kis=>$vis){
         if(!empty($vis)){
         if(in_array($vis,$tagarr)){
         $j = 1;
		 }else{
         $j = 0;
		 break;
		 }
		 }
		 }
		 if($j==0){
         unset($tclothes[$kid]);
		 }
		 }
		 }
			
		}else if($sid>0){//优选性别
		$ctagid = '';
        $ftagid = '';
	  if($cid && !empty($cid)){
		 $is_g1 = is_int(strpos($cid,'_'));
		 if(!$is_g1){
          $cid = $cid.'_';
		 }
		 $cidarr = explode('_',$cid);
		 foreach($cidarr as $k=>$v){
		 if($v){
		 $ctagid.=$v.',';
		 }
		 }
	     $ctagid = rtrim($ctagid,',');
	  }

	   if(!empty($fid)){
		$is_g2 = is_int(strpos($fid,'_'));
		 if(!$is_g2){
          $fid = $fid.'_';
		 }
		$fidarr = explode('_',$fid);
		 foreach($fidarr as $k=>$v){
		 if($v){
		 $ftagid.=$v.',';
		 }
		 }
	     $ftagid = rtrim($ftagid,',');
		}

		//套装
	    $where = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'4','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	    if(!empty($ctagid)){
	     $wh2['u_goodtag.tag_id'] = array('exp','IN('.$ctagid.')');
        }
		if($ftagid){
		$wh2['_logic'] = 'OR';
        $wh2['u_goodtag.ftag_id'] = array('exp','IN('.$ftagid.')');
		}
		if($wh2){
		$where['_complex'] = $wh2;
		}
	  if($zid && !empty($zid)){
        $is_g = is_int(strpos($zid,'_'));
		 if(!$is_g){
          $zid = $zid.'_';
		 }
		  $cstr = '';
		 $ccid = explode('_',$zid);
		 foreach($ccid as $k=>$v){
		 if($v){
		 $cstr.=$v.',';
		 }
		 }
		 $cstr = rtrim($cstr,',');
        $where['u_goodtag.ccateid'] = array('exp','IN('.$cstr.')');
	  }
	    $where1 = $where;
		$where1['u_goodtag.wid'] = $widvalue['wid'];
	    $tclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where1)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
	    $tclothesy = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
		$windex->saomo($tclothes,$tclothesy);
        foreach($tclothesy as $tk=>$tv){
		if(!empty($tv)){
        $tclothes[] = $tv;
		}
		}
		$tclothesy = array();		
		if(!empty($cid) && !empty($fid)){
		 if(!$is_g1 && !$is_g2){
		 foreach($fidarr as $kc=>$vc){
		 if(!empty($vc)){
         $cidarr[] = $vc;
		 }
		 }
		 }
         foreach($tclothes as $kid=>$vid){
		 if($is_g1 && $is_g2){
		$cidarr = array();
		if(!empty($cid2)){
		$cid_arr = explode('_',$cid2);
		switch($vid['type']){
			case 1 : //女
			$ctagid = $cid_arr[0];
			break;
			case 2 :
			$ctagid = $cid_arr[1];
			break;
			case 3 :
			$ctagid = $cid_arr[2];
			break;		
		}
		}
	   if(!empty($fid2)){
		$fid_arr = explode('_',$fid2);
		switch($vid['type']){
			case 1 : //女
			$ftagid= $fid_arr[0];
			break;
			case 2 :
			$ftagid= $fid_arr[1];
			break;
			case 3 :
			$ftagid= $fid_arr[2];
			break;		
		}
		}
		$cidarr[] = $ctagid?$ctagid:-1;
        $cidarr[] = $ftagid?$ftagid:-1;
		}
         $gtlist = $goodtag->field('tag_id,ftag_id')->where(array('good_id'=>$vid['id']))->select();
         $tagarr = array();
		 foreach($gtlist as $kv=>$vv){
         $tagarr[] = $vv['tag_id'];
		 $tagarr[] = $vv['ftag_id'];
		 }
         $gtlist = array();
		 $j = 0;
         foreach($cidarr as $kis=>$vis){
         if(!empty($vis)){
         if(in_array($vis,$tagarr)){
         $j = 1;
		 }else{
         $j = 0;
		 break;
		 }
		 }
		 }
		 if($j==0){
         unset($tclothes[$kid]);
		 }
		 }
		 }	
		}
		break;		
	}
	//如果没有则显示推荐的8件
    if($tid==0){
	if($cid!=0 || $sid!=0){
	if(empty($uclothes) && empty($dclothes)){
	$recomodel = D('Reco');
 	$recogood = $recomodel->getrec($tem);
	$uclothes = $recogood[0];
	$dclothes = $recogood[1];
	$arr['fl'] = 1;
	}else{
    $arr['fl'] = 0;
	}
	}
    }
	//上装
	$ustr = '';
	if(!empty($uclothes)){
    foreach($uclothes as $k=>$v){
	if($v){
    switch($v['type']){
		case '1' :
		$sexname = '女装';
		break;
        case '2' :
		$sexname = '男装';
		break;
		case '3' :
		$sexname = '童装';
		break;
    }
		//风格
	$gtag = $goodtag->join('u_tag on u_tag.id=u_goodtag.ftag_id')->field('u_tag.name')->where(array('u_goodtag.good_id'=>$v['id'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>2))->find();
	$reulist[$k]['tagname1'] = $gtag['name'];
	//场合
	$gtag2 = $goodtag->join('u_tag on u_tag.id=u_goodtag.tag_id')->field('u_tag.name')->where(array('u_goodtag.good_id'=>$v['id'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>1))->find();
      $ustr.='<li><img sex="'.$v['type'].'" fg="'.$v['ccateid'].'" data-original="'.__ROOT__.'/'.$v['pic_url'].'" id="'.$v['num_iid'].'" place="'.$gtag2['name'].'" csex="'.$sexname.'" tag="'.$gtag['name'].'" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'" miniUrl="'.C('UNIQLOURL').'mini.php/Index/index/num/'.$v['num_iid'].'">
              </li>';
	}
      }	
	}
    $arr['ustr'] = $ustr;
	$arr['flag1'] = 'p';	
	//下装
	$dstr = '';
	if(!empty($dclothes)){
	foreach($dclothes as $k=>$v){
	if($v){
    switch($v['type']){
		case '1' :
		$sexname = '女装';
		break;
        case '2' :
		$sexname = '男装';
		break;
		case '3' :
		$sexname = '童装';
		break;
    }
		//风格
	$gtag = $goodtag->join('u_tag on u_tag.id=u_goodtag.ftag_id')->field('u_tag.name')->where(array('u_goodtag.good_id'=>$v['id'],'u_tag.parent_id'=>2))->find();
	$reulist[$k]['tagname1'] = $gtag['name'];
	//场合
	$gtag2 = $goodtag->join('u_tag on u_tag.id=u_goodtag.tag_id')->field('u_tag.name')->where(array('u_goodtag.good_id'=>$v['id'],'u_tag.parent_id'=>1))->find();
    $dstr.='<li><img sex="'.$v['type'].'" fg="'.$v['ccateid'].'" data-original="'.__ROOT__.'/'.$v['pic_url'].'" id="'.$v['num_iid'].'" place="'.$gtag2['name'].'" csex="'.$sexname.'" tag="'.$gtag['name'].'" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'" miniUrl="'.C('UNIQLOURL').'mini.php/Index/index/num/'.$v['num_iid'].'">
              </li>';
	}
	}
    }
    $arr['dstr'] = $dstr;
	$arr['flag1'] = 'p';
	//套装
	$tstr = '';
	if(!empty($tclothes)){
	foreach($tclothes as $k=>$v){
	if($v){
    $tstr.='<li>
                <img src="'.__ROOT__.'/'.$v['pic_url'].'" id="0" sex="'.$v['type'].'" place="家居2" tag="淑女10" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'">
              </li>';
	}
	}
    }
    $arr['tstr'] = $tstr;
    if(!empty($tstr)){
	$arr['flag'] = 't';	
	}

	echo json_encode($arr);
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

public function updateActive(){
	/*
	$message = "SMS_MESSAGE : msgFormat = ".$_REQUEST['mobs']." ; moID = ".$_REQUEST['moID'] . " ; moTime = ".$_REQUEST['moTime']." ; mobs = ".$_REQUEST['mobs']." ; destID = ".$_REQUEST['destID']." ; msg = ".$_REQUEST['msg'];
	$code = 0;
	if($_REQUEST['mobs']){
		$flag = M('user')->where(array('mobile'=>$_REQUEST['mobs']))->save(array('is_active'=>1));
		if($flag){
			//$returnArr = array('code'=>1,'msg'=>'更新成功');
			$code = 100;
		}else{
			$code = 100;
			//$returnArr = array('code'=>-1,'msg'=>'更新失败');
		}
	}else{
		$code = 100;
		//$returnArr = array('code'=>-1,'msg'=>'返回的号码格式数据有误');
	}
	echo $code;
	//return json_encode($returnArr);
	*/
}


}