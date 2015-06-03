<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
    public function index(){
    $aid = session('aid');
	$nick = session('nickn');
    if(!empty($aid)){
    $this->assign('level',session('level'));
    $this->assign('aid',$aid);
	$this->assign('nick',$nick);
    $this->display();
		exit;
    }else{
    $this->display('Login/index');
    }
}
	//登陆
	public function login(){
		$username = trim($this->_post('uname'));
		$pass = trim($this->_post('pass'));
		if(empty($username)){
		$this->error('账号不能为空',U('Index/index'));
			exit;	
		}
		if(empty($pass)){
		$this->error('密码不能为空',U('Index/index'));
			exit;	
		}
		$res = M('Admin')->field('aid,nickname,level')->where(array('email'=>$username,'pwd'=>md5($pass)))->find();
		if($res && !empty($res)){
        session('aid',$res['aid']);
		session('nickn',$res['nickname']);
        session('level',$res['level']);
        $this->assign('level',$res['level']);
		$this->success('登录成功',U('Index/index'));
		}else{
          $this->error('登录失败',U('Index/index'));
		}
	}
	//退出
	public function loginout(){
	$_SESSION=array();
	if(isset($_COOKIE[session_name()])){
		setCookie(session_name(), '', time()-100, '/');
	}
	session_destroy();
	$this->success('退出成功',U('Index/index'));
	exit;
	}
    public function _empty(){
	header("HTTP/1.1 404 Not Found");
	$this->error('次方法不存在',U('Index/index'));
	}
}