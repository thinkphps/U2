<?php
/**
合图
 */
require_once('init.php');
set_time_limit(0);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(__FILE__)));
$db = new DB();
$offset = 0;
$limit = 200;
$ke = 1;
while($ke>0){
    $sql = "select `suitid`,`pic_head`,`pic_body`,`pic_shose`,`pic_match` from `u_suit_pic` where isdown=0 limit $offset,$limit";
    $result = $db->mysqlfetch($sql);
    if(empty($result)){
        $ke = 0;
    }else{
    foreach($result as $k=>$v){
        $extension = pathinfo($v['pic_body'], PATHINFO_EXTENSION);
        if(file_exists($root_dir.'/'.$v['pic_body']) && $extension=='png'){
        $white=new Imagick();
        $white->newImage(600, 800, "white");
        $im1=new Imagick($root_dir.'/'.$v['pic_body']);//身体
        $im2=new Imagick($root_dir.'/'.$v['pic_match']);//衣服
        $im3=new Imagick($root_dir.'/'.$v['pic_head']);//头
        $white->compositeimage($im1, Imagick::COMPOSITE_OVER, 0, 0);
        $white->compositeimage($im2, Imagick::COMPOSITE_OVER, 0, 0);
        $white->compositeimage($im3, Imagick::COMPOSITE_OVER, 0, 0);
        $white->setImageFormat('png');
        $image = $db->createdir($v['suitid'],$root_dir.'/Upload/gsuits/','Upload/gsuits/',$v['pic_head'],2);
        $white->writeImage($image[0]);
        $white->destroy($white);
        $upsql = "update `u_beubeu_suits` set `suitImageUrl`='".$image[1]."' where suitID=".$v['suitid'];
        $db->mysqlquery($upsql);
        $upsql2 = "update `u_suit_pic` set `isdown`=1 where suitID=".$v['suitid'];
        $db->mysqlquery($upsql2);
            sleep(1);
        }
    }
    }
    $offset+=$limit;
    unset($result);
}
exit;