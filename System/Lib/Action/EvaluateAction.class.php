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
        $this->assign('aid',$this->aid);
        $this->assign('nick',$this->nick);
    }
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
               $result = M('Evaluate')->field('*')->where(array('createtime'=>'2015-05-10'))->limit($offset.','.$step)->select();
               if(empty($result)){
                   $ke = 0;
               }else{
                   foreach($result as $k=>$v){
                       $pl = array();
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
                       $pl[10] = iconv('utf-8','gb2312',$v['评价解释']);
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
            $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/U2/'.$filepath[1];
            echo "<a href='".$url."'>下载评论</a>";
            exit;
        }else{
        $this->display('Login/index');
        }
    }
}