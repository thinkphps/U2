<?php
require_once('init.php');
set_time_limit(0);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(__FILE__)));
$db = new DB();
$sql = 'select * from `u_sellercats` where selected=1';
$good_result = $db->mysqlfetch($sql);
unset($sql);
foreach($good_result as $k=>$v){
$sql = "select num_iid from `u_catesgoods` where `cateID`=".$v['ID'];
$result = $db->mysqlfetch($sql);
if(empty($result)){
    $upsql = "update `u_sellercats` set isshow=1 where `ID`=".$v['ID'];
    $db->mysqlquery($upsql);
}
}