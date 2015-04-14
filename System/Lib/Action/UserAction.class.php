<?php
class UserAction extends Action{
	private $aid;
	private $nick;
	public function _initialize(){
        $this->aid = session('aid');
        $this->nick = session('nickn');
        $this->assign('aid',$this->aid);
        $this->assign('nick',$this->nick);
	}
    //按日统计注册用户数量
    private function countuser($countrow,$p,$daterange){
        $startdate = "1900-01-01";
        $enddate = "9900-01-01";
        if (!empty($daterange)){
            $list = explode('-', $daterange);
            $startdate = current($list);
            $enddate = end($list);

            $startdate = $this->validateDate(trim($startdate),"1900-01-01");
            $enddate = $this->validateDate(trim($enddate),"9900-01-01");
        }

        if (!empty($countrow)){
            $sql = "SELECT COUNT(DISTINCT(DATE(createtime))) AS rowcount ".
                "FROM uniqlo.u_user u WHERE DATE(createtime) BETWEEN '$startdate' AND '$enddate'";
        }else{
            $sql = "SELECT tb_inc.dn as registdate,tb_sum.ucount as uercount,tb_inc.ucount as incusercount,tb_taobao.ucount as inctbusercount FROM"
                ."(SELECT DATE(createtime) as dn,count(0) as ucount FROM uniqlo.u_user u GROUP BY DATE(createtime)) tb_inc "
                ."INNER JOIN "
                ."(SELECT tb_today.dn, sum(tb_his.ucount) as ucount FROM"
                ."(SELECT DATE(createtime) as dn,count(0) as ucount FROM uniqlo.u_user u GROUP BY DATE(createtime)) tb_today,"
                ."(SELECT DATE(createtime) as dn,count(0) as ucount FROM uniqlo.u_user u GROUP BY DATE(createtime)) tb_his "
                ."WHERE tb_today.dn >=tb_his.dn group by tb_today.dn)tb_sum "
                ."ON (tb_sum.dn=tb_inc.dn) "
                ."LEFT JOIN "
                ."(SELECT DATE(createtime) as dn,count(0) as ucount FROM uniqlo.u_user u WHERE NOT taobao_name = '' GROUP BY DATE(createtime)) tb_taobao "
                ."ON (tb_taobao.dn=tb_inc.dn) "
                ."WHERE tb_inc.dn BETWEEN '$startdate' AND '$enddate' "
                ."ORDER BY tb_inc.dn desc ";
            if (!empty($p)){
                $sql = $sql."limit $p->firstRows,$p->maxRows";
            }
        }
        $udb = M();
        $list = $udb->query($sql);
        return $list;
    }
    //验证日期格式是否合法
    private function validateDate($date,$defvalue)
    {
        $formats = array('Y/m/d','d/m/Y','yyyymmdd');
        foreach ( $formats as $f => $format ){
            $d = DateTime::createFromFormat($format, $date);
            if ($d && $d->format($format) == $date){
                return $date;
            }
        }
        return $defvalue;
    }

    public function index(){
        if(!empty($this->aid)){
            $daterange = $this->_request('daterange');
            $pagestr = "";
        //        保存查询条件
            if(!empty($daterange)){
                $pagestr ="?daterange=".$daterange;
            }
        //        取得分页
            $rowcount = $this->countuser(1,"",$daterange);
            $count = $rowcount[0]['rowcount'];
            import("@.ORG.Pageyu");
            $p = new Page($count,20,$pagestr);
            $page = $p->showPage();

            $usercount = $this->countuser("",$p,$daterange);
            $this->assign('usercount',$usercount);
            $this->assign('page',$page);
            $this->assign('daterange',$daterange);
            $this->assign('p',$_GET['p']);
            $this->display();
            exit;
        }else{
            $this->display('Login/index');
        }
    }

    public function _empty(){
        header("HTTP/1.1 404 Not Found");
        $this->error('次方法不存在',U('Index/index'));
	}

    public function download(){
        $daterange = $this->_request('daterange');
        $data = $this->countuser("","",$daterange);
        /** 加载PHPExcel包 */
        Vendor ( 'Excel.Classes.PHPExcel' );
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d',time()));
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', '日期')
        ->setCellValue('B1', '总用户数')
        ->setCellValue('C1', '当日新增用户数')
        ->setCellValue('D1', '当日新增关联淘宝用户数');
        $baseRow = 2;
        foreach ( $data as $r => $dataRow ){
            $row = $baseRow + $r;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $dataRow['registdate'])
                ->setCellValue('B'.$row, $dataRow['uercount'])
                ->setCellValue('C'.$row, $dataRow['incusercount'])
                ->setCellValue('D'.$row, $dataRow['inctbusercount']);
        }

        $filename = "according_user_regist_".date('Y-m-d',time()).'.xlsx';
        header ( 'Content-Disposition: attachment;filename="'.$filename);
        header ( 'Content-Type: application/vnd.ms-excel' );
        header ( 'Cache-Control: max-age=0' );
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        exit;
    }
}
