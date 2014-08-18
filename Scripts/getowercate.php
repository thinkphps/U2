<?php
//获取自定义分类
require_once('init.php');
require_once('TopSdk.php');
set_time_limit(0);
$db = new DB();
$c = new TopClient;
$c->appkey = $db->appkey;
$c->secretKey = $db->secretKey;
$c->format = 'json';
$req = new SellercatslistGetRequest;
$req->setNick('优衣库官方旗舰店');
$resp = $c->execute($req, $sessionKey);
$cate = (array)$resp->seller_cats->seller_cat;
foreach($cate as $k=>$v){
    $v = (array)$v;
    $cidArr[] = $v['cid'];
    if($v['parent_cid']==0){
        $oneCate[] = $v;
    }
}
foreach($oneCate as $k2=>$v2){
    $arrCate[$k2][] = array('cid'=>$v2['cid'],'name'=>$v2['name']);
    $sql = "select ID from u_sellercats where ID=".$v2['cid'];
    $result = $db->mysqlfetch($sql);
    unset($sql);
    if(!empty($result[0])){
     //分类已经存在
     $upsql = "update u_sellercats set cateName='".$v2['name']."' where ID=".$v2['cid'];
     $db->mysqlquery($upsql);
     unset($upsql);
        foreach($cate as $k3=>$v3){
          $v3 = (array)$v3;
          $ucsql = "select ID from u_sellercats where ID=".$v3['cid'];
          $result2 = $db->mysqlfetch($ucsql);
          if(!empty($result2[0])){
              $insql = "update u_sellercats set cateName='".$v3['name']."' where ID=".$v3['cid'];
              $db->mysqlquery($insql);
          }else{
              $insql = "insert into u_sellercats (`ID`,`cateName`,`parentID`,`sort_order`) values ('".$v3['cid']."','".$v3['name']."','".$v3['parent_cid']."','999')";
              $db->mysqlquery($insql);
          }
        }
    }else{
       $csql = "insert into u_sellercats (`ID`,`cateName`,`parentID`,`sort_order`) values ('".$v2['cid']."','".$v2['name']."','".$v2['parent_cid']."','999')";
       $db->mysqlquery($csql);
        unset($csql);
        foreach($cate as $k3=>$v3){
            $v3 = (array)$v3;
            if($v2['cid']==$v3['parent_cid']){
             $insql = "insert into u_sellercats (`ID`,`cateName`,`parentID`,`sort_order`) values ('".$v3['cid']."','".$v3['name']."','".$v3['parent_cid']."','999')";
             $db->mysqlquery($insql);
            }
        }
    }
}
//数据库有，天猫店铺没有则删除
$localsql = "select `ID` from `u_sellercats`";
$localresult = $db->mysqlfetch($localsql);
foreach($localresult as $k=>$v){
     if(!in_array($v['ID'],$cidArr)){
      $delsql = "delete from `u_sellercats` where `ID`=".$v['ID'];
      $db->mysqlquery($delsql);
     }
}
exit;