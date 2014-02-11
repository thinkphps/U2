<?php
class TagsAction extends Action{
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
	 $list = M('Tag')->field('*')->where(array('parent_id'=>0))->select();
	 $this->assign('list',$list);
     $this->display();
	}else{
    $this->display('Login/index');
    }
}
public function add(){
	if(!empty($this->aid)){
	$ptag = $this->_post('ptag');
    if($ptag==0){
    	$this->error('一级标签没有选择',U('Tags/index'));
		exit;
    }
	$typename = $this->_post('typename');
	$tagname = trim($this->_post('tagname'));
	if(!empty($tagname)){
		$data = array('name'=>$tagname,
		              'parent_id'=>$ptag,
					  'type'=>$typename,
					  'createtime'=>date('Y-m-d H:i:s'),
					  '__hash__'=>$_POST['__hash__']);
	  $tag = M('Tag');
	  $tag->create($data);
      $res = $tag->add();
	  if($res){
	  	$this->success('添加成功',U('Tags/index'));
		  exit;
	  }else{
	  	$this->error('添加失败',U('Tags/index'));
		exit;
	  }
	}else{
		$this->error('标签名不对',U('Tags/index'));
		exit;
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
		$gtag = M('Goodtag');
		$good = M('Goods');
		$tag = M('Tag');
		$time = date('Y-m-d H:i:s');
        while($data = fgetcsv($handle)){
	     if($i>0){
	     $gresult = $good->field('id,type')->where(array('num_iid'=>$data[0]))->find();
			 //有这个商品
		 if(!empty($gresult)){
		 $tagcount = count($data);
		 //标签表里没有
		 foreach($data as $k=>$v){
		 	if($k>=4){
		 	$v = trim($v);
		 	$tagresult = $tag->field('id,parent_id')->where(array('name'=>array('like','%'.$v.'%'),'type'=>trim($data['3'])))->find();
			$result = $gtag->field('id,tag_id')->where(array('good_id'=>$gresult['id'],'tag_id'=>$tagresult['id']))->select();
			if(empty($result)){
			$arr = array('good_id'=>$gresult['id'],
			              'stm'=>$data[1],
						  'etm'=>$data[2],
						  'gtype'=>$gresult['type'],
						  'tag_id'=>$tagresult['id'],
						  'parent_id'=>$tagresult['parent_id']);
			$gtag->add($arr);
			}
		 	}
		 }
		 }
	     }
		 $i++;
        }
		$this->sucess('导入完成',U('Products/index'));
        }else{
        	$this->error('文件格式必须是csv格式',U('Tags/index'));
			exit;
        }
		}else{
    $this->display('Login/index');
    }
}
}