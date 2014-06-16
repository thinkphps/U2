<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 14-5-30
 * Time: 下午4:18
 * To change this template use File | Settings | File Templates.
 */
class SellcateAction extends Action{
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
            $sell = M('Sellercats');
            $keyword = trim($this->_request('keyword'));
            $gender = $this->_request('gender');//类别
            $isud = $this->_request('isud');//部位
            $issel = $this->_request('issel');//是否显示

            $pagestr = '';
            import("@.ORG.Pageyu");
            if(!empty($gender)){
                $map['gender'] = $gender;
                $pagestr.= '/gender/'.$gender;
            }
            if(!empty($isud)){
                $map['isud'] = $isud;
                $pagestr.= '/isud/'.$isud;
            }
            if($issel==1 || $issel==0){
                $map['selected'] = $issel;
                $pagestr.= '/issel/'.$issel;
            }else{
                $issel = -1;
            }
            if(!empty($keyword)){
                $where['ID'] = $keyword;
                $where['cateName']  = array('like','%'.$keyword.'%');
                $where['shortName']  = array('like','%'.$keyword.'%');
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $pagestr.="/keyword/".$keyword;
            }
            $count = $sell->where($map)->count();
            $p = new Page($count,20,$pagestr);
            $sells = $sell->field('*')->where($where)->order('ID desc')->limit($p->firstRows.','.$p->maxRows)->select();
            $page = $p->showPage();
            $this->assign('sells',$sells);
            $this->assign('page',$_GET['page']);
            $this->assign('gender',$gender);
            $this->assign('isud',$isud);
            $this->assign('issel',$issel);
            $this->assign('keyword',$keyword);
            $this->display();
        }else{
            $this->display('Login/index');
        }
    }
    public function selledit(){
        if(!empty($this->aid)){
           $id = trim($this->_request('id'));
           if($id>0){
               $sell = M('Sellercats');
               $result = $sell->field('*')->where(array('ID'=>$id))->find();
               $this->assign('result',$result);
               $this->display();
           }else{
               $this->error('参数错误',U('Sellcate/index'));
               exit;
           }
        }else{
            $this->display('Login/index');
        }
    }

    public function add(){
        if(!empty($this->aid)){
          $cid = trim($this->_request('cid'));
          if($cid>0){
              $sell = M('Sellercats');
              $sort_order = trim($this->_request('sort_order'));
              $issel = trim($this->_request('issel'));
              $gender = trim($this->_request('gender'));
              $isud = trim($this->_request('isud'));
              $shortName = trim($this->_request('shortName'));
              $data = array('sort_order'=>$sort_order,
                            'gender'=>$gender,
                            'isud'=>$isud,
                            'selected'=>$issel,
                            'shortName'=>$shortName);
              $res = $sell->where(array('ID'=>$cid))->save($data);
              if($res){
                  $this->success('提交成功',U('Sellcate/index'));
                  exit;
              }else{
                  $this->error('提交失败',U('Sellcate/selledit',array('id'=>$cid)));
                  exit;
              }
          }else{
              $this->error('参数错误',U('Sellcate/index'));
              exit;
          }
        }else{
            $this->display('Login/index');
        }
    }
}