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
$products->setFields('num,modified,approve_status,sku.quantity,sku.sku_id,sku.modified');
//判断是否能取到数据

$req = new ItemsOnsaleGetRequest;
$req->setFields("num_iid,title,price");
$req->setIsTaobao("true");
$req->setPageSize(1);
$resp = $c->execute($req, $db->token);
if(isset($resp->items->item) && !empty($resp->items->item)){
//判断是否能取到数据
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
    if(!empty($product_arr)){
    $product_arr_sku = (array)$product_arr['skus']->sku;
	$sql = "update `u_goods` set `approve_status`='".$product_arr['approve_status']."',`num`='".$product_arr['num']."',`sort`='".$v['modified']."' where `id`='".$v['id']."'";
	$db->mysqlquery($sql);
	unset($sql);
    //更新products表
    foreach($product_arr_sku as $k2=>$v2){
        $v2 = (array)$v2;
        $psql = "update `u_products` set `quantity`=".$v2['quantity'].",`modified`='".$v2['modified']."' where goods_id=".$v['id']." and sku_id=".$v2['sku_id'];
        $db->mysqlquery($psql);
     }
    }
	}
	}
	$offset+=$limit;
	sleep(2);
	unset($result);
	}
}
exit;