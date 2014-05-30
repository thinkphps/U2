<?php
require_once('init.php');
require_once('TopSdk.php');
$db = new DB();
$c = new TopClient;
$c->appkey = $db->appkey;
$c->secretKey = $db->secretKey;
$c->format = 'json';
/*$req = new ItemsOnsaleGetRequest;
$req->setFields("num_iid,title,price");
$req->setIsTaobao("true");
$req->setPageSize(1);
$resp = $c->execute($req, $db->token);
print_r($resp->items->item);
exit;*/
$req = new ItemGetRequest;
//$req->setFields("num_iid,property_alias,outer_id,desc,title,price,approve_status,desc_modules,props_namesku.properties_name,sku.properties,sku.quantity,sku.sku_id");
$req->setFields("seller_cids,num,approve_status,sku.quantity,sku.sku_id");
$req->setNumIid(35740703948);
$resp = $c->execute($req, $sessionKey);
$item = $resp->item;
print_r($resp);
