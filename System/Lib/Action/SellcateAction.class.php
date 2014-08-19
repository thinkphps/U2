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
            if(isset($_REQUEST['issel'])){
            $issel = $this->_request('issel');//是否显示
            }else{
                $issel = -1;
            }
            if(isset($_REQUEST['isshow'])){
               $isshow = $this->_request('isshow');//是否有商品
            }else{
               $isshow = -1;
            }
            $pagestr = '';
            $sqlwhere = '';
            import("@.ORG.Pageyu");
            if(!empty($gender)){
                $map['gender'] = $gender;
                $sqlwhere.=' and s2.gender='.$gender;
                $pagestr.= '/gender/'.$gender;
            }
            if(!empty($isud)){
                $map['isud'] = $isud;
                $sqlwhere.=' and s2.isud='.$isud;
                $pagestr.= '/isud/'.$isud;
            }
            if($issel==1 || $issel==0){
                $map['selected'] = $issel;
                $sqlwhere.=' and s2.selected='.$issel;
                $pagestr.= '/issel/'.$issel;
            }else{
                $issel = -1;
            }
            if($isshow==1 || $isshow==0){
                $map['isshow'] = $isshow;
                $sqlwhere.=' and s2.isshow='.$isshow;
                $pagestr.= '/isshow/'.$isshow;
            }else{
                $isshow = -1;
            }
            if(!empty($keyword)){
                $where['ID'] = $keyword;
                $where['cateName']  = array('like','%'.$keyword.'%');
                $where['shortName']  = array('like','%'.$keyword.'%');
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $sqlwhere.=" and (s2.ID=".$keyword." or s2.cateName like '%".$keyword."%' or s2.shortName like '%".$keyword."%')";
                $pagestr.="/keyword/".$keyword;
            }
            $count = $sell->where($map)->count();
            $p = new Page($count,20,$pagestr);
            //$sells = $sell->field('*')->where($map)->order('ID desc')->limit($p->firstRows.','.$p->maxRows)->select();
            $sql = "SELECT s2.*,s3.cateName as pname from u_sellercats as s2 left join (select * from u_sellercats where parentID=0) as s3 on s3.ID=s2.parentID where 1".$sqlwhere." order by sort_order asc,gender asc limit ".$p->firstRows.",".$p->maxRows;
            $sells = $sell->query($sql);
            $page = $p->showPage();
            $this->assign('sells',$sells);
            $this->assign('p',$_REQUEST['p']);
            $this->assign('page',$page);
            $this->assign('gender',$gender);
            $this->assign('isud',$isud);
            $this->assign('issel',$issel);
            $this->assign('keyword',$keyword);
            $this->assign('isshow',$isshow);
            $this->display();
        }else{
            $this->display('Login/index');
        }
    }
    public function selledit(){
        if(!empty($this->aid)){
           $id = trim($this->_request('id'));
           $p = trim($this->_request('p'));
           if($id>0){
               $sell = M('Sellercats');
               $result = $sell->field('*')->where(array('ID'=>$id))->find();
               $this->assign('result',$result);
               $this->assign('p',$p);
               $this->display();
           }else{
               $this->error('参数错误',U('Sellcate/index',array('p'=>$p)));
               exit;
           }
        }else{
            $this->display('Login/index');
        }
    }

    public function add(){
        if(!empty($this->aid)){
          $cid = trim($this->_request('cid'));
          $p = trim($this->_request('p'));
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
                  $this->success('提交成功',U('Sellcate/index',array('p'=>$p)));
                  exit;
              }else{
                  $this->error('提交失败',U('Sellcate/selledit',array('id'=>$cid,'p'=>$p)));
                  exit;
              }
          }else{
              $this->error('参数错误',U('Sellcate/index',array('p'=>$p)));
              exit;
          }
        }else{
            $this->display('Login/index');
        }
    }
    public function uporder(){
        if(!empty($this->aid)){
         $otype = trim($this->_post('otype'));
         $cateid = trim($this->_post('cateid'));
         if($otype=='up'){
          $wap['sort_order'] = array('exp','sort_order-1');
         }else if($otype=='down'){
          $wap['sort_order'] = array('exp','sort_order+1');
         }
          M('Sellercats')->where(array('ID'=>$cateid))->save($wap);
            $arr['code'] = 1;
        }else{
         $arr['code'] = 0;
         $arr['msg'] = '没有登录';
        }
        $this->ajaxReturn($arr, 'JSON');
    }
}