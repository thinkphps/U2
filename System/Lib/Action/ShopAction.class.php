<?php
class ShopAction extends Action{
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
     $area = M('Areas');
    $keyword = trim($this->_request('keyword'));
    $pid = $this->_request('pid');
    $cid = $this->_request('cid');
    $aid = $this->_request('aid');
	$shop = M('Shop');
    $pagestr = '';
	import("@.ORG.Pageyu");
    $where = array();
    if(!empty($pid)){
    $where['pid'] = $pid;
    $clist = $area->field('region_id,local_name')->where(array('p_region_id'=>$pid))->select();
    $pagestr.= '/pid/'.$pid;
    }
    if(!empty($cid)){
    $where['cityid'] = $cid;
    $alist = $area->field('region_id,local_name')->where(array('p_region_id'=>$cid))->select();
    $pagestr.= '/cid/'.$cid;
    }
    if(!empty($aid)){
    $where['aid'] = $aid;
    $pagestr.= '/aid/'.$aid;
    }
	if(!empty($keyword)){
    $where['sname'] = array('like','%'.$keyword.'%');
	}
	$count = $shop->where($where)->count();
	$p = new Page($count,20,$pagestr);
	$shops = $shop->field('*')->where($where)->order('id desc')->limit($p->firstRows.','.$p->maxRows)->select();
	$page = $p->showPage();
    $plist = $area->field('region_id,local_name')->where(array('p_region_id'=>array('exp','IS NULL')))->select();
	$this->assign('shop',$shops);
	$this->assign('page',$page);
    $this->assign('plist',$plist);
    $this->assign('clist',$clist);
    $this->assign('alist',$alist);
    $this->assign('pid',$pid);
    $this->assign('cid',$cid);
    $this->assign('aid',$aid);
	$this->display();	
    }else{
    $this->display('Login/index');
    }	
	}
	
	public function create(){
	if(!empty($this->aid)){
	$area = M('Areas');
	$plist = $area->field('*')->where(array('p_region_id'=>array('exp','IS NULL')))->select();
	$this->assign('plist',$plist);
	$this->display();	
    }else{
    $this->display('Login/index');
    }		
	}
    public function add(){
 	if(!empty($this->aid)){
 	$id = $this->_post('id');	
	$sname = trim($this->_post('sname'));
	if(empty($sname)){
	$this->error('名称不能为空',U('Shop/create'));
		exit;
	}
	$address = trim($this->_post('address'));
	if(empty($address)){
	$this->error('地址不能为空',U('Shop/create'));
		exit;
	}
	$stime = trim($_POST['stime']);
	if(empty($stime)){
	$this->error('营业时间不能为空',U('Shop/create'));
		exit;
	}
	$call = trim($this->_post('call'));
	if(empty($call)){
	$this->error('电话不能为空',U('Shop/create'));
		exit;
	}
	$range = trim($this->_post('range'));
	if(empty($range)){
	$this->error('商品范围不能为空',U('Shop/create'));
		exit;
	}
	//20140328kimi
	$pid = $this->_post('pid');
	if($pid<=0){
    $this->error('省没有选',U('Shop/create'));
	exit;
	}
	$cid = $this->_post('cid');
	if($cid<=0){
    $this->error('城市没有选择',U('Shop/create'));
	exit;
	}
	$aid = $this->_post('aid');
	$aid = !empty($aid)?$aid:0;

	$longitude = trim($this->_post('longitude'));
	if(empty($longitude)){
    $this->error('经度没有填',U('Shop/create'));
	exit;
	}
	$latitude = trim($this->_post('latitude'));
	if(empty($latitude)){
    $this->error('纬度不能为空',U('Shop/create'));
	exit;
	}
	$showtag = intval(trim($this->_post('showtag')));
    $message = trim($this->_post('message'));
    $store_id = $this->_post('store_id');
    $fla = $this->_post('fla');
	//20140328kimi
	$time = date('Y-m-d H:i:s');
	$shop = M('Shop');
	if(!empty($id)){
	$data = array('pid'=>$pid?$pid:0,
		          'cityid'=>$cid?$cid:0,
		          'aid'=>$aid?$aid:0,
                  'store_id'=>$store_id,
		          'longitude'=>$longitude,
		          'latitude'=>$latitude,
		          'showtag'=>$showtag,
		          'sname'=>$sname,
	              'saddress'=>$address,
				  'tradetime'=>$stime,
				  'scall'=>$call,
				  'sange'=>$range,
		          'message'=>$message,
                  'flag'=>$fla,
				  'uptime'=>$time);
    $res = $shop->where(array('id'=>$id))->save($data);		
	}else{
	$data = array('pid'=>$pid?$pid:0,
		          'cityid'=>$cid?$cid:0,
		          'aid'=>$aid?$aid:0,
                  'store_id'=>$store_id,
		          'longitude'=>$longitude,
		          'latitude'=>$latitude,
		          'showtag'=>$showtag,
		          'sname'=>$sname,
	              'saddress'=>$address,
				  'tradetime'=>$stime,
				  'scall'=>$call,
				  'sange'=>$range,
		          'message'=>$message,
                  'flag'=>$fla,
				  'createtime'=>$time,
				  'uptime'=>$time,
				  '__hash__'=>$_POST['__hash__']);
	  $shop->create($data);
      $res = $shop->add();
	  }
	  if($res){
	  	$this->success('提交成功',U('Shop/index'));
		  exit;
	  }else{
	  	$this->error('提交失败',U('Shop/create'));
		exit;
	  }	
    }else{
    $this->display('Login/index');
    }   	
    }
  public function shopedit(){
  	if(!empty($this->aid)){
  	$id = trim($this->_get('id'));
	if($id>0){
	$shop = M('Shop');
	$area = M('Areas');
	$shops = $shop->field('*')->where(array('id'=>$id))->find();
	$result = $area->field('*')->where(array('p_region_id'=>array('exp','IS NULL')))->select();
	$city = $area->field('*')->where(array('region_id'=>$shops['cityid']))->select();
	$area = $area->field('*')->where(array('region_id'=>$shops['aid']))->select();
	   //取出所属城市显示标记的最大值
	$logmax = M('Shop')->field('showtag')->where(array('cityid'=>$shops['cityid']))->order('showtag desc')->limit('0,1')->find();
	$this->assign('result',$result);
	$this->assign('logmax',$logmax);
    $this->assign('city',$city);
	$this->assign('area',$area);
	$this->assign('shop',$shops);	
	$this->display('create');	
	}else{
	$this->error('参数错误',U('Shop/index'));
	}
    }else{
    $this->display('Login/index');
    }
  }
  public function del(){
  	if(!empty($this->aid)){
  	$id = trim($this->_get('id'));
	if($id>0){
	$res = M('Shop')->where(array('id'=>$id))->delete();
	if($res){
	  	$this->success('删除成功',U('Shop/index'));
		  exit;
	  }else{
	  	$this->error('删除失败',U('Shop/index'));
		exit;
	  }
	}else{
	$this->error('参数错误',U('Shop/index'));
	}		 		
    }else{
    $this->display('Login/index');
    }
  }

 public function getcity(){
     if(!empty($this->aid)){
	  $pid = $this->_post('pid');//省id
	  $area = M('Areas');
      $list = $area->field('region_id,local_name')->where(array('p_region_id'=>$pid))->select();
	  if(!empty($list)){
 	   $str = "<select name='cid' id='cname'><option value='0'>请选择</option>";
       foreach($list as $k=>$v){
		$str.="<option value='".$v['region_id']."'>".$v['local_name']."</option>";   
       }
	   $str.="</select>";
	   unset($list);
      $returnArr = array('code'=>1,'data'=>$str);
	  }else{
       $returnArr = array('code'=>0,'msg'=>'没有数据');
	  }
	 }else{
     $returnArr = array('code'=>0,'msg'=>'没有登录');
	 }
     $this->ajaxReturn($returnArr,'json');
 }

 public function getarea(){
     if(!empty($this->aid)){
	  $cid = $this->_post('cid');//省id
	  $area = M('Areas');
      $list = $area->field('region_id,local_name')->where(array('p_region_id'=>$cid))->select();
	  if(!empty($list)){
 	   $str = "<select name='aid'><option value='0'>请选择</option>"; 
       foreach($list as $k=>$v){
		$str.="<option value='".$v['region_id']."'>".$v['local_name']."</option>";   
       }
	   $str.="</select>";
	   //取出所属城市显示标记的最大值
	   $result = M('Shop')->field('showtag')->where(array('cityid'=>$cid))->order('showtag desc')->limit('0,1')->find();
	   unset($list);
      $returnArr = array('code'=>1,'data'=>$str,'showtag'=>$result['showtag']);
	  }else{
       $returnArr = array('code'=>0,'msg'=>'没有数据');
	  }
	 }else{
     $returnArr = array('code'=>0,'msg'=>'没有登录');
	 }
     $this->ajaxReturn($returnArr,'json');
 }
 public function export(){
		if(!empty($this->aid)){
	   $file = $_FILES['Filedata'];
		$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        //设置上传文件类型
        $allowExts = array('csv');
		$root_dir = realpath(dirname(dirname(__FILE__)));
		if(in_array($extension,$allowExts)){
		$handle = fopen($file['tmp_name'],'r');
		$i = 0;
		$tag = M('Goodtag');
		$good = M('Goods');
		$time = date('Y-m-d H:i:s');
        while($data = fgetcsv($handle)){
	     if($i>0){
	     //插入主分类
	     $data = array('sname'=>trim($data[0]),
			           'saddress'=>trim($data[1]),
			           'tradetime'=>trim($data[2]),
			           'scall'=>$data[3],
			           'sange'=>$data[4],
			           'pname'=>$data[5],
			           'cname'=>$data[6],
			           'aname'=>$data[7],
			           'createtime'=>$time,
				       'uptime'=>$time,);
		 $res = $tag->add($data);
	     }
		 $i++;
        }
        }else{
        	$this->error('文件格式必须是csv格式',U('Shop/index'));
			exit;
        }
		}else{
    $this->display('Login/index');
    }
    }
}