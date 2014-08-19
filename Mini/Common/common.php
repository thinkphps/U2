<?php
function getIPLoc_sina($queryIP)
{
    $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $queryIP;
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_ENCODING, 'utf8');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回 
    $location = curl_exec($ch);
    $location = json_decode($location);
    curl_close($ch);
    $loc = "";
    if ($location === FALSE)
        return "";
    if (empty($location->desc)) {
        //$loc      = $location->city;
        //$full_loc = $location->province . $location->city . $location->district . $location->isp;
        $arr['city'] = $location->city;
		$arr['province'] = $location->province;
    } else {
        //$loc = $location->desc;
		$arr['loc'] = $location->desc;;
    }
    return $arr;
}

function sms_send($corpID="2062249",$loginName="DENSON",$password="123456",$mobile,$msg){
	//$corpID = "2062249"; 
	//$loginName = "DENSON";//必需大写
	//$password = "123456";
	
	set_time_limit(0); // 设置自己服务器超时时
	
	$ch = curl_init();
	
	//第一步：取随机码
	$url = 'http://api.cosms.cn/sms/getMD5str/';		
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);	
	curl_setopt($ch,CURLOPT_COOKIEJAR,'/tmp/cookie');
	curl_setopt($ch,CURLOPT_POST,TRUE);
	curl_setopt($ch,CURLOPT_POSTFIELDS,'');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);		
	$md5str = curl_exec($ch);        
	//echo 'md5str:'.$md5str.'<br>';
	
	//第二步：企业编辑+登录名+密码  组合后MD5加密
	$str=$corpID.$loginName.$password;				
	$md5str2 = md5($str);
	
	//第三步：第二步与第一步内容组合后再进行MD5加密
	$str=$md5str2.$md5str;
	$pass = md5($str);		
	//echo 'pass:'.$pass.'<br>';		
	
	//第四步：发送
	//$content = iconv("utf-8","gb2312",$msg);
	$msg = mb_convert_encoding($msg, "gb2312", "utf-8");
	$fields_string = 'msgFormat=1&corpID='.$corpID.'&loginName='.$loginName.'&password='.$pass.'&Mobs='.$mobile.'&msg='.urlencode($msg).'&mtLevel=1&MD5str='.$md5str;
	$url = 'http://api.cosms.cn/sms/putMt/index.asp';
	//echo 'fields_string:'.$fields_string.'<br>';exit;
	
	curl_setopt($ch, CURLOPT_URL,$url) ;
	curl_setopt($ch, CURLOPT_POST,8) ;
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string) ; 

	$str= curl_exec($ch);
	//echo 'return_str:'.$str;
	return $str;

	curl_close($ch);
	
}

function randStr($len=6,$format='ALL'){
	switch($format){
		case 'ALL':
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
		break;
		case 'CHAR':
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
		break;
		case 'NUMBER':
			$chars='0123456789';
		break;
		case 'NUMBERCHAR':
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		break;
		default :
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~'; 
		break;
	}
	mt_srand((double)microtime()*1000000*getmypid()); 
	$password='';
	while(strlen($password)<$len){
		$password.=substr($chars,(mt_rand()%strlen($chars)),1);
	}
	return $password;
}
//判断是否是手机
function is_mobile() {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';;
    $mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel",
        "amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio",
        "au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu",
        "cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ",
        "fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi",
        "htc","huawei","hutchison","inno","ipad","ipaq","iphone","ipod","jbrowser","kddi",
        "kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9",
        "lg-","lge-","lge9","longcos","maemo",
        "mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-",
        "moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia",
        "nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-",
        "playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo",
        "samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank",
        "sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit",
        "tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin",
        "vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce",
        "wireless","xda","xde","zte");
    $is_mobile = false;
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
            break;
        }
    }
    if(strpos($user_agent,'ipad')){
        $is_mobile = false;
    }
    return $is_mobile;
}