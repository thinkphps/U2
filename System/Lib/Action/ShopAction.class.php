<?php
class ShopAction extends Action{
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
    $keyword = trim($this->_post('keyword'));
	$shop = M('Shop');
	import("@.ORG.Pageyu");
	if(empty($keyword)){
	$count = $shop->count();
	$p = new Page($count,8);
	$shops = $shop->field('*')->order('id desc')->limit($p->firstRows.','.$p->maxRows)->select();
	}else{
	$where = array('sname'=>array('like','%'.$keyword.'%'));
	$count = $shop->where($where)->count();
	$p = new Page($count,8);
	$shops = $shop->field('*')->where($where)->order('id desc')->limit($p->firstRows.','.$p->maxRows)->select();		
	}
	$page = $p->showPage();
	$this->assign('shop',$shops);
	$this->assign('page',$page);
	$this->display();	
    }else{
    $this->display('Login/index');
    }	
	}
	
	public function create(){
	if(!empty($this->aid)){
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
	$stime = trim($this->_post('stime'));
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
	$time = date('Y-m-d H:i:s');
	$shop = M('Shop');
	if(!empty($id)){
	$data = array('sname'=>$sname,
	              'saddress'=>$address,
				  'tradetime'=>$stime,
				  'scall'=>$call,
				  'sange'=>$range,
				  'uptime'=>$time);
    $res = $shop->where(array('id'=>$id))->save($data);		
	}else{
	$data = array('sname'=>$sname,
	              'saddress'=>$address,
				  'tradetime'=>$stime,
				  'scall'=>$call,
				  'sange'=>$range,
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
	$shops = $shop->field('*')->where(array('id'=>$id))->find();
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
