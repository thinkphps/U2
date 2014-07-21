<?php
require_once('init.php');
set_time_limit(0);
$db = new DB();
$beubeusuits = getsuits($db,1);
upbeubeusuitsappove($db,$beubeusuits);
unset($beubeusuits);

function getsuits($db,$type){
    if($type==1){
        $sql = "select `suitID` from `u_beubeu_suits` where `approve_status`=0";
    }
    $result = $db->mysqlfetch($sql);
    return $result;
}


function upbeubeusuitsappove($db,$suits){
    if(!empty($suits)){
        $tempCurrentTime = time();
        $currentDateTime = date('Y-m-d H:i:s', $tempCurrentTime);
        $i = 0;
        foreach($suits as $i=>$suit){
            //如果百衣推过来的图片数据里边有没有上架的商品，则此模特图不显示
            $usql = "select udg.item_bn from `u_beubeu_suits_goodsdetail` udg inner join `u_beubeu_goods` ugs on left(ugs.`item_bn`,8) = left(udg.`item_bn`,8) where  udg.suitID=".$suit['suitID'];
            $item_result = $db->mysqlfetch($usql);
            $desql = "select item_bn from `u_beubeu_suits_goodsdetail` where suitID=".$suit['suitID']." and left(item_bn,2)='UQ'";
            $dresult = $db->mysqlfetch($desql);
            foreach($item_result as $k=>$v){
                $item_arr[] = $v['item_bn'];
            }
            foreach($dresult as $k2=>$v2){
                if(!in_array($v2['item_bn'],$item_arr)){
                    echo $suit['suitID'];
                    $sql123 = "update `u_beubeu_suits` set `approve_status`=1 , `uptime`='".$currentDateTime."' where `suitID`=".$suit['suitID'];
                    $db->mysqlquery($sql123);
                    break;
                }
            }
        }
    }
}
exit;