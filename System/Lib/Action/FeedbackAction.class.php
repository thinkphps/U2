<?php
class FeedbackAction extends Action{
    private $aid;
    private $nick;
    public function _initialize(){
        $this->aid = session('aid');
        $this->nick = session('nickn');
        $this->assign('aid',$this->aid);
        $this->assign('nick',$this->nick);
    }
    public function index(){
        if(!empty($this->aid)){
            $goodmodel = M('leave');
            $keyword = trim($this->_request('keyword'));
            $catid = $this->_request('ist');
            $cate1 = $this->_request('cate1');
            $cate2 = $this->_request('cate2');
            $isdoubt = $this->_request('isdoubt');
            $pagestr = '';
            import("@.ORG.Pageyu");
            if(!empty($cate1)){
                $pagestr.="/cate1/".$cate1;
            }
            if(!empty($cate2)){
                $map['catid'] = $cate2;
                $pagestr.="/cate2/".$cate2;
            }
            if(!empty($keyword)){
                $where['id'] = $keyword;
                $where['num_iid']  = $keyword;
                $where['title']  = array('like','%'.$keyword.'%');
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $pagestr.="/keyword/".$keyword;
            }
            if(!empty($catid)){
                $map['$catid'] = $catid;
                $pagestr.="/ist/".$catid;
            }
            if(!empty($isdoubt)){
                $map['isdoubt'] = $isdoubt;
                $pagestr.="/isdoubt/".$isdoubt;
            }
            $count = $goodmodel->where($map)->count();
            $p = new Page($count,20,$pagestr);
            $goods = $goodmodel->field('*')->where($map)->order('id desc')->limit($p->firstRows.','.$p->maxRows)->select();
            $page = $p->showPage();
            //取得分类
            $onecate = $cate->field('*')->where(array('parent_id'=>0))->select();
            if(!empty($cate1)){
                $twocate = $cate->field('*')->where(array('pcid'=>$cate1))->select();
            }
            $this->assign('goods',$goods);
            $this->assign('page',$page);
            $this->assign('keyword',$keyword);
            $this->assign('$catid',$catid);
            $this->assign('onecate',$onecate);
            $this->assign('twocate',$twocate);
            $this->assign('cate1',$cate1);
            $this->assign('cate2',$cate2);
            $this->assign('isdoubt',$isdoubt);
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
        $cate1 = $this->_request('id');

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

        $filename = "according_user_regist_".date('Y-m-d',time()).'.xlsx';
        header ( 'Content-Disposition: attachment;filename="'.$filename);
        header ( 'Content-Type: applicationnd.ms-excel' );
        header ( 'Cache-Control: max-age=0' );
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
    }

}
