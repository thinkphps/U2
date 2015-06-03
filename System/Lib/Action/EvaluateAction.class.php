<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 15-5-11
 * Time: 下午6:05
 * 导出优衣库评论数据
 */
class EvaluateAction extends Action{
    private $aid;
    private $nick;
    public function _initialize(){
        $this->aid = session('aid');
        $this->nick = session('nickn');
        $level = session('level');
        if($level!=1 && $level!=2){
            $this->error('权限不够',U('Index/index'));
            exit;
        }
        $this->assign('aid',$this->aid);
        $this->assign('nick',$this->nick);
    }
    /*
     *
     评论生成csv
    */
    public function index(){
        if(!empty($this->aid)){
            header("Content-type: text/html; charset=utf-8");
            set_time_limit(0);
            import("@.ORG.Uniqlo");
            $uniqlo = new Uniqlo();
            $ke = 1;
            $root_dir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
            $time = time();
            $date = date('Y-m-d',$time-24*3600);
            $filepath = $uniqlo->createdir($root_dir.'/Upload/evaluate/','/Upload/evaluate/',$time,'',0,'');
            $step = 500;
            $offset=0;
            $fp = fopen($filepath[0],'w');
            $head = array('交易ID','子订单ID','角色','评价者昵称','评价结果','评价时间','被评价者昵称','商品标题','商品价格','评价内容','评价解释','淘宝ID','评价信息是否用于记分');
            foreach ($head as $i => $v) {
                $head[$i] = iconv('utf-8', 'gbk', $v);
            }
            fputcsv($fp,$head);
            while($ke>0){
                $result = array();
               $result = M('Evaluate')->field('*')->where(array('createtime'=>$date))->limit($offset.','.$step)->select();
               if(empty($result)){
                   $ke = 0;
               }else{
                   foreach($result as $k=>$v){
                       $pl = array();
					   $v['item_title'] = stripslashes($v['item_title']);
                       $pl[0] = $v['tid']."\t";
                       $pl[1] = $v['oid']."\t";
                       $pl[2] = $v['role']."\t";
                       $pl[3] = iconv('utf-8','gb2312',$v['nick']);
                       $pl[4] = $v['result']."\t";
                       $pl[5] = $v['created']."\t";
                       $pl[6] = iconv('utf-8','gb2312',$v['rated_nick']);
                       $pl[7] = iconv('utf-8','gb2312',$v['item_title']);
                       $pl[8] = $v['item_price'];
                       $pl[9] = iconv('utf-8','gb2312',$v['content']);
                       $pl[10] = iconv('utf-8','gb2312',$v['reply']);
                       $pl[11] = $v['num_iid']."\t";
                       if($v['valid_score']=='true'){
                           $valid_score = '是';
                       }else{
                           $valid_score = '否';
                       }
                       $pl[12] = iconv('utf-8','gb2312',$valid_score);
                       fputcsv($fp,$pl);
                   }
               }
                $offset+=$step;
            }
            fclose($fp);
            $url = 'http://'.$_SERVER['HTTP_HOST'].$filepath[1];
            echo "<a href='".$url."'>下载评论</a>";
            exit;
        }else{
        $this->display('Login/index');
        }
    }
    /*
     评论生成excell
    */
    public function CreateExcell(){
       if(!empty($this->aid)){
           header("Content-type: text/html; charset=utf-8");
           set_time_limit(0);
           import("@.ORG.Uniqlo");
           $uniqlo = new Uniqlo();
           $ke = 1;
           $root_dir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
           $time = time();
           $date = date('Y-m-d',$time-24*3600);
           $filepath = $uniqlo->createdir($root_dir.'/Upload/evaluate/','/Upload/evaluate/',$time,'',1,'');
           $step = 500;
           $offset=0;

           Vendor ( 'Excel.Classes.PHPExcel' );
           $objPHPExcel = new PHPExcel();
           $objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d',time()));
           $objPHPExcel->setActiveSheetIndex(0)
               ->setCellValue('A1', '交易ID')
               ->setCellValue('B1', '子订单ID')
               ->setCellValue('C1', '角色')
               ->setCellValue('D1', '评价者昵称')
               ->setCellValue('E1', '评价结果')
               ->setCellValue('F1', '评价时间')
               ->setCellValue('G1', '被评价者昵称')
               ->setCellValue('H1', '商品标题')
               ->setCellValue('I1', '商品价格')
               ->setCellValue('J1', '评价内容')
               ->setCellValue('K1', '评价解释')
               ->setCellValue('L1', '淘宝ID')
               ->setCellValue('M1', '评价信息是否用于记分');
           $baseRow = 1;
           $i2 = 0;
           while($ke>0){
               $result = array();
               $result = M('Evaluate')->field('*')->where(array('createtime'=>$date))->limit($offset.','.$step)->select();
               if(empty($result)){
                   $ke = 0;
               }else{
                    foreach($result as $k=>$v){
                        if($v['valid_score']){
                            $valid_score = '是';
                        }else{
                            $valid_score = '否';
                        }
                        $row = $baseRow + $i2+1;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$row, $v['tid']."\t")
                            ->setCellValue('B'.$row, $v['oid']."\t")
                            ->setCellValue('C'.$row, $v['role']."\t")
                            ->setCellValue('D'.$row, $v['nick'])
                            ->setCellValue('E'.$row, $v['result'])
                            ->setCellValue('F'.$row, $v['created']."\t")
                            ->setCellValue('G'.$row, $v['rated_nick'])
                            ->setCellValue('H'.$row, $v['item_title'])
                            ->setCellValue('I'.$row, $v['item_price'])
                            ->setCellValue('J'.$row, $v['content'])
                            ->setCellValue('K'.$row, $v['reply'])
                            ->setCellValue('L'.$row, $v['num_iid']."\t")
                            ->setCellValue('M'.$row, $valid_score);
                        $i2++;
                    }
               }
               $offset+=$step;
           }
           $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
           $objWriter->save($filepath[0]);
           $objPHPExcel->disconnectWorksheets();
           unset($objPHPExcel);
           $url = 'http://'.$_SERVER['HTTP_HOST'].$filepath[1];
           echo "<a href='".$url."'>下载评论</a>";
           exit;
       }
    }
}