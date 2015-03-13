<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14-1-27
 * Time: 下午6:41
 */

class SuitsSynModel extends Model{

    public function UpdatebeubeuSuits($app,$key,$suits,$batchid){

        $returnProductValue = array(
            'code' => -1,
            'msg' => array(
                'success' => '',
                'error' =>	''
            )
        );

        $currentDateTime = date('Y-m-d H:i:s', time());

        $uqSuitsCounts = count($suits);

        $checkmsg = $this->CheckUpdateSuits($app,$key,$uqSuitsCounts);
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
        if(isset($result) !empty($result))
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

//    matchtype =1仅更新suits表的beubeuSuitID
//    除此情况以外仅更新beubeusuit
    private function AddSuitsHistory($suits,$batchid,$createtime){
        for($i = 0; $i < count($suits); $i++)
        {
            $matchtype = $suits[$i]['matchtype'];
            $fx_matchid = $suits[$i]["fx_matchid"];
            if (!isset($matchtype)|| empty($matchtype)){
                $matchtype = 0;
            }else{
                if ($matchtype==1){
                    $matchtype = 1;
                }else{
                    $matchtype = 0;
                }
            }
            if (!isset($fx_matchid)|| empty($fx_matchid)){
                $fx_matchid = "0";
            }

            $suitsHistory = M('beubeu_suits_history');
            $map = array();
            $map['suitID'] =  $suits[$i]['id'];
            if($matchtype == 1){
                $map['matchtype'] = $matchtype;
                $map['fx_matchid'] = $fx_matchid;

            }else{
                $map['suitStyleID'] = $suits[$i]['style'];
                $map['suitGenderID'] = $suits[$i]['gender'];
                $map['suitImageUrl'] = $suits[$i]['pic'];

                $pic = $suits[$i]['pic_head'];
                if (isset($pic)){
                   if(empty($pic)){
                       $map['suitImageUrlHead'] = "";
                   } else{
                       $map['suitImageUrlHead'] = $pic;
                   }
                }

                $pic = $suits[$i]['pic_body'];
                if (isset($pic)){
                    if(empty($pic)){
                        $map['suitImageUrlBody'] = "";
                    } else{
                        $map['suitImageUrlBody'] = $pic;
                    }
                }

                $pic = $suits[$i]['pic_shose'];
                if (isset($pic)){
                    if(empty($pic)){
                        $map['suitImageUrlShose'] = "";
                    } else{
                        $map['suitImageUrlShose'] = $pic;
                    }
                }

                $pic = $suits[$i]['pic_match'];
                if (isset($pic)){
                    if(empty($pic)){
                        $map['suitImageUrlMatch'] = "";
                    } else{
                        $map['suitImageUrlMatch'] = $pic;
                    }
                }

                $tag = $suits[$i]['tag'];
                if (isset($tag)){
                    if(empty($tag)){
                        $map['tag'] = "";
                    } else{
                        $map['tag'] = $tag;
                    }
                }

                for($j = 0; $j<count($suits[$i]['products']); $j++){
                    if($j<11){
                        $map['suitproduct'.($j+1)] = $suits[$i]['products'][$j]['uq'];
                    }
                }
            }
            $map['batchid'] = $batchid;
            $map['createtime'] = $createtime;
            $suitsHistory->add($map);
        }
    }

//    matchtype =1仅更新suits表的beubeuSuitID
//    除此情况以外仅更新beubeusuit
    private function AddOrUpdateSuits($beubeusuit,$createtime){
        $returnProduct = array();
        $suits = M('beubeu_suits');
        $fxsuits = M('suits');

        for($i = 0; $i < count($beubeusuit); $i++)
        {
            $returnProduct[] = $beubeusuit[$i]['id'];
            $matchtype = $beubeusuit[$i]['matchtype'];
            $fx_matchid = $beubeusuit[$i]["fx_matchid"];
            if (!isset($fx_matchid)|| empty($fx_matchid)){
                $fx_matchid = 0;
            }
            if (!isset($matchtype)|| empty($matchtype)){
                $matchtype = 0;
            }else{
                if ($matchtype==1){
                    $matchtype = 1;
                }else{
                    $matchtype = 0;
                }
            }
            $map = array();
            if ($matchtype ==1){
                $map['uptime'] = $createtime;
                $map['beubeuSuitID'] = $beubeusuit[$i]['id'];
                $fxsuits->where(array('suitID'=>$fx_matchid))->save($map);
            }else{
                $map['suitID'] = $beubeusuit[$i]['id'];
                $result = $suits->field('suitID')->where($map)->find();

                $map['suitStyleID'] = $beubeusuit[$i]['style'];
                $map['suitGenderID'] = $beubeusuit[$i]['gender'];
                $map['suitImageUrl'] = $beubeusuit[$i]['pic'];
                $pic = $beubeusuit[$i]['pic_head'];
                if (isset($pic)){
                    if(empty($pic)){
                        $map['suitImageUrlHead'] = "";
                    } else{
                        $map['suitImageUrlHead'] = $pic;
                    }
                }

                $pic = $beubeusuit[$i]['pic_body'];
                if (isset($pic)){
                    if(empty($pic)){
                        $map['suitImageUrlBody'] = "";
                    } else{
                        $map['suitImageUrlBody'] = $pic;
                    }
                }

                $pic = $beubeusuit[$i]['pic_shoes'];
                if (isset($pic)){
                    if(empty($pic)){
                        $map['suitImageUrlShose'] = "";
                    } else{
                        $map['suitImageUrlShose'] = $pic;
                    }
                }

                $pic = $beubeusuit[$i]['pic_match'];
                if (isset($pic)){
                    if(empty($pic)){
                        $map['suitImageUrlMatch'] = "";
                    } else{
                        $map['suitImageUrlMatch'] = $pic;
                    }
                }

                $tag = $beubeusuit[$i]['tag'];
                if (isset($tag)){
                    if(empty($tag)){
                        $map['tag'] = "";
                    } else{
                        $map['tag'] = $tag;
                    }
                }
                if(isset($result) && !empty($result))
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

    //uqid前八位匹配的商品
    public function GetTagsByUQID($app,$key,$products)
    {
        $returnValue = array(
            'code' => 1,
            'msg' => array(
                'success' => array(),
                'error' =>	''
            )
        );
        $checkmsg = $this->CheckUpdateSuits($app,$key,count($products));
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