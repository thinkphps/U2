<?php
class TagModel extends Model{
public function gettagid($name){
	$tag = M('Tag');
	$list = $tag->field('id,type')->where(array('name'=>$name))->select();
	$str = '';
	foreach($list as $k=>$v){
	if($v['type']=='1'){
	$fir = $v['id'];	
	}
	if($v['type']=='2'){
	$sec = $v['id'];	
	}
    if($v['type']=='3'){
	$th = $v['id'];
	}
	}
	$str = $fir.'_'.$sec.'_'.$th;
	return $str;
}
}
