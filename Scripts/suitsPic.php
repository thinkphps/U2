<?php
require_once('init.php');
set_time_limit(0);
error_reporting(E_ALL);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(__FILE__)));
$db = new DB();
$offset = 0;
$limit = 200;
$ke = 1;
$time = date('Y-m-d',time()-604800);
$starttime = $time.' 00:00:00';
while($ke>0){
    //$sql = "select `suitID`,`suitImageUrl` from `u_beubeu_suits` where createtime>={$starttime} limit $offset,$limit";
    $sql = "select `suitID`,`suitImageUrl` from `u_beubeu_suits` where suitImageUrl!='' limit $offset,$limit";
    $result = $db->mysqlfetch($sql);
    unset($sql);
    if(empty($result)){
        $ke = 0;
    }else{
        foreach($result as $k2=>$v2){
        $save_image = $db->createdir($v2['suitID'],$root_dir.'/Upload/suits/','Upload/suits/',$v2['suitImageUrl'],2);
        @file_put_contents($save_image[0], file_get_contents($v2['suitImageUrl']));
        chmod($save_image[0],0777);
        $im = new imagick($save_image[0]);
        $im->thumbnailImage(400,533);
        $im->setImageFormat('png');
        $im->writeImage($save_image[0]);
        $im->clear();
        $im->destroy();
        $upsql = "update `u_beubeu_suits` set `suitImageUrl`='".$save_image[1]."' where suitID=".$v2['suitID'];
        $db->mysqlquery($upsql);
      }
    }
    $offset+=$limit;
    sleep(1);
    unset($result);
}
exit;