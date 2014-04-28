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
       $beubeu_suits_list = $beubeu_suits->cache(true)->field('suitID,suitGenderID,suitImageUrl')->where(array('suitGenderID'=>1))->order('uptime desc')->select();
        $beubeu_detail = M('BeubeuSuitsGoodsdetail');
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
           $detailResult = $beubeu_detail->cache(true)->field('item_bn')->where(array('suitID'=>$v['suitID']))->select();
           if(!empty($detailResult)){
            foreach($detailResult as $k2=>$v2){
                $detailResult[$k2]['sex'] = $sex;
            }
               $beubeu_suits_list[$k]['detail'] = json_encode($detailResult);
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
	$sid = trim($this->_post('sid'));//性别id形如1,2,3 all为0
	$lid = trim($this->_post('lid'));//收藏id
    $bid = trim($this->_post('bid'));//购买id
	$fid = trim($this->_post('fid'));//风格id
	$zid = trim($this->_post('zid'));//自定义分类
    $kid = trim($this->_post('kid'));//快速搜索标记
    $page = trim($this->_post('page'));
    $keyword = trim($this->_post('keyword'));

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
	$goodtag = M('Goodtag');
	$windex = D('Windex');
    $page_num = 50;
    $start = ($page-1)*$page_num;
    if(isset($tem)){
	$widvalue = $windex->getwindex($tem);
    }
    $productSyn = D('ProductSyn');
    if($lid>0){
     //点击收藏走这里
     if(S('coll'.session("uniq_user_id").$page)){
       $result = unserialize(S('coll'.session("uniq_user_id").$page));
     }else{
     $collection = M('Collection');
      $result = $collection->join('inner join u_beubeu_goods bg on bg.num_iid=u_collection.num_iid')->field('bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url')->order('u_collection.id desc')->where(array('u_collection.uid'=>session("uniq_user_id")))->limit($start.','.$page_num)->select();
         if(!empty($result)){
         foreach($result as $k1=>$v1){
             $result[$k1]['products'] = $productSyn->GetProductColorByID($v1['num_iid']);
         }
         }else{
             $result = array();
         }
         if($page==1){
             $result = $this->waterdata($result);
         }
    S('coll'.session("uniq_user_id").$page,serialize($result),array('type'=>'file'));
    }
    }else if($bid>0){
      //点击购买走这里
    if(S('buy'.session("uniq_user_id").$page)){
        $result = unserialize(S('buy'.session("uniq_user_id").$page));
    }else{
        $buy = M('Buy');
        $result = $buy->join('inner join u_beubeu_goods bg on bg.num_iid=u_buy.num_iid')->field('bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url')->order('u_buy.id desc')->where(array('u_buy.uid'=>session("uniq_user_id")))->limit($start.','.$page_num)->select();
        if(!empty($result)){
        foreach($result as $k1=>$v1){
            $result[$k1]['products'] = $productSyn->GetProductColorByID($v1['num_iid']);
        }
        }else{
            $result = array();
        }
        if($page==1){
            $result = $this->waterdata($result);
        }
        S('buy'.session("uniq_user_id").$page,serialize($result),array('type'=>'file'));
    }
    }else if($kid>0){
       //快速搜索走这里
        if($keyword){
       $result = M('BeubeuGoods')->field('u_beubeu_goods.num_iid,u_beubeu_goods.type,u_beubeu_goods.isud,u_beubeu_goods.title,u_beubeu_goods.num,u_beubeu_goods.price,u_beubeu_goods.pic_url,u_beubeu_goods.detail_url')->where(array('approve_status'=>'onsale','num'=>array('egt','15'),'title'=>array('like','%'.$keyword.'%')))->order('uptime desc')->limit($start.','.$page_num)->select();
      }
        if(!empty($result)){
        foreach($result as $k1=>$v1){
            $result[$k1]['products'] = $productSyn->GetProductColorByID($v1['num_iid']);
        }
        }else{
            $result = array();
        }
        if($page==1){
            $result = $this->waterdata($result);
        }
     }else{
        //普通走这里
            if(S('good'.$sid.$fid.$zid.$tem.$page)){
             $result = unserialize(S('good'.$sid.$fid.$zid.$tem.$page));
            }else{
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
                   $where.="and g.ccateid in (".$cstr.")";
                }
                $where.=" and bg.approve_status='onsale' and bg.num>=15";
                if(isset($tem)){
                  $case = ",case when g.wid=".$widvalue['wid']." then 0 end wo";
                  $ordr = "order by wo asc,";
                }else{
                  $case = '';
                  $ordr = "order by ";
                }
               $sql = "select g.good_id".$case.", bg.num_iid,bg.type,bg.isud,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join `u_beubeu_goods` as bg on bg.id=g.good_id where 1 ".$where." group by g.good_id ".$ordr."uptime desc limit ".$start.",".$page_num;
            $result = $goodtag->query($sql);
            if(!empty($result)){
            foreach($result as $k1=>$v1){
                $result[$k1]['products'] = $productSyn->GetProductColorByID($v1['num_iid']);
            }
            }else{
                $result = array();
            }
            if($page==1){
                $result = $this->waterdata($result);
            }
            S('good'.$sid.$fid.$zid.$tem.$page,serialize($result),array('type'=>'file'));
           }
      }
    if(!empty($result)){
        $arr_count = count($result);
        $returnArr = array('code'=>1,'da'=>$result,'count'=>$arr_count,'page'=>$page,'nextpage'=>$page+1);
    }else{
    $returnArr = array('code'=>0,'msg'=>'没有数据');
     }
    $this->ajaxReturn($returnArr, 'JSON');
}
    public function waterdata($result){
        $ad = "<div class='productinfo wrapper_box banner_box'><a href='javascript:;'><img src='".__ROOT__."/".APP_PATH."Tpl/Public/images/xsyh.jpg' width='228' height='471' alt='' /></a></div>";
        $ad2 = '<div class="productinfo wrapper_box banner_box"><a href="javascript:;"><img src="'.__ROOT__."/".APP_PATH.'Tpl/Public/images/xinzuoshangpin.jpg" width="228" height="228" alt="" /></a></div>';


        $str = '<div class="productinfo"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>已收藏</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><input name="search" type="text" value="" placeholder="输入您想要的款式或名称" autocomplete="off" id="keywordid"><a href="javascript:;" id="keybutton"></a></div></div>';

//        $str = '<div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>已收藏</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a><div class="wrapper_box_search"><input name="search" type="text" value="" placeholder="输入您想要的款式或名称" autocomplete="off" id="keywordid"><a href="javascript:;" id="keybutton"></a></div></div>';
        array_unshift($result,array('first'=>1,'ad'=>$ad));
        $arr_count = count($result);
        if($arr_count>=4){
            array_splice($result,2,0,array(array('first'=>1,'ad'=>$ad2)));
        }else if($arr_count == 3){
            array_splice($result,($arr_count-1),0,array(array('first'=>1,'ad'=>$ad2)));
        }else{
            array_splice($result,($arr_count),0,array(array('first'=>1,'ad'=>$ad2)));
        }
        $arr_count = count($result);
        if($arr_count>=4){
            array_splice($result,3,0,array(array('first'=>1,'cb'=>$str)));
        }else{
            array_splice($result,($arr_count),0,array(array('first'=>1,'cb'=>$str)));
        }
        return $result;
    }
//取出颜色和图片
public function getColorPic($numiid){
    $sql = "SELECT if( length( cid ) =1, concat( 0, cid ) , cid ) as cid,url FROM `u_products` where `num_iid`='".$numiid."'";
    $list = M('Products')->query($sql);
    return $list;
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