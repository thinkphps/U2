<?php
class ExportAction extends Action{
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
	public function ecportindex(){
           ini_set('memory_limi','200M');
		   set_time_limit(0);
		   date_default_timezone_set('Asia/Shanghai');
		   $filename = 'Upload/shop/'.date('YmdHis').'.csv';
		   $step = 200;
		   $offset = 0;
		   $ke = 1;
		   $fp = fopen($filename,'w');
		   $head = array('后台ID','货号','商品名称','商品数字ID','商品库存','上架状态','性别','部位','指数','最低气温','最高气温','风格','自定义分类');
		   foreach($head as $i=>$v){
		   $head[$i] = iconv('utf-8','gbk',$v);
		   }
		   fputcsv($fp,$head);
		   $gtag = M('Goodtag');
		   $customcate = M('Customcate');
		   while($ke>0){
		   $list = $gtag->join('u_goods on u_goods.id=u_goodtag.good_id')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.isud,u_goods.approve_status,u_goods.item_bn,u_goods.title,u_goods.num')->group('u_goodtag.good_id')->order('u_goods.list_time desc')->limit($offset.','.$step)->select();
		   if(!$list){
		   	$ke = 0;
		   }else{
		   foreach($list as $k=>$v){
		   $sd[0] = $v['id'];
		   $sd[1] = $v['item_bn'];   
		   $sd[2] = iconv('utf-8','gbk',$v['title']);
		   $sd[3] = $v['num_iid']."\t";
		   $sd[4] = $v['num'];
		   $sd[5] = ($v['approve_status']=='onsale')?iconv('utf-8','gbk','在售'):iconv('utf-8','gbk','下架');
		   switch($v['type']){
		   case '0' :
		   $sd[6] = iconv('utf-8','gbk','通用');
		   break;
		   case '1' :
		   $sd[6] = iconv('utf-8','gbk','女装');
		   break;
		   case '2' :
		   $sd[6] = iconv('utf-8','gbk','男装');
		   break;
		   case '3' :
		   $sd[6] = iconv('utf-8','gbk','女童');
		   break;
           case '4' :
           $sd[6] = iconv('utf-8','gbk','童装');
           break;
               case '5' :
                   $sd[6] = iconv('utf-8','gbk','婴幼儿');
           break;
		   }
		   switch($v['isud']){
		   case '0' :
		   $sd[7] = iconv('utf-8','gbk','未选择');
           break;
		   case '1' :
		   $sd[7] = iconv('utf-8','gbk','上装');
		   break;
		   case '2' :
		   $sd[7] = iconv('utf-8','gbk','下装');
		   break;
		   case '3' :
		   $sd[7] = iconv('utf-8','gbk','配饰');
		   break;
		   case '4' : 
		   $sd[7] = iconv('utf-8','gbk','套装');
		   break;
		   case '5' : 
		   $sd[7] = iconv('utf-8','gbk','内衣');
		   break;
           case '6' :
              $sd[7] = iconv('utf-8','gbk','婴幼儿');
              break;
		   }
		   $windexlist = $gtag->join('u_windex on u_windex.id=u_goodtag.wid')->field('u_goodtag.stm,u_goodtag.etm,u_goodtag.ccateid,u_windex.wname')->where(array('u_goodtag.good_id'=>$v['id']))->group('u_goodtag.wid')->select();
		   $wstr = '';
		   $sstr = '';
		   $estr = '';

		   foreach($windexlist as $v2=>$wv){
		   $wstr.=$wv['wname'].',';
		   $sstr.= $wv['stm'].',';
		   $estr.= $wv['etm'].',';
		   }
		   if(!empty($wstr)){
		   $wstr = rtrim($wstr,',');
		   $sd[8] = iconv('utf-8','gbk',$wstr);
		   }else{
		   $sd[8] = '';
		   }
		   if(!empty($sstr)){
		   $sstr = rtrim($sstr,',');
		   $sd[9] = $sstr."\t";
		   }else{
		   $sd[9] = 0;
		   }
		   if(!empty($estr)){
		   $estr = rtrim($estr,',');
		   $sd[10] = $estr."\t";
		   }else{
		   $sd[10] = 0;
		   }

		   $ftaglist = $gtag->join('u_settings_suit_style us on us.ID=u_goodtag.ftag_id')->field('us.description')->where(array('u_goodtag.good_id'=>$v['id']))->group('u_goodtag.ftag_id')->select();
		   $fstr = '';
		   foreach($ftaglist as $fk=>$fv){
		   if($fv['description']){
		   $fstr.=$fv['description'].',';
		   }
		   }	   
           if(!empty($fstr)){
           $fstr = rtrim($fstr,',');
           $sd[11] = iconv('utf-8','gbk',$fstr);
           }else{
           $sd[11] = '';
           }
		   $cfind = $customcate->field('name')->where(array('id'=>$windexlist[0]['ccateid']))->find();		   
		   $sd[12] = iconv('utf-8','gbk',$cfind['name']);
		   fputcsv($fp,$sd);
		   }
           $offset+=$step;
		   $list = array();
		   }
		   }
fclose($fp);
echo '完成';
	}

    public function exportshop(){
        $handle = fopen('Upload/1.csv','r');
        $i = 0;
        $shop = M('Shop');
        while($data = fgetcsv($handle)){
            if($i>0 && !empty($data)){
                $time = date('Y-m-d H:i:s');
                //$data[3] = $this->iconvfun($data[3]);
                $data[0] = $this->iconvfun($data[0]);
                $result = $shop->field('id,store_id')->where(array('store_id'=>$data[0]))->find();
                if(!empty($result)){
                    $daarr = array('store_id'=>$this->iconvfun(intval($data[0])),
                        'longitude'=>$this->iconvfun($data[6]),
                        'latitude'=>$this->iconvfun($data[7]),
                        'saddress'=>$this->iconvfun($data[5]),
                        'tradetime'=>$this->iconvfun($data['11']),
                        'scall'=>$this->iconvfun($data[10]),
                        'sange'=>$this->iconvfun($data[12]));
                    $daarr = array('tradetime'=>$this->iconvfun(trim($data['11'])));
                    $shop->where(array('id'=>$result['id']))->save($daarr);
                }else{
                    echo $data[3].'<br/>';
                    $daarr = array('store_id'=>$this->iconvfun(intval($data[0])),
                        'longitude'=>$this->iconvfun($data[6]),
                        'latitude'=>$this->iconvfun($data[7]),
                        'sname'=>$this->iconvfun($data[3]),
                        'saddress'=>$this->iconvfun($data[5]),
                        'tradetime'=>$this->iconvfun($data[11]),
                        'scall'=>$this->iconvfun($data[10]),
                        'sange'=>$this->iconvfun($data[12]),
                        'createtime'=>$time);
                    $shop->add($daarr);
                }
            }
            $i++;
        }
    }

    public function iconvfun($v){
        return iconv('GB2312','UTF-8',$v);
    }
    public function updateshop(){
        $handle = fopen('Upload/uniqloShop.csv','r');
        $i = 0;
        $shop = M('Shop');
        while($data = fgetcsv($handle)){
            if($i>0 && !empty($data)){
                $data[3] = $this->iconvfun($data[3]);
                $data[0] = trim($data[0]);
                $result = $shop->field('id,store_id')->where(array('store_id'=>$data[0]))->find();
                if(!empty($result)){
                        $daarr = array('sname'=>$data[3],
                        'saddress'=>$this->iconvfun($data[4]),
                        'tradetime'=>$this->iconvfun($data[6]),
                        'scall'=>$this->iconvfun($data[5]),
                        'sange'=>$this->iconvfun($data[7]));
                    $shop->where(array('id'=>$result['id']))->save($daarr);
                }else{
                    echo $data[0].'<p>';
                }
            }
            $i++;
        }
    }
}