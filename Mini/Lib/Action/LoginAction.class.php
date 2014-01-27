<?php
class LoginAction extends Action{
	
	function login(){
		$user_name	 = isset($_POST['user_name']) && !empty($_POST['user_name']) ? $_POST['user_name'] : '' ;
		$password    = isset($_POST['password']) && !empty($_POST['password']) ? md5($_POST['password']) : '' ;
		$login    = isset($_POST['is_remember_login']) && !empty($_POST['is_remember_login']) ? md5($_POST['is_remember_login']) : 0 ;
		$where_str = " ( taobao_name = '{$user_name}' OR mobile = '{$user_name}' ) AND password = '{$password}' ";
		$user = M('user')->where($where_str)->find();
		if($user){
			if(!empty($user['mobile'])){
				$user_name = $user['mobile'];
			}
			if(!empty($user['taobao_name'])){
				$user_name = $user['taobao_name'];
			}
			session("uniq_user_name",$user_name);
			session("uniq_user_id",$user['id']);
			if($login){
				cookie('uniq_user_name',$user_name,604800);
				cookie('uniq_user_id',$user['id'],604800);
			}
			if($user['is_active']){
				M('Collection')->where(array('uid'=>session("uniq_user_id")))->save(array('is_delete'=>0));
			}else{
				M('Collection')->where(array('uid'=>session("uniq_user_id")))->delete();
			}
			$login_arr = array('code'=>1,'msg'=>'登录成功！');
		}else{
			$login_arr = array('code'=>-1,'msg'=>'请输入正确的淘宝登录名或密码');
		}
		$this->ajaxReturn($login_arr,'JSON');
	}

	function register(){
		$user_name	 = isset($_POST['user_name']) && !empty($_POST['user_name']) ? $_POST['user_name'] : '' ;
		$mobile      = isset($_POST['mobile']) && !empty($_POST['mobile']) ? $_POST['mobile'] : '' ;
		$taobao_name = isset($_POST['taobao_name']) && !empty($_POST['taobao_name']) ? $_POST['taobao_name'] : '' ;
		$password    = isset($_POST['password']) && !empty($_POST['password']) ? $_POST['password'] : '' ;
		$type		 = isset($_POST['type']) && !empty($_POST['type']) ? $_POST['type'] : 0 ;
		$verifying_code = isset($_POST['verifying_code']) && !empty($_POST['verifying_code']) ? $_POST['verifying_code'] : 0 ;
		$isChecked = isset($_POST['isChecked']) && !empty($_POST['isChecked']) ? $_POST['isChecked'] : 0 ;
		$is_active = 0 ;
		$mobileCode = session("mobileCode");
		if($isChecked){
			if($mobileCode && $verifying_code){
				if($mobileCode==$verifying_code){
					$is_active = 1 ;
				}else{
					$login_arr = array('code'=>-1,'msg'=>'验证码错误');
					$this->ajaxReturn($login_arr,'JSON');
				}
			}
			if($verifying_code && empty($mobileCode)){
				$login_arr = array('code'=>-1,'msg'=>'验证码错误');
				$this->ajaxReturn($login_arr,'JSON');
			}
		}
		$data = array(
				  'user_name'  =>	$user_name,
	              'mobile'	   =>	$mobile,
				  'taobao_name'=>	$taobao_name,
				  'password'   =>	md5($password),
				  'type'	   =>	$type,
				  'createtime' =>	date('Y-m-d H:i:s'),
				  'ip'		   =>	get_client_ip(),
				  'is_active'  =>	$is_active
				);
		$res = M('user')->add($data);
		if($res){
			if($mobile){
				session("is_active",$user_name);
			}
			if($mobile){
				$user_name = $mobile;
			}
			if($taobao_name){
				$user_name = $taobao_name;
			}
			session("uniq_user_name",$user_name);
			session("uniq_user_id",$res);
			$login_arr = array('code'=>1,'msg'=>'注册成功');
		}else{
			$login_arr = array('code'=>-1,'msg'=>'注册失败');
		}
		$this->ajaxReturn($login_arr,'JSON');
	}

	function forget_pwd(){
		$user_name	 = isset($_POST['user_name']) && !empty($_POST['user_name']) ? $_POST['user_name'] : '' ;
		$mobile      = isset($_POST['mobile']) && !empty($_POST['mobile']) ? $_POST['mobile'] : '' ;
		//$where_str = " taobao_name = '{$user_name}' AND mobile = '{$mobile}' ";
		$where_str = " mobile = '{$mobile}' ";
		$mobileRow = M('user')->where("mobile = '{$mobile}'")->find();
		if($mobileRow){
			if(session('verify') != md5($_POST['verify'])) {
				$login_arr = array('code'=>-2,'msg'=>'请填写正确的验证码');
			}else{
				$user = M('user')->where($where_str)->find();
				if($user){
					//$where_phone_str = " taobao_name = '{$user_name}' AND mobile = '{$mobile}' ";
					$where_phone_str = " mobile = '{$mobile}' ";
					$row = M('user')->where($where_phone_str)->find();
					if($row){
						//调用手机短信接口
						$new_passwrod = randStr(6,'NUMBER');
						$msg="我们为您重置了密码，您的新密码为：{$new_passwrod}【优衣柜】";
						$sms_str = sms_send('2062343','66801','66801',$mobile,$msg);
						if($sms_str){
							$data['password'] = md5($new_passwrod);
							$flag = M('user')->where("mobile='{$mobile}'")->save($data);
							if($flag){
								$login_arr = array('code'=>1,'msg'=>'');	
							}else{
								$login_arr = array('code'=>-2,'msg'=>'密码找回失败');	
							}
						}else{
							$login_arr = array('code'=>-2,'msg'=>'对不起，短信发送失败');			
						}
					}else{
						//$login_arr = array('code'=>-2,'msg'=>'请输入正确的淘宝登录名或手机号码');	
						$login_arr = array('code'=>-1,'msg'=>'请输入正确的手机号码');
					}
				}else{
					//$login_arr = array('code'=>-1,'msg'=>'请输入正确的淘宝登录名或手机号码');
					$login_arr = array('code'=>-1,'msg'=>'请输入正确的手机号码');
				}
			}
		}else{
			//$login_arr = array('code'=>-2,'msg'=>'对不起，该手机号没有关联记录');
			$login_arr = array('code'=>-2,'msg'=>'请输入正确的手机号码');
		}
		$this->ajaxReturn($login_arr,'JSON');
	}

	function active_phone(){
		$mobile      = isset($_POST['mobile']) && !empty($_POST['mobile']) ? $_POST['mobile'] : '' ;
		$mobileCode = randStr(4,'NUMBER');
		$msg="您的验证码为：{$mobileCode}，请登录优衣柜网站验证您的手机号码【优衣柜】";
		$sms_str = sms_send('2062343','66801','66801',$mobile,$msg);
		if($sms_str){
			session("mobileCode",$mobileCode);
			$returnArr = array('code'=>1,'msg'=>'');
		}else{
			$returnArr = array('code'=>-1,'msg'=>'验证码发送失败');			
		}
		$this->ajaxReturn($returnArr,'JSON');
	}
	
	function activate_succ(){
		$verCode      = isset($_POST['verCode']) && !empty($_POST['verCode']) ? $_POST['verCode'] : '' ;
		$mobileCode = session("mobileCode");
		$user_id = session("uniq_user_id");
		if($mobileCode && $verCode){
			if($mobileCode==$verCode){
				$flag = M('user')->where("id = {$user_id}")->save(array('is_active'=>1));
				if($flag){
					$login_arr = array('code'=>1,'msg'=>'');
				}
			}else{
				$login_arr = array('code'=>-1,'msg'=>'');
			}
		}else{
			$login_arr = array('code'=>-1,'msg'=>'');
		}
		$this->ajaxReturn($login_arr,'JSON');
	}


	function change_pwd(){
		$old_password	 = isset($_POST['old_password']) && !empty($_POST['old_password']) ? md5($_POST['old_password']) : '' ;
		$new_password    = isset($_POST['new_password']) && !empty($_POST['new_password']) ? $_POST['new_password'] : '' ;
		$user_name = session("uniq_user_name");
		$where_str = " ( taobao_name = '{$user_name}' OR mobile = '{$user_name}' ) AND password = '{$old_password}' ";
		$user = M('user')->where($where_str)->find();
		if($user){
			$data['password'] = md5($new_password);
			$flag = M('user')->where("taobao_name='{$user_name}' OR mobile = '{$user_name}' ")->save($data);
			if($flag){
				$returnArr = array('code'=>1,'msg'=>'');
			}else{
				$returnArr = array('code'=>-1,'msg'=>'请输入正确的旧密码');
			}
		}else{
			$returnArr = array('code'=>-1,'msg'=>'请输入正确的旧密码');
		}
		$this->ajaxReturn($returnArr,'JSON');
	}

	function active(){
		$user_name	 = isset($_POST['user_name']) && !empty($_POST['user_name']) ? $_POST['user_name'] : '' ;
		$mobile	 = isset($_POST['mobile']) && !empty($_POST['mobile']) ? $_POST['mobile'] : '' ;
		$mobileRow = M('user')->where("mobile = '{$mobile}'")->find();
		if($mobileRow){
			$returnArr = array('code'=>-1,'msg'=>'该手机号码已被注册');
		}else{
			$flag = M('user')->where("taobao_name='{$user_name}'")->save(array('mobile'=>$mobile));
			if($flag){
				$returnArr = array('code'=>1,'msg'=>'');
			}else{
				$returnArr = array('code'=>-1,'msg'=>'更新失败');
			}
		}
		$this->ajaxReturn($returnArr,'JSON');
	}

	function relate(){
		$mobile	 = isset($_POST['mobile']) && !empty($_POST['mobile']) ? $_POST['mobile'] : '' ;
		$mobileRow = M('user')->where("mobile = '{$mobile}'")->find();
		if($mobileRow['is_active']){
			$returnArr = array('code'=>1,'msg'=>'');
		}else{
			$returnArr = array('code'=>-1,'msg'=>'');
		}
		$this->ajaxReturn($returnArr,'JSON');
	}

	function check_user_exists(){
		$user_name	 = isset($_POST['user_name']) && !empty($_POST['user_name']) ? $_POST['user_name'] : '' ;
		$where_str = " taobao_name = '{$user_name}' OR mobile = '{$user_name}' ";
		$user = M('user')->where($where_str)->find();
		if($user){
			$login_arr = array('code'=>-1,'msg'=>'该淘宝登录名已被注册');
		}else{
			$login_arr = array('code'=>1,'msg'=>'');
		}
		$this->ajaxReturn($login_arr,'JSON');
	}

	function check_mobile_exists(){
		$mobile	 = isset($_POST['mobile']) && !empty($_POST['mobile']) ? $_POST['mobile'] : '' ;
		$where_str = " mobile = '{$mobile}' ";
		$user = M('user')->where($where_str)->find();
		if($user){
			$login_arr = array('code'=>-1,'msg'=>'该手机号码已被注册');
		}else{
			$login_arr = array('code'=>1,'msg'=>'');
		}
		$this->ajaxReturn($login_arr,'JSON');
	}

	function check_user_mobile_exists(){
		$user_name	 = isset($_POST['user_name']) && !empty($_POST['user_name']) ? $_POST['user_name'] : '' ;
		$mobile	 = isset($_POST['mobile']) && !empty($_POST['mobile']) ? $_POST['mobile'] : '' ;
		$where_str = " taobao_name = '{$user_name}' AND mobile = '{$mobile}' ";
		$user = M('user')->where($where_str)->find();
		if($user){
			$login_arr = array('code'=>1,'msg'=>'');
		}else{
			$login_arr = array('code'=>-1,'msg'=>'对不起，该手机号没有关联记录');
		}
		$this->ajaxReturn($login_arr,'JSON');
	}

	Public function verify(){
        import('ORG.Util.Image');
        Image::buildImageVerify();
    }

}
?>