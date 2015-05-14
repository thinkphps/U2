<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 15-5-8
 * Time: 下午4:42
 * 获取评价数据
 */
error_reporting(0);
require_once('../init.php');
require_once('../TopSdk.php');
set_time_limit(0);
$time = time();
$enddate = date('Y-m-d',$time);
$endtime = strtotime($enddate);
$enddate = date('Y-m-d H:i:s',$endtime);
$startdate = date('Y-m-d H:i:s',$endtime-24*3600);
$db = new DB();
$c = new TopClient;
$c->appkey = $db->appkey;
$c->secretKey = $db->secretKey;
$c->format = 'json';
$req = new TraderatesGetRequest;
$req->setFields('tid,oid,role,nick,result,created,rated_nick,item_title,item_price,content,reply,num_iid,valid_score');
$req->setRateType("get");
$req->setRole("buyer");
$req->setStartDate($startdate);
$req->setEndDate($enddate);
$req->setUseHasNext("true");
//差评
$i = 1;
$j = 0;
while($j==0){
    $req->setPageNo($i);
    $req->setPageSize(150);
    $req->setResult("bad");
    $resp = $c->execute($req, $db->token);
    if($resp->has_next){
    $result = $resp->trade_rates->trade_rate;
    $sql = "insert into `u_evaluate` (`tid`,`oid`,`role`,`nick`,`result`,`created`,`rated_nick`,`item_title`,`item_price`,`content`,`reply`,`num_iid`,`valid_score`,`createtime`) values ";
    foreach($result as $k=>$v){
        $v = (array)$v;
        if($v['reply']){
            $reply = $v['reply'];
        }else{
            $reply = '';
        }
        if($v['valid_score']){
            $valid_score = 'true';
        }else{
            $valid_score = 'false';
        }
        //$tid = (string)NumToStr($v['tid']);
        //$oid = (string)NumToStr($v['oid']);
        $tid = (string)$v['tid'];
        $oid = (string)$v['oid'];
        $sql.="('".$tid."','".$oid."','".$v['role']."','".$v['nick']."','".$v['result']."','".$v['created']."','".$v['rated_nick']."','".$v['item_title']."','".$v['item_price']."','".$v['content']."','".$reply."','".$v['num_iid']."','".$v['valid_score']."','".$startdate."'),";
    }
    $sql = rtrim($sql,',');
    $db->mysqlquery($sql);
    $sql = '';
    $i++;
    }
    if(!$resp->has_next){
        $j = 1;
    }
}
//好评
$ci = 1;
$cj = 0;
while($cj==0){
    $req->setPageNo($ci);
    $req->setPageSize(150);
    $req->setResult("good");
    $resp = $c->execute($req, $db->token);
    if($resp->has_next){
    $result = $resp->trade_rates->trade_rate;
    $sql = "insert into `u_evaluate` (`tid`,`oid`,`role`,`nick`,`result`,`created`,`rated_nick`,`item_title`,`item_price`,`content`,`reply`,`num_iid`,`valid_score`,`createtime`) values ";
    foreach($result as $k=>$v){
        $v = (array)$v;
        if($v['reply']){
            $reply = $v['reply'];
        }else{
            $reply = '';
        }
        if($v['valid_score']){
            $valid_score = 'true';
        }else{
            $valid_score = 'false';
        }
        //$tid = (string)NumToStr($v['tid']);
        //$oid = (string)NumToStr($v['oid']);
        $tid = (string)$v['tid'];
        $oid = (string)$v['oid'];
        $sql.="('".$tid."','".$oid."','".$v['role']."','".$v['nick']."','".$v['result']."','".$v['created']."','".$v['rated_nick']."','".$v['item_title']."','".$v['item_price']."','".$v['content']."','".$reply."','".$v['num_iid']."','".$v['valid_score']."','".$startdate."'),";
    }
    $sql = rtrim($sql,',');
    $db->mysqlquery($sql);
    $sql = '';
    $ci++;
    }
    if(!$resp->has_next){
        $cj = 1;
    }
}
//中评
$zi = 1;
$zj = 0;
while($zj==0){
    $req->setPageNo($zi);
    $req->setPageSize(150);
    $req->setResult("neutral");
    $resp = $c->execute($req, $db->token);
    if($resp->has_next){
    $result = $resp->trade_rates->trade_rate;
    $sql = "insert into `u_evaluate` (`tid`,`oid`,`role`,`nick`,`result`,`created`,`rated_nick`,`item_title`,`item_price`,`content`,`reply`,`num_iid`,`valid_score`,`createtime`) values ";
    foreach($result as $k=>$v){
        $v = (array)$v;
        if($v['reply']){
            $reply = $v['reply'];
        }else{
            $reply = '';
        }
        if($v['valid_score']){
            $valid_score = 'true';
        }else{
            $valid_score = 'false';
        }
        //$tid = (string)NumToStr($v['tid']);
        //$oid = (string)NumToStr($v['oid']);
        $tid = (string)$v['tid'];
        $oid = (string)$v['oid'];
        $sql.="('".$tid."','".$oid."','".$v['role']."','".$v['nick']."','".$v['result']."','".$v['created']."','".$v['rated_nick']."','".$v['item_title']."','".$v['item_price']."','".$v['content']."','".$reply."','".$v['num_iid']."','".$v['valid_score']."','".$startdate."'),";
    }
    $sql = rtrim($sql,',');
    $db->mysqlquery($sql);
    $sql = '';
    $zi++;
    }
    if(!$resp->has_next){
        $zj = 1;
    }
}
function NumToStr($num){
    if (stripos($num,'e')===false) return $num;
    $num = trim(preg_replace('/[=\'"]/','',$num,1),'"');//出现科学计数法，还原成字符串
    $result = "";
    while ($num > 0){
        $v = $num - floor($num / 10)*10;
        $num = floor($num / 10);
        $result   =   $v . $result;
    }
    return $result;
}
exit;