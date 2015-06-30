<?php
//导入优衣库给的商品数据然后获取商品所对应的自定义分类名称和别名
error_reporting(0);
require_once('../init.php');
set_time_limit(0);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(dirname(__FILE__))));
$db = new DB();
$db->mysqlquery("truncate table u_data_goodcate");
//售前
$filepath = $root_dir.'/Upload/q1.csv';
$i = 0;
$fp = fopen($filepath,"r");
while($data = fgetcsv($fp)){
   if($i>0){
       $data[0] = iconv('gb2312','UTF-8',addslashes(trim($data[0])));
     $sql = "insert into `u_data_goodcate` (`goodsname`,`GoodsPrice`,`num`,`dnum`,`flag`) values ('".$data[0]."','".$data[1]."','".$data[2]."','".$data[3]."',1)";
       try{
       $db->mysqlquery($sql);
   }catch(Exception $e) {
           echo $sql.'<p>';
           print_r($e);
           exit();
       }
   }
    $i++;
}

//售后
$filepath = $root_dir.'/Upload/h2.csv';
$i = 0;
$fp = fopen($filepath,"r");
while($data = fgetcsv($fp)){
    if($i>0){
        $data[0] = iconv('gb2312','UTF-8',addslashes(trim($data[0])));
        $sql = "insert into `u_data_goodcate` (`goodsname`,`GoodsPrice`,`num`,`dnum`,`flag`) values ('".$data[0]."','".$data[1]."','".$data[2]."','".$data[3]."',2)";
        try{
            $db->mysqlquery($sql);
        }catch(Exception $e) {
            echo $sql.'<p>';
            print_r($e);
            exit();
        }
    }
    $i++;
}
echo '结束';
