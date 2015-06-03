<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 15-1-19
 * Time: 下午5:27
 * 更新收藏数据，增加uq
 */
class DelpicAction extends Action{
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
    public function index(){
        if(!empty($this->aid)){
            echo $root_dir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
            $this->display();
        }else{
                $this->display('Login/index');
        }
    }

    //更新收藏明细
    public function updateCollDetail(){
        if(!empty($this->aid)){
        //$list = M('BeubeuCollection')->field('id,uid,gender,suitID')->limit('100,100')->select();
        $sql = "select `id`,`suitID` from `u_beubeu_collection` as bc where exists (select `bcid`,`uq` from `u_beubeu_coll_goods` as bcg where bcg.`bcid`=bc.`id` and bcg.`uq`='0') limit 0,500";
        $list = M('BeubeuCollection')->query($sql);
        $this->assign('list',json_encode($list));
        $this->display();
        }else{
            $this->display('Login/index');
        }
    }
    public function doupdatecoll(){
        $uq = trim($this->_post('uq'));
        $bcid = trim($this->_post('bcid'));
        if(!empty($uq)){
            $goods = M('Goods');
            $is_g = is_int(strpos($uq,'_'));
            if(!$is_g){
                $uq = $uq.'_';
            }
            $arr_uq = explode('_',$uq);
            foreach($arr_uq as $k=>$v){
                if($v){
                    $uv = substr($v,0,8);
                    $sql = "select `num_iid` from `u_goods` where item_bn like '".$uv."%'";
                    $result = $goods->query($sql);
                    unset($sql);
                    $sql = "select `num_iid` from `u_beubeu_coll_goods` where `bcid`=".$bcid;
                    $row = $goods->query($sql);
                    $baiArr = array();$fanArr = array();$tid = 0;
                    foreach($result as $k2=>$v2){
                        $baiArr[] = $v2['num_iid'];
                    }
                    foreach($row as $k3=>$v3){
                        $fanArr[] = $v3['num_iid'];
                    }
                    $endArr = array_intersect($baiArr,$fanArr);
                    $keyArr = array_keys($endArr);
                    $tid = $endArr[$keyArr[0]];
                    $re = M('BeubeuCollGoods')->where(array('bcid'=>$bcid,'num_iid'=>$tid))->save(array('uq'=>$v));
                    /*$sql = "update `u_beubeu_coll_goods` set `uq`='".$v."' where `bcid`={$bcid} and `num_iid`=".$tid;
                    if(!$re){
                        $str = $bcid.'_'.$tid.'_'.$v."\n";
                        error_log(print_r($str,1),3,'co.txt');
                    }*/
                }
            }
        }
    }
}