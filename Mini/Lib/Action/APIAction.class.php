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

    public function Test()
    {

        $intputValue = array(
            'app' => 'baiyi',
            'key' => '123456789',
            'batchid' => '20140120',
            'products' => array()
        );

        for ($i = 0; $i < 4; $i++) {
            $intputValue['products'][$i]['uq'] = 'UQ1234560' . $i;
            $intputValue['products'][$i]['type'] = -1;

        }

        $this->ajaxReturn($intputValue, 'JSON');
    }

}