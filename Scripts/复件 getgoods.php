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
for($i=1;$i<=$pagenum;$i++){
//if($i==1){
$req->setFields("num_iid,title,cid,approve_status,pic_url,num,list_time,delist_time,price");
$req->setPageNo($i);
$req->setIsTaobao("true");
$req->setPageSize(200);
$result = $c->execute($req, $db->token);

$products->setFields('detail_url,property_alias,outer_id,change_prop,props_name,sku.properties_name,sku.properties,sku.quantity,sku.sku_id,prop_img');
$goods = (array)$result->items->item;

$goods_arr = array();
$pstr = '';
$time = date('Y-m-d H:i:s');
foreach($goods as $k=>$v){
$psql = "insert into `u_products` (`goods_id`,`num_iid`,`sku_id`,`properties`,`properties_name`,`quantity`,`url`) values ";
	$v = (array)$v;
	if($v['num']>0){
	$ppsql = '';
	//获取分类
	$catarr = array();
	getcat($v['cid'],$catarr);
    //获取图片
    $save_image = $db->createdir('pic_url',$root_dir.'/Upload/goods/','Upload/goods/',$v['pic_url']);
    @file_put_contents($save_image[0], file_get_contents($v['pic_url']));
	$products->setNumIid($v['num_iid']);
	$product = $c->execute($products, $db->token);
	$product_arr = (array)$product->item;
	//插入goods
	$cname = '';
	foreach($catarr as $ck=>$cv){
    $cname.=$cv['name'].'-';
	}
	$cname = rtrim($cname,'-');
	//区分是上装还是下装
	switch($catarr[0]['cid']){
		case 50008885 :
		case 50010394 :
		case 50008886 :
		case 50000436 :
		case 50000671 :
			
		$isud = 1;	
		break;
	}
	$sql = "insert into `u_goods` (`num_iid`,`catid`,`outer_id`,`title`,`approve_status`,`num`,`price`,`pic_url`,`detail_url`,`cname`,`props_name`,`property_alias`,`list_time`,`delist_time`,`createtime`,`uptime`) values ('".$v['num_iid']."','".$catarr[0]['cid']."','".$product_arr['outer_id']."','".$v['title']."','".$v['approve_status']."','".$v['num']."','".$v['price']."','".$save_image[1]."','".$product_arr['detail_url']."','".$cname."','".$product_arr['props_name']."','".$product_arr['property_alias']."','".$v['list_time']."','".$v['delist_time']."','".$time."','".$time."')";
    $db->mysqlquery($sql);
    $goods_id = mysql_insert_id();
    unset($sql);
	$product_arr_sku = (array)$product_arr['skus']->sku;
	$prop_imgs = (array)$product_arr['prop_imgs']->prop_img;
	if(!empty($product_arr_sku)){
	foreach($product_arr_sku as $k2=>$v2){
	$v2 = (array)$v2;
	if($v2['quantity']>0){
	$properties = explode(';',$v2['properties']);
	$url = '';
	foreach($prop_imgs as $k5=>$v5){
	if($v5->properties==$properties[0]){
	$url = $v5->url;
	break;
	}
	}
	//获取product的图片
    $save_image = $db->createdir('pic_url',$root_dir.'/Upload/products/','Upload/products/',$url);
    @file_put_contents($save_image[0], file_get_contents($url));	
	$ppsql.="('".$goods_id."','".$v['num_iid']."','".$v2['sku_id']."','".$v2['properties']."','".$v2['properties_name']."','".$v2['quantity']."','".$save_image[1]."'),";
	}
	}
	//插入product
	$psql.=$ppsql;
	$psql = rtrim($psql,',');
    $db->mysqlquery($psql);
	unset($ppsql);
	unset($psql);
	}
	}
}
unset($result);
//}
}
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
	   return $catarr;
}
exit;