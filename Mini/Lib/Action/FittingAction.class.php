<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 15-5-11
 * Time: 下午3:31
 * 4D试衣间接口
 */
class FittingAction extends Action{
    private $root_dir='';
    public function _initialize(){
        import("@.ORG.SoapDiscovery");
        $this->root_dir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
    }
    public function index(){
        $username = trim($this->_post('fittingu'));
        $userpass = trim($this->_post('fittingp'));
        import('@.ORG.FittingServer');
        if($username==C('CREATEU') && $userpass==C('CREATEP')){
            ini_set('soap.wsdl_cache_enabled', "0");
            $disco = new SoapDiscovery('FittingServer','UniqloApi');
            $disco->getWSDL($this->root_dir.'/Upload/uawsdl/FittingWsdl.wsdl');
        }else{
            ini_set('soap.wsdl_cache_enabled', "0");
            $servidorSoap = new SoapServer($this->root_dir.'/Upload/uawsdl/FittingWsdl.wsdl');
            $servidorSoap->setClass('FittingServer');
            $servidorSoap->handle();
        }
    }
}