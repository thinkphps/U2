<?php
//更新显示的城市
require_once('../init.php');
set_time_limit(0);
$db = new DB();
$offset = 0;
$limit = 200;
$ke = 1;
while($ke>0){
    $sql = "select * from `u_areas` where 1 limit $offset,$limit";
    $result = $db->mysqlfetch($sql);
    unset($sql);
    if(empty($result)){
        $ke = 0;
    }else{
        foreach($result as $k=>$v){
           switch($v['region_grade']){
               case 1 :
                 $sql = "select `id` from `u_shop` where `pid`=".$v['region_id'];
                 $onelevel = $db->mysqlfetch($sql);
                  unset($sql);
                 if(empty($onelevel)){
                   $sql = "update `u_areas` set `disabled`='true' where `region_id`=".$v['region_id'];
                   $db->mysqlquery($sql);
                 }
                 unset($sql);
               break;
               case 2 :
                   $sql = "select `id` from `u_shop` where `cityid`=".$v['region_id'];
                   $oneleve2 = $db->mysqlfetch($sql);
                   unset($sql);
                   if(empty($oneleve2)){
                       $sql = "update `u_areas` set `disabled`='true' where `region_id`=".$v['region_id'];
                       $db->mysqlquery($sql);
                   }
                   unset($sql);
                   break;
               case 3 :
                   $sql = "select `id` from `u_shop` where `aid`=".$v['region_id'];
                   $oneleve3 = $db->mysqlfetch($sql);
                   unset($sql);
                   if(empty($oneleve3)){
                       $sql = "update `u_areas` set `disabled`='true' where `region_id`=".$v['region_id'];
                       $db->mysqlquery($sql);
                   }
                   unset($sql);
                   break;
           }
        }
    }
    $offset+=$limit;
    sleep(1);
    unset($result);
}
exit;