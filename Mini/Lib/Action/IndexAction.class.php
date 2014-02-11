<?php
// 优衣库mini站,author:kimi
class IndexAction extends Action {
	public $callback_url;
	public $appkey;
	public $client;
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
	
	$time = date('Y-m-d H:i:s');
	
	$collection = M('Collection');
	$goods = M('Goods');
	$areas = M('Areas');
	$shop = M('Shop');
	$goodtag = M('Goodtag');
	$customcate = M('Customcate');
	$time = date('Y-m-d H:i:s');
	$love = M('Love');
	$buy = M('Buy');
	if(empty($result)){
	//$res = $user->add(array('token'=>$token,'nick'=>$nick,'createtime'=>$time));
	$u_id = 0;
	}else{
	$u_id = $result['id'];
	}
    $_SESSION['u_id'] = $u_id;
	//放到收藏里去
	$num_iid = $_SESSION['num_iid'];
	if(!empty($num_iid) && !empty($u_id)){
	$cresult = $collection->field('id')->where(array('num_iid'=>$num_iid,'uid'=>$u_id))->find();
	if(empty($cresult)){
		if(session("uniq_user_id")){
			$collection->add(array('num_iid'=>$num_iid,'uid'=>$u_id,'cratetime'=>$time));
		}
	}
	}
	//取出收场数据
	$clothes = $goods->join('u_collection on u_goods.num_iid=u_collection.num_iid')->field('u_goods.id as gid,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_collection.id')->where(array('u_collection.uid'=>$u_id,'u_goods.isud'=>'1','u_collection.is_delete'=>'0'))->order('u_collection.id desc')->select();
   foreach($clothes as $k=>$v){
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
	$clothes[$k]['csex'] = $sexname;
   	$gtag = $goodtag->join('u_tag on u_tag.id=u_goodtag.ftag_id')->field('u_tag.name,u_goodtag.ccateid')->where(array('u_goodtag.good_id'=>$v['gid'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>2))->find();
	$clothes[$k]['tagname1'] = $gtag['name'];
	$clothes[$k]['fg'] = $gtag['ccateid'];
	//场合
	$gtag2 = $goodtag->join('u_tag on u_tag.id=u_goodtag.tag_id')->field('u_tag.name')->where(array('u_goodtag.good_id'=>$v['gid'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>1))->find();
	$clothes[$k]['tagname2'] = $gtag2['name'];
	$islove = $love->field('id')->where(array('num_iid'=>$v['num_iid'],'uid'=>$u_id))->find();
	if(!empty($islove)){
     $clothes[$k]['love'] = 1;
	}
	$isbuy = $buy->field('id')->where(array('num_iid'=>$v['num_iid'],'uid'=>$u_id))->find();
	if(!empty($isbuy)){
     $clothes[$k]['buy'] = 1;
	}
   }
	$pants = $goods->join('u_collection on u_goods.num_iid=u_collection.num_iid')->field('u_goods.id as gid,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_collection.id')->where(array('u_collection.uid'=>$u_id,'u_goods.isud'=>'2','u_collection.is_delete'=>'0'))->order('u_collection.id desc')->select();
   foreach($pants as $k=>$v){
   	switch($v['type']){
		case 1 :
		$sexname = '女装';
		break;
        case 2 :
		$sexname = '男装';
		break;
		case 3 :
		$sexname = '童装';
		break;
    }
	$pants[$k]['csex'] = $sexname;
   	$gtag = $goodtag->join('u_tag on u_tag.id=u_goodtag.ftag_id')->field('u_tag.name,u_goodtag.ccateid')->where(array('u_goodtag.good_id'=>$v['gid'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>2))->find();
	$pants[$k]['tagname1'] = $gtag['name'];
	$pants[$k]['fg'] = $gtag['ccateid'];
	//场合
	$gtag2 = $goodtag->join('u_tag on u_tag.id=u_goodtag.tag_id')->field('u_tag.name')->where(array('u_goodtag.good_id'=>$v['gid'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>1))->find();
	$pants[$k]['tagname2'] = $gtag2['name'];
	$islove = $love->field('id')->where(array('num_iid'=>$v['num_iid'],'uid'=>$u_id))->find();
	if(!empty($islove)){
     $pants[$k]['love'] = 1;
	}
	$isbuy = $buy->field('id')->where(array('num_iid'=>$v['num_iid'],'uid'=>$u_id))->find();
	if(!empty($isbuy)){
     $pants[$k]['buy'] = 1;
	}	
   }
   
   //取出性别所对应的tagid
   $tag = M('Tag');
   $recomodel = D('Reco');
   $wclist = $recomodel->getfc('1','1','1');//女性场合
   $wflist = $recomodel->getfc('2','1','1');//女性风格
   $mclist = $recomodel->getfc('1','2','1');//男性场合
   $mflist = $recomodel->getfc('2','2','1');//男性风格
   $cclist = $recomodel->getfc('1','3','1');//小孩场合
   $cflist = $recomodel->getfc('2','3','1');//小孩风格
   $bflist = $recomodel->getfc('2','4','1');//baby风格
		//取得官方推荐数据
	$windex = D('Windex');
	$tag = D('Tag');
    $cbusiness = $tag->gettagid('商务',1);
	$mcarr[] = array('id'=>$cbusiness,'name'=>'商务','c'=>1);
    $courism = $tag->gettagid('逛街',1);
	$mcarr[] = array('id'=>$courism,'name'=>'逛街','c'=>5);
	$csport = $tag->gettagid('运动',1);
	$mcarr[] = array('id'=>$csport,'name'=>'运动','c'=>3);
	$cjujia = $tag->gettagid('居家',1);
	$mcarr[] = array('id'=>$cjujia,'name'=>'居家','c'=>4);
    //取得场合id 
    $fbusiness = $tag->gettagid('休闲',2);
	$mfarr[] = array('id'=>$fbusiness,'name'=>'休闲','c'=>6);
    $fourism = $tag->gettagid('酷',2);
	$mfarr[] = array('id'=>$fourism,'name'=>'酷','c'=>4);
	$fsport = $tag->gettagid('英伦',2);
	$mfarr[] = array('id'=>$fsport,'name'=>'英伦','c'=>8);
	$fjujia = $tag->gettagid('学院',2);
	$mfarr[] = array('id'=>$fjujia,'name'=>'学院','c'=>9);

    //取出自定义分类
	$ucuslist = array();//上装
	$dcuslist = array();//下装
    //$custom = $customcate->cache(true)->field('id,name')->where(array('isud'=>'1'))->order('orderby')->group('name')->select();
    $custom = $customcate->field('id,name')->where(array('isud'=>'1'))->group('name')->select();
    //echo $customcate->getLastSql();exit;
	foreach($custom as $k=>$v){
    $idlist = $customcate->cache(true)->field('id')->where(array('name'=>$v['name'],'isud'=>'1'))->select();
	$idstr = '';
	foreach($idlist as $k1=>$v1){
	if($v1){
     $idstr.=$v1['id'].'_';
		}
	}
	$idstr = rtrim($idstr,'_');

    $ucuslist[] = array('id'=>$idstr,'name'=>$v['name']);
	}
	//转换自定义分类显示顺序s
	$k1 = 0;$k2 = 0;$k3 = 0;$k4 = 0;$k5 = 0;$k6 = 0;$k7 = 0;$k8 = 0;$k9 = 0;
	$k10 = 0;$k11 = 0;$k12 = 0;$k13 = 0;$k14 = 0;$k15 = 0;$k16 = 0;$k17 = 0;
	$k18 = 0;$k19 = 0;$k20 = 0;$k21 = 0;$k22 = 0;$k23 = 0;
	foreach($ucuslist as $ku=>$vu){
	if($vu['name']=='茄克'){
	$k1 = $ku;	
	}else if($vu['name']=='羽绒服'){
	$k2 = $ku;	
	}else if($vu['name']=='外套'){
	$k3 = $ku;	
	}else if($vu['name']=='大衣'){
	$k4 = $ku;	
	}else if($vu['name']=='开衫'){
	$k5 = $ku;	
	}else if($vu['name']=='连帽开衫'){
	$k6 = $ku;	
	}else if($vu['name']=='卫衣'){
	$k7 = $ku;	
	}else if($vu['name']=='马甲'){
	$k8 = $ku;	
	}else if($vu['name']=='毛衣'){
	$k9 = $ku;	
	}else if($vu['name']=='针织衫'){
	$k10 = $ku;	
	}else if($vu['name']=='法兰绒衬衫'){
	$k11 = $ku;	
	}else if($vu['name']=='衬衫'){
	$k12 = $ku;	
	}else if($vu['name']=='UT合作款'){
	$k13 = $ku;	
	}else if($vu['name']=='T恤'){
	$k14 = $ku;	
	}else if($vu['name']=='背心'){
	$k15 = $ku;	
	}else if($vu['name']=='吊带衫'){
	$k16 = $ku;	
	}else if($vu['name']=='婴幼儿装'){
	$k17 = $ku;	
	}else if($vu['name']=='连体装'){
	$k18 = $ku;	
	}else if($vu['name']=='POLO'){
	$k19 = $ku;	
	}else if($vu['name']=='连衣裙'){
	$k20 = $ku;	
	}else if($vu['name']=='保暖上装'){
	$k21 = $ku;	
	}else if($vu['name']=='家居服'){
	$k22 = $ku;	
	}else if($vu['name']=='内衣'){
	$k23 = $ku;	
	}
	}
	/*
	$middle = $ucuslist[2];
	$ucuslist[2] = $ucuslist[$k3];
	$ucuslist[$k3] = $middle;

	$middle = $ucuslist[0];
	$ucuslist[0] = $ucuslist[$k1];
	$ucuslist[$k1] = $middle;
    
	$middle = $ucuslist[1];
	$ucuslist[1] = $ucuslist[$k2];
	$ucuslist[$k2] = $middle;

	$middle = $ucuslist[3];
	$ucuslist[3] = $ucuslist[$k4];
	$ucuslist[$k4] = $middle;
	
	$middle = $ucuslist[4];
	$ucuslist[4] = $ucuslist[$k5];
	$ucuslist[$k5] = $middle;
	
	$ucuslist[5] = $ucuslist[19];
	*/
	$newucuslist = array();//上装
	$newucuslist[0] = $ucuslist[16];
	$newucuslist[1] = $ucuslist[14];
	$newucuslist[2] = $ucuslist[7];
	$newucuslist[3] = $ucuslist[8];
	$newucuslist[4] = $ucuslist[11];
	$newucuslist[5] = $ucuslist[19];
	$newucuslist[6] = $ucuslist[5];
	$newucuslist[7] = $ucuslist[22];
	$newucuslist[8] = $ucuslist[12];
	$newucuslist[9] = $ucuslist[21];
	$newucuslist[10] = $ucuslist[13];
	$newucuslist[11] = $ucuslist[17];
	$newucuslist[12] = $ucuslist[2];
	$newucuslist[13] = $ucuslist[1];
	$newucuslist[14] = $ucuslist[15];
	$newucuslist[15] = $ucuslist[6];
	$newucuslist[16] = $ucuslist[9];
	$newucuslist[17] = $ucuslist[18];
	$newucuslist[18] = $ucuslist[0];
	$newucuslist[19] = $ucuslist[20];
	$newucuslist[20] = $ucuslist[3];
	$newucuslist[21] = $ucuslist[10];
	$newucuslist[22] = $ucuslist[4];
		
	
	//转换自定义分类显示顺序e
    $dcustom = $customcate->cache(true)->field('id,name')->where(array('isud'=>'2'))->group('name')->select();
	foreach($dcustom as $k=>$v){
    $didlist = $customcate->cache(true)->field('id')->where(array('name'=>$v['name'],'isud'=>'2'))->select();
	$didstr = '';
	foreach($didlist as $k1=>$v1){
	if($v1){
     $didstr.=$v1['id'].'_';
		}
	}
	$didstr = rtrim($didstr,'_');
    $dcuslist[] = array('id'=>$didstr,'name'=>$v['name']);
	}
	foreach($dcuslist as $ku=>$vu){
	/*if($vu['name']=='婴幼儿装'){
		$dcuslist[$ku] = array();
	}*/
	if($vu['name']=='保暖下装'){
	$k1 = $ku;	
	}else if($vu['name']=='紧身裤'){
	$k2 = $ku;	
	}else if($vu['name']=='长裤'){
	$k3 = $ku;	
	}else if($vu['name']=='牛仔裤'){
	$k4 = $ku;	
	}
	}
	/*
	$middle = $dcuslist[2];
	$dcuslist[2] = $dcuslist[$k3];
	$dcuslist[$k3] = $middle;

	$middle = $dcuslist[0];
	$dcuslist[0] = $dcuslist[$k1];
	$dcuslist[$k1] = $middle;
    
	$middle = $dcuslist[1];
	$dcuslist[1] = $dcuslist[$k2];
	$dcuslist[$k2] = $middle;
	

	$middle = $dcuslist[3];
	$dcuslist[3] = $dcuslist[$k4];
	$dcuslist[$k4] = $middle;
	*/
	$newdcuslist = array();//下装
	$newdcuslist[0] = $dcuslist[7];
	$newdcuslist[1] = $dcuslist[13];
	$newdcuslist[2] = $dcuslist[4];
	$newdcuslist[3] = $dcuslist[10];
	$newdcuslist[4] = $dcuslist[14];
	$newdcuslist[5] = $dcuslist[9];
	$newdcuslist[6] = $dcuslist[11];
	$newdcuslist[7] = $dcuslist[12];
	$newdcuslist[8] = $dcuslist[1];
	$newdcuslist[9] = $dcuslist[0];
	$newdcuslist[10] = $dcuslist[2];
	$newdcuslist[11] = $dcuslist[8];
	$newdcuslist[12] = $dcuslist[3];
	$newdcuslist[13] = $dcuslist[6];
	$newdcuslist[14] = $dcuslist[5];
	
	
    $this->assign('nick',$nick);
	$this->assign('token',$token);
	$this->assign('clothes',$clothes);
	$this->assign('pants',$pants);
	
	/*$this->assign('reulist',$reulist);
	$this->assign('redlist',$redlist);*/
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

	//默认场合,风格id
	$this->assign('cbusiness',$cbusiness);
	$this->assign('courism',$courism);
	$this->assign('csport',$csport);
	$this->assign('cjujia',$cjujia);
	
	$this->assign('fbusiness',$fbusiness);
	$this->assign('fourism',$fourism);
	$this->assign('fsport',$fsport);
	$this->assign('fjujia',$fjujia);
	//自定义分类
	$this->assign('ucuslist',$newucuslist);
	$this->assign('dcuslist',$newdcuslist);
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
	$this->display();
	//}
    }
	
  public function callback(){
  	$gettoken = D('Gettoken');
	$url = U('Index/index');
    if(empty($_GET['code'])){
     header('Location: '.$url);
  	}else{
		//请求参数
		$postfields= array(
				'grant_type'	=> 'authorization_code',
				'client_id'     => $this->appkey,
				'client_secret' => $this->secretKey,
				'code'          => $_GET['code'],
				'redirect_uri'  => $this->callback_url
		);
		$url = 'https://oauth.taobao.com/token';
		$token = json_decode($gettoken->curltoken($url,$postfields));
		$_SESSION['token'] = $token->access_token;
		$this->redirect('Index/index');
	}
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
	//$this->success('退出成功',U('Index/index'));
	$this->redirect('Index/index');
  }

//登录
public function login(){
	$this->client = new TopClient;
	$this->client->format = 'json';
	$this->client->appkey = $this->appkey;
	$this->client->secretKey = $this->secretKey;
    $token = session('token');
	if(empty($token)){
    $url = 'https://oauth.taobao.com/authorize?response_type=code'
				. '&client_id=' . $this->appkey
				. '&redirect_uri=' . urldecode($this->callback_url);
	header('Location: '.$url);
	}else{
	 $url = U('Index/index');
     header('Location: '.$url);
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
if($id>0){
//if(!empty($_SESSION['token'])){
	if($flag==1){
	$love = M('Love');
	$time = date('Y-m-d H:i:s');
	$cresult = $love->field('id')->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->find();
	if(empty($cresult)){
	$love->add(array('num_iid'=>$id,'uid'=>session("uniq_user_id"),'cratetime'=>$time));
	}
	}else if($flag==2){
	$buy = M('Buy');
	$time = date('Y-m-d H:i:s');
	$cresult = $buy->field('id')->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->find();
	if(empty($cresult)){
	$buy->add(array('num_iid'=>$id,'uid'=>session("uniq_user_id"),'cratetime'=>$time));
	}		
	}
//}
}
}

//取出大配件图片
public function getdapeijian(){
  $id = trim($this->_post('id'));//Collectio表的num_iid
  $po = trim($this->_post('po'));
  $po = trim($po,'#');
  if($id>0){
  	//if(!empty($_SESSION['token'])){
  	$collection = M('Collection');
	$goods = M('Goods');
	$goodtag = M('Goodtag');
   	$cgtag = $goodtag->field('u_goodtag.wid,u_goodtag.gtype,u_goodtag.ccateid')->where(array('u_goodtag.num_iid'=>$id))->group('wid')->find();
   	$tag = $goodtag->field('u_goodtag.gtype,u_goodtag.tag_id,u_goodtag.ftag_id')->where(array('u_goodtag.wid'=>$cgtag['wid'],'u_goodtag.num_iid'=>$id))->select();

	//取出商品所对应的所有天气指数
	$widarr = $goodtag->field('wid')->where(array('num_iid'=>$id))->group('wid')->select();

    $wstr = '';
	foreach($widarr as $k=>$v){
	if($v){
    $wstr.=$v['wid'].',';
	}
	}
	if($cgtag['wid']!=8){
    $wstr.='8,';
	}

	$str='';//场合标签
	$fstr = '';//风格标签
	foreach($tag as $k=>$v){
	if($v){
	$str.=$v['tag_id'].',';	
    $fstr.=$v['ftag_id'].',';
	}
	}
	$str = rtrim($str,',');
	$fstr = rtrim($fstr,',');

	if($po=='cab-top'){
		//场合
    $where1 = array('u_goodtag.gtype'=>$cgtag['gtype'],'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>array('exp','IN('.$str.')'),'u_goods.num'=>array('egt','15'));
	//风格
    $where2 = array('u_goodtag.gtype'=>$cgtag['gtype'],'u_goodtag.isud'=>'2','u_goodtag.ftag_id'=>array('exp','IN('.$fstr.')'),'u_goods.num'=>array('egt','15'));
	//都符合的
    $where3 = array('u_goodtag.gtype'=>$cgtag['gtype'],'u_goodtag.isud'=>'2','u_goodtag.tag_id'=>array('exp','IN('.$str.')'),'u_goodtag.ftag_id'=>array('exp','IN('.$fstr.')'),'u_goods.num'=>array('egt','15'));

	if($cgtag['gtype']=='3' && ($cgtag['ccateid']!=75 && $cgtag['ccateid']!=86)){
    $where1['u_goodtag.ccateid'] = array(array('neq',116),array('exp','NOT IN(75,86)'),'and');
    $where2['u_goodtag.ccateid'] = array(array('neq',116),array('exp','NOT IN(75,86)'),'and');
	$where3['u_goodtag.ccateid'] = array(array('neq',116),array('exp','NOT IN(75,86)'),'and');
	}else if($cgtag['gtype']=='3' && ($cgtag['ccateid']==75 || $cgtag['ccateid']==86)){
    $where1['u_goodtag.ccateid'] = array(array('neq',116),array('exp','IN(75,86)'),'and');
    $where2['u_goodtag.ccateid'] = array(array('neq',116),array('exp','IN(75,86)'),'and');
	$where3['u_goodtag.ccateid'] = array(array('neq',116),array('exp','IN(75,86)'),'and');
	}
	}else if($po=='cab-bot'){
     //场合
	$where1 = array('u_goodtag.gtype'=>$cgtag['gtype'],'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>array('exp','IN('.$str.')'),'u_goods.num'=>array('egt','15'));	
	//风格
	$where2 = array('u_goodtag.gtype'=>$cgtag['gtype'],'u_goodtag.isud'=>'1','u_goodtag.ftag_id'=>array('exp','IN('.$fstr.')'),'u_goods.num'=>array('egt','15'));
	//全部
	$where3 = array('u_goodtag.gtype'=>$cgtag['gtype'],'u_goodtag.isud'=>'1','u_goodtag.tag_id'=>array('exp','IN('.$str.')'),'u_goodtag.ftag_id'=>array('exp','IN('.$fstr.')'),'u_goods.num'=>array('egt','15'));

	if($cgtag['gtype']=='3' && ($cgtag['ccateid']!=75 && $cgtag['ccateid']!=86)){
    $where1['u_goodtag.ccateid'] = array(array('neq',116),array('exp','NOT IN(75,86)'),'and');
    $where2['u_goodtag.ccateid'] = array(array('neq',116),array('exp','NOT IN(75,86)'),'and');
	$where3['u_goodtag.ccateid'] = array(array('neq',116),array('exp','NOT IN(75,86)'),'and');
	}else if($cgtag['gtype']=='3' && ($cgtag['ccateid']==75 || $cgtag['ccateid']==86)){
    $where1['u_goodtag.ccateid'] = array(array('neq',116),array('exp','IN(75,86)'),'and');
    $where2['u_goodtag.ccateid'] = array(array('neq',116),array('exp','IN(75,86)'),'and');
	$where3['u_goodtag.ccateid'] = array(array('neq',116),array('exp','IN(75,86)'),'and');
	}
	}
    if($cgtag['wid']!=8){
     $wstr = rtrim($wstr,',');
     $where1['u_goodtag.wid'] = array('exp','IN('.$wstr.')');
	 $where2['u_goodtag.wid'] = array('exp','IN('.$wstr.')');
	 $where3['u_goodtag.wid'] = array('exp','IN('.$wstr.')');
	}
    //全部
	$allclothes = $goodtag->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where($where3)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
    //场合
	$tclothes = $goodtag->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where($where1)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
	//风格
	$ftclothes = $goodtag->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where($where2)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
   
	foreach($tclothes as $k=>$v){
     $allclothes[] = $v;
	}
	foreach($ftclothes as $k=>$v){
     $allclothes[] = $v;
	}
	$dstr = '';
	foreach($allclothes as $k=>$v){
	$dstr.='<li><img id="'.$v['id'].'" price="'.$v['price'].'"  url="'.$v['detail_url'].'"data-original="'.__ROOT__.'/'.$v['pic_url'].'" alt="'.$v['title'].'" rest="'.$v['num'].'"></li>';	
	}
	echo $dstr;
  //}	
}
}

//ajax取数据
public function ajaxgood(){
    $type = trim($this->_request('tageid')); //场合id
    $sex = trim($this->_request('sexid'));//性别id
	$tem = trim($this->_request('tem'));//平均温度
	$pro = trim($this->_post('pro'));//省
	if($tem<=-10){
	$tem = -10;	
	}
	$goodtag = M('Goodtag');
		//取得官方推荐数据
    $windex = D('Windex');
	$recogood = $windex->getrecommend($pro);
	$reulist = $recogood[0];
	$redlist = $recogood[1];
	
	if(isset($tem)){
	//取出温度对应的商品数据 
	$widvalue = $windex->getwindex($tem);
	$wvalue = $widvalue['str'];
	$wherex = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'1','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$uclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($wherex)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
	$where = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'1','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$uclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
	$windex->saomo($uclothesx,$uclothes);

	$where2x = array('u_goodtag.wid'=>$widvalue['wid'],'u_goodtag.isud'=>'2','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$dclothesx = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2x)->group('u_goodtag.good_id')->order('u_goods.outer_id desc')->select();
	$where2 = array('u_goodtag.wid'=>array('exp','IN('.$wvalue.')'),'u_goodtag.isud'=>'2','u_goods.approve_status'=>'onsale','u_goods.num'=>array('egt','15'));
	$dclothes = $goodtag->cache(true)->join('INNER JOIN u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url,u_goodtag.ccateid')->where($where2)->group('u_goodtag.good_id')->order('u_goodtag.wid asc,u_goods.outer_id desc')->select();
	$windex->saomo($dclothesx,$dclothes);

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
	}
	$ustr = '';
	if(!empty($reulist)){
    foreach($reulist as $k=>$v){
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
    $arr['ustr'] = $ustr;
	$arr['flag'] = true;   
	}
	
	$dstr = '';
	if(!empty($redlist)){
	foreach($redlist as $k=>$v){
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

    $dstr.='<li><img sex="'.$v['type'].'" fg="'.$v['ccateid'].'" data-original="'.__ROOT__.'/'.$v['pic_url'].'" id="'.$v['num_iid'].'" place="'.$gtag2['name'].'" csex="'.$sexname.'" tag="'.$gtag['name'].'" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'" miniUrl="'.C('UNIQLOURL').'mini.php/Index/index/num/'.$v['num_iid'].'">
              </li>';
	}
    $arr['dstr'] = $dstr;
	$arr['flag'] = true;
    }
	echo json_encode($arr);
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
         $where1['u_goodtag.isud'] = '1';
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

         //吧当天的指数数据放前边
         foreach($uclothesy as $kx=>$vx){
		 if(!empty($vx)){
         $uclothes[] = $vx;
		 }
		 }
         $uclothesy = array();
         foreach($dclothesy as $kx=>$vx){
	     if(!empty($vx)){
         $dclothes[] = $vx;
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
	$arr['flag'] = 't';	

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