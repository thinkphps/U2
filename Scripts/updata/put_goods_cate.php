<?php
//导入优衣库给的商品数据然后获取商品所对应的自定义分类名称和别名
error_reporting(0);
require_once('../init.php');
set_time_limit(0);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(dirname(__FILE__))));
$db = new DB();
//售前
$ke = 1;
$offset = 0;
$limit = 400;
$fp = fopen($root_dir.'/Upload/shouqian.csv','w');
$head = array('goodsname','GoodsPrice','购买数量','下单数','自定义分类名称','别名');
foreach ($head as $i => $v) {
    $head[$i] = iconv('utf-8', 'gbk', $v);
}
fputcsv($fp,$head);
while($ke>0){
    $sql = "select * from u_data_goodcate where flag=1 limit {$offset},{$limit}";
    $result = $db->mysqlfetch($sql);
    if(empty($result) && count($result)<=0){
        $ke = 0;
    }else{
        foreach($result as $k=>$v){
            $sql = "select num_iid from u_goods where title='".$v['goodsname']."'";
            $re = $db->mysqlfetch($sql);
            if(!empty($re)){
                $sql2 = "select u_sellercats.* from u_catesgoods inner join u_sellercats on u_sellercats.ID=u_catesgoods.cateID where u_catesgoods.num_iid=".$re[0]['num_iid'];
                $rec = $db->mysqlfetch($sql2);
                $zidingyiname = '';$bieming = '';
                foreach($rec as $k2=>$v2){
                    if($v2['cateName']){
                      $zidingyiname.=$v2['cateName'].'_';
                    }
                    if($v2['shortName']){
                        $bieming.=$v2['shortName'].'_';
                    }
                }
                $zidingyiname = rtrim($zidingyiname,'_');
                $bieming = rtrim($bieming,'_');
                $pl = array();
                $v['goodsname'] = stripslashes($v['goodsname']);
                $pl[0] = iconv('utf-8','gb2312',$v['goodsname']);
                $pl[1] = $v['GoodsPrice'];
                $pl[2] = $v['num'];
                $pl[3] = $v['dnum'];
                $pl[4] = iconv('utf-8','gb2312',$zidingyiname);
                $pl[5] = iconv('utf-8','gb2312',$bieming);
                fputcsv($fp,$pl);
            }else{
                //没有查到数据
                $pl = array();
                $v['goodsname'] = stripslashes($v['goodsname']);
                $pl[0] = iconv('utf-8','gb2312',$v['goodsname']);
                $pl[1] = $v['GoodsPrice'];
                $pl[2] = $v['num'];
                $pl[3] = $v['dnum'];
                $pl[4] = '';
                $pl[5] = '';
                fputcsv($fp,$pl);
            }
        }
    }
    $offset+=$limit;
    unset($result);
}
fclose($fp);

//售后
$ke2 = 1;
$offset2 = 0;
$limit2 = 400;
$fp2 = fopen($root_dir.'/Upload/shouhou.csv','w');
$head = array('goodsname','GoodsPrice','购买数量','下单数','自定义分类名称','别名');
foreach ($head as $i => $v) {
    $head[$i] = iconv('utf-8', 'gbk', $v);
}
fputcsv($fp2,$head);
while($ke2>0){
    $sql = "select * from u_data_goodcate where flag=2 limit {$offset2},{$limit2}";
    $result = $db->mysqlfetch($sql);
    if(empty($result) && count($result)<=0){
        $ke2 = 0;
    }else{
        foreach($result as $k=>$v){
            $sql = "select num_iid from u_goods where title='".$v['goodsname']."'";
            $re = $db->mysqlfetch($sql);
            if(!empty($re)){
                $sql2 = "select u_sellercats.* from u_catesgoods inner join u_sellercats on u_sellercats.ID=u_catesgoods.cateID where u_catesgoods.num_iid=".$re[0]['num_iid'];
                $rec = $db->mysqlfetch($sql2);
                $zidingyiname = '';$bieming = '';
                foreach($rec as $k2=>$v2){
                    if($v2['cateName']){
                        $zidingyiname.=$v2['cateName'].'_';
                    }
                    if($v2['shortName']){
                        $bieming.=$v2['shortName'].'_';
                    }
                }
                $zidingyiname = rtrim($zidingyiname,'_');
                $bieming = rtrim($bieming,'_');
                $pl = array();
                $v['goodsname'] = stripslashes($v['goodsname']);
                $pl[0] = iconv('utf-8','gb2312',$v['goodsname']);
                $pl[1] = $v['GoodsPrice'];
                $pl[2] = $v['num'];
                $pl[3] = $v['dnum'];
                $pl[4] = iconv('utf-8','gb2312',$zidingyiname);
                $pl[5] = iconv('utf-8','gb2312',$bieming);
                fputcsv($fp2,$pl);
            }else{
                //没有查到数据
                $pl = array();
                $v['goodsname'] = stripslashes($v['goodsname']);
                $pl[0] = iconv('utf-8','gb2312',$v['goodsname']);
                $pl[1] = $v['GoodsPrice'];
                $pl[2] = $v['num'];
                $pl[3] = $v['dnum'];
                $pl[4] = '';
                $pl[5] = '';
                fputcsv($fp2,$pl);
            }
        }
    }
    $offset2+=$limit2;
    unset($result);
}
fclose($fp2);
/*$sql = "select `goodsname` from u_data_goodcate";
$result = $db->mysqlfetch($sql);
unset($sql);
foreach($result as $k=>$v){
   $sql = "select num_iid from u_goods where title='".$v['goodsname']."'";
    $re = $db->mysqlfetch($sql);
    if(empty($re)){
     echo $v['goodsname'].'<p>';
    }
}*/