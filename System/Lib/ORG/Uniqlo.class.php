<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 15-5-11
 * Time: 下午6:17
 * 公共方法
 */
class Uniqlo{
    //生成年月日目录和文件
    public function createdir($dir,$path,$time,$name='',$flag=0,$str=''){
        $timenow = $time;
        $year = date('Y',$timenow);
        $date = date('Y-m',$timenow);
        $time = date('Y-m-d',$timenow);
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
        if($flag==0){
            $filename = date('YmdHis',$timenow);
            $extension = 'csv';
        }else{

        }
        $save_image = $dir1.'/'.$filename.'.'.$extension;
        $path.='/'.$filename.'.'.$extension;
        $arr[] = $save_image;
        $arr[] = $path;
        return $arr;
    }
}