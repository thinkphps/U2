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
      $this->GetTagsByUqID111();
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
              $firstRows = ($page -1)*5;
              if($firstRows>=$count){
                  echo "1";
                  exit;
              }
              $maxRows = 5;
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
                  "4"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>""),
                  "5"=>array("id"=>"","img"=>"","styleID"=>"","desp"=>""),"page"=>$page);
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
          $type = $this->_post('type');
          $recosuits = $this->_post('reco');
          $recosuits = str_replace("'",'"',$recosuits);
          $recosuits = json_decode($recosuits);

          $msuit = M('suits_select');
          $res = $msuit->where(array("type"=>$type))->delete();

          if(!empty($recosuits)){
              foreach ( $recosuits as $r => $dataRow ){
                  $sel=0;
                  if($dataRow->reco=="true"){
                      $sel=1;
                  }
                  $data = array("suitID"=>$dataRow->sid,"selected"=>$sel,"type"=>$type);
//                      $msuit->create($data);
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





    //dean 同步搭配接口
    function UpdateSuits111()
    {
        $returnProductValue = array(
            'code' => 1,
            'msg' => array(
                'success' => '',
                'error' => ''
            )
        );

        //$callback=$_GET["callback"];

        $app = 'baiyi';
        $key = '12345678';
        $data = array(
            "suits"=>array(
                array('id'=>1,'style'=>1,'gender'=>1,'pic'=>'/img/stqt1.jpg',
                    'products'=>array(array('uq'=>'UQ080776000'),array('uq'=>'UQ079696000'),array('uq'=>'UQ080038000'))
                ),
                array('id'=>2,'style'=>2,'gender'=>2,'pic'=>'/img/stqt2.jpg',
                    'products'=>array(array('uq'=>'UQ080776001'),array('uq'=>'UQ079696001'),array('uq'=>'UQ080038001'))
                )
            )
        );
        $batchid = '1';

        if (!isset($data) || !isset($key) || !isset($app) || !isset($batchid) ||
            empty($data) || empty($key) || empty($app) || empty($batchid)
        ) {
            $returnProductValue['msg']['error'] = '1. 传递参数错误';
        } else {

            $suits = $data["suits"];

            if (!isset($suits)|| empty($suits)){
                $returnProductValue['msg']['error'] = '1. 传递参数错误';
            }else{
//                $suitSyn = D('SuitsSyn');
                $returnProductValue = $this->UpdateSuits($app, $key, $suits, $batchid);
            }
        }

        $this->ajaxReturn($returnProductValue, 'JSONP');
        //echo $callback."($json)";
//        echo $callback.'('.$json.')';

    }


     function UpdateSuits($app,$key,$suits,$batchid){

        $returnProductValue = array(
            'code' => -1,
            'msg' => array(
                'success' => '',
                'error' =>	''
            )
        );

        $currentDateTime = date('Y-m-d H:i:s', time());

        $uqSuitsCounts = count($suits);

//        $checkmsg = $this->CheckUpdateSuits($app,$key,$uqSuitsCounts);
         $checkmsg = '';
        if($checkmsg == '')
        {
            $this->AddSuitsHistory($suits,$batchid,$currentDateTime);
            $returnProductValue['code'] = 1;
            $returnProductValue['msg']['success'] = $this->AddOrUpdateSuits($suits,$currentDateTime);
        }
        else
        {
            $returnProductValue['code'] = -1;
            $returnProductValue['msg']['error'] = $checkmsg;
        }
        return $returnProductValue;
    }

    private function CheckUpdateSuits($app,$key,$uqProductCounts){

        date_default_timezone_set('PRC');
        $tempCurrentTime = time();
        $currentDateTime = date('Y-m-d H:i:s', $tempCurrentTime);
        $currentDate = date('Y-m-d',$tempCurrentTime);

        $returnValue = '';
        $currentInvokeCounts = 0;
        $settings = D('Settings');
        $apiCounts = $settings->getAPIInvokeCounts();

        $appKey = M('App_key');
        $map['app'] = $app;
        $map['appkey'] = $key;

        $result = $appKey->field('id,invoketime,counts')->where($map)->find();
        if(isset($result))
        {
            $lastInvokeDate = date('Y-m-d',strtotime($result['invoketime']));
            $lastInvokeCounts = $result['counts'];
            $currentInvokeCounts = $lastInvokeCounts;

            if($currentDate == $lastInvokeDate)
            {
                if($lastInvokeCounts <= $apiCounts)
                {
                    $tableRowCounts = $settings->getTableRowCounts();
                    if($uqProductCounts <= $tableRowCounts)
                    {
                        $returnValue = '';
                    }
                    else
                    {
                        $returnValue = '4. 商品大于'.$tableRowCounts.'条';
                    }
                }
                else
                {
                    $returnValue = '3. 调用次数频繁，请稍后再试';
                }
            }
            else if($currentDate > $lastInvokeDate)
            {
                $currentInvokeCounts = 0;
            }
            $appKey->where($result['id'])->setField(array('invoketime'=> $currentDateTime,'counts'=>$currentInvokeCounts + 1));
        }
        else
        {
            $returnValue = '2. 参数Key或者app错误';
        }
        return $returnValue;
    }

    private function AddSuitsHistory($suits,$batchid,$createtime){
        for($i = 0; $i < count($suits); $i++)
        {
            $suitsHistory = M('beubeu_suits_history');
            $map['suitID'] =  $suits[$i]['id'];
            $map['suitStyleID'] = $suits[$i]['style'];
            $map['suitGenderID'] = $suits[$i]['gender'];
            $map['suitImageUrl'] = $suits[$i]['pic'];

            for($j = 0; $j<count($suits[$i]['products']); $j++){
                if($j<11){
                    $map['suitproduct'.($j+1)] = $suits[$i]['products'][$j]['uq'];
                }
            }
            $map['batchid'] = $batchid;
            $map['createtime'] = $createtime;
            $suitsHistory->add($map);
        }
    }

    private function AddOrUpdateSuits($beubeusuit,$createtime){
        $returnProduct = array();
        $suits = M('beubeu_suits');

        for($i = 0; $i < count($beubeusuit); $i++)
        {
            $map['suitID'] = $beubeusuit[$i]['id'];
            $map['suitStyleID'] = $beubeusuit[$i]['style'];
            $map['suitGenderID'] = $beubeusuit[$i]['gender'];
            $map['suitImageUrl'] = $beubeusuit[$i]['pic'];


            $result = $suits->field('suitID')->where($map)->find();
            if(isset($result))
            {
                $map['uptime'] = $createtime;
                $suits->where('suitID='.$result['suitID'])->save($map);
            }
            else
            {
                $map['createtime'] = $createtime;
                $map['uptime'] = $createtime;
                $suits->add($map);
            }

            $this->UpdateSuitGoods($beubeusuit[$i]);

        }

        return $returnProduct;
    }

    private function UpdateSuitGoods($uqproduct){
        $goods = M('beubeu_suits_goodsdetail');
        $goods->where(array("suitID"=>$uqproduct['id']))-> delete();

        for($i = 0; $i < count($uqproduct['products']); $i++)
        {
            $map['suitID'] = $uqproduct['id'];
            $map['item_bn'] = $uqproduct['products'][$i]['uq'];
            $goods->add($map);
        }
    }




    public function GetTagsByUqID111()
    {
        $returnValue = array(
            'code' => -1,
            'msg' => array(
                'success' => [],
                'error' => ''
            )
        );
        $app = 'baiyi';
        $key = '12345678';
        $data = array(
            'products'=>array(
                array('uq'=>'UQ080776000'),
                array('uq'=>'UQ079696000'),
                array('uq'=>'UQ080038000'),
                array('uq'=>'UQ079064000')
            )
        );

        if (!isset($data) || !isset($key) || !isset($app) ||
            empty($data) || empty($key) || empty($app)
        ) {
            $returnValue['msg']['error'] = '1. 传递参数错误';
        }else{

            $products = $data["products"];
            if (!isset($products)|| empty($products)){
                $returnValue['msg']['error'] = '1. 传递参数错误';
            }else{
//                $suitSyn = D('SuitsSyn');
                $returnValue = $this->GetTagsByUQID($app, $key, $products);
            }
        }
        $this->ajaxReturn($returnValue, 'JSONP');
    }



    //uqid前八位匹配的商品
    public function GetTagsByUQID($app,$key,$products)
    {
        $returnValue = array(
            'code' => 1,
            'msg' => array(
                'success' => [],
                'error' =>	''
            )
        );
//        $checkmsg = $this->CheckUpdateSuits($app,$key,count($products));
        $checkmsg = '';
        if($checkmsg == '')
        {
            $goods = M('Goods');
            for($i = 0; $i < count($products); $i++){
                $tag = array();
                $tag['uq'] = $products[$i]['uq'];
                $tag['tags'] = array();
                $returntags = $goods
                    ->join('INNER JOIN u_goodtag on u_goods.num_iid = u_goodtag.num_iid')
                    ->field('distinct u_goodtag.ftag_id as tagid')
                    ->where(array('left(u_goods.item_bn,8)'=>substr($products[$i]['uq'],0,8)))
                    ->select();

                if (isset($returntags)){
                    for($j = 0; $j < count($returntags); $j++)
                    {
                        array_push($tag['tags'],$returntags[$j]['tagid']);
                    }
                }
                array_push($returnValue['msg']['success'],$tag);
            }
        }
        else
        {
            $returnValue['code'] = -1;
            $returnValue['msg']['error'] = $checkmsg;
        }
        return $returnValue;
    }
}
