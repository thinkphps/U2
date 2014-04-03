<?php
class IndexAction extends Action {
    public function index(){
    $SA_IP = get_client_ip();
    $city     = getIPLoc_sina($SA_IP);
		//取得官方推荐数据
	$windex = D('Windex');
	$tag = D('Tag');
	$recogood = $windex->getrecommend($city['province']);
	$reulist = $recogood[0];
	$redlist = $recogood[1];
    //取得场合id 
    $business = $tag->gettagid('商务');
    $ourism = $tag->gettagid('逛街');
	$sport = $tag->gettagid('运动');
	$jujia = $tag->gettagid('居家');
	$gosch = $tag->gettagid('上学');
	
	$this->assign('reulist',$reulist);
	$this->assign('redlist',$redlist);
	
	$this->assign('business',$business);
	$this->assign('ourism',$ourism);
	$this->assign('sport',$sport);
	$this->assign('jujia',$jujia);
	$this->assign('gosch',$gosch);
	$this->assign('cityn',$_SESSION['cityn']);
	$this->assign('provi',$_SESSION['pro']);
	$this->assign('uniurl',C('UNIQLOURL'));

	$this->display();
    }
//ajax取数据
public function ajaxgood(){
    $type = trim($this->_request('tageid')); //场合id
    $sex = trim($this->_request('sexid'));//性别id
	$tem = trim($this->_request('tem'));//平均温度
	$pro = trim($this->_request('pro'));//省
	$callback=$_GET['callback'];
	$goodtag = M('Goodtag');
	if($tem<=-10){
	$tem = -10;	
	}
	//取得推荐
    $windex = D('Windex');
	$recomodel = D('Reco');
	$recogood = $recomodel->getrec($tem);
	$reulist = $recogood[0];
	$redlist = $recogood[1];
	/*if(isset($tem)){
	//取出温度对应的商品数据 
	$widvalue = $windex->getwindex($tem);
	$wvalue = $widvalue['str'];
	//上装
	$wherex = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'1','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$uclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();
	$where = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'1','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$uclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
	$windex->saomo($uclothesx,$uclothes);
	//上装
	//下装
	$where2x = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'2','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$dclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();	
	$where2 = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'2','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$dclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
	$windex->saomo($dclothesx,$dclothes);
	//下装

	//组织数据
	foreach($uclothesx as $k=>$v){
    if(!empty($v)){
	$reulist[] = $v;//上装
	}
	}
	$uclothesx = array();
	foreach($uclothes as $k=>$v){
	if(!empty($v)){
	$reulist[] = $v;//上装
	}
	}
    $uclothes = array();
   foreach($dclothesx as $k=>$v){
	if(!empty($v)){
	$redlist[] = $v;//下装
	}
	}
	$dclothesx = array();
   foreach($dclothes as $k=>$v){
	if(!empty($v)){
	$redlist[] = $v;//下装
	}
	}
   $dclothes = array();
	}*/
	$ustr = '';
	if(!empty($reulist)){
    foreach($reulist as $k=>$v){
	  $v['title'] = iconv('utf8','gbk',$v['title']);
      $ustr.='<li><img fg="'.$v['ccateid'].'" data-original="'.C('UNIQLOURL').$v['pic_url'].'" id="1" place="家居1" tag="淑女1" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'" miniUrl="'.C('UNIQLOURL').'mini.php/Index/index/num/'.$v['num_iid'].'">
              </li>';
      }
	$ustr = iconv('gbk','utf8',$ustr);
    $arr['ustr'] = $ustr;
	$arr['flag'] = true;   
	}
	
	$dstr = '';
	if(!empty($redlist)){
	foreach($redlist as $k=>$v){
	$v['title'] = iconv('utf8','gbk',$v['title']);
    $dstr.='<li><img fg="'.$v['ccateid'].'" data-original="'.C('UNIQLOURL').$v['pic_url'].'" id="10" place="家居2" tag="淑女10" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'" miniUrl="'.C('UNIQLOURL').'mini.php/Index/index/num/'.$v['num_iid'].'">
              </li>';
	}
	$dstr = iconv('gbk','utf8',$dstr);
    $arr['dstr'] = $dstr;
	$arr['flag'] = true;
    }
	$re = json_encode($arr);
    $re = iconv('utf8','gbk',$re);
	echo $callback."($re)";
}
//点击按钮取数据
public function getgood(){
	$tem = trim($this->_request('tem'));//平均温度
	$cid = trim($this->_request('cid'));//场合形如1_2_3全部为0
	$sid = trim($this->_request('sid'));//性别id形如1,2,3 all为0
	$tid = trim($this->_request('tid'));//套装id
	$pro = trim($this->_request('pro'));//省
	$callback=$_GET['callback'];
	if($tem<=-10){
	$tem = -10;	
	}
	$cid = $cid?$cid:0;
	$sid = $sid?$sid:0;
	$tid = $tid?$tid:0;
	$goodtag = M('Goodtag');
	$windex = D('Windex');
	$recomodel = D('Reco');
	$widvalue = $windex->getwindex($tem);
	$wvalue = $widvalue['str'];
	switch($tid){
		case 0 : //没有选中套装
    if($cid==0 && $sid==0){//场合跟性别都为0
		//取得官方推荐数据
	if(!empty($pro)){
	$recogood = $recomodel->getrec($tem);
	$uclothes = $recogood[0];
	$dclothes = $recogood[1];
	}

	/*if(isset($tem)){
	//上装 
	$wherex = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'1','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$reulistx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();
	$where = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'1','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$reulist = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
	$windex->saomo($reulistx,$reulist);
	//下装
	$where2x = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'2','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$redlistx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();

	$where2 = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'2','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$redlist = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
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
	}*/		
	}else if($sid==0 && $cid!='0'){//性别为all
		 $cidarr = explode('_',$cid);
		 $cidstr = '';
		 foreach($cidarr as $k=>$v){
		  if($v){
		 $cidstr.=$v.',';
		  }
		 }
		 $cidstr = rtrim($cidstr,',');
		 //上装
		 $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>array('exp','IN('.$cidstr.')'),'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	     $uclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();
		 $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>array('exp','IN('.$cidstr.')'),'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	     $uclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
		 $windex->saomo($uclothes,$uclothesx);
		 //下装
		 $where2 = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>array('exp','IN('.$cidstr.')'),'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	     $dclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();
		 $where2x = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>array('exp','IN('.$cidstr.')'),'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	     $dclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
		 $windex->saomo($dclothes,$dclothesx);
		 	 
		}else if($sid!=0 && $cid=='0'){
		 if($sid==4){
		 $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
		 }else{
		 $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'1','u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
		 }
	     $uclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();
		 $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'1','u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	     $uclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
		 $windex->saomo($uclothes,$uclothesx);
		 //下装
		 if($sid!=4){
		 $where2 = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'2','u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	     $dclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();
		 $where2x = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'2','u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	     $dclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
		 $windex->saomo($dclothes,$dclothesx);
         }
		}else if($sid!=0 && $cid!='0'){//性别跟场合都不为0
		$cidarr = explode('_',$cid);
		switch($sid){
			case 1 : //女
			$ctagid = $cidarr[0];
			break;
			case 2 :
			$ctagid = $cidarr[1];
			break;
			case 3 :
			$ctagid = $cidarr[2];
			break;		
		}
		//上装
		if($sid==4){
		$where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
		}else{
	    $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>$ctagid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
		}
	
	    $uclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();
	    $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>$ctagid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));	
	    $uclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
		$windex->saomo($uclothes,$uclothesx);
		//下装 
		if($sid!=4){
		 $where2 = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>$ctagid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	     $dclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();	
		 $where2x = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>$ctagid,'u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	     $dclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
		 $windex->saomo($dclothes,$dclothesx);
		}
		}
		break;
		case 1 : //选了套装
		if($sid==0){//性别为all
		 if(!empty($cid)){
		 $cidarr = explode('_',$cid);
		 }
		 $cidstr = '';
		 foreach($cidarr as $k=>$v){
		 if($v){
		 $cidstr.=$v.',';
		 }
		 }
		 $cidstr = rtrim($cidstr,',');		
		 //套装
		 $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'4','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
		 if(!empty($cidstr)){
         $where['u_goodtag.tag_id'] = array('exp','IN('.$cidstr.')');
		 }
	     $tclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();

		 $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'4','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
		 if(!empty($cidstr)){
         $wherex['u_goodtag.tag_id'] = array('exp','IN('.$cidstr.')');
		 }
	     $tclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
		 $windex->saomo($tclothes,$tclothesx);

		}else if($sid>0){//优选性别
		if(!empty($cid)){
		$cidarr = explode('_',$cid);
		}
		switch($sid){
			case 1 : //女
			$ctagid = $cidarr[0];
			break;
			case 2 :
			$ctagid = $cidarr[1];
			break;
			case 3 :
			$ctagid = $cidarr[2];
			break;		
		}	
		//套装
	    $where = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'4','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
		if($ctagid){
        $where['u_goodtag.tag_id'] = $ctagid;
		}
	    $tclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goods.uptime desc')->select();

	    $wherex = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.gtype'=>$sid,'u_goodtag.isud'=>'4','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
		if($ctagid){
        $wherex['u_goodtag.tag_id'] = $ctagid;
		}
	    $tclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.uptime desc')->select();
		$windex->saomo($tclothes,$tclothesx);
		}
		break;		
	}
    //如果没有上装跟下装走这里
    if($tid==0){
    if($cid!=0 || $sid!=0){
	if(empty($uclothes) && empty($dclothes)){
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
	if(!empty($uclothesx)){
    foreach($uclothesx as $k=>$v){
	if(!empty($v)){
    $uclothes[] = $v;
	}
	}
	}
    $uclothesx = array();
	$ustr = '';
	if(!empty($uclothes)){
    foreach($uclothes as $k=>$v){
	  $v['title'] = iconv('utf8','gbk',$v['title']);
      $ustr.='<li><img fg="'.$v['ccateid'].'" data-original="'.C('UNIQLOURL').$v['pic_url'].'" id="1" place="家居1" tag="淑女1" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'" miniUrl="'.C('UNIQLOURL').'mini.php/Index/index/num/'.$v['num_iid'].'">
              </li>';
      }	
	}
	$ustr = iconv('gbk','utf8',$ustr);
    $arr['ustr'] = $ustr;
	$arr['flag1'] = 'p';	
	//下装
    if(!empty($dclothesx)){
    foreach($dclothesx as $k=>$v){
	if(!empty($v)){
    $dclothes[] = $v;
	}
	}
	}
	$dclothesx = array();
	$dstr = '';
	if(!empty($dclothes)){
	foreach($dclothes as $k=>$v){
	$v['title'] = iconv('utf8','gbk',$v['title']);
    $dstr.='<li><img fg="'.$v['ccateid'].'" data-original="'.C('UNIQLOURL').$v['pic_url'].'" id="10" place="家居2" tag="淑女10" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'" miniUrl="'.C('UNIQLOURL').'mini.php/Index/index/num/'.$v['num_iid'].'">
              </li>';
	}
    }
	$dstr = iconv('gbk','utf8',$dstr);
    $arr['dstr'] = $dstr;
	$arr['flag1'] = 'p';
	//套装
	if(!empty($tclothesx)){
    foreach($tclothesx as $k=>$v){
	if(!empty($v)){
    $tclothes[] = $v;
	}
	}
	}
	$tclothesx = array();
	$tstr = '';
	if(!empty($tclothes)){
	foreach($tclothes as $k=>$v){
	$v['title'] = iconv('utf8','gbk',$v['title']);
    $tstr.='<li>
                <img data-original="'.C('UNIQLOURL').$v['pic_url'].'" id="10" place="家居2" tag="淑女10" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'">
              </li>';
	}
	$tstr = iconv('gbk','utf8',$tstr);
    }
    $arr['tstr'] = $tstr;
	if(!empty($tstr)){
     $arr['flag'] = 't';
	}
	$arr['sid'] = $sid;
	$re = json_encode($arr);
    $re = iconv('utf8','gbk',$re);
	echo $callback."($re)"; 
}
    //get weatherinfo
    public function GetWeatherByCityID()
    {
        $id = trim($this->_request('id'));
        $callback=$_GET['callback'];
        $Weather = D('Weather');
        $returnObj =  $Weather->GetWeatherInfoByID($id);
        $weatherInfo["cityname"] = $returnObj[0]['commoncityname'];
        $weatherInfo["weather1"] = json_decode($returnObj[0]['weather1'],true);
        $weatherInfo["weather2"] = json_decode($returnObj[0]['weather2'],true);
        $weatherInfo["weather3"] = json_decode($returnObj[0]['weather3'],true);
        $weatherInfo["weather4"] = json_decode($returnObj[0]['weather4'],true);
        $weatherInfo["weather5"] = json_decode($returnObj[0]['weather5'],true);
        $weatherInfo["weather6"] = json_decode($returnObj[0]['weather6'],true);
        $re = json_encode($weatherInfo);
        $re = iconv('utf8','gbk',$re);
        echo $callback."($re)";

    }
}