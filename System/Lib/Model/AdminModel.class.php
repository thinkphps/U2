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

    public function getSexStyle($sid){
        $list = M('SettingsGenderStyle')->join('inner join u_settings_suit_style as usss on usss.ID=u_settings_gender_style.styleID')->field('usss.ID,usss.description')->where(array('u_settings_gender_style.genderID'=>$sid))->select();
        return  $list;
    }
    public function getSellCate($num_iid){
        $sql = "select s.shortName from `u_catesgoods` as c inner join `u_sellercats` as s on s.ID=c.cateID where c.num_iid={$num_iid}";
        return M('Sellercats')->query($sql);
    }
}
