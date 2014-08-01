<?php
class DB{
protected $link;
public $appkey = '21366151';
public $secretKey = 'd7580da21e45ed89535e0702abda5a2d';
public $token = '62018043f2a6ZZ82a764cb0754c9d1e75f8a180b3a98018196993935';
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
public function createdir($filename,$dir='',$path='',$img='',$flag = 1){
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
	  $dir1 = $dir1.'/'.$date;
	  $path.='/'.$date;
	  if(!file_exists($dir1)&&!is_dir($dir1)){
	  	mkdir($dir1,0777);
	  }
	  $dir1 = $dir1.'/'.$time;
	  $path.='/'.$time;
	  if(!file_exists($dir1)&&!is_dir($dir1)){
	  	mkdir($dir1,0777);
	  }

	  /*$dir1 = $dir1.'/'.$hou;
	  $path.='/'.$hou;
	  if(!file_exists($dir1)&&!is_dir($dir1)){
	  	mkdir($dir1,0777);
	  }*/

	  $extension = pathinfo($img, PATHINFO_EXTENSION);
	  $save_image = $dir1.'/'.$filename.'.'.$extension;
      if($flag==1){
	  $path.='/'.$filename.'.png';
      }else if($flag==2){
      //$path.='/'.$filename.'.jpg';
          $path.='/'.$filename.'.'.$extension;
      }
	  $arr[] = $save_image;
	  $arr[] = $path;
	  return $arr;
}
    function sexhead($sex,$id){
        $arrsex[1] = array('1'=>'http://pic1.beubeu.com/6/7/0/670e0e6376cb9d2d1712689329587c41.png','2'=>'http://pic1.beubeu.com/d/8/b/d8bc92f7741d6b459357f20212a412de.png','3'=>'http://pic1.beubeu.com/d/d/c/ddc9ab600d47fd5f06fc43121bc94e53.png','4'=>'http://pic1.beubeu.com/e/8/4/e8423528666c034dd05c154fc71fa6c8.png');
        $arrsex[2] = array('1'=>'http://pic1.beubeu.com/1/b/f/1bfb6ea23cb6b3b8b227e49b9d2dd999.png','2'=>'http://pic1.beubeu.com/2/e/1/2e1769f488f0172edb69a6504d3de4a8.png','3'=>'http://pic1.beubeu.com/3/2/a/32a89985ce7acce06800cb72b02bcfd7.png','4'=>'http://pic1.beubeu.com/d/1/c/d1c78f42b990c41ae96e36afde436482.png');
        $arrsex[3] = array('1'=>'http://pic1.beubeu.com/b/9/f/b9f82e10dc9d183c4b353f88d13c4c41.png','2'=>'http://pic1.beubeu.com/b/e/1/be130a8d605cfde1dce3d883c2877ee8.png','3'=>'http://pic1.beubeu.com/c/c/5/cc55b76860cdefc9dfe0203fa0d8d4de.png','4'=>'http://pic1.beubeu.com/f/e/9/fe916846db6ed98fc02264c2c7f23f1a.png');
        $arrsex[4] = array('1'=>'http://pic1.beubeu.com/4/c/f/4cf2ed48cb73a35c3cceb17799ed22bf.png','2'=>'http://pic1.beubeu.com/8/6/3/8638f844ddb77569fa7b1d9e3776e1c2.png','3'=>'http://pic1.beubeu.com/9/a/d/9ad5c5d3a5656305992f04e18b5dc0ba.png','4'=>'http://pic1.beubeu.com/d/8/e/d8e4ad00852fb849ebbcdbd8f3844b31.png');
        return $arrsex[$sex][$id];
    }
}
