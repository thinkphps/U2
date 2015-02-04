<?php
class DB{
protected $link;
/*public $appkey = '21366151';
public $secretKey = 'd7580da21e45ed89535e0702abda5a2d';
public $token = '62018043f2a6ZZ82a764cb0754c9d1e75f8a180b3a98018196993935';*/
public $appkey = '21805942';
public $secretKey = 'a90b12c2a9bc72df603e90e7728abda9';
public $token = '6201e24b0995f8af686fad9e7cd1d1b0ZZ5338185af9ff5196993935';
function __construct(){
	$this->link = mysql_connect('localhost','root','root');
	mysql_select_db('uniqlo',$this->link);
    mysql_query('set names UTF8',$this->link);
}
function mysqlquery($sql){
	$result = mysql_query($sql,$this->link);
	return $result;
}
function mysqlfetch($sql){
	$arr = array();
	$result = $this->mysqlquery($sql);
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
    $arr[] = $row;
	}
	return $arr;
}
public function createdir($filename,$dir='',$path='',$img='',$flag = 1,$offset=0){
	  $timenow = time();
	  $year = date('Y',$timenow);
 	  $date = date('Y-m',$timenow);
      $time = date('Y-m-d',$timenow);
      $hou = date('Y-m-d-H',$timenow);
	  $dir1 = $dir.$year;
	  $path.=$year;
	  if(!file_exists($dir1)&&!is_dir($dir1)){
	  	mkdir($dir1,0777);
	  }
	  chmod($dir1,0777);
	  $dir1 = $dir1.'/'.$date;
	  $path.='/'.$date;
	  if(!file_exists($dir1)&&!is_dir($dir1)){
	  	mkdir($dir1,0777);
	  }
	  chmod($dir1,0777);
	  $dir1 = $dir1.'/'.$time;
	  $path.='/'.$time;
	  if(!file_exists($dir1)&&!is_dir($dir1)){
	  	mkdir($dir1,0777);
	  }
      chmod($dir1,0777);
	 if($offset>0){
         $dir1 = $dir1.'/'.$offset;
         $path.='/'.$offset;
         if(!file_exists($dir1)&&!is_dir($dir1)){
             mkdir($dir1,0777);
         }
		 chmod($dir1,0777);
     }
	  /*$dir1 = $dir1.'/'.$hou;
	  $path.='/'.$hou;
	  if(!file_exists($dir1)&&!is_dir($dir1)){
	  	mkdir($dir1,0777);
	  }*/

	  $extension = pathinfo($img, PATHINFO_EXTENSION);
	  $save_image = $dir1.'/'.$filename.'.'.$extension;
	  if($flag == 1){
      $path.='/'.$filename.'.png';
	  }else{
	  $path.='/'.$filename.'.'.$extension;
	  }
	  $arr[] = $save_image;
	  $arr[] = $path;
	  return $arr;
}
}
