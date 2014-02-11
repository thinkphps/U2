<?php
//更新商品
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
echo $pagenum;
exit;
//获取商品详细信息
for($i=1;$i<=$pagenum;$i++){
//if($i==1){
$req->setFields("num_iid,title,cid,pic_url,num,approve_status,list_time,delist_time,price");
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
	$v = (array)$v;
	if($v['num']>0){
	//查询数据库里是否已经有了开始
	$gsql = "select `id`,`num_iid`,`pic_url` from `u_goods` where `num_iid`='".$v['num_iid']."'";
	$good_list = $db->mysqlfetch($gsql);

	unset($gsql);
	if(!empty($good_list[0])){
	
	$products->setNumIid($v['num_iid']);
	$product = $c->execute($products, $db->token);
	$product_arr = (array)$product->item;
	//更新goods
    $proarr = explode(';',$product_arr['props_name']);
	foreach($proarr as $kp=>$vp){
    $vparr = explode(':',$vp);
	if(in_array('1632501',$vparr) || in_array('2100010',$vparr)){
	$item_bn = $vparr[3];
	break;
	}
	}
	$sql = "update `u_goods` set `approve_status`='".$v['approve_status']."',`item_bn`='".$item_bn."',`outer_id`='".$product_arr['outer_id']."',`title`='".$v['title']."',`num`='".$v['num']."',`price`='".$v['price']."',`props_name`='".$product_arr['props_name']."',`list_time`='".$v['list_time']."',`delist_time`='".$v['delist_time']."',`uptime`='".$time."' where `num_iid`='".$v['num_iid']."'";
    $db->mysqlquery($sql);
    unset($sql);

	$product_arr_sku = (array)$product_arr['skus']->sku;
	$prop_imgs = (array)$product_arr['prop_imgs']->prop_img;

	foreach($product_arr_sku as $k2=>$v2){
	$v2 = (array)$v2;
	if($v2['quantity']>0){
	$psql = "select `id`,`url` from `u_products` where `sku_id`='".$v2['sku_id']."'";
	$p_list = $db->mysqlfetch($psql);
	unset($psql);
	if(!empty($p_list[0])){
	//更新
	$psql = "update `u_products` set `properties`='".$v2['properties']."',`properties_name`='".$v2['properties_name']."',`quantity`='".$v2['quantity']."' where `sku_id`='".$v2['sku_id']."'";
	$db->mysqlquery($psql);
	unset($psql);
	/*if(file_exists($root_dir.'/'.$p_list[0]['url'])){
     unlink($root_dir.'/'.$p_list[0]['url']);
	}*/
	}else{
	//插入
	$properties = explode(';',$v2['properties']);
	$url = '';
	foreach($prop_imgs as $k5=>$v5){
	if($v5->properties==$properties[0]){
	$url = $v5->url;
	break;
	}
	}
	//获取product的图片
    $save_image = $db->createdir($v2['sku_id'],$root_dir.'/Upload/products/','Upload/products/',$url);
    @file_put_contents($save_image[0], file_get_contents($url));
	$insql = "insert into `u_products` (`goods_id`,`num_iid`,`sku_id`,`properties`,`properties_name`,`quantity`,`url`) values ('".$good_list[0]['id']."','".$good_list[0]['num_iid']."','".$v2['sku_id']."','".$v2['properties']."','".$v2['properties_name']."','".$v2['quantity']."','".$save_image[1]."')";
	$db->mysqlquery($insql);
	unset($insql);
	}
	}else{
	$desql = "select `url` from `u_products` where `sku_id`='".$v2['sku_id']."'";
    $deresult = $db->mysqlfetch($desql);
	unset($desql);
    if(file_exists($root_dir.'/'.$deresult[0]['url'])){
	unlink($root_dir.'/'.$deresult[0]['url']);
	}
    $delsql = "delete from `u_products` where `sku_id`='".$v2['sku_id']."'";
    $db->mysqlquery($delsql);
    unset($delsql);
	}
	}		
	}else{
	$prosql = "insert into `u_products` (`goods_id`,`num_iid`,`sku_id`,`properties`,`properties_name`,`quantity`,`url`) values ";
     //有新商品
	$ppsql = '';
	//获取分类
	$catarr = array();
	getcat($v['cid'],$catarr);
	
    //获取图片
    $save_image = $db->createdir($v['num_iid'],$root_dir.'/Upload/goods/','Upload/goods/',$v['pic_url']);
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
	$sql = "insert into `u_goods` (`num_iid`,`approve_status`,`catid`,`outer_id`,`title`,`num`,`price`,`pic_url`,`detail_url`,`cname`,`dname`,`props_name`,`list_time`,`delist_time`,`createtime`,`uptime`) values ('".$v['num_iid']."','".$v['approve_status']."','".$catarr[1]['cid']."','".$product_arr['outer_id']."','".$v['title']."','".$v['num']."','".$v['price']."','".$save_image[1]."','".$product_arr['detail_url']."','".$cname."','".$catarr[1]['name']."','".$product_arr['props_name']."','".$v['list_time']."','".$v['delist_time']."','".$time."','".$time."')";
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
    $save_image = $db->createdir($v2['sku_id'],$root_dir.'/Upload/products/','Upload/products/',$url);
    @file_put_contents($save_image[0], file_get_contents($url));	
	$ppsql.="('".$goods_id."','".$v['num_iid']."','".$v2['sku_id']."','".$v2['properties']."','".$v2['properties_name']."','".$v2['quantity']."','".$save_image[1]."'),";
	}
	}
	//插入product
	$prosql.=$ppsql;
	$psql = rtrim($prosql,',');
    $db->mysqlquery($prosql);
	unset($ppsql);
	unset($prosql);
	}
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