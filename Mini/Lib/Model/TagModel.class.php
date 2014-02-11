<?php
class TagModel extends Model{
public function gettagid($name,$parent_id){
	$tag = M('Tag');
	$list = $tag->cache(true)->field('id,type')->where(array('name'=>$name,'parent_id'=>$parent_id))->select();
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
