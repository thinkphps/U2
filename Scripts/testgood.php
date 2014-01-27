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
$req = new ItemsOnsaleGetRequest;
$req->setFields("num_iid,title,price");
$req->setIsTaobao("true");
$req->setPageSize(1);
$resp = $c->execute($req, $db->token);
$resp = (array)$resp;
$pagenum = ceil($resp['total_results']/200);
$products = new ItemGetRequest;//获取商品详细信息
$ccate = new ItemcatsGetRequest;
$ccate->setFields('cid,parent_cid,name,is_parent');

//获取商品详细信息
$goods_arr = array();
for($i=1;$i<=$pagenum;$i++){
//if($i==1){
$req->setFields("num_iid,title,cid,approve_status,pic_url,num,list_time,delist_time,price");
$req->setPageNo($i);
$req->setIsTaobao("true");
$req->setPageSize(200);
$result = $c->execute($req, $db->token);
//$products->setFields('detail_url,property_alias,outer_id,change_prop,props_name,sku.properties_name,sku.properties,sku.quantity,sku.sku_id,prop_img');
$products->setFields('detail_url,property_alias,outer_id,change_prop,props_name,sku.properties_name,sku.properties,sku.quantity,sku.sku_id,item_imgs,prop_imgs,prop_img,input_pids,input_str,created,modified');
$goods = (array)$result->items->item;
foreach($goods as $k=>$v){
	$v = (array)$v;
	if($v['num']>0){
	//获取分类
	/*$ccate->setCids($v['cid']);
	$recate = $c->execute($ccate, $db->token);
	$recate = (array)$recate->item_cats;
	$recate = $recate['item_cat'][0];
	$products->setNumIid($v['num_iid']);
	$product = $c->execute($products, $db->token);
	$product_arr = (array)$product->item;

	$v['pro'] = $product_arr;*/
	$catarr = array();
	getcat($v['cid'],$catarr);
	$sql = "select * from `u_category` where `cid`='".$catarr[0]['cid']."'";
	$result = $db->mysqlfetch($sql);
	if(empty($result[0])){
	unset($sql);
    $sql = "insert into `u_category` (`cid`,`name`,`pcid`,`parent_id`) values ('".$catarr[0]['cid']."','".$catarr[0]['name']."','0','0')";
	$db->mysqlquery($sql);
	$id = mysql_insert_id();
	unset($sql);
	$sql = "select id from `u_category` where `cid`='".$catarr[1]['cid']."'";
	$cresult = $db->mysqlfetch($sql);
	if(empty($cresult[0])){
	$sql = "insert into `u_category` (`cid`,`name`,`pcid`,`parent_id`) values ('".$catarr[1]['cid']."','".$catarr[1]['name']."','".$catarr[1]['cid']."','".$id."')";
	$db->mysqlquery($sql);
	unset($sql);
	}
	}else{
	$sql = "select id from `u_category` where `cid`='".$catarr[1]['cid']."'";
	$cresult = $db->mysqlfetch($sql);
	if(empty($cresult[0])){
	$sql = "insert into `u_category` (`cid`,`name`,`pcid`,`parent_id`) values ('".$catarr[1]['cid']."','".$catarr[1]['name']."','".$result[0]['cid']."','".$result[0]['id']."')";
	$db->mysqlquery($sql);
	unset($sql);		
	}

	//print_r($catarr);
	//$v['cat'] = $catarr;
	//$goods_arr[] = $v;
	}
}
//}
}
}
//print_r($goods_arr);
//获取顶级分类
 function getcat($cid,&$catarr){
	   global $c;
       global $ccate;
       global $db;
       $ccate->setCids($cid);
	   $recate = $c->execute($ccate,$db->token);
	   $recate = (array)$recate->item_cats;
	   $recate = (array)$recate['item_cat'][0];
	   if($recate['parent_cid']!=0){
	   	getcat($recate['parent_cid'],$catarr);
	   }
	   $catarr[] = $recate;
	   //return $catarr;
}