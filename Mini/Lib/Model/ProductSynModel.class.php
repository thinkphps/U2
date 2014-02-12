<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14-1-27
 * Time: 下午6:41
 */

class ProductSynModel extends Model{

    public function UpdateUQProduct($app,$key,$uqproduct,$batchid){

        $returnProductValue = array(
            'code' => -1,
            'msg' => array(
                'success' => '',
                'error' =>	''
            )
        );

        $currentDateTime = date('Y-m-d H:i:s', time());

        $uqProductCounts = count($uqproduct);

        $checkmsg = $this->CheckUpdateUQProduct($app,$key,$uqProductCounts);
        if($checkmsg == '')
        {
            $this->AddUQProductHistory($uqproduct,$batchid,$currentDateTime);
            $returnProductValue['code'] = 1;
            $returnProductValue['msg']['success'] = $this->AddOrUpdateUQProduct($uqproduct);
        }
        else
        {
            $returnProductValue['code'] = -1;
            $returnProductValue['msg']['error'] = $checkmsg;
        }
        return $returnProductValue;
    }

    private function CheckUpdateUQProduct($app,$key,$uqProductCounts){

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

    private function AddUQProductHistory($uqproduct,$batchid,$createtime){
        for($i = 0; $i < count($uqproduct); $i++)
        {
            $productsHistory = M('products_beubeu_history');
            $map['uq'] =  $uqproduct[$i]['uq'];
            $map['status'] = $uqproduct[$i]['type'];
            $map['batchid'] = $batchid;
            $map['createtime'] = $createtime;

            $productsHistory->add($map);
        }
    }

    private function AddOrUpdateUQProduct($uqproduct){
        $returnProduct = array();

        $productsbeubeu = M('products_beubeu');

        $goods = M('Goods');

        for($i = 0; $i < count($uqproduct); $i++)
        {
            $baiyiuq =  $uqproduct[$i]['uq'];
            $type = $uqproduct[$i]['type'];
            if(isset($baiyiuq) && isset($type))
            {
                if(strlen($baiyiuq)>=10)
                {
                    $map['uq'] = substr($baiyiuq,0,8);
                    $map['color'] = substr($baiyiuq,8);
                    unset($map['status']);

                    $result = $productsbeubeu->field('uq,color')->where($map)->find();
                    $map['status'] = $type;

                    if(isset($result))
                    {
                        $productsbeubeu->setField($map);
                    }
                    else
                    {
                        $productsbeubeu->add($map);
                    }


                    $returnProduct[$i]['uq'] = $baiyiuq;

                    // get goods title from goods
                    $mapgoods['item_bn'] = array('like',substr($baiyiuq,0,8).'%');
                    $goodsResult = $goods->field('title')->where($mapgoods)->find();
                    $goodsName = '';
                    if(isset($goodsResult))
                    {
                        $goodsName = $goodsResult['title'];
                    }
                    $returnProduct[$i]['name'] = $goodsName;
                }
            }
        }

        return $returnProduct;
    }

    public function GetProductColorByID($id)
    {
        $goods = M('Goods');
//        return $goods
//            ->join('u_products on u_goods.num_iid=u_products.num_iid')
//            ->join('u_settings on u_settings.`key` = u_goods.gender')
//            ->join('u_color on u_color.id = left(u_products.cvalue,2)')
//            ->field('distinct distinct left(u_products.cvalue,2) as colorid, u_color.color_code as colorcode,u_color.color_name as colorname,left(u_goods.item_bn,8) as uq ,u_settings.value as gender ')
//            ->where(array('u_products.num_iid'=>$id))
//            ->select();


        return $goods
            ->join('u_products_beubeu on left(u_goods.item_bn,8) = u_products_beubeu.uq')
            ->join('u_settings on u_settings.`key` = u_goods.gender')
            ->join('u_color on u_color.id = u_products_beubeu.color')
            ->field('
                    distinct u_products_beubeu.color as colorid,
                    u_color.color_code as colorcode,
                    u_color.color_name as colorname,
                    left(u_goods.item_bn,8) as uq ,
                    u_settings.value as gender
                    ')
            ->where(array('u_goods.num_iid'=>$id))
            ->select();
    }
} 