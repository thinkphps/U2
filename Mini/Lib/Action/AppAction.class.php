<?php
class AppAction extends Action {
    protected function _initialize(){
        import("@.ORG.SoapDiscovery");
    }
    public function index() {
           $uua = trim($this->_post('uua'));
           $upa = trim($this->_post('upa'));
           $root_dir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
           import('@.ORG.Appserver');
           if($uua==C('UUA') && $upa==C('UPA')){
               $disco = new SoapDiscovery('Appserver','MyApi');
               $disco->getWSDL($root_dir.'/Upload/uawsdl/UserWsdl.wsdl');
           }else{
               ini_set('soap.wsdl_cache_enabled', "0");
               $servidorSoap = new SoapServer($root_dir.'/Upload/uawsdl/UserWsdl.wsdl');
               $servidorSoap->setClass('Appserver');
               $servidorSoap->handle();
           }
    }
    public function df(){
        import('@.ORG.Appserver');
        $sd = new Appserver();
        $sd->ActivePhone();
    }
}