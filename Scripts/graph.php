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
        $white=new Imagick($root_dir.'/'.$v['pic_body']);//身体
        //$white->newImage(600, 800, "white");
        //$im1=new Imagick($root_dir.'/'.$v['pic_body']);
        $exten = pathinfo($v['pic_shose'], PATHINFO_EXTENSION);
        if($exten=='png'){
        $im4=new Imagick($root_dir.'/'.$v['pic_shose']);//鞋子
        }
        $im2=new Imagick($root_dir.'/'.$v['pic_match']);//衣服
        $im3=new Imagick($root_dir.'/'.$v['pic_head']);//头
        //$white->compositeimage($im1, Imagick::COMPOSITE_OVER, 0, 0);
        if($exten=='png'){
        $white->compositeimage($im4, Imagick::COMPOSITE_OVER, 0, 0);
        }
        $white->compositeimage($im2, Imagick::COMPOSITE_OVER, 0, 0);
        $white->compositeimage($im3, Imagick::COMPOSITE_OVER, 0, 0);
        $white->thumbnailImage( 400, 533);
        $white->setImageFormat('png');
        $image = $db->createdir($v['suitid'],$root_dir.'/Upload/suits/','Upload/suits/',$v['pic_head'],2,$offset+1);
        $white->writeImage($image[0]);
        $white->clear();
        $white->destroy();
        $upsql = "update `u_beubeu_suits` set `suitImageUrl`='".$image[1]."' where suitID=".$v['suitid'];
        $db->mysqlquery($upsql);
        $upsql2 = "update `u_suit_pic` set `isdown`=1 where suitID=".$v['suitid'];
        $db->mysqlquery($upsql2);
        $upsql3 = "update `u_suit_order` set `suitImageUrl`='".$image[1]."' where suitID=".$v['suitid'];
        $db->mysqlquery($upsql3);
        }
    }
    }
    $offset+=$limit;
    unset($result);
}
exit;