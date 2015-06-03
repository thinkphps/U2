<?php
class WsdlAction extends Action{
    private $aid;
    private $nick;
    public function _initialize(){
        $this->aid = session('aid');
        $this->nick = session('nickn');
        $level = session('level');
        if($level!=1){
            $this->error('权限不够',U('Index/index'));
            exit;
        }
        $this->assign('aid',$this->aid);
        $this->assign('nick',$this->nick);
    }
    function index(){
        if(!empty($this->aid)){
           $this->display();
        }else{
            $this->display('Login/index');
        }
    }
}