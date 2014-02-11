<?php
require_once('init.php');
require_once('TopSdk.php');
$db = new DB();
$c = new TopClient;
$c->appkey = $db->appkey;
$c->secretKey = $db->secretKey;
$c->format = 'json';
$req = new ItemGetRequest;
$req->setFields("num_iid,outer_id,title,price,sku,props_name,property_alias,input_pid,item_img,prop_img");
$req->setNumIid(36876747187);
$resp = $c->execute($req, $sessionKey);
//$item = $resp->item;
print_r($resp);
