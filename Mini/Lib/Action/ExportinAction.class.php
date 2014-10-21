<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 14-7-10
 * Time: 下午3:49
 * To change this template use File | Settings | File Templates.
 */
class ExportinAction extends Action{
    public function exportSuit(){
       set_time_limit(0);
      //$handle = fopen('Upload/1.csv','r');
      $handle = fopen('/data/upload/Upload/3.csv','r');
      $i = 0;
      $suit = M('BeubeuSuits');
      while($data = fgetcsv($handle)){
       if($i>0){
           /*$da = array('suitImageUrl'=>$data[3],
                       'suitImageUrlHead'=>$data[4],
                       'suitImageUrlBody'=>$data[5],
                       'suitImageUrlShose'=>$data[6],
                       'suitImageUrlMatch'=>$data[7],
                       'tag'=>$data[8]);*/
          $sql = "update `u_beubeu_suits` set `suitImageUrlHead`='".$data[3]."',`suitImageUrlBody`='".$data[4]."',`suitImageUrlShose`='".$data[5]."',`suitImageUrlMatch`='".$data[6]."' where `suitID`=".$data[0];
           $suit->query($sql);
          //$suit->where(array('suitID'=>$data[0]))->save($da);
       }
          $i++;
      }
     exit;
    }
    //获取商品库存
    public function GetGoodNum(){
        set_time_limit(0);
        //$handle = fopen('Upload/good.csv','r');
        //$fp = fopen('Upload/goodnum.csv','w');
        $handle = fopen('/data/upload/Upload/good.csv','r');
        $fp = fopen('/data/upload/Upload/goodnum.csv','w');
        $i = 0;
        $goods = M('Goods');
        $head = array('货号','标题','库存');
        foreach($head as $k=>$v){
           $head[$k] = iconv('utf-8', 'gbk',$v);
        }
        fputcsv($fp,$head);
        while($data = fgetcsv($handle)){
            if($i>0){
                 $arr = array();
                 $sql = "select `num` from `u_goods` where `item_bn` ='UQ".$data[0]."'";
                 $re = $goods->query($sql);
                 $arr[] = $data[0]."\t";
                 $arr[] = $data[1];
                 $arr[] = $re[0]['num'];
                 fputcsv($fp,$arr);
            }
            $i++;
        }
        fclose($fp);
        fclose($handle);
    }
}