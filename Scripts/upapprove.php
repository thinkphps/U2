<?php
require_once('init.php');
require_once('TopSdk.php');
set_time_limit(0);
$db = new DB();

$suits = getsuits($db,0);
upsuitsappove($db,$suits);
unset($suits);
$beubeusuits = getsuits($db,1);
upbeubeusuitsappove($db,$beubeusuits);
unset($beubeusuits);

function getsuits($db,$type){
    if($type==0){
        $sql = "select `suitID` from `u_suits`";
    }
    if($type==1){
        $sql = "select `suitID` from `u_beubeu_suits`";
    }
    $result = $db->mysqlfetch($sql);
    return $result;
}

function upsuitsappove($db,$suits){
    if(!empty($suits)){
        $tempCurrentTime = time();
        $currentDateTime = date('Y-m-d H:i:s', $tempCurrentTime);
        foreach($suits as $i=>$suit){
            $sql = "select count(0) as apcount from `u_suits_goodsdetail` udg inner join `u_beubeu_goods` ugs on ugs.`num_iid` = udg.`num_iid` where ugs.approve_status = 'onsale' and ugs.num>=15 and udg.suitID=".$suit['suitID'];
            $result = $db->mysqlfetch($sql);
            //exist onsale goods
            if(!empty($result)){
                if($result[0]['apcount']>0){
                    //？？？？是否需要再上架
                    $sql = "update `u_suits` set `approve_status`=0 , `uptime`='".$currentDateTime."' where `suitID`=".$suit['suitID'];
                    $db->mysqlquery($sql);
                }else{//not exist onsale goods
                    $sql = "update `u_suits` set `approve_status`=1 , `uptime`='".$currentDateTime."' where `suitID`=".$suit['suitID'];
                    $db->mysqlquery($sql);
                }
            }else{
                $sql = "update `u_suits` set `approve_status`=1 , `uptime`='".$currentDateTime."' where `suitID`=".$suit['suitID'];
                $db->mysqlquery($sql);

            }
        }
    }
    unset($sql);
}

function upbeubeusuitsappove($db,$suits){
    if(!empty($suits)){
        $tempCurrentTime = time();
        $currentDateTime = date('Y-m-d H:i:s', $tempCurrentTime);
        foreach($suits as $i=>$suit){
            $sql = "select count(0) as apcount from `u_beubeu_suits_goodsdetail` udg inner join `u_beubeu_goods` ugs on left(ugs.`item_bn`,8) = left(udg.`item_bn`,8) where ugs.approve_status = 'onsale' and ugs.num>=15 and udg.suitID=".$suit['suitID'];
            $result = $db->mysqlfetch($sql);
            //exist onsale goods
            if(!empty($result)){
                if($result[0]['apcount']>0){
                    //？？？？是否需要再上架
                    $sql = "update `u_beubeu_suits` set `approve_status`=0 , `uptime`='".$currentDateTime."' where `suitID`=".$suit['suitID'];
                    $db->mysqlquery($sql);
                }else{//not exist onsale goods
                    $sql = "update `u_beubeu_suits` set `approve_status`=1 , `uptime`='".$currentDateTime."' where `suitID`=".$suit['suitID'];
                    $db->mysqlquery($sql);
                }
            }else{
                $sql = "update `u_beubeu_suits` set `approve_status`=1 , `uptime`='".$currentDateTime."' where `suitID`=".$suit['suitID'];
                $db->mysqlquery($sql);
            }
        }
    }
    unset($sql);
}
?>
