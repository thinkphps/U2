<?php
//删除重复的sku只留一条,比如重复10条删除9条
require_once('init.php');
set_time_limit(0);
$db = new DB();
$offset = 0;
$limit = 200;
$ke = 1;
while($ke>0){

    $gsql = "select num_iid from u_goods where 1 limit $offset,$limit";
    $gresult = $db->mysqlfetch($gsql);
    if(empty($gresult)){
        $ke = 0;
    }else{
        foreach($gresult as $k=>$v){
            $sql = "select id from u_products where num_iid=".$v['num_iid']." and sku_id in( select p1.sku_id from u_products as p1 where p1.num_iid=".$v['num_iid']." group by p1.sku_id   having  count(p1.sku_id) > 1) and id not in (select min(p2.id) from  u_products as p2 where p2.num_iid=".$v['num_iid']." group by p2.sku_id  having count(p2.sku_id )>1)";
            $result = $db->mysqlfetch($sql);
            if(!empty($result)){
            foreach($result as $k2=>$v2){
                $desql = "delete from u_products where id=".$v2['id'];
                $db->mysqlquery($desql);
            }
          }
        }
    }
    $offset+=$limit;
    /*$sql = "select id from u_products where sku_id in( select sku_id from u_products group by sku_id   having  count(sku_id) > 1) and id not in (select min(p2.id) from  u_products as p2 group by p2.sku_id  having count(p2.sku_id )>1) limit $offset,$limit";
     $result = $db->mysqlfetch($sql);
     unset($sql);
     if(empty($result)){
         $ke = 0;
     }else{
        foreach($result as $k=>$v){
           $desql = "delete from u_products where id=".$v['id'];
           $db->mysqlquery($desql);
        }
     }*/
}
exit;