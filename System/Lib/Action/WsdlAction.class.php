<?php
class WsdlAction extends Action{
    private $aid;
    private $nick;
    public function _initialize(){
        $this->aid = session('aid');
        $this->nick = session('nickn');
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