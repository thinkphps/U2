<?php
require_once('init.php');
require_once('TopSdk.php');
set_time_limit(0);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(__FILE__)));
$db = new DB();
$c = new TopClient;
$c->appkey = $db->appkey;
$c->secretKey = $db->secretKey;
$c->format = 'json';
$products = new ItemGetRequest;//获取商品详细信息
$products->setFields('num,approve_status');
    $offset = 0;
	$limit = 200;
    $ke = 1;
	while($ke>0){
    $sql = "select `id`,`num_iid`,`approve_status` from `u_goods` where 1 limit $offset,$limit";
	$result = $db->mysqlfetch($sql);
	unset($sql);
	if(empty($result)){
	$ke = 0;
	}else{
	foreach($result as $k=>$v){
	$products->setNumIid($v['num_iid']);
	$product = $c->execute($products, $db->token);
	$product_arr = (array)$product->item;

	$sql = "update `u_goods` set `approve_status`='".$product_arr['approve_status']."',`num`='".$product_arr['num']."' where `id`='".$v['id']."'";
	$db->mysqlquery($sql);
	unset($sql);
	}
	}
	$offset+=$limit;
	sleep(2);
	unset($result);
	}
exit;