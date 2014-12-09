<?php
class LeaAction extends Action{
	public function addlea(){
    $ip = get_client_ip();
    $catid = $this->_post('cate');
	$content = trim($this->_post('con'));
	$data = array('catid'=>$catid,
	              'content'=>$content,
				  'ip'=>$ip,
				  'createtime'=>date('Y-m-d H:i:s'));
    $res = M('Leave')->add($data);
	if($res){
	echo '感谢您的反馈';	
	}else{
	echo '添加失败';	
	}
	}
    public function upShowTag(){
        $uid = session("uniq_user_id");
        $flag = trim($this->_post('flag'));
        if($uid>0){
            if($flag==1){
                M('User')->where(array('id'=>$uid))->save(array('type'=>array('exp','type+1')));
            }else{
               M('User')->where(array('id'=>$uid))->save(array('showtag'=>array('exp','showtag+1')));
            }
        }
    }
    /*
    添加试衣日志
     */
    public function addLog(){
        $uq = trim($this->_post('sq'));
        if($uq){
            $key = 'fitting';
            import("@.ORG.Reds");
            $redis = new Reds();
            $rdValue = $redis->get($key);
            $result = unserialize($rdValue);
            $uq = rtrim($uq,',');
            $root_dir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
            $mac = D('Macapp');
            $ip = get_client_ip();
            $uid = session("uniq_user_id");
            $strotime = time();
            $time = date('Y-m-d H:i:s',$strotime);
            $sid = session_id();
            $str = $time.'_'.$uq.'_'.$ip.'_'.$uid.'_'.$sid."\n";
            $hour = date('H',$strotime);
            if(count($result)<1000){   //小于1000条放入redis
                $result[] = $str;
                $redis->set($key,serialize($result));
            }else{
            $fileArr = $mac->createdir($hour,$root_dir.'/Upload/log/fitting/','/Upload/log/fitting/','1.txt',2);
            $fp = fopen($fileArr[0],'a');
            foreach($result as $k=>$v){
                if(filesize($fileArr[0])<10485760){
                    fwrite($fp,$v);
                }else{
                    $fileArr = $mac->createdir($hour.'_'.$hour,$root_dir.'/Upload/log/fitting/','/Upload/log/fitting/','1.txt',2);
                    fwrite($fp,$v);
                }
            }
            fclose($fp);
            $redis->delete($key);
        }
        }
    }
}