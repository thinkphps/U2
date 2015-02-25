<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 14-7-28
 * Time: 下午7:11
 * 每周星期三凌晨1点40更新beubeu_goods
 */
require_once('init.php');
set_time_limit(0);
$db = new DB();
$db->mysqlquery("call update_beubeu_goods()");
//统计风格和自定义分类下的商品数量
//$sql = "select g.gtype,g.ftag_id,count(DISTINCT(g.good_id)) as num from u_beubeu_goods as bg inner join u_goodtag as g on good_id=bg.id inner join u_catesgoods as c on c.num_iid=bg.num_iid group by g.gtype,g.ftag_id";

$sql = "select g.gtype,g.ftag_id,count(DISTINCT(g.good_id)) as num from u_beubeu_goods as bg inner join u_catesgoods as c on c.num_iid=bg.num_iid inner join (select   good_id,gtype,ftag_id from u_goodtag where 1 group by good_id) as g on g.good_id=bg.id group by g.gtype,g.ftag_id";
$result = $db->mysqlfetch($sql);
unset($sql);
$arr = array();
foreach($result as $k=>$v){
    if($v['gtype']==1 || $v['gtype']==2){
        $sql = "update `u_settings_gender_style` set `goodnum`=".$v['num']." where `genderID`=".$v['gtype']." and `styleID`=".$v['ftag_id'];
        $db->mysqlquery($sql);
        unset($sql);
    }else if($v['gtype']==3 || $v['gtype']==4){
        if(empty($arr[$v['ftag_id']])){
            $arr[$v['ftag_id']] = 0;
        }
        $arr[$v['ftag_id']]+=$v['num'];
        $sql = "update `u_settings_gender_style` set `goodnum`=".$arr[$v['ftag_id']]." where `genderID`=3 and `styleID`=".$v['ftag_id'];
        $db->mysqlquery($sql);
        unset($sql);
    }
}
//自定义分类
unset($result);
$sql = "select `ID`,`gender` from `u_sellercats` where `selected`=1 and `isshow`=0";
$result = $db->mysqlfetch($sql);
unset($sql);
foreach($result as $k=>$v){
    switch($v['gender']){
        case 4:
        case 5:
            $goodstable = '`u_goods`';
            break;
        default:
            $goodstable = '`u_beubeu_goods`';
    }
    $sql = "select count(bg.num_iid) as num from {$goodstable} as bg where EXISTS(select 1 from `u_goodtag` as g where bg.id=g.good_id) and exists(select 1 from `u_catesgoods` as cg where cg.num_iid=bg.num_iid and cg.`cateID`=".$v['ID'].")";
    $count = $db->mysqlfetch($sql);
    unset($sql);
    $sql = "update `u_sellercats` set `goodnum`=".$count[0]['num']." where `ID`=".$v['ID'];
    $db->mysqlquery($sql);
}

exit;