<?php
class GetisutAction extends Action{
	//获取衣服的部位 
	public function index(){
		
		$id	 = trim($this->_post('id'));
		if($id>0){
		$goodtag = M('Goodtag');
		$list = $goodtag->field('isud')->where(array('num_iid'=>$id))->find();
		if(!empty($list)){
		$returnArr = array('code'=>1,'isud'=>$list['isud']);		
		}else{
		$returnArr = array('code'=>-2,'msg'=>'参数错误');		
		}
	    }else{
	    $returnArr = array('code'=>-2,'msg'=>'参数错误');	
	    }
		$this->ajaxReturn($returnArr,'JSON');
	}
}
