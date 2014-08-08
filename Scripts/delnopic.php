<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 14-8-8
 * Time: 上午11:33
 * 删除时优衣库logo图片的sku
 */
require_once('init.php');
set_time_limit(0);
$db = new DB();
$root_dir = realpath(dirname(dirname(__FILE__)));
$fp = fopen($root_dir.'/Upload/2.csv','r');
while($data = fgetcsv($fp)){
      $data[0] = trim($data[0]);
      $sql = "select url from `u_products` where sku_id=".$data[0];
      $result = $db->mysqlfetch($sql);
      if(!empty($result)){
       if(file_exists($root_dir.'/'.$result[0]['url'])){
          unlink($root_dir.'/'.$result[0]['url']);
          $dsql = "delete from `u_products` where sku_id=".$data[0];
          $db->mysqlquery($dsql);
       }
      }
}
exit;