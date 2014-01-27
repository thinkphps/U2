<?php
class ProductseditAction extends Action{
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
	$id = trim($this->_get('id'));
	if($id>0){
	$keyword = trim($this->_request('keyword'));
	$istag = $this->_request('ist');
	$cate1 = $this->_request('cate1');
	$cate2 = $this->_request('cate2');
	$isdoubt = $this->_request('isdoubt');
	
	$goodmodel = M('Goods');
	$customcate = M('Customcate');
	$windex = M('Windex')->field('*')->order('id desc')->select();;
	$tag = M('Tag');
    $gtmodel = M('Goodtag');
	$result = $goodmodel->field('*')->where(array('id'=>$id))->find();

	$goodtaglist = M('Goodtag')->field('*')->where(array('good_id'=>$result['id']))->select();
	$list = $tag->field('id,name,parent_id,type')->where(array('parent_id'=>0))->order('id asc')->select();
	foreach($list as $k=>$v){
	$tagtype = $tag->field('id,name,parent_id,type')->group('type')->where(array('parent_id'=>$v['id']))->select();
	foreach($tagtype as $k3=>$v3){
	 $type2 = $tag->field('id,name,parent_id,type')->where(array('parent_id'=>$v['id'],'type'=>$v3['type']))->select();
	 foreach($type2 as $k4=>$v4){
       foreach($goodtaglist as $k5=>$v5){
          if($v4['id']==$v5['tag_id'] || $v4['id']==$v5['ftag_id']){
           $type2[$k4]['sel'] = 1;
		   break;
		  }
	   }
	 }
	 $tagtype[$k3]['type2'] = $type2;
	}
    $list[$k]['ctag'] = $tagtype;
	}
	$where['good_id'] = $id;
    $wh2['tag_id'] = array('neq','0');
    $wh2['_logic'] = 'OR';
    $wh2['ftag_id'] = array('neq','0');
    $where['_complex'] = $wh2;
    $gtaglist = $gtmodel->field('*')->where($where)->order('id asc')->group('wid')->select();
	//温度
	$temarr = array();
	for($i=-10;$i<=40;$i++){
    $temarr[] = $i;
	}
	//取出自定义分类
      switch($gtaglist[0]['gtype']){
	  	case 1 :
		if($gtaglist[0]['isud']==1){
		$str="<span style='color:red;'>WOMEN-上装</span>";	
		}else if($gtaglist[0]['isud']==2){
		$str="<span style='color:red;'>WOMEN-下装</span>";		
		}else if($gtaglist[0]['isud']==3){
		$str="<span style='color:red;'>WOMEN-配饰</span>";	
		}else if($gtaglist[0]['isud']==4){
		$str="<span style='color:red;'>WOMEN-套装</span>";	
		}else if($gtaglist[0]['isud']==5){
		$str="<span style='color:red;'>WOMEN-内衣</span>";	
		}
		break;
	  	case 2 :
		if($gtaglist[0]['isud']==1){
		$str="<span style='color:red;'>MAN-上装</span>";	
		}else if($gtaglist[0]['isud']==2){
		$str="<span style='color:red;'>MAN-下装</span>";		
		}else if($gtaglist[0]['isud']==3){
		$str="<span style='color:red;'>MAN-配饰</span>";	
		}else if($gtaglist[0]['isud']==4){
		$str="<span style='color:red;'>MAN-套装</span>";	
		}else if($gtaglist[0]['isud']==5){
		$str="<span style='color:red;'>MAN-内衣</span>";	
		}
		break;	
	  	case 3 :
		if($gtaglist[0]['isud']==1){
		$str="<span style='color:red;'>KIDS-上装</span>";	
		}else if($gtaglist[0]['isud']==2){
		$str="<span style='color:red;'>KIDS-下装</span>";		
		}else if($gtaglist[0]['isud']==3){
		$str="<span style='color:red;'>KIDS-配饰</span>";	
		}else if($gtaglist[0]['isud']==4){
		$str="<span style='color:red;'>KIDS-套装</span>";	
		}else if($gtaglist[0]['isud']==5){
		$str="<span style='color:red;'>KIDS-内衣</span>";	
		}
		break;	
      }	
	$ccatelist = $customcate->field('*')->where(array('gtype'=>$gtaglist[0]['gtype'],'isud'=>$gtaglist[0]['isud']))->select();

	$this->assign('list',$list);
	$this->assign('result',$result);
	$this->assign('gtaglist',$gtaglist);
	$this->assign('windex',$windex);
	$this->assign('temarr',$temarr);
	$this->assign('ccatelist',$ccatelist);
	
	$this->assign('keyword',$keyword);
	$this->assign('istag',$istag);
	$this->assign('cate1',$cate1);
	$this->assign('cate2',$cate2);
	$this->assign('isdoubt',$isdoubt);
	$this->assign('p',$_GET['p']);
	$this->assign('str',$str);
    $this->display();
	}else{
    $this->error('参数错误',U('Products/index'));
	}
    }else{
    $this->display('Login/index');
    }
}
public function doedit(){
    if(!empty($this->aid)){
	$id = trim($this->_post('id'));
	$num_iid = trim($this->_post('num_iid'));
    $originalid = trim($this->_post('originalid'));
	$keyword = trim($this->_request('keyword'));
	$istag = $this->_request('ist');
	$cate1 = $this->_request('cate1');
	$cate2 = $this->_request('cate2');
	$isdoubt = $this->_request('isdoubt');

	$this->assign('keyword',$keyword);
	$this->assign('istag',$istag);
	$this->assign('cate1',$cate1);
	$this->assign('cate2',$cate2);
	$this->assign('isdoubt',$isdoubt);
	$this->assign('p',$_REQUEST['p']);
	if($id>0){
	$goods = M('Goods');
	$gtmodel = M('Goodtag');
	if(empty($originalid)){
    $tag = array();
	$arr = $_POST;

	$tagarr = array();

	$arr['gtype'] = $arr['gtype']?$arr['gtype']:'0';
	$arr['isud'] = $arr['isud']?$arr['isud']:'0';
	$arr['ccate'] =$arr['ccate']?$arr['ccate']:0;
	unset($_POST);
	$taglist = M('Tag')->field('id')->where(array('parent_id'=>0))->order('id asc')->select();
	//场合
	$carr = $arr['tag'.$taglist[0]['id']];
	$carrcount = count($carr);
	//风格
    $farr = $arr['tag'.$taglist[1]['id']];
	$farrcount = count($farr);
    
	//去除前端提交的重复指数
	$arrwid = array();

	foreach($arr['wid'] as $kw=>$vw){
	if(!empty($vw)){
    if(!in_array($vw,$arrwid)){
    $arrwid[$kw] = $vw;
	}
	}
	}

    foreach($arr['wid'] as $k=>$v){
	if($carrcount==0 && $farrcount==0){//如果只有温度
	//判断是否已经有数据了
    $tagresult = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id))->find();
	if(empty($tagresult)){
    $data = array('wid'=>$v,
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$arr['gtype'],
			            'isud'=>$arr['isud'],
			            'tag_id'=>0,
		                'ftag_id'=>0,
		                'ccateid'=>$arr['ccate'],
                        'stm'=>$arr['stp'][$k],
                        'etm'=>$arr['etp'][$k]);
	$gtmodel->add($data);
	}else{
	//判断当前指数下是否有多条数据
	$widresult = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id))->select();
    $widresult_count = count($widresult);
	if($widresult_count==1){
    $data = array('wid'=>$v,
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$arr['gtype'],
			            'isud'=>$arr['isud'],
			            'tag_id'=>0,
                        'ftag_id'=>0,
		                'ccateid'=>$arr['ccate'],
                        'stm'=>$arr['stp'][$k]?$arr['stp'][$k]:0,
                        'etm'=>$arr['etp'][$k]?$arr['etp'][$k]:0);
	$gtmodel->where(array('wid'=>$v,'good_id'=>$id))->save($data);
	}else{
    foreach($widresult as $kw=>$vw){
     if($kw>0){
     $gtmodel->where(array('id'=>$vw['id']))->delete();
	 }
	}
	}
	}
	}else{
	$tagresult = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id))->find();//判断当前指数是否存在
    if(empty($tagresult)){//如果不存在
    if($carrcount>=$farrcount){//如果场合打的标签比风格多
    foreach($carr as $k1=>$v1){
    $data = array('wid'=>$v,
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$arr['gtype'],
			            'isud'=>$arr['isud'],
			            'tag_id'=>$v1,//场合
	                    'ftag_id'=>$farr[$k1]?$farr[$k1]:0,
			            'ccateid'=>$arr['ccate'],
                        'stm'=>$arr['stp'][$k]?$arr['stp'][$k]:0,
                        'etm'=>$arr['etp'][$k]?$arr['etp'][$k]:0);
    $res = $gtmodel->add($data);
	}
	}else{//风格标签比场合多
    foreach($farr as $k1=>$v1){
    $data = array('wid'=>$v,
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$arr['gtype'],
			            'isud'=>$arr['isud'],
			            'tag_id'=>$carr[$k1]?$carr[$k1]:0,//场合
	                    'ftag_id'=>$v1,
			            'ccateid'=>$arr['ccate'],
                        'stm'=>$arr['stp'][$k]?$arr['stp'][$k]:0,
                        'etm'=>$arr['etp'][$k]?$arr['etp'][$k]:0);
    $gtmodel->add($data);
	}
	}
	}else{//如果存在
    if($carrcount>=$farrcount){//如果场合打的标签比风格多
	if(!empty($carr)){
	foreach($carr as $k1=>$v1){
	$tagre = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id,'tag_id'=>$v1))->find();
    if(empty($tagre)){
    $tagre = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id,'tag_id'=>0))->find();
	}
	if(empty($tagre)){
    $data = array('wid'=>$v,
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$arr['gtype'],
			            'isud'=>$arr['isud'],
			            'tag_id'=>$v1,//场合
			            'ccateid'=>$arr['ccate'],
                        'stm'=>$arr['stp'][$k]?$arr['stp'][$k]:0,
                        'etm'=>$arr['etp'][$k]?$arr['etp'][$k]:0);
    $res = $gtmodel->add($data);    
	}else{
     $data = array('wid'=>$v,
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$arr['gtype'],
			            'isud'=>$arr['isud'],
			            'tag_id'=>$v1,
			            'ccateid'=>$arr['ccate'],
                        'stm'=>$arr['stp'][$k]?$arr['stp'][$k]:0,
                        'etm'=>$arr['etp'][$k]?$arr['etp'][$k]:0);
     $res2 = $gtmodel->where(array('id'=>$tagre['id']))->save($data);
	 $res = !empty($tagre['id'])?$tagre['id']:$res;
	 $gtmodel->where(array('wid'=>$v,'good_id'=>$id))->save(array('ccateid'=>$arr['ccate']));
	}

	//判断风格是否存在
	if(!empty($farr[$k1])){
	$ftagre = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id,'ftag_id'=>$farr[$k1]))->find();
	if(empty($ftagre)){
     $freeftag = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id,'ftag_id'=>'0'))->find();
     $gtmodel->where(array('id'=>$freeftag['id']))->save(array('ftag_id'=>$farr[$k1]));
	}
    }
	}
	}
	}else{
    if(!empty($farr)){
    foreach($farr as $k1=>$v1){
	$tagre = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id,'ftag_id'=>$v1))->find();
    if(empty($tagre)){
    $tagre = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id,'ftag_id'=>0))->find();
	}
	if(empty($tagre)){
    $data = array('wid'=>$v,
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$arr['gtype'],
			            'isud'=>$arr['isud'],
	                    'ftag_id'=>$v1,
			            'ccateid'=>$arr['ccate'],
                        'stm'=>$arr['stp'][$k]?$arr['stp'][$k]:0,
                        'etm'=>$arr['etp'][$k]?$arr['etp'][$k]:0);
    $res = $gtmodel->add($data);
	}else{
     $data = array('wid'=>$v,
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$arr['gtype'],
			            'isud'=>$arr['isud'],
			            'ftag_id'=>$v1,
			            'ccateid'=>$arr['ccate'],
                        'stm'=>$arr['stp'][$k]?$arr['stp'][$k]:0,
                        'etm'=>$arr['etp'][$k]?$arr['etp'][$k]:0);
     $gtmodel->where(array('id'=>$tagre['id']))->save($data);
     $res = !empty($tagre['id'])?$tagre['id']:$res;
	 $gtmodel->where(array('wid'=>$v,'good_id'=>$id))->save(array('ccateid'=>$arr['ccate']));
	}
	//判断场合是否存在
	if(!empty($carr[$k1])){
	$ctagre = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id,'tag_id'=>$carr[$k1]))->find();
	if(empty($ctagre)){
	 $freetag = $gtmodel->field('id')->where(array('wid'=>$v,'good_id'=>$id,'tag_id'=>'0'))->find();
     $gtmodel->where(array('id'=>$freetag['id']))->save(array('tag_id'=>$carr[$k1]));
	}
    }
	}
	}
	}
	}
	//删除掉风格跟场合都为0的记录
    $gtmodel->where(array('good_id'=>$id,'tag_id'=>0,'ftag_id'=>0))->delete();
	}
	}

	//更新的时候删除去掉的标签
    $w_arr = $arr['wid'];
    $wlist = $gtmodel->field('id,wid')->where(array('good_id'=>$id))->group('wid')->select();
	if(!empty($w_arr)){
    $w_arr = array_flip($w_arr);
    foreach($wlist as $k=>$v){
    if(!array_key_exists($v['wid'],$w_arr)){
    $gtmodel->where(array('wid'=>$v['wid'],'good_id'=>$id))->delete();
	}
	}
    }

	$glist = $gtmodel->field('id,tag_id,ftag_id')->where(array('good_id'=>$id))->select();
	if(!empty($carr)){
    $carr = array_flip($carr);
    foreach($glist as $k=>$v){
    if(!array_key_exists($v['tag_id'],$carr)){
    $gtmodel->where(array('id'=>$v['id']))->save(array('tag_id'=>0));
	}
    }
    }else{
    foreach($glist as $k=>$v){
    $gtmodel->where(array('id'=>$v['id']))->save(array('tag_id'=>0));  
	}
	}

	if(!empty($farr)){
    $farr = array_flip($farr);
    foreach($glist as $k=>$v){
    if(!array_key_exists($v['ftag_id'],$farr)){
    $gtmodel->where(array('id'=>$v['id']))->save(array('ftag_id'=>0));
	}
    }
    }else{
    foreach($glist as $k=>$v){
    $gtmodel->where(array('id'=>$v['id']))->save(array('ftag_id'=>0));  
	}
	}
	//更新goods表数据
    $goodarr['type'] = $arr['gtype'];
	$goodarr['isud'] = $arr['isud'];
	if(!empty($arr['cy'])){
    $goodarr['isdoubt'] = $arr['cy'];
	}else{
    $goodarr['isdoubt'] = '1';
	}
    $goodarr['istag'] = '2';
	$goods->where(array('id'=>$id))->save($goodarr);

    $this->success('编辑成功',U('Productsedit/index',array('id'=>$id,'p'=>$_REQUEST['p'],'keyword'=>$keyword,'istag'=>$istag,'cate1'=>$cate1,'cate2'=>$cate2,'isdoubt'=>$isdoubt)));

	}else{//复制id的标签
     $olist = $gtmodel->field('*')->where(array('good_id'=>$originalid))->select();
	 $oresult = $goods->field('isdoubt')->where(array('id'=>$originalid))->find();
	 if(!empty($olist)){
     //取出实际商品的打标签情况
	 $ylist = $gtmodel->field('*')->where(array('good_id'=>$id))->select();
	 if(!empty($ylist)){
       $ocount = count($olist);
       $ycount = count($ylist);
	   if($ocount>=$ycount){
        foreach($olist as $k=>$v){
        if(!empty($ylist[$k])){
            $data = array('wid'=>$v['wid'],
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$v['gtype'],
			            'isud'=>$v['isud'],
			            'tag_id'=>$v['tag_id'],
				        'ftag_id'=>$v['ftag_id'],
				        'ccateid'=>$v['ccateid'],
                        'stm'=>$v['stm'],
                        'etm'=>$v['etm']);

          $gtmodel->where(array('id'=>$ylist[$k]['id']))->save($data);       
		}else{
          $data = array('wid'=>$v['wid'],
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$v['gtype'],
			            'isud'=>$v['isud'],
			            'tag_id'=>$v['tag_id'],
			            'ftag_id'=>$v['ftag_id'],
			            'ccateid'=>$v['ccateid'],
                        'stm'=>$v['stm'],
                        'etm'=>$v['etm']);
		  $gtmodel->add($data); 
		}
		}
	   }else{//原来的商品标签大于提交过来那个商品的
         foreach($ylist as $k=>$v){
          if(!empty($olist[$k])){
            $data = array('wid'=>$v['wid'],
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$olist[$k]['gtype'],
			            'isud'=>$olist[$k]['isud'],
			            'tag_id'=>$olist[$k]['tag_id'],
				        'ftag_id'=>$olist[$k]['ftag_id'],
				        'ccateid'=>$olist[$k]['ccateid'],
                        'stm'=>$olist[$k]['stp'],
                        'etm'=>$olist[$k]['etp']);
          $gtmodel->where(array('id'=>$v['id']))->save($data);  
		  }else{
           $gtmodel->where(array('id'=>$v['id']))->delete();
		  }
		 }
	   }
	 }else{
       foreach($olist as $k=>$v){
          $data = array('wid'=>$v['wid'],
			            'good_id'=>$id,
                        'num_iid'=>$num_iid,
                        'gtype'=>$v['gtype'],
			            'isud'=>$v['isud'],
			            'tag_id'=>$v['tag_id'],
			            'ftag_id'=>$v['ftag_id'],
			            'ccateid'=>$v['ccateid'],
                        'stm'=>$v['stm'],
                        'etm'=>$v['etm']);
		  $gtmodel->add($data);        
	   }
	 }
	$goodarr['type'] = $olist[0]['gtype'];
	$goodarr['isud'] = $olist[0]['isud'];
    $goodarr['istag'] = '2';
	$goodarr['isdoubt'] = $oresult['isdoubt'];
	$goods->where(array('id'=>$id))->save($goodarr);
	 $this->success('编辑成功',U('Productsedit/index',array('id'=>$id,'p'=>$_REQUEST['p'],'keyword'=>$keyword,'istag'=>$istag,'cate1'=>$cate1,'cate2'=>$cate2,'isdoubt'=>$isdoubt)));
	 }else{
      $this->error('所选商品还没有设定标签',U('Productsedit/index',array('id'=>$id,'p'=>$_REQUEST['p'],'keyword'=>$keyword,'istag'=>$istag,'cate1'=>$cate1,'cate2'=>$cate2,'isdoubt'=>$isdoubt)));
	 }
	}
	}else{
    $this->error('参数错误',U('Products/index'));
	}
    }else{
    $this->display('Login/index');
    }
}

public function ajaxt(){
    $selid = trim($this->_post('selid'));
	//温度
	$temarr = array();
	for($i=-10;$i<=40;$i++){
    $temarr[] = $i;
	}
	  switch($selid){
      case 1 :
	  $str1 = '<option value="">请选择</option>';
      $str2 = '<option value="">请选择</option>';
      foreach($temarr as $k=>$v){
	  if($v==29){
      $str1.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str1.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  if($v==40){
      $str2.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str2.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  }
	  break;
      case 2 :
	  $str1 = '<option value="">请选择</option>';
      $str2 = '<option value="">请选择</option>';
      foreach($temarr as $k=>$v){
	  if($v==24){
      $str1.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str1.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  if($v==28){
      $str2.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str2.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  }
	  break;
      case 3 :
	  $str1 = '<option value="">请选择</option>';
      $str2 = '<option value="">请选择</option>';
      foreach($temarr as $k=>$v){
	  if($v==20){
      $str1.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str1.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  if($v==23){
      $str2.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str2.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  }
	  break;
      case 4 :
	  $str1 = '<option value="">请选择</option>';
      $str2 = '<option value="">请选择</option>';
      foreach($temarr as $k=>$v){
	  if($v==15){
      $str1.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str1.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  if($v==19){
      $str2.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str2.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  }
	  break;
      case 5 :
	  $str1 = '<option value="">请选择</option>';
      $str2 = '<option value="">请选择</option>';
      foreach($temarr as $k=>$v){
	  if($v==11){
      $str1.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str1.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  if($v==14){
      $str2.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str2.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  }
	  break;
      case 6 :
	  $str1 = '<option value="">请选择</option>';
      $str2 = '<option value="">请选择</option>';
      foreach($temarr as $k=>$v){
	  if($v==6){
      $str1.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str1.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  if($v==10){
      $str2.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str2.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  }
	  break;
      case 7 :
	  $str1 = '<option value="">请选择</option>';
      $str2 = '<option value="">请选择</option>';
      foreach($temarr as $k=>$v){
	  if($v==-10){
      $str1.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str1.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  if($v==5){
      $str2.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str2.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  }
	  break;
      case 8 :
	  $str1 = '<option value="">请选择</option>';
      $str2 = '<option value="">请选择</option>';
      foreach($temarr as $k=>$v){
	  if($v==0){
      $str1.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str1.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  if($v==0){
      $str2.='<option value="'.$v.'" selected="selected">'.$v.'</option>';
	  }else{
      $str2.='<option value="'.$v.'">'.$v.'</option>';
	  }
	  }
	  break;
	  }
      $arr['stm1'] = $str1;
	  $arr['stm2'] = $str2;
	  echo json_encode($arr);
}

public function ajaxcate(){
     $gtype = trim($this->_post('gtype'));
	 $isud = trim($this->_post('isud'));
	 if(!empty($gtype) && !empty($isud)){
      $customcate = M('Customcate');
	  $list = $customcate->field('*')->where(array('gtype'=>$gtype,'isud'=>$isud))->select();
	  $str = '';
      switch($gtype){
	  	case 1 :
		if($isud==1){
		$str.="<span style='color:red;'>WOMEN-上装</span>";	
		}else if($isud==2){
		$str.="<span style='color:red;'>WOMEN-下装</span>";		
		}else if($isud==3){
		$str.="<span style='color:red;'>WOMEN-配饰</span>";	
		}else if($isud==4){
		$str.="<span style='color:red;'>WOMEN-套装</span>";	
		}else if($isud==5){
		$str.="<span style='color:red;'>WOMEN-内衣</span>";	
		}
		break;
	  	case 2 :
		if($isud==1){
		$str.="<span style='color:red;'>MAN-上装</span>";	
		}else if($isud==2){
		$str.="<span style='color:red;'>MAN-下装</span>";		
		}else if($isud==3){
		$str.="<span style='color:red;'>MAN-配饰</span>";	
		}else if($isud==4){
		$str.="<span style='color:red;'>MAN-套装</span>";	
		}else if($isud==5){
		$str.="<span style='color:red;'>MAN-内衣</span>";	
		}
		break;	
	  	case 3 :
		if($isud==1){
		$str.="<span style='color:red;'>KIDS-上装</span>";	
		}else if($isud==2){
		$str.="<span style='color:red;'>KIDS-下装</span>";		
		}else if($isud==3){
		$str.="<span style='color:red;'>KIDS-配饰</span>";	
		}else if($isud==4){
		$str.="<span style='color:red;'>KIDS-套装</span>";	
		}else if($isud==5){
		$str.="<span style='color:red;'>KIDS-内衣</span>";	
		}
		break;	
      }
	  if(!empty($list)){
	  foreach($list as $k=>$v){
      $str.='<input type="radio" name="ccate" value="'.$v['id'].'">'.$v['name'].'&nbsp;&nbsp;';
	  }
      $arr['flag'] = true;
	  $arr['str'] = $str;
	  }else{
      $arr['flag'] = false;
	  $arr['msg'] = '没有数据';
	  }
	 }else{
      $arr['flag'] = false;
	  $arr['msg'] = '参数错误';
	 }
	 echo json_encode($arr);
}
}