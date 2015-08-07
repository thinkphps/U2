<?php
class Fitting3dAction extends Action{
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
           $daylog = M('DayLog');
           $where = array();
           $daterange = trim($this->_request('daterange'));
           $pagestr = '';
           if(!empty($daterange)){
               $arr_date = explode('-',$daterange);
               $arr_date[0] = str_replace('/','-',$arr_date[0]);
               $arr_date[1] = str_replace('/','-',$arr_date[1]);
               if($arr_date[0]==$arr_date[1]){
                   $where['log_day'] = $arr_date[0];
               }else{
                   $where['log_day'] = array(array('egt',$arr_date[0]),array('elt',$arr_date[1]));
               }
               $pagestr.="/daterange/".$daterange;
           }
           import("@.ORG.Pageyu");
           $count = $daylog->where($where)->count();
           $p = new Page($count,20,$pagestr);
           $list = $daylog->field('*')->where($where)->order('createtime desc')->limit($p->firstRows.','.$p->maxRows)->select();
           $page = $p->showPage();
           $this->assign('list',$list);
           $this->assign('page',$page);
           $this->assign('daterange',$daterange);
           $this->assign('p',$_GET['p']);
           $this->display();
        }else{
            $this->display('Login/index');
        }
    }
    public function logview(){
        if(!empty($this->aid)){
            $log_day = $this->_get('log_day');
            $fittinglog = M('Fitting3dLog');
            import("@.ORG.Pageyu");
            $pagestr = '';
            $starttime = $log_day.' 00:00:00';
            $endtime = $log_day.' 23:59:59';
			$pagestr.="/log_day/".$log_day;
            $where['fitting_time'] = array(array('egt',$starttime),array('elt',$endtime));
            $count = $fittinglog->where($where)->count();
            $p = new Page($count,20,$pagestr);
            $list = $fittinglog->field('*')->where($where)->order('fitting_time desc')->limit($p->firstRows.','.$p->maxRows)->select();
            $page = $p->showPage();
            $this->assign('list',$list);
            $this->assign('page',$page);
            $this->assign('p',$_GET['p']);
            $this->assign('log_day',$log_day);
            $this->display('logview');
        }else{
            $this->display('Login/index');
        }
    }
    public function logedit(){
        if(!empty($this->aid)){
            $id = $this->_request('id');
            $daterange = trim($this->_request('daterange'));
            $p = $this->_request('p');
            $daylog = M('DayLog');
            $visitnum = trim($this->_request('visitnum'));
            $result = $daylog->field('*')->where(array('id'=>$id))->find();
            if(empty($visitnum)){
                $this->assign('result',$result);
                $this->assign('p',$p);
                $this->assign('daterange',$daterange);
                $this->display();
            }else{
                //总访客平均试衣件数
                $fitting_avg_num = number_format(($result['fitting_num']/$visitnum),2,'.','');
                //访客下载比
                $avg_download = sprintf("%.2f",number_format(($result['download_num']/$visitnum),4,'.','')*100);
                $daylog->where(array('id'=>$id))->save(array('visitnum'=>$visitnum,'fitting_avg_num'=>$fitting_avg_num,'avg_download'=>$avg_download));
                $this->redirect('Fitting3d/index',array('p'=>$p,'daterange'=>$daterange));
            }
        }else{
            $this->display('Login/index');
        }
    }
    public function export(){
        if(!empty($this->aid)){
            $log_day = $this->_request('log_day');
            $fittinglog = M('Fitting3dLog');
            $this->exportheader($log_day);
            $starttime = $log_day.' 00:00:00';
            $endtime = $log_day.' 23:59:59';
            $where['fitting_time'] = array(array('egt',$starttime),array('elt',$endtime));
            $list = $fittinglog->field('*')->where($where)->order('fitting_time desc')->select();
            $data = '用户ID,IP地址,访问日期,进入时间,是否下载插件,性别,身高,体重,肩宽,上臂围,胸围,罩杯,腰围,臀围,腿围,腿长,商品ID,商品尺码,商品颜色,点击购买,分享微博,分享微信'."\n";
            $data = $this->changecode($data);
            foreach($list as $k=>$v){
                if($v['isdown']==1){
                   $isdown = '是';
                }else{
                   $isdown = '否';
                }
                $isdown = $this->changecode($isdown);
                if($v['isbuy']==1){
                    $isbuy = '是';
                }else{
                    $isbuy = '否';
                }
                $isbuy = $this->changecode($isbuy);
                if($v['isweibo']==1){
                    $isweibo = '是';
                }else{
                    $isweibo = '否';
                }
                $isweibo = $this->changecode($isweibo);
                if($v['isweixin']==1){
                    $isweixin = '是';
                }else{
                    $isweixin = '否';
                }
                $isweixin = $this->changecode($isweixin);
                $data.=$v['uid'].",".$v['ip'].",".$v['visittime']."\t,".$v['intime']."\t,".$isdown.",".$v['gender'].",".$v['height'].",".$v['weight'].",".$v['shoulder'].",".$v['upper_arm'].",".$v['chest'].",".$v['cup'].",".$v['waist'].",".$v['hip'].",".$v['leg'].",".$v['leg_long'].",".$v['item_bn'].",".$v['goodsize'].",".$v['color'].",".$isbuy.",".$isweibo.",".$isweixin."\n";
            }
            unset($list);
            echo $data;
        }else{
            $this->display('Login/index');
        }
    }
    public function exportheader($date){
        $filename = $date.".csv";//文件名
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
    }
    public function changecode($strInput) {
    return iconv('utf-8','gb2312',$strInput);//页面编码为utf-8时使用，否则导出的中文为乱码
   }
}