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
       $beubeu_suits_list = $beubeu_suits->cache(true)->field('suitID,suitImageUrl')->where(array('suitStyleID'=>1,'suitGenderID'=>1))->order('uptime desc')->select();
        $beubeu_detail = M('BeubeuSuitsGoodsdetail');
       foreach($beubeu_suits_list as $k=>$v){
           $detailResult = $beubeu_detail->cache(true)->field('item_bn')->where(array('suitID'=>$v['suitID']))->select();
           if(!empty($detailResult)){
           $beubeu_suits_list[$k]['detail'] = serialize($detailResult);
           }else{
           $beubeu_suits_list[$k]['detail'] = 0;
           }
       }
      S('styledata',serialize($beubeu_suits_list),array('type'=>'file'));
    }
    //默认女士上下装自定义分类
    if(S('cust1')){
        $ucuslist = unserialize(S('cust1'));
    }else{
        $ucuslist  = $recomodel->getCusData(array('gtype'=>'1','isud'=>'1'));//上装
        S('cust11',serialize($ucuslist),array('type'=>'file'));
    }
    if(S('cust12')){
        $dcuslist = unserialize(S('cust12'));
    }else{
        $dcuslist  = $recomodel->getCusData(array('gtype'=>'1','isud'=>'2'));//下装
        S('cust12',serialize($dcuslist),array('type'=>'file'));
    }
    $this->assign('beubeu_suits_list',$beubeu_suits_list);
    $this->assign('stylenum',ceil(count($suit_style_list)/2));
    $this->assign('suit_style_list',$suit_style_list);
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
	$sid = trim($this->_post('sid'));//性别id形如1,2,3 all为0
	$lid = trim($this->_post('lid'));//收藏id
    $bid = trim($this->_post('bid'));//购买id
	$fid = trim($this->_post('fid'));//风格id
	$zid = trim($this->_post('zid'));//自定义分类
    $kid = trim($this->_post('kid'));//快速搜索标记
    $page = trim($this->_post('page'));
	if($tem<=-10){
	$tem = -10;	
	}
	$lid = $cid?$lid:0;
	$sid = $sid?$sid:0;
	$bid = $tid?$bid:0;
    $fid = $fid?$fid:0;
    $zid = $zid?$zid:0;
    $kid = $kid?$kid:0;
    $page = $page?$page:1;
	$goodtag = M('Goodtag');
	$windex = D('Windex');
    if(isset($tem)){
	$widvalue = $windex->getwindex($tem);
    }
    if($lid>0){
     //点击收藏走这里
     if(S('coll'.session("uniq_user_id"))){
       $result = S('coll'.session("uniq_user_id"));
     }else{
     $collection = M('Collection');
      $result = $collection->join('inner join u_beubeu_goods bg on bg.num_iid=u_collection.num_iid')->field('bg.num_iid,bg.type,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url')->order('u_collection.id desc')->where(array('u_collection.uid'=>session("uniq_user_id")))->select();
    S('coll'.session("uniq_user_id"),serialize($result),array('type'=>'file'));
    }
    }else if($bid>0){
      //点击购买走这里
    if(S('buy'.session("uniq_user_id"))){
        $result = S('buy'.session("uniq_user_id"));
    }else{
        $buy = M('Buy');
        $result = $buy->join('inner join u_beubeu_goods bg on bg.num_iid=u_buy.num_iid')->field('bg.num_iid,bg.type,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url')->order('u_buy.id desc')->where(array('u_buy.uid'=>session("uniq_user_id")))->select();
        S('buy'.session("uniq_user_id"),serialize($result),array('type'=>'file'));
    }
    }else if($kid>0){
       //快速搜索走这里
       $result = M('BeubeuGoods')->field('u_beubeu_goods.num_iid,u_beubeu_goods.type,u_beubeu_goods.title,u_beubeu_goods.num,u_beubeu_goods.price,u_beubeu_goods.pic_url,u_beubeu_goods.detail_url')->where()->select();
     }else{
        //普通走这里
            if(S('good'.$sid.$fid.$zid.$tem.$page)){
             $result = unserialize(S('good'.$sid.$fid.$zid.$tem.$page));
            }else{
            $where = '';
            if(isset($tem)){
            $where.="and g.wid in ('".$widvalue['str']."')";
            }
            if(!empty($sid)){
                $where.=" and g.gtype='".$sid."'";
            }
            if(!empty($fid)){
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
                   $where.="and g.ccateid in ('".$cstr."')";
                }
           $start = ($page-1)*10;
           $sql = "select distinct g.good_id,case when g.wid=".$widvalue['wid']." then 0 end wo, bg.num_iid,bg.type,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join `u_beubeu_goods` as bg on bg.id=g.good_id where 1 ".$where." order by wo asc,uptime desc limit ".$start.",10";
            $result = $goodtag->query($sql);
            if($page==1){
                $ad = "<div class='wrapper_box banner_box'><a href='javascript:;'><img src='".__ROOT__."/".APP_PATH."Tpl/Public/images/xsyh.jpg' width='228' height='471' alt='' /></a></div>";
                $str = '<div class="wrapper_box wrapper_box_btn_group"><a href="#" class="ysc_btn select"><i></i>已收藏</a><a href="#" class="ygm_btn"><i></i>已购买</a><form action="#" method="get" class="wrapper_box_search"><input name="search" type="text" value="" placeholder="输入您想要的款式或名称" autocomplete="off"><a href="#"></a></form></div>';
                array_unshift($result,array('first'=>1,'ad'=>$ad));
                array_splice($result,3,0,array(array('first'=>1,'cb'=>$str)));
            }
            S('good'.$sid.$fid.$zid.$tem.$page,serialize($result),array('type'=>'file'));
           }
      }
    if(!empty($result)){
        $returnArr = array('code'=>1,'da'=>$result,'page'=>$page,'nextpage'=>$page+1);
    }else{
    $returnArr = array('code'=>0,'msg'=>'没有数据');
     }
    $this->ajaxReturn($returnArr, 'JSON');
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