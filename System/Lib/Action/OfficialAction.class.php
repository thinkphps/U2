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
            $msuit = M('suits_select u1');

            $selsuits1 = $msuit->field('u3.description,u2.suitImageUrl,u2.suitID,u2.suitStyleID,u1.selected')->
                join('inner join u_suits u2 on u1.suitID=u2.suitID')->
                join('left join u_settings_suit_style u3 on u2.suitStyleID = u3.ID')->
                where("u1.`type` = '1'")->select();

            $selsuits2 = $msuit->field('u3.description,u2.suitImageUrl,u2.suitID,u2.suitStyleID,u1.selected')->
                join('inner join u_suits u2 on u1.suitID=u2.suitID')->
                join('left join u_settings_suit_style u3 on u2.suitStyleID = u3.ID')->
                where("u1.`type` = '2'")->select();

            $mtm = M('settings');
            $tm = $mtm -> field('value')->where(array('key'=>'temperature'))->select();
            if(!empty($tm)){
                $tm = $tm[0]['value'];
            }else{
                $tm = '';
            }

            $this->assign('stylelist',$stylelist);
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
          $page = $this->_post('page');
          $act = $this->_post('act');
          if(!empty($style)){
              $suits = M('Suits u1');

              $count = $suits->field('u1.suitID,u1.suitStyleID,u1.suitImageUrl,u2.description')
                  ->join("left join u_settings_suit_style u2 on u1.suitStyleID=u2.ID")
                  ->where(array('u1.suitStyleID'=>$style))->count();

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
              $result = $suits->field('u1.suitID,u1.suitStyleID,u1.suitImageUrl,u2.description')
                  ->join("left join u_settings_suit_style u2 on u1.suitStyleID=u2.ID")
                  ->where(array('u1.suitStyleID'=>$style))->order('u1.uptime desc')->limit($firstRows.','.$maxRows)->select();

              if(empty($result)){
                  echo "";
                  exit;
              }
//              $result = $suits->field('*')->where(array('suitStyleID'=>$style))->order('uptime desc')->limit($p->firstRows.','.$p->maxRows)->select();
              $imgs = array("1"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>""),
                  "2"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>""),
                  "3"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>""),
                  "4"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>""),"page"=>$page);
              foreach ( $result as $r => $dataRow ){
                  $imgs[$r+1]["id"] = $dataRow["suitID"];
                  $imgs[$r+1]["img"] = $dataRow["suitImageUrl"];
                  $imgs[$r+1]["styleID"] = $dataRow["suitStyleID"];
                  $imgs[$r+1]["desp"] = $dataRow["description"];
              }
              $stylelist = array($imgs);
              echo json_encode($stylelist);
          }else{
              echo "";
          }
      }else{
        $this->display('Login/index');
      }


  }

  public function recommend(){
      if(!empty($this->aid)){
          $recosuits = $this->_post('reco');
          $recosuits = str_replace("'",'"',$recosuits);
          $recosuits = json_decode($recosuits);

          $msuit = M();
          $res = $msuit->query('truncate table u_suits_select');
          $msuit = M('suits_select');
          if(!empty($recosuits)){
              foreach ( $recosuits as $r => $dataRow ){
                  $sel='0';
                  if($dataRow->reco=="true"){
                      $sel='1';
                  }
                  $data = array("suitID"=>$dataRow->sid,"selected"=>$sel,"type"=>$dataRow->type);
                  $res = $msuit->add($data);
                  if(!$res){
                      echo 0;
                      exit;
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

}
