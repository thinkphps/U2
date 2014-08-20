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

        $product = M('Products');

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

                    $result = $productsbeubeu->field('*')->where($map)->find();
                    $map['status'] = $type;

                    if(isset($result))
                    {
                        $productsbeubeu->where('id='.$result['id'])->save($map);
                    }
                    else
                    {
                        $productsbeubeu->add($map);
                    }


                    $returnProduct[$i]['uq'] = $baiyiuq;

                    // get goods title from goods
                    $mapgoods['item_bn'] = array('like',substr($baiyiuq,0,8).'%');

                    $goodsResult = $goods->field('title,num_iid')->where($mapgoods)->find();
                    $goodsName = '';
                    $goodsUrl = '';
                    if(isset($goodsResult))
                    {
                        $goodsName = $goodsResult['title'];

                        $productUrlResult = $product->field('url')->where(array('num_iid'=>$goodsResult['num_iid'],'left(cvalue,2)'=>substr($baiyiuq,8)))->find();
                        if(isset($productUrlResult))
                        {
                            $goodsUrl = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/'.$productUrlResult['url'];
                            $goodsUrl = substr($goodsUrl,0,strlen($goodsUrl)-3).'jpg';
                            unset($productUrlResult);
                        }
                    }
                    $returnProduct[$i]['name'] = $goodsName;
                    $returnProduct[$i]['url'] = $goodsUrl;
                }
            }
        }

        return $returnProduct;
    }

    public function GetProductColorByID($id)
    {
        $goods = M('Goods');

        //由于客户需要展示图片，所以将所有的颜色改成图片地址。
        return $goods
            ->join('INNER JOIN u_products_beubeu on left(u_goods.item_bn,8) = u_products_beubeu.uq')
            ->join('INNER JOIN u_settings on u_settings.key = u_goods.gender')
            ->join('INNER JOIN u_color on u_color.id = u_products_beubeu.color')
            ->join('INNER JOIN u_products on u_products.num_iid=u_goods.num_iid and u_products.cid=u_products_beubeu.color')
            ->field('
                    distinct u_products_beubeu.color as colorid,
                    u_products.num_iid as num_iid,
                    u_products.url  as colorcode,
                    u_color.color_name as colorname,
                    left(u_goods.item_bn,8) as uq ,
                    u_settings.value as gender
                    ')
            ->where(array('u_goods.num_iid'=>$id,'u_products_beubeu.status'=>'1'))
            ->group('uq,colorid')
            ->order('u_products_beubeu.id')
            ->select();
    }

    /*leon 3.13 新增
    通过uq号，获取色号和图片地址
    */
    public function GetProductColorByUqID($id)
    {
        $goods = M('Goods');

        //由于客户需要展示图片，所以将所有的颜色改成图片地址。
        return $goods
            ->join('INNER JOIN u_products_beubeu on left(u_goods.item_bn,8) = u_products_beubeu.uq')
            ->join('INNER JOIN u_color on u_color.id = u_products_beubeu.color')
            ->join('INNER JOIN u_products on u_products.num_iid=u_goods.num_iid and left(u_products.cvalue,2)=u_products_beubeu.color')
            ->field("
                    distinct u_products_beubeu.color as colorid,
                    CONCAT(LEFT(u_products.url,LENGTH(u_products.url)-3), 'jpg')  as url,
                    left(u_goods.item_bn,8) as uq,u_products.cvalue
                    ")
            ->where(array('u_products_beubeu.uq'=>$id,'u_products_beubeu.status'=>'1'))
            ->group('uq,colorid')
            ->order('u_products_beubeu.color')
            ->select();
    }

    //计算每种颜色的库存
    public function getSkuNum($num_iid){
        $sql = "select sum(quantity) as skunum from u_products where num_iid={$num_iid} group by cid";
        $result = M('Goods')->query($sql);
        $skunum = 1;
        foreach($result as $k=>$v){
        if($v['skunum']==0){
         $skunum = 0;
         break;
        }
        }
        return $skunum;
    }
} 