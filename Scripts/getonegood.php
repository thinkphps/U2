<?php
require_once('init.php');
require_once('TopSdk.php');
$db = new DB();
$c = new TopClient;
$c->appkey = $db->appkey;
$c->secretKey = $db->secretKey;
$c->format = 'json';
$req = new ItemGetRequest;
$req->setFields("num_iid,cid,outer_id,title,price,sku,props,props_name,property_alias,seller_cids,item_img,prop_img");
$req->setNumIid(36566985907);
$resp = $c->execute($req, $sessionKey);
//$item = $resp->item;
//print_r($resp);
$ccate = new ItemcatsGetRequest;
$ccate->setFields('cid,parent_cid,name,is_parent');

$ccate2 = new SellercatsListGetRequest;
$ccate2->setNick("优衣库官方旗舰店");
$resp2 = $c->execute($ccate2);
$seller_cat = (array)$resp2->seller_cats->seller_cat;
//print_r($seller_cat);
//exit;
	//$catarr = array();
	//getcat($resp->item->cid,$catarr);
//print_r($catarr);
$arrcids = explode(',',$resp->item->seller_cids);
foreach($arrcids as $k=>$v){
if($v){
foreach($seller_cat as $k2=>$v2){
$v2 = (array)$v2;
	if($v==$v2['cid']){
	print_r($v2);
	break;	
	}	
}	
}	
}
 function getcat2($cid){
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