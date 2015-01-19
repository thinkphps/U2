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
            $styles = M('settings_suit_style');
            $stylelist = $styles->field('*')->select();
            $genderlist = M('SettingsSuitGender')->field('*')->select();
            $msuit = M('suits_select u1');

            $selsuits1 = $msuit->field('u3.description,u2.suitImageUrl,u2.suitID,u2.suitStyleID as ID,u1.selected,u1.sortid')->
                join('inner join u_suits u2 on u1.suitID=u2.suitID')->
                join('left join u_settings_suit_style u3 on u2.suitStyleID = u3.ID')->
                where(array('u1.selected'=>'1','u1.type' =>'1'))->select();

            /*$selsuits1 = array();
            foreach ( $stylelist as $r => $styleRow ){
                $tmpimg = array("description"=>$styleRow["description"],"suitImageUrl"=>"__TMPL__Public/images/nosetting.jpg",
                    "suitID"=>"","suitStyleID"=>$styleRow['ID'],"selected"=>"");
                foreach ( $result as $r => $suitRow ){
                   if($styleRow['ID']== $suitRow['suitStyleID']) {
                       $tmpimg['description'] = $suitRow['description'];
                       $tmpimg['suitImageUrl'] = $suitRow['suitImageUrl'];
                       $tmpimg['suitID'] = $suitRow['suitID'];
                       $tmpimg['suitStyleID'] = $suitRow['suitStyleID'];
                       $tmpimg['selected'] = $suitRow['selected'];
                       break;
                   }
                }
                array_push($selsuits1,$tmpimg);
            }*/

            $selsuits2 = $msuit->field('u3.description,u2.suitImageUrl,u2.suitID,u2.suitStyleID as ID,u1.selected,u1.sortid')->
                join('inner join u_suits u2 on u1.suitID=u2.suitID')->
                join('left join u_settings_suit_style u3 on u2.suitStyleID = u3.ID')->
                where(array('u1.selected'=>'1','u1.type' =>'2'))->select();

            /*$selsuits2 = array();
            foreach ( $stylelist as $r => $styleRow ){
                $tmpimg = array("description"=>$styleRow["description"],"suitImageUrl"=>"__TMPL__Public/images/nosetting.jpg",
                    "suitID"=>"","suitStyleID"=>$styleRow['ID'],"selected"=>"");
                foreach ( $result as $r => $suitRow ){
                    if($styleRow['ID']== $suitRow['suitStyleID']) {
                        $tmpimg['description'] = $suitRow['description'];
                        $tmpimg['suitImageUrl'] = $suitRow['suitImageUrl'];
                        $tmpimg['suitID'] = $suitRow['suitID'];
                        $tmpimg['suitStyleID'] = $suitRow['suitStyleID'];
                        $tmpimg['selected'] = $suitRow['selected'];
                        break;
                    }
                }
                array_push($selsuits2,$tmpimg);

            }*/
            $mtm = M('settings');
            $tm = $mtm -> field('value')->where(array('key'=>'temperature'))->select();
            if(!empty($tm)){
                $tm = $tm[0]['value'];
            }else{
                $tm = '';
            }
//            $daterange = "2013-04-03";
            $this->assign('stylelist',$stylelist);
            $this->assign('genderlist',$genderlist);
            $this->assign('tm',$tm);
            $this->assign('selsuits1',$selsuits1);
            $this->assign('selsuits2',$selsuits2);
            $this->display();
        }else{
            $this->display('Login/index');
        }
	}

  public function search(){
      if(!empty($this->aid)){
          $style = $this->_post('isstyle');
          $sex = $this->_post('sex');
          $numiid = $this->_post('numiid');
          $page = $this->_post('page');
          $act = $this->_post('act');
          $map = array();
          if(!empty($style)){
              $map['u1.suitStyleID'] = $style;
          }
          if(!empty($sex)){
              $map['u1.suitGenderID'] = $sex;
          }
          $map['u1.approve_status'] = '0';
          $map['u1.beubeuSuitID'] = array('exp','is not null');
              $suits = M('Suits u1');
              $goodsdetail = M('suits_goodsdetail u3');
              if(empty($numiid)){
                  $count = $suits->field('u1.suitID,u1.suitStyleID,u1.suitImageUrl,u2.description')
                      ->join("left join u_settings_suit_style u2 on u1.suitStyleID=u2.ID")
                      ->where($map)->count();
              }else{
                  $map['u3.num_iid'] = $numiid;
                      $count = $goodsdetail->field('distinct u1.suitID,u1.suitStyleID,u1.suitImageUrl,u2.description')
                          ->join("inner join u_suits u1 on u1.suitID=u3.suitID")
                          ->join("left join u_settings_suit_style u2 on u1.suitStyleID=u2.ID")
                          ->where($map)->count();
              }
              if($count=='0'){
                  echo "";
                  exit;
              }
              $count = intval($count);
              $page = intval($page);
              if($act=="per"){
                  $page = $page - 1;
              }
              if($act=="next"){
                  $page = $page + 1;
              }
              if($page<=0){
                  $page = 1;
              }
              $firstRows = ($page -1)*4;
              if($firstRows>=$count){
                  echo "1";
                  exit;
              }
              $maxRows = 4;
              if(empty($numiid)){
                  $result = $suits->field('u1.suitID,u1.suitStyleID,u1.suitImageUrl,u2.description')
                      ->join("left join u_settings_suit_style u2 on u1.suitStyleID=u2.ID")
                      ->where($map)->order('u1.suitID desc')->limit($firstRows.','.$maxRows)->select();
              }else{
                      $result = $goodsdetail->field('distinct u1.suitID,u1.suitStyleID,u1.suitImageUrl,u2.description')
                          ->join("inner join u_suits u1 on u1.suitID=u3.suitID")
                          ->join("left join u_settings_suit_style u2 on u1.suitStyleID=u2.ID")
                          ->where($map)->order('u1.suitID desc')->limit($firstRows.','.$maxRows)->select();
              }
              if(empty($result)){
                  echo "";
                  exit;
              }
//              $result = $suits->field('*')->where(array('suitStyleID'=>$style))->order('uptime desc')->limit($p->firstRows.','.$p->maxRows)->select();
              $imgs = array("1"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>"","goods"=>""),
                  "2"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>"","goods"=>""),
                  "3"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>"","goods"=>""),
                  "4"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>"","goods"=>""),"page"=>$page);

              $suitgoods = M('suits_goodsdetail u1');

              foreach ( $result as $r => $dataRow ){
                  $imgs[$r+1]["id"] = $dataRow["suitID"];
                  $imgs[$r+1]["img"] = $dataRow["suitImageUrl"];
                  $imgs[$r+1]["styleID"] = $dataRow["suitStyleID"];
                  $imgs[$r+1]["desp"] = $dataRow["description"];

                  $result = $suitgoods->field('u1.num_iid,u2.title,u2.detail_url,u2.pic_url')
                      ->join("inner join u_beubeu_goods u2 on u1.num_iid=u2.num_iid")
                      ->where(array('u1.suitID'=>$dataRow["suitID"],'u2.approve_status' => 'onsale','u2.num'=>array('egt',5)))->select();
                  if(!empty($result)){
                      $imgs[$r+1]["goods"] = $result;
                  }
              }
              $stylelist = array($imgs);
              echo json_encode($stylelist);

      }else{
        $this->display('Login/index');
      }
  }

  public function recommend(){
      if(!empty($this->aid)){
          $recosuits = $this->_post('reco');
          $recosuits = str_replace("'",'"',$recosuits);
          $recosuits = json_decode($recosuits,true);

          $msuit = M('SuitsSelect');
          //$res = $msuit->query('truncate table u_suits_select');
          if(!empty($recosuits)){
              foreach ( $recosuits as $r => $dataRow ){
                  if(!empty($dataRow['sid'])){
                      $result = $msuit->field('ID')->where(array("suitID"=>$dataRow['sid'],"type"=>$dataRow['type']))->find();
                      if(empty($result)){
                      $sel='1';
                      $data = array("suitID"=>$dataRow['sid'],"selected"=>$sel,"type"=>$dataRow['type'],'sortid'=>$dataRow['oid']);
                      $res = $msuit->add($data);
                      if(!$res){
                          echo 0;
                          exit;
                      }
                  }else{
                      $data = array('sortid'=>$dataRow['oid']);
                      $msuit->where(array("suitID"=>$dataRow['sid'],"type"=>$dataRow['type']))->save($data);
                   }
                  }
              }
              echo 1;
              exit;
          }else{
              echo 1;
          }

      }else{
          $this->display('Login/index');
      }
  }
  public function savetmp(){
      if(!empty($this->aid)){
          $tmp = $this->_post('tmp');
          $mtm = M('settings');
          $mtm->where(array('key'=>'temperature'))->setField('value',$tmp);
          echo(1);
      }else{
          $this->display('Login/index');
      }
  }
 public function del(){
     if(!empty($this->aid)){
       $suitid = trim($this->_post('suitid'));
       $type = trim($this->_post('type'));
       $msuit = M('suits_select');
       $resu = $msuit->where(array('suitID'=>$suitid,'type'=>$type))->find();
       if(!empty($resu)){
       $res = $msuit->where(array('suitID'=>$suitid,'type'=>$type))->delete();
       if($res){
           $arr['code'] = 1;
           $arr['msg'] = '删除成功';
       }else{
           $arr['code'] = 0;
           $arr['msg'] = '删除失败';
       }
       }else{
           $arr['code'] = 1;
           $arr['msg'] = '删除成功';
       }
     }else{
         $arr['code'] = 0;
         $arr['msg'] = '没有登录';
     }
     $this->ajaxReturn($arr, 'JSON');
 }
    public function getsexstyle(){
    if(!empty($this->aid)){
      $sex = trim($this->_post('sex'));
      $stylemodel = M('SettingsSuitStyle t1');
      if(!empty($sex)){
         $result = $stylemodel->field('t1.ID,t1.description')->join('inner join `u_settings_gender_style` as t2 on t2.styleID=t1.ID')->where(array('t2.genderID'=>$sex))->select();
      }else{
          $result = $stylemodel->field('*')->select();
      }
      $arr = array('code'=>1,'re'=>$result);
        $this->ajaxReturn($arr, 'JSON');
    }else{
     $arr['code'] = 0;
     $arr['msg'] = '没有登录';
    }
    }
}
