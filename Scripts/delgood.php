<?php
session_start();
require_once('init.php');
$db = new DB();
if(!empty($_SESSION['token'])){
$num_iid = trim($_POST['num_iid']);
$uid = trim($_POST['hid']);
$sql = "delete from `u_collection` where `num_iid`='".$num_iid."' and `uid`='".$uid."'";
$db->mysqlquery($sql);
$arr['flag'] = true;
$arr['mag'] = '删除成功';
}else{
$arr['flag'] = false;
$arr['mag'] = '没有登录';
}
echo json_encode($arr);