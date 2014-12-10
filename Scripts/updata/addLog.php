<?php
require_once('../init.php');
set_time_limit(0);
$db = new DB();
function dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/') $path = $path . '/';
    return $path;
}
/**
 * 列出目录下的所有文件
 *
 * @param str $path 目录
 * @param str $exts 后缀
 * @param array $list 路径数组
 * @return array 返回路径数组
 */
function dir_list($path, $exts = '', $list = array()) {
    $path = dir_path($path);
    $files = glob($path . '*');
    foreach($files as $v) {
        if (!$exts || preg_match("/\.($exts)/i", $v)) {
            if(file_exists($v) && !is_dir($v)){
                $list[] = $v;
            }
            if (is_dir($v)) {
                $list = dir_list($v, $exts, $list);
            }
        }
    }
    return $list;
}
$time = time()-86400;
$year = date('Y',$time);$mounth = date('Y-m',$time);$date = date('Y-m-d',$time);
$path = '/data/upload/Upload/log/fitting/'.$year.'/'.$mounth.'/'.$date;
$fileList = dir_list($path);
foreach($fileList as $k=>$v){
  $fileArr = file($v);
  $sql = 'insert into `u_fitting_log` (`createtime`,`uq`,`ip`,`uid`,`sessionid`) values ';
  $strsql = '';
  foreach($fileArr as $k2=>$v2){
     $str = explode('_',$v2);
     $strsql.="('".$str[0]."','".$str[1]."','".$str[2]."','".$str[3]."','".$str[4]."'),";
  }
    unset($fileArr);
    $strsql = rtrim($strsql,',');
    $sql.=$strsql;
    $db->mysqlquery($sql);
}
exit;