<?php
class FeedbackAction extends Action{
    private $aid;
    private $nick;
    public function _initialize(){
        $this->aid = session('aid');
        $this->nick = session('nickn');
        $level = session('level');
        if($level!=1){
            $this->error('权限不够',U('Index/index'));
            exit;
        }
        $this->assign('aid',$this->aid);
        $this->assign('nick',$this->nick);
    }
    public function index(){
        if(!empty($this->aid)){
            $leavemodel = M('Leave');
            $keyword = trim($this->_request('keyword'));
            $daterange = $this->_request('daterange');
            $catid = $this->_request('catid');
            $pagestr = "";
            if(!empty($keyword)){
                $map['content']  = array('like','%'.$keyword.'%');
                $pagestr.="?keyword=".$keyword;
            }
            if(!empty($catid)){
                $map['catid'] = $catid;
                if(empty($pagestr)){
                    $pagestr.="?catid=".$catid;
                }else{
                    $pagestr.="&catid=".$catid;
                }
            }
            //        保存查询条件
            if(!empty($daterange)){
                $rangedate = $this->splitdaterange($daterange);
                $map['DATE(createtime)']  = array('between',$rangedate[0].','.$rangedate[1]);
                if(empty($pagestr)){
                    $pagestr ="?daterange=".$daterange;
                }else{
                    $pagestr.="&daterange=".$daterange;
                }
            }
                //        取得分页
            $count = $leavemodel->where($map)->count();
            import("@.ORG.Pageyu");
            $p = new Page($count,20,$pagestr);
            $leaves = $leavemodel->field('*')->where($map)->order('id desc')->limit($p->firstRows.','.$p->maxRows)->select();
            $page = $p->showPage();

            $this->assign('leaves',$leaves);
            $this->assign('page',$page);
            $this->assign('keyword',$keyword);
            $this->assign('catid',$catid);
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
        $leavemodel = M('Leave');
        $keyword = trim($this->_request('keyword'));
        $daterange = $this->_request('daterange');
        $catid = $this->_request('catid');
        if(!empty($keyword)){
            $map['content']  = array('like','%'.$keyword.'%');
        }
        if(!empty($catid)){
            $map['catid'] = $catid;
        }
        //        保存查询条件
        if(!empty($daterange)){
            $rangedate = $this->splitdaterange($daterange);
            $map['DATE(createtime)']  = array('between',$rangedate[0].','.$rangedate[1]);
        }
        $leaves = $leavemodel->field('*')->where($map)->order('id desc')->select();

        /** 加载PHPExcel包 */
        Vendor ( 'Excel.Classes.PHPExcel' );
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle(date('Y-m-d',time()));
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '主题')
            ->setCellValue('B1', '内容')
            ->setCellValue('C1', '用户')
            ->setCellValue('D1', '创建时间');
        $baseRow = 2;
        foreach ( $leaves as $r => $dataRow ){
            $row = $baseRow + $r;
            $theme = "";
            switch($dataRow['catid']){
                case 1:
                    $theme = "设计风格";
                    break;
                case 2:
                    $theme = "使用体验";
                    break;
                case 3:
                    $theme = "功能建议";
                    break;
                case 4:
                    $theme = "其他建议";
                    break;
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$row, $theme)
                ->setCellValue('B'.$row, $dataRow['content'])
                ->setCellValue('C'.$row, $dataRow['ip'])
                ->setCellValue('D'.$row, $dataRow['createtime']);
        }

        $filename = "user_leave_".date('Y-m-d',time()).'.xlsx';
        header ( 'Content-Disposition: attachment;filename="'.$filename);
        header ( 'Content-Type: application/vnd.ms-excel' );
        header ( 'Cache-Control: max-age=0' );
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        exit;
    }

    private function splitdaterange($daterange){
        $startdate = "1900-01-01";
        $enddate = "9900-01-01";
        if (!empty($daterange)){
            $list = explode('-', $daterange);
            $startdate = current($list);
            $enddate = end($list);
            $startdate = $this->validateDate(trim($startdate),"1900-01-01");
            $enddate = $this->validateDate(trim($enddate),"9900-01-01");
        }
        return array($startdate,$enddate);
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
}
