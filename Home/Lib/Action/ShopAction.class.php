<?php
class ShopAction extends Action {
    public function index(){
    $SA_IP = get_client_ip();
	$SA_IP = '114.80.163.67';
    $city     = getIPLoc_sina($SA_IP);

    $citycode = mb_convert_encoding($city['city'], "gb2312", "utf-8");
    $fg = file_get_contents("http://php.weather.sina.com.cn/xml.php?city=" . $citycode . "&password=DJOYnieT8234jlsK&day=7");
	$doc      = new DOMDocument();
	@$doc->load("http://php.weather.sina.com.cn/xml.php?city=" . $citycode . "&password=DJOYnieT8234jlsK&day=0");
	$fg = file_get_contents("http://php.weather.sina.com.cn/xml.php?city=" . $citycode . "&password=DJOYnieT8234jlsK&day=0");
    print_r($fg);
	}
    public function getexten(){
    	$goods = M('Goods');
		$list = $goods->field('id,pic_url')->select();
		foreach($list as $k=>$v){
		$info = pathinfo($v['pic_url']);
        if($info['extension']=='jpg'){
        $pic_url = $info['dirname'].'/'.$info['filename'].'.png';
	    $goods->where(array('id'=>$v['id']))->save(array('pic_url'=>$pic_url));
		}
         
		}
    }
}