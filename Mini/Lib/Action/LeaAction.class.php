<?php
class LeaAction extends Action{
	public function addlea(){
    $ip = get_client_ip();
    $catid = $this->_post('cate');
	$content = trim($this->_post('con'));
	$data = array('catid'=>$catid,
	              'content'=>$content,
				  'ip'=>$ip,
				  'createtime'=>date('Y-m-d H:i:s'));
    $res = M('Leave')->add($data);
	if($res){
	echo '感谢您的反馈';	
	}else{
	echo '添加失败';	
	}
	}
    public function upShowTag(){
        $uid = session("uniq_user_id");
        if($uid>0){
        M('User')->where(array('id'=>$uid))->save(array('showtag'=>array('exp','showtag+1')));
        }
    }
}