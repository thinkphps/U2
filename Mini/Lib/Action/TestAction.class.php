<?php

class TestAction extends Action {
    public $products ;
    public $key;
    public $app;

    public function index(){
     $this->display();
    }
    public function jk(){
        ini_set('soap.wsdl_cache_enabled', "0");//注意该选项 soap有缓存
        //$client = new SoapClient('http://uniqlo.bigodata.com.cn/utes8dlkt/mini.php/App/index?wsdl');
        $client = new SoapClient('http://localhost:81/U2/mini.php/App/index?wsdl');
        //$client->setkey('xxxx');
//$client = new Api();
        try {
//$result = $client->select('ieliwb');
            $result = $client->login(json_encode(array('uname'=>'h43bhkjh8702','upass'=>'3nl97s9dj28s02ne','user_name'=>'18964889732','password'=>'123456')));
            //$result = $client->GetCollData(json_encode(array('uname'=>'h43bhkjh8702','upass'=>'3nl97s9dj28s02ne','seeid'=>'c4s67nl5q893fo09g2rr631s60')));
            print_r(json_decode($result));
            echo $client->__getLastRequest();
            echo '<p>';
            echo $client->__getLastResponse();
        }
        catch (SoapFault $f){
            echo "Error Message: {$f->getMessage()}";
            echo '<p>';
        }
    }
    public function jk2(){
        ini_set('soap.wsdl_cache_enabled', "0");//注意该选项 soap有缓存
        //$client = new SoapClient('http://localhost:81/U2/mini.php/App/index?wsdl');
        $client = new SoapClient('http://uniqlo.bigodata.com.cn/u2/mini.php/App/index?wsdl');
        try {
            $result = $client->getgoods(json_encode(array('uname'=>'h43bhkjh8702','upass'=>'3nl97s9dj28s02ne','uniq_user_id'=>7,'uniq_user_name'=>'13641946979','sid'=>1)));
            print_r(json_decode($result));
            echo $client->__getLastRequest();
            echo '<p>';
            echo $client->__getLastResponse();
        }
        catch (SoapFault $f){
            echo "Error Message: {$f->getMessage()}";
            echo '<p>';
        }
    }
    public function jk3(){
        ini_set('soap.wsdl_cache_enabled', "0");//注意该选项 soap有缓存
        $client = new SoapClient('http://localhost:81/U2/mini.php/App/index?wsdl');
        try {
            $result = $client->CheckMobileExists(json_encode(array('uname'=>'h43bhkjh8702','upass'=>'3nl97s9dj28s02ne','mobile'=>'18964889732')));
            print_r(json_decode($result));
            echo $client->__getLastRequest();
            echo '<p>';
            echo $client->__getLastResponse();
        }
        catch (SoapFault $f){
            echo "Error Message: {$f->getMessage()}";
            echo '<p>';
        }
    }
    public function jk4(){
        ini_set('soap.wsdl_cache_enabled', "0");//注意该选项 soap有缓存
        //$client = new SoapClient('http://uniqlo.bigodata.com.cn/utes8dlkt/mini.php/App/index?wsdl');
        //$client = new SoapClient('http://uniqlo.bigodata.com.cn/u2/mini.php/App/index?wsdl');
        $client = new SoapClient('http://localhost:81/U2/mini.php/App/index?wsdl');
        try {
            $result = $client->GetCollData(json_encode(array('uname'=>'h43bhkjh8702','upass'=>'3nl97s9dj28s02ne','taobao_name'=>'似懂非懂分','uniq_user_id'=>7,'uniq_user_name'=>'13641946979','old_password'=>12345678,'new_password'=>123456,'verycode'=>'804741413d7fe0e515b19a7ffc7b3027','uqstr'=>'UQ13681902_15474,UQ12711779_15474','uverify'=>7752,'sid'=>'1','zid'=>907510112,'page'=>1,'num_iid'=>0,'uq'=>'UQttuewtru2','gender'=>127,'picurl'=>'6.png')));
            //print_r($result);
            print_r(json_decode($result));
            echo $client->__getLastRequest();
            echo '<p>';
            echo $client->__getLastResponse();
        }
        catch (SoapFault $f){
            echo "Error Message: {$f->getMessage()}";
            echo '<p>';
        }
    }
    public function jk5(){
        ini_set('soap.wsdl_cache_enabled', "0");//注意该选项 soap有缓存
        $client = new SoapClient('http://localhost:81/U2/mini.php/App/index?wsdl');
        try {
            $result = $client->SinaQq(json_encode(array('uname'=>'h43bhkjh8702','upass'=>'3nl97s9dj28s02ne','openid'=>'fs9j8g1rc2sbq4h71qh307ifr5','nickname'=>'是否是','access_token'=>'dsgdfhdfgsgfdg','type'=>1)));
            print_r(json_decode($result));
            echo $client->__getLastRequest();
            echo '<p>';
            echo $client->__getLastResponse();
        }
        catch (SoapFault $f){
            echo "Error Message: {$f->getMessage()}";
            echo '<p>';
        }
    }
    public function jk6(){
        ini_set('soap.wsdl_cache_enabled', "0");//注意该选项 soap有缓存
        $client = new SoapClient('http://localhost:81/U2/mini.php/App/index?wsdl');
        try {
            $result = $client->SharePic(json_encode(array('uname'=>'h43bhkjh8702','upass'=>'3nl97s9dj28s02ne','bbody'=>'http://pic1.beubeu.com/modeldapei/0/9/6/0962cadd0db35ae554124d04aa41c359.png.400x533.png','bclose'=>'http://pic1.beubeu.com/2014/11/02/2457c1da0d9654df4dbeb5ec90ee9264.png.400x533.png','bhead'=>'http://pic1.beubeu.com/modeldapei/2/e/1/2e1769f488f0172edb69a6504d3de4a8.png.400x533.png','bshose'=>'	http://new.beubeu.com/js/empty.gif')));
            print_r(json_decode($result));
            echo $client->__getLastRequest();
            echo '<p>';
            echo $client->__getLastResponse();
        }
        catch (SoapFault $f){
            echo "Error Message: {$f->getMessage()}";
            echo '<p>';
        }
    }
    //4D时间webservice客户端
    public function jk7(){
        ini_set('wsdl_cache_enabled',0);
        //$client = new SoapClient('http://localhost:81/U2/fxuby.php/Fitting/index?wsdl');
        //$client = new SoapClient('http://uniqlo.bigodata.com.cn/utes8dlkt/fxuby.php/Fitting/index?wsdl');
        $client = new SoapClient('http://uniqlo.bigodata.com.cn/u2/fxuby.php/Fitting/index?wsdl');
        try{
           $result = $client->GetSku(json_encode(array('uname'=>'df@s89H2390NL20Y','upass'=>'3sd897Djkkls048HDOs')));
           print_r(json_decode($result));
        }catch(SoapFault $f){
            echo 3;
            echo "Error Message: {$f->getMessage()}";
            echo '<p>';
        }
    }
    //redis测试
    public function jk8(){
        set_time_limit(0);
        import("@.ORG.Reds");
        $redis = new Reds();
        $k = 0;
        $client = new SoapClient('http://localhost:81/U2/mini.php/Fitting/index?wsdl');
        while($k==0){
            try{
                $result = $client->GetStatusValue(json_encode(array('uname'=>'df@s89H2390NL20Y','upass'=>'3sd897Djkkls048HDOs','type'=>'d3sku')));
                $re = json_decode($result,true);
                if($re['code']==0 && empty($re['data'])){
                    $k=1;
                }else{
                    unset($result);
                    /*$sql = 'insert into `u_d3goods` (`num_iid`,`approve_status`,`num`) values ';
                    foreach($re['data'] as $k2=>$v){
                        $sql.="('".$v['num_iid']."','".$v['approve_status']."','".$v['num']."'),";
                    }
                    $sql = rtrim($sql,',');
                    M('D3goods')->query($sql);*/
                    $sql = 'insert into `u_d3sku` (`num_iid`,`sku_id`,`quantity`) values ';
                    foreach($re['data'] as $k2=>$v){
                        $sql.="('".$v['num_iid']."','".$v['sku_id']."','".$v['quantity']."'),";
                    }
                    $sql = rtrim($sql,',');
                    M('D3sku')->query($sql);
                }
            }catch(SoapFault $f){
                echo "Error Message: {$f->getMessage()}";
                echo '<p>';
            }
        }
    }
    public function jk9(){
        ini_set('wsdl_cache_enabled',0);
        $client = new SoapClient('http://localhost:81/U2/mini.php/Fitting/index?wsdl');
        //$client = new SoapClient('http://uniqlo.bigodata.com.cn/u2/fxuby.php/Fitting/index?wsdl');
        try{
            $functions = $client->__getFunctions ();
            var_dump ($functions);
            $result = $client->GetUserStatus(json_encode(array('uname'=>'df@s89H2390NL20Y','upass'=>'3sd897Djkkls048HDOs')));
            print_r(json_decode($result));
        }catch(SoapFault $f){
            echo "Error Message: {$f->getMessage()}";
            echo '<p>';
        }
    }
    public function jk10(){
      ini_set("wsdl_cache_enabled",0);
      $client = new SoapClient('http://localhost:81/U2/mini.php/Fitting/index?wsdl');
      //$client = new SoapClient('http://uniqlo.bigodata.com.cn/u2/fxuby.php/Fitting/index?wsdl');
      try{
          $result = $client->AddSyLog(json_encode(array('uname'=>'df@s89H2390NL20Y','upass'=>'3sd897Djkkls048HDOs','data'=>array(array('uid'=>1,'taobao_name'=>1,'ip'=>'127.0.0.1','visittime'=>'2015-12-14 12:23:23','intime'=>'2015-12-14 12:23:23','isdown'=>0,'fitting_time'=>'2015-12-14 12:23:23','	gender'=>'Male','height'=>1,'weight'=>1,'shoulder'=>1,'upper_arm'=>1,'chest'=>1,'cup'=>1,'waist'=>1,'hip'=>1,'leg'=>1,'leg_long'=>1,'num_iid'=>1234,'goodsize'=>'L','color'=>'02','isbuy'=>0,'isweibo'=>0,'isweixin'=>0,'source'=>'4dweb'),array('uid'=>1,'taobao_name'=>1,'ip'=>'127.0.0.1','visittime'=>'2015-12-04 12:23:23','intime'=>'2015-12-04 12:23:23','isdown'=>0,'fitting_time'=>'2015-12-04 12:23:23','	gender'=>'Male','height'=>1,'weight'=>1,'shoulder'=>1,'upper_arm'=>1,'chest'=>1,'cup'=>1,'waist'=>1,'hip'=>1,'leg'=>1,'leg_long'=>1,'num_iid'=>1234,'goodsize'=>'L','color'=>'02','isbuy'=>0,'isweibo'=>0,'isweixin'=>0,'source'=>'4dweb')))));
          print_r(json_decode($result));
      }catch (SoapFault $f){
          echo "Error Message: {$f->getMessage()}";
          echo '<p>';
      }
    }
    public function jk11(){
        ini_set("wsdl_cache_enabled","0");
        $client = new SoapClient('http://localhost:81/U2/mini.php/Fitting/index?wsdl');
        //$client = new SoapClient('http://uniqlo.bigodata.com.cn/u2/fxuby.php/Fitting/index?wsdl');
        try{
            $functions = $client->__getFunctions ();
            var_dump ($functions);
            //$result = $client->GetUserInfo(json_encode(array('uname'=>'df@s89H2390NL20Y','upass'=>'3sd897Djkkls048HDOs','fitting_num'=>3207,'fitting_avg_num'=>4.5553977272727,'modify_num'=>704,'click_buy_num'=>210,'sku_num'=>17,'download_num'=>0,'log_day'=>'2015-07-15','uniq_user_id'=>7,'uniq_user_name'=>'13641946979')));
            //$result = $client->App3dFigure(json_encode(array('uname'=>'df@s89H2390NL20Y','upass'=>'3sd897Djkkls048HDOs','uniq_user_id'=>7,'uniq_user_name'=>'13641946979','type'=>2,'ffigure'=>'1_2_4','mfigure'=>'1_4_5')));
			$result = $client->TotalDayLog('{"uname":"df@s89H2390NL20Y","upass":"3sd897Djkkls048HDOs","data":[{"fitting_num":297,"fitting_avg_num":3.26,"modify_num":91,"click_buy_num":2,"sku_num":3640,"download_num":0,"log_day":"2015-12-27","source":"ios"},{"fitting_num":1018,"fitting_avg_num":4.01,"modify_num":254,"click_buy_num":69,"sku_num":3640,"download_num":360,"log_day":"2015-12-27","source":"web"}]}');
            var_dump($client->__getLastRequest());
            var_dump($client->__getLastRequestHeaders());
            var_dump($client->__getLastResponse());
            var_dump($client->__getLastResponseHeaders());
            print_r(json_decode($result));
        }catch(SoapFault $f){
            echo "Error Message: {$f->getMessage()}";
            echo '<p>';
        }
    }
    public function jk12(){
        $ip = get_client_ip();
        $str = getIPLoc_sina($ip);
        echo "var remote_ip_info=".$str.";";
    }
}