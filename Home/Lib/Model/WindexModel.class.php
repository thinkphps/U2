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
	//ȡ�����벻���ֵĵ���
	public function getrecommend($pro){
	$rem = M('Recommend');
	switch($pro){
		case '�Ϻ�' :	
		case '����' :
		case '�㽭' :
		case '����' :
		case '̨��' :
		case '����' :
		case '����' :
		case '����' :
		case '����' :
		case '�㶫' :
		case '����' :
		case '����' :
		case '����' :
		case '����' :
		case '�Ĵ�' :
		case '����' :
		case '����' :
		case '���' :
		case '����' :
	    $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//��װ
	    $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//��װ	    		
		break;
		case '����' :
		case '����' :
		case '������' :
		case '���ɹ�' :
		case '����' :
		case '���' :
		case '�ӱ�' :
		case 'ɽ��' :
		case 'ɽ��' :	
		case '����' :	
		case '����' :	
		case '����' :	
		case '����' :	
		case '�ຣ' :	
		case '�½�' :	
	    $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'1'))->select();//��װ
	    $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'2'))->select();//��װ	    		
		break;
		default :
	   $reulist = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//��װ
	    $redlist = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//��װ			
	}
    $arr[] = $reulist;
	$arr[] = $redlist;
	return $arr;
	}
//����ĳ��д�ʡ
public function getrecommend2($pro){
    $rem = M('Recommend');
	switch($pro){
		case '�Ϻ���' :	
		case '����ʡ' :
		case '�㽭ʡ' :
		case '����ʡ' :
		case '̨��ʡ' :
		case '����ʡ' :
		case '����ʡ' :
		case '����ʡ' :
		case '����ʡ' :
		case '�㶫ʡ' :
		case '����' :
		case '����ʡ' :
		case '������' :
		case '����ʡ' :
		case '�Ĵ�ʡ' :
		case '����ʡ' :
		case '����' :
		case '���' :
		case '����' :
	    $uclothes = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//��װ
	    $dclothes = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//��װ$dclothes	    		
		break;
		case '����ʡ' :
		case '����ʡ' :
		case '������ʡ' :
		case '���ɹ�' :
		case '������' :
		case '�����' :
		case '�ӱ�ʡ' :
		case 'ɽ��ʡ' :
		case 'ɽ��ʡ' :	
		case '����ʡ' :	
		case '����ʡ' :	
		case '����' :	
		case '����ʡ' :	
		case '�ຣʡ' :	
		case '�½�' :		
	    $uclothes = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'1'))->select();//��װ
	    $dclothes = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'2','u_recommend.isud'=>'2'))->select();//��װ	    		
		break;
		default :
	   $uclothes = $rem->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'1'))->select();//��װ
	    $dclothes = $rem->cache(true)->join('u_goods on u_goods.num_iid=u_recommend.num_iid')->field('u_goods.id,u_goods.num_iid,u_goods.type,u_goods.title,u_goods.num,u_goods.price,u_goods.pic_url,u_goods.detail_url')->where(array('u_recommend.type'=>'1','u_recommend.isud'=>'2'))->select();//��װ				
	}
	$arr[] = $uclothes;
	$arr[] = $dclothes;
	return $arr;
}
//ȥ��
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
