<?php

class APIAction extends Action {
    public $products ;
    public $key;
    public $app;

    public function UpdateProductsWithColor(){

        $products	 =  $_POST['products'] ;
        $key      = $_POST['key'];
        $app = $_POST['app'];

        $return_msg = array(
            '0' =>	'success',
            '1' =>	'missing parameter',
            '2'	=>  'parameter error'
        );

        $return_code = array(
            'success' =>	'1',
            'error' =>	'-1',
        );

        $return_arr = array(
            'code'=>$return_code['success'],
            'msg'=>$return_msg[0]
        );

        if ( !isset($products) ||
            !isset($key)  ||
            !isset($app)  ) {

            $return_arr['code'] = $return_code['error'];
            $return_arr['msg'] = $return_msg[1];

        }else if ( empty($products) ||
            empty($key) ||
            empty($app) ) {

            $return_arr['code'] = $return_code['error'];
            $return_arr['msg'] = $return_msg[2];

        }else{
            $return_arr['code'] = $return_code['success'];
            $return_arr['msg'] = $return_msg[0];
        }

        $this->ajaxReturn($return_arr,'JSON');

    }

}