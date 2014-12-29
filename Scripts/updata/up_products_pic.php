<?php
require_once('../init.php');
require_once('../TopSdk.php');
set_time_limit(0);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(dirname(__FILE__))));
$db = new DB();
$c = new TopClient;
$c->appkey = $db->appkey;
$c->secretKey = $db->secretKey;
$c->format = 'json';
$products = new ItemGetRequest;//获取商品详细信息
$products->setFields('sku.properties_name,sku.properties,sku.quantity,sku.sku_id,prop_img');
$offset = 0;
$limit = 200;
$ke = 1;
$time = strtotime(date('Y-m-d'));
$oneMtonth = $time-86400*30;
$starttime = date('Y-m-d H:i:s',$oneMtonth);
while($ke>0){
    $sql = "select `id`,`num_iid`,`approve_status` from `u_goods` where `list_time`>='{$starttime}' limit $offset,$limit";
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
                $prop_imgs = (array)$product_arr['prop_imgs']->prop_img;
                //更新某个商品下所有的sku图片
                foreach($product_arr_sku as $k2=>$v2){
                    $v2 = (array)$v2;
                    $properties = explode(';',$v2['properties']);
                    $psql = "select `id`,`url` from `u_products` where `sku_id`='".$v2['sku_id']."'";
                    $p_list = $db->mysqlfetch($psql);
                    unset($psql);
                    if(!empty($p_list[0])){
                        $url = '';
                        foreach($prop_imgs as $k5=>$v5){
                            if($v5->properties==$properties[0]){
                                $url = $v5->url;
                                break;
                            }
                        }
                        $filepath = $root_dir.'/'.dirname($p_list[0]['url']);
                        $extension = pathinfo($url, PATHINFO_EXTENSION);
                        if(!file_exists($filepath)){
                           mkdir($filepath,777,true);
                        }
                        @file_put_contents($filepath.'/'.$v2['sku_id'].'.'.$extension, file_get_contents($url));
                        /*$psql = "update `u_products` set `url`='".$p_list[0]['url']."' where `sku_id`='".$v2['sku_id']."'";
                        $db->mysqlquery($psql);
                        unset($psql);
                        if(file_exists($root_dir.'/'.$p_list[0]['url'])){
                          unlink($root_dir.'/'.$p_list[0]['url']);
                        }*/
                    }
                }
            }
        }
    }
    $offset+=$limit;
    sleep(1);
    unset($result);
   }
exit;