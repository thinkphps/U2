<?php
class OfficialAction extends Action{
	private $aid;
	private $nick;
	public function _initialize(){
	$this->aid = session('aid');
	$this->nick = session('nickn');
    $this->assign('aid',$this->aid);
    $this->assign('nick',$this->nick);
	}
	public function index(){
	if(!empty($this->aid)){
	$reco = M('Recommend');
	$products = M('Products');
	$list1 = $reco->field('*')->where(array('type'=>'1'))->select();
	$list2 = $reco->field('*')->where(array('type'=>'2'))->select();
	//取出颜色信息
	foreach($list1 as $k=>$v){
	$list1[$k]['color'] = $products->field('cvalue,url')->where(array('num_iid'=>$v['num_iid']))->group('cid')->select();
	}
	foreach($list2 as $k=>$v){
	$list2[$k]['color'] = $products->field('cvalue,url')->where(array('num_iid'=>$v['num_iid']))->group('cid')->select();
	}
	$tm = $list1[0]['tm'] ? $list1[0]['tm'] : $list2[0]['tm'];
	$this->assign('tm',$tm);
	$this->assign('list1',$list1);
	$this->assign('list2',$list2);
	$this->display();
	}else{
    $this->display('Login/index');
    }		
	}
	
  public function add(){
  if(!empty($this->aid)){
	  $num_iid = $this->_post('kuid');
	  $issue = $this->_post('issue');
	  $type = $this->_post('type');
	  $rid = trim($this->_post('rid'));
	  $tm = trim($this->_post('lttem'));
	  $tm = intval($tm);
	  $srcvalue = trim($this->_post('srcvalue'));
	  $reco = M('Recommend');
	  $good = M('Goods');
	  if($rid>0){
	  $data = array('num_iid'=>$num_iid,
		            'type'=>$type,
		            'isud'=>$issue,
					'tm'=>$tm);
	  $res = $reco->where(array('id'=>$rid))->save($data);
	  if(!empty($srcvalue)){
	  $good->where(array('num_iid'=>$num_iid))->save(array('pic_url'=>$srcvalue));
	  }
  }else{
	  $data = array('num_iid'=>$num_iid,
		            'type'=>$type,
		            'isud'=>$issue,
					'tm'=>$tm);
	  $res = $reco->add($data);
  }
	  $wap['type'] = array(array('eq','1'),array('eq','2'),'or');
	  $reco->where($wap)->save(array('tm'=>$tm));
	  $arr = array();
  if($res){
	   $arr['flag']=true;
	   $arr['num_iid'] = $num_iid;
	   $arr['rid'] = $res;
	   $arr['stm'] = $tm;
  }else{
	   $arr['flag'] =false;
	   $arr['msg'] = '添加失败';
  }
}else{
	   $arr['flag'] =false;
	   $arr['msg'] = '你还没有登陆';
}
       echo json_encode($arr);
  }

//获取颜色
public function getcolor(){
	if(!empty($this->aid)){
	$num_iid = trim($this->_post('num_iid'));
	$type = $this->_post('type');
	$id = $this->_post('id');
	$num_iid = !empty($num_iid)?$num_iid:0;
	$products = M('Products');
	$product = $products->field('cvalue,url')->where(array('num_iid'=>$num_iid))->group('cid')->select();
	$str = '';
	if(!empty($product)){
	foreach($product as $k=>$v){
	if(!empty($v)){
	$str.="<img src='".__ROOT__."/".$v['url']."' title='".$v['cvalue']."' width='50px' height='50px' onclick=\"getcolorUrl($id,$type,'".$v['url']."');\">&nbsp;&nbsp;";
	}
	}
	$returnArr = array('code'=>1,'msg'=>$str);	
	}else{
	$returnArr = array('code'=>-1,'msg'=>'没有数据');	
	}
	}else{
	$returnArr = array('code'=>-1,'msg'=>'你还没有登录');	
	}
	$this->ajaxReturn($returnArr,'JSON');
}
}
