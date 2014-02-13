<?php

class APIAction extends Action
{

    public function UpdateProductsWithColor()
    {
        $returnProductValue = array(
            'code' => -1,
            'msg' => array(
                'success' => '',
                'error' => ''
            )
        );

        //$callback=$_GET["callback"];

        $app = $_GET['app'];
        $key = $_GET['key'];
        $products = $_GET['products'];
        $batchid = $_GET['batchid'];

        //$products1 = unserialize(base64_decode());

        //$strRequest = file_get_contents('php://input');
        /*$strRequest = $this->_param('data');
        $Request = json_decode($strRequest,true);*/

        /*	$app = $Request['app'];
            $key = $Request['key'];
            $products =  $Request['products'];
            $batchid = $Request['batchid'];*/

        if (!isset($products) || !isset($key) || !isset($app) || !isset($batchid) ||
            empty($products) || empty($key) || empty($app) || empty($batchid)
        ) {
            $returnProductValue['msg']['error'] = '1. 传递参数错误';
        } else {
            $productSyn = D('ProductSyn');
            $returnProductValue = $productSyn->UpdateUQProduct($app, $key, $products, $batchid);
        }
        $json = json_encode($returnProductValue);

        $this->ajaxReturn($returnProductValue, 'JSONP');
        //echo $callback."($json)";
//        echo $callback.'('.$json.')';

    }

    public function GetProductColorByID()
    {
        $id = $_GET['id'];

        $returnValue = array();

        //$id = '17141542788';

        if (isset($id) &&  ! empty($id) )
        {
            $productSyn = D('ProductSyn');
            $returnOjb =  $productSyn->GetProductColorByID($id);
            if (isset($returnOjb))
            {
                for($i = 0; $i < count($returnOjb); $i++)
                {
                    $returnValue['color'][$i]['id'] = $returnOjb[$i]['colorid'];
                    $returnValue['color'][$i]['code'] = $returnOjb[$i]['colorcode'];
                    $returnValue['color'][$i]['name'] = $returnOjb[$i]['colorname'];
                    $returnValue['gender'] = $returnOjb[$i]['gender'];
                    $returnValue['uq'] = $returnOjb[$i]['uq'];
                }
            }
        }

        $this->ajaxReturn($returnValue, 'JSON');
    }

    public function GetGoodInfoByUQ()
    {
        $id = $_GET['id'];
        $u_id = $_SESSION['u_id'];
        $goodInfo = array();

        if(isset($id) && ! empty($id))
        {
            $collection = M('Collection');
            $goods = M('beubeu_goods');
            $goodtag = M('Goodtag');
            $love = M('Love');
            $buy = M('Buy');

            $mapgoods['left(u_beubeu_goods.item_bn,8)'] = substr($id,0,8);

            //取出收藏数据
            $clothes = $goods
                ->field('u_beubeu_goods.id as gid,u_beubeu_goods.isud as isud,u_beubeu_goods.num_iid,u_beubeu_goods.type,u_beubeu_goods.title,u_beubeu_goods.num,u_beubeu_goods.price,u_beubeu_goods.pic_url,u_beubeu_goods.detail_url')
                ->where($mapgoods)
                ->select();
            foreach($clothes as $k=>$v){
                switch($v['type']){
                    case '1' :
                        $sexname = '女装';
                        break;
                    case '2' :
                        $sexname = '男装';
                        break;
                    case '3' :
                        $sexname = '童装';
                        break;
                    case '4' :
                        $sexname = '婴幼儿';//todo
                        break;
                }
                $clothes[$k]['csex'] = $sexname;
                $gtag = $goodtag->join('u_tag on u_tag.id=u_goodtag.ftag_id')
                    ->field('u_tag.name,u_goodtag.ccateid')
                    ->where(array('u_goodtag.good_id'=>$v['gid'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>2))
                    ->find();
                $clothes[$k]['tagname1'] = $gtag['name'];
                $clothes[$k]['fg'] = $gtag['ccateid'];
                //场合
                $gtag2 = $goodtag->join('u_tag on u_tag.id=u_goodtag.tag_id')
                    ->field('u_tag.name')
                    ->where(array('u_goodtag.good_id'=>$v['gid'],'u_goodtag.gtype'=>$v['type'],'u_tag.parent_id'=>1))
                    ->find();
                $clothes[$k]['tagname2'] = $gtag2['name'];

                if(isset($u_id))
                {
                    $islove = $love->field('id')->where(array('num_iid'=>$v['num_iid'],'uid'=>$u_id))->find();
                    if(!empty($islove)){
                        $clothes[$k]['love'] = 1;
                    }
                    $isbuy = $buy->field('id')->where(array('num_iid'=>$v['num_iid'],'uid'=>$u_id))->find();
                    if(!empty($isbuy)){
                        $clothes[$k]['buy'] = 1;
                    }
                }
                //add color,uq,gender, added by David
                $productSyn = D('ProductSyn');
                $returnOjb =  $productSyn->GetProductColorByID($clothes[$k]['num_iid']);
                if (isset($returnOjb))
                {
                    $uq_color = array();
                    for($i = 0; $i < count($returnOjb); $i++)
                    {
                        $uq_color[$i]['id'] = $returnOjb[$i]['colorid'];
                        $uq_color[$i]['code'] = $returnOjb[$i]['colorcode'];
                        $uq_color[$i]['name'] = $returnOjb[$i]['colorname'];
                        $clothes[$k]['color'] = $uq_color;
                        $clothes[$k]['gender'] = $returnOjb[$i]['gender'];
                        $clothes[$k]['uq'] = $returnOjb[$i]['uq'];
                    }
                }
            }

            if(count($clothes) > 0)
            {
                $taobaoInfo['id'] = $clothes[0]['num_iid'];
                $taobaoInfo['src'] = __ROOT__.'/'.$clothes[0]['pic_url'];
                $taobaoInfo['sex'] = $clothes[0]['type'];
                $taobaoInfo['csex'] = $clothes[0]['csex'];
                $taobaoInfo['tag'] = $clothes[0]['tagname1'];
                $taobaoInfo['url'] = $clothes[0]['detail_url'];
                $taobaoInfo['place'] = $clothes[0]['tagname2'];
                $taobaoInfo['price'] = $clothes[0]['price'];
                $taobaoInfo['rest'] = $clothes[0]['num'];
                $taobaoInfo['fg'] = $clothes[0]['fg'];
                $taobaoInfo['alt'] = $clothes[0]['title'];

                $taobaoInfo['data-like'] =  $clothes[0]['love'];
                $taobaoInfo['data-had'] =  $clothes[0]['buy'];
                $taobaoInfo['isud'] = $clothes[0]['isud'];

                $taobaoInfo['color'] =  $clothes[0]['color'];
                $taobaoInfo['gender'] =  $clothes[0]['gender'];
                $taobaoInfo['uq'] =  $clothes[0]['uq'];
            }
        }

        $this->ajaxReturn($taobaoInfo, 'JSON');
    }

}