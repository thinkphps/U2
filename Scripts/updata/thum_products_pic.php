<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 15-1-5
 * Time: 下午4:42
 * 吧sku图片改为32*32大小
 */
require_once('../init.php');
set_time_limit(0);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(dirname(__FILE__))));
$db = new DB();
//把图片处理小
$ke = 1;
$offset = 0;
$limit = 200;
$result = array();
$time = strtotime(date('Y-m-d'));
$oneMtonth = $time-86400*30;
$starttime = date('Y-m-d H:i:s',$oneMtonth);
while($ke>0){
    $sql = "select `id`,`num_iid`,`approve_status` from `u_goods` where `list_time`>='{$starttime}' limit $offset,$limit";
    $result = $db->mysqlfetch($sql);
    unset($sql);
    if(empty($result)){
        $ke = 0;
    }else{
        foreach($result as $k=>$v){
            $psql = "select `sku_id`,`url` from `u_products` where `num_iid`=".$v['num_iid'];
            $p_list = $db->mysqlfetch($psql);
            foreach($p_list as  $k2=>$v2){
                $filepath = $root_dir.'/'.dirname($v2['url']);
                $ext = pathinfo($v2['url'], PATHINFO_EXTENSION);
                $newfiledir = $filepath.'/'.'32_32';
                $macfiledir = $filepath.'/'.'mac100';
                if(file_exists($filepath)){
                if(!file_exists($newfiledir)){
                    mkdir($newfiledir,777,true);
                }
                if(!file_exists($macfiledir)){
                    mkdir($macfiledir,777,true);
                }
                    $oldpath = $root_dir.'/'.$v2['url'];
                    $newfilepath = $newfiledir.'/'.$v2['sku_id'].'.'.$ext;
                    $macfilepath = $macfiledir.'/'.$v2['sku_id'].'.png';
                    //pc的颜色图
                    exec('convert -resize 32x32 '.$oldpath.' '.$newfilepath.'');
                    //苹果app颜色图
                    exec('convert -antialias -transparent white -resize 100x100 '.$oldpath.' '.$macfilepath.'');
                }
            }
        }
    }
    $offset+=$limit;
    unset($result);
}
exit;