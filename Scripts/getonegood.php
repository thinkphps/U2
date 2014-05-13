<?php
require_once('init.php');
require_once('TopSdk.php');
$db = new DB();
$c = new TopClient;
$c->appkey = $db->appkey;
$c->secretKey = $db->secretKey;
$c->format = 'json';
$req = new ItemGetRequest;
$req->setFields("num_iid,property_alias,outer_id,desc,title,price,approve_status,desc_modules,props_namesku.properties_name,sku.properties,sku.quantity,sku.sku_id");
$req->setNumIid(37147586038);
$resp = $c->execute($req, $sessionKey);
$item = $resp->item;
print_r($resp);
