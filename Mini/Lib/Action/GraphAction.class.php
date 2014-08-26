<?php
class GraphAction extends Action{
    public function downpic(){
        $bbody = trim($this->_post('bbody'));//身体
        $bshose = trim($this->_post('bshose'));
        $bclose = trim($this->_post('bclose'));
        $bhead = trim($this->_post('bhead'));
        $root_dir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        $md5b = md5($bbody);$md5s = md5($bshose);$md5c = md5($bclose);$md5h = md5($bhead);$md5share = md5($bbody.$bshose.$bclose.$bhead);
        $share = M('Share');
        $result = $share->field('pic_url')->where(array('id'=>$md5share))->find();
        if(!empty($result)){
           $url = 'http://'.$_SERVER['HTTP_HOST'].$result['pic_url'];
        }else{
           $down = M('ShareDown');
           $shoseexten = pathinfo($bshose,PATHINFO_EXTENSION);
           $downb = $down->field('pic_url')->where(array('id'=>$md5b))->find();
              if(!empty($downb)){
                  $bimage[0] = $downb['pic_url'];
              }else{
                  $bimage = $this->createdir($md5b,$root_dir.'/Upload/sharedownpic/Body/','Upload/sharedownpic/Body/',$bbody,2);//身体
                  @file_put_contents($bimage[0],file_get_contents($bbody));
                  $down->add(array('id'=>$md5b,'pic_url'=>$bimage[0]));
              }
           $downs = $down->field('pic_url')->where(array('id'=>$md5s))->find();
           if(!empty($downs)){
               $simage[0] = $downs['pic_url'];
           }else{
               if($shoseexten=='png'){
                   $simage = $this->createdir($md5s,$root_dir.'/Upload/sharedownpic/Shose/','Upload/sharedownpic/Shose/',$bshose,2);//鞋子
                   @file_put_contents($simage[0],$bshose);
                   $down->add(array('id'=>$md5s,'pic_url'=>$simage[0]));
               }
           }
            $downc = $down->field('pic_url')->where(array('id'=>$md5c))->find();
            if(!empty($downc)){
                $cimage[0] = $downc['pic_url'];
            }else{
                $cimage = $this->createdir($md5c,$root_dir.'/Upload/sharedownpic/Match/','Upload/sharedownpic/Match/',$bclose,2);//衣服
                @file_put_contents($cimage[0],file_get_contents($bclose));
                $down->add(array('id'=>$md5c,'pic_url'=>$cimage[0]));
            }
            $downh = $down->field('pic_url')->where(array('id'=>$md5h))->find();
            if(!empty($downh)){
                $himage[0] = $downh['pic_url'];
            }else{
                $himage = $this->createdir($md5h,$root_dir.'/Upload/sharedownpic/Head/','Upload/sharedownpic/Head/',$bhead,2);//头
                @file_put_contents($himage[0],file_get_contents($bhead));
                $down->add(array('id'=>$md5h,'pic_url'=>$himage[0]));
            }
            $white=new Imagick($bimage[0]);//身体
            if($shoseexten=='png'){
                $im4=new Imagick($simage[0]);//鞋子
            }
            $im2=new Imagick($cimage[0]);//衣服
            $im3=new Imagick($himage[0]);//头
            if($shoseexten=='png'){
                $white->compositeimage($im4, Imagick::COMPOSITE_OVER, 0, 0);
            }
            $white->compositeimage($im2, Imagick::COMPOSITE_OVER, 0, 0);
            $white->compositeimage($im3, Imagick::COMPOSITE_OVER, 0, 0);
            $white->thumbnailImage( 400, 533);
            $white->setImageFormat('png');
            $image = $this->createdir($md5share,$root_dir.'/Upload/sharepic/','/Upload/sharepic/',$bclose,2);
            $white->writeImage($image[0]);
            $white->clear();
            $white->destroy();
            $share->add(array('id'=>$md5share,'pic_url'=>$image[1]));
            $url = 'http://'.$_SERVER['HTTP_HOST'].$image[1];
        }
        echo $url;
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
            $path.='/'.$filename.'.'.$extension;
        }
        $arr[] = $save_image;
        $arr[] = $path;
        return $arr;
    }
}