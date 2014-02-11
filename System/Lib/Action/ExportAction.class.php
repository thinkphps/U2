<?php
class ExportAction extends Action{
	public function ecportindex(){
           ini_set('memory_limi','200M');
		   set_time_limit(0);
		   date_default_timezone_set('Asia/Shanghai');
		   $filename = 'Upload/shop/'.date('YmdHis').'.csv';
		   $step = 200;
		   $offset = 0;
		   $ke = 1;
		   $fp = fopen($filename,'w');
		   $head = array('后台ID','货号','商品名称','商品数字ID','商品库存','上架状态','性别','部位','指数','最低气温','最高气温','场合','风格','自定义分类');
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
		   $sd[6] = 'WOMEN';
		   break;
		   case '2' :
		   $sd[6] = 'MEN';
		   break;
		   case '3' :
		   $sd[6] = 'KIDS';
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
		   $taglist = $gtag->join('u_tag on u_tag.id=u_goodtag.tag_id')->field('u_tag.name')->where(array('u_goodtag.good_id'=>$v['id']))->group('u_goodtag.tag_id')->select();
		   $tstr = '';
		   foreach($taglist as $tk=>$tv){
		   if($tv['name']){
		   $tstr.=$tv['name'].',';
		   }
		   }	   
           if(!empty($tstr)){
           $tstr = rtrim($tstr,',');
           $sd[11] = iconv('utf-8','gbk',$tstr);
           }else{
           $sd[11] = '';
           }
		   $ftaglist = $gtag->join('u_tag on u_tag.id=u_goodtag.ftag_id')->field('u_tag.name')->where(array('u_goodtag.good_id'=>$v['id']))->group('u_goodtag.ftag_id')->select();
		   $fstr = '';
		   foreach($ftaglist as $fk=>$fv){
		   if($fv['name']){
		   $fstr.=$fv['name'].',';
		   }
		   }	   
           if(!empty($fstr)){
           $fstr = rtrim($fstr,',');
           $sd[12] = iconv('utf-8','gbk',$fstr);
           }else{
           $sd[12] = '';
           }
		   $cfind = $customcate->field('name')->where(array('id'=>$windexlist[0]['ccateid']))->find();		   
		   $sd[13] = iconv('utf-8','gbk',$cfind['name']);
		   fputcsv($fp,$sd);
		   }
           $offset+=$step;
		   $list = array();
		   }
		   }
fclose($fp);
echo '完成';
	}
}