<?php
class AdminModel extends Model{
	// 获取管理员信息
	public function getuser($arr){
		$admin = M('Admin');
		$result = $admin->field('*')->where(array('email'=>$arr['email'],'pwd'=>$arr['pwd']))->find();
		if(!empty($result)){
		return $result;
		}else{
			return false;
		}
	}
}
