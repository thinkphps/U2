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
                    $returnValue['gender'] = $returnOjb[$i]['sex'];
                    $returnValue['uq'] = $returnOjb[$i]['uq'];
                }
            }
        }

        $json = json_encode($returnValue);

        $this->ajaxReturn($returnValue, 'JSON');
    }

}