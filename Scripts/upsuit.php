<?php
//更新u_beubeu_suits表的显示状态
require_once('init.php');
set_time_limit(0);
$db = new DB();
ini_set('memory_limit','200M');
$ke = 1;
$offset = 0;
$limit = 200;
$result = array();
$tempCurrentTime = time();
$currentDateTime = date('Y-m-d H:i:s', $tempCurrentTime);
while($ke>0){
    $sql = "select `suitID` from `u_beubeu_suits` where `approve_status`=0 limit $offset,$limit";
    $result = $db->mysqlfetch($sql);
    unset($sql);
    if(empty($result)){
        $ke = 0;
    }else{
        foreach($result as $i=>$suit){
            $item_arr = array();
            //如果百衣推过来的图片数据里边有没有上架的商品，则此模特图不显示
            $usql = "select udg.item_bn from `u_beubeu_suits_goodsdetail` udg inner join `u_beubeu_goods` ugs on left(ugs.`item_bn`,8) = left(udg.`item_bn`,8) where  udg.suitID=".$suit['suitID']." and ugs.approve_status = 'onsale'";
            $item_result = $db->mysqlfetch($usql);
            $desql = "select item_bn from `u_beubeu_suits_goodsdetail` where suitID=".$suit['suitID']." and left(item_bn,2)='UQ'";
            $dresult = $db->mysqlfetch($desql);
            if(!empty($item_result)){
                foreach($item_result as $k=>$v){
                    $item_arr[] = $v['item_bn'];
                }
            }
            //只有要一个num_iid不能显示，则这个图片就不能显示
            $i = 0;//可以显示
            foreach($dresult as $k2=>$v2){
                if(!in_array($v2['item_bn'],$item_arr)){
                    $sql123 = "update `u_beubeu_suits` set `approve_status`=1, `uptime`='".$currentDateTime."' where `suitID`=".$suit['suitID'];
                    $db->mysqlquery($sql123);
                    $i = 1;//不能显示
                    break;
                }
            }
            if($i==0){
                $sql123 = "update `u_beubeu_suits` set `approve_status`=0, `uptime`='".$currentDateTime."' where `suitID`=".$suit['suitID'];
                $db->mysqlquery($sql123);
            }
        }
    }
    $offset+=$limit;
    unset($result);
}
exit;