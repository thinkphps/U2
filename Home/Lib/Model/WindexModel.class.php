<?php
class WindexModel extends Model{
	public function getwindex($tem=0){
	 $windex = M('Windex');
	 if($tem<=-10){
	 $tem = -10;
	 }
	 $result = $windex->field('id')->where(array('stm'=>array('elt',$tem),'etm'=>array('egt',$tem)))->find();
	 switch($result['id']){
	 	case 1 :
		$tstr = '2,8';
		break;
		case 7 :
		$tstr = '6,8';
		break;
		default :
		$tstr = ($result['id']-1).','.($result['id']+1).',8';
		break;
	 }
	 $arr['wid'] = $result['id'];
	 $arr['str'] = $tstr;
	 return $arr;
	}
	//取出传入不带字的地区
	public function getrecommend($pro){
	$rem = M('Recommend');
	switch($pro){
		case '上海' :	
		case '江苏' :
		case '浙江' :
		case '福建' :
		case '台湾' :
		case '湖北' :
		case '湖南' :
		case '江西' :
		case '安徽' :
		case '广东' :
		case '广西' :
		case '海南' :
		case '重庆' :
		case '贵州' :
		case '四川' :
		case '云南' :
		case '西藏' :
		case '香港' :
		case '澳门' :
	    $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//上装
	    $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//上装	    		
		break;
		case '辽宁' :
		case '吉林' :
		case '黑龙江' :
		case '内蒙古' :
		case '北京' :
		case '天津' :
		case '河北' :
		case '山西' :
		case '山东' :	
		case '河南' :	
		case '陕西' :	
		case '宁夏' :	
		case '甘肃' :	
		case '青海' :	
		case '新疆' :	
	    $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'1'))->select();//上装
	    $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'2'))->select();//上装	    		
		break;
		default :
	   $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//上装
	    $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//上装			
	}
    $arr[] = $reulist;
	$arr[] = $redlist;
	return $arr;
	}
//传入的城市带省
public function getrecommend2($pro){
    $rem = M('Recommend');
	switch($pro){
		case '上海市' :	
		case '江苏省' :
		case '浙江省' :
		case '福建省' :
		case '台湾省' :
		case '湖北省' :
		case '湖南省' :
		case '江西省' :
		case '安徽省' :
		case '广东省' :
		case '广西' :
		case '海南省' :
		case '重庆市' :
		case '贵州省' :
		case '四川省' :
		case '云南省' :
		case '西藏' :
		case '香港' :
		case '澳门' :
	    $uclothes = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//上装
	    $dclothes = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//上装$dclothes	    		
		break;
		case '辽宁省' :
		case '吉林省' :
		case '黑龙江省' :
		case '内蒙古' :
		case '北京市' :
		case '天津市' :
		case '河北省' :
		case '山西省' :
		case '山东省' :	
		case '河南省' :	
		case '陕西省' :	
		case '宁夏' :	
		case '甘肃省' :	
		case '青海省' :	
		case '新疆' :		
	    $uclothes = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'1'))->select();//上装
	    $dclothes = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'2'))->select();//上装	    		
		break;
		default :
	   $uclothes = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//上装
	    $dclothes = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//上装				
	}
	$arr[] = $uclothes;
	$arr[] = $dclothes;
	return $arr;
}
//去重
public function saomo($arr1,&$arr2){
       foreach($arr2 as $k=>$v){
       foreach($arr1 as $k2=>$v2){
       if($v['id']==$v2['id']){
       unset($arr2[$k]);
	   break;
	   }
	   }
	   }
}
}
