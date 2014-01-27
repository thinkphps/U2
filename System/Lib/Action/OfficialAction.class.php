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
	$list1 = $reco->field('*')->where(array('type'=>'1'))->select();
	$list2 = $reco->field('*')->where(array('type'=>'2'))->select();
	$tm = $list1[0]['tm']?$list1[0]['tm']:$list2[0]['tm'];
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
  $reco = M('Recommend');
  if($rid>0){
  $data = array('num_iid'=>$num_iid,
	            'type'=>$type,
	            'isud'=>$issue,
				'tm'=>$tm);
  $res = $reco->where(array('id'=>$rid))->save($data);
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
}
