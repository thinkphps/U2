<?php
require_once('init.php');
require_once('TopSdk.php');
set_time_limit(0);
$db = new DB();
cleartable($db);
$weathercity = getcityinfo($db);
$channelinfo =  getchannelinfo($db);

$weatherlist = array();
$maxcommitcount = 100;
foreach($weathercity as $city){
    $jsonobj = json_decode("[".$city["channelcity"]."]");
    $commoncityid = $city["commoncityid"];
    foreach($jsonobj as $channel){
        if($channel->channelcity !=""){

            if(checkchannel($channel->channelid,$channelinfo) == false){
                continue;
            }
            switch ($channel->channelid)
            {
                case 1:
                    $ret = getweatherapi($commoncityid,$channel);
                    if(checkemptyweather($ret)==true){
                        array_push($weatherlist,$ret);
                    }
                    break;
                case 2:
                    $ret = getsinaapi($commoncityid,$channel);
                    if(checkemptyweather($ret)==true){
                        array_push($weatherlist,$ret);
                    }
                    break;
                case 3:
                    $ret = getbaiduapi($commoncityid,$channel);
                    if(checkemptyweather($ret)==true){
                        array_push($weatherlist,$ret);
                    }
                    break;
                case 4:
                    $ret = getyahooapi($commoncityid,$channel);
                    if(checkemptyweather($ret)==true){
                        array_push($weatherlist,$ret);
                    }
                    break;
                case 5:
                    $ret = get360api($commoncityid,$channel);
                    if(checkemptyweather($ret)==true){
                        array_push($weatherlist,$ret);
                    }
                    break;
                case 6:
                    $ret = getmsnapi($commoncityid,$channel);
                    if(checkemptyweather($ret)==true){
                        array_push($weatherlist,$ret);
                    }
                    break;
                case 7:
//                echo "getsogoapi($commoncityid,$channel)";
                    break;
                default:
                    echo "invalid channel";
            }
        }
    }
    if (count($weatherlist)>=$maxcommitcount){
        updateweather($weatherlist,$db);
        $weatherlist = array();
    }

}
if (count($weatherlist)>0){
    updateweather($weatherlist,$db);
}
mergeweather($db);

unset($weathercity);
unset($weatherlist);
//$db->close();
//unset($db);

//json,chinese
function getweatherapi($commoncityid,$channel){
    $ret = array("cityid"=>$commoncityid,"channelid"=>$channel->channelid,"updatetime"=>"1000-01-01 00:00:00","weather1"=>"",
        "weather2"=>"","weather3"=>"","weather4"=>"","weather5"=>"","weather6"=>"");
    $url = "http://m.weather.com.cn/data/$channel->channelcity.html";
    try{
        $weather = myclient($url,true);
        if(empty($weather)){
            return $ret;
        }
        $weather = json_decode($weather);
        if(!empty($weather)){
            $updatetime = $weather->weatherinfo->date_y;
            if(!empty($updatetime)){
                $updatetime = str_replace(array("年","月"),"-",$updatetime);
                $ld = new DateTime(str_replace("日","",$updatetime)." 00:00:00");
                $ret["updatetime"] =$ld->format("Y-m-d H:i:s");
            }
            $j = 0;
            for($i=1;$i<=6;$i++){
                $tempindex = "temp$i";
                $weatherindex = "weather$i";
                if(array_key_exists($tempindex,$weather->weatherinfo)){
                    $templist = explode("~",$weather->weatherinfo->$tempindex);
                    if(!empty($templist)){
                        $dayindex = "img_title".($i+$j);
                        $nightindex = "img_title".($i+$j+1);
                        $htemp = $templist[0];
                        $ltemp = $htemp;
                        if(count($templist)==2){
                            $ltemp = $templist[1];
                        }
                        $dw = fixweatherimg($weather->weatherinfo->$dayindex);
                        $nw = fixweatherimg($weather->weatherinfo->$nightindex);
                        $wt = $weather->weatherinfo->$weatherindex;

                        $jsonobj = array('ht'=>gettempnumber($htemp),
                            'lt'=>gettempnumber($ltemp),
                            'wt'=>$wt,
                            'dw'=>$dw["descp"],
                            'nw'=>$nw["descp"],
                            'di'=>$dw["img"],
                            'ni'=>$nw["img"]);
                        $ret["weather$i"] =myjsonencode($jsonobj);
                    }
                }
                $j = 1;
            }
        }
        unset($weather);
        unset($jsonobj);
    }
    catch(Exception $e){
        unset($weather);
        unset($jsonobj);
    }
    return $ret;
}

//xml,chinese
function getsinaapi($commoncityid,$channel){
    //$tplurl = "http://php.weather.sina.com.cn/iframe/index/w_cl.php?code=js&day=4&city=";
    $ret = array("cityid"=>$commoncityid,"channelid"=>$channel->channelid,"updatetime"=>"1000-01-01 00:00:00","weather1"=>"",
        "weather2"=>"","weather3"=>"","weather4"=>"","weather5"=>"","weather6"=>"");
    try{
        for ($i = 0; $i <= 4; $i++){
            $tplurl = "http://php.weather.sina.com.cn/xml.php?password=DJOYnieT8234jlsK&day=$i&city=";
//        $citycode = urlencode(mb_convert_encoding("上海",'gb2312','utf-8' ));
            $citycode = urlencode(mb_convert_encoding($channel->channelcity,'gb2312','utf-8' ));
            $url = $tplurl.$citycode;
            $weather = myclient($url,true);
            if(!empty($weather)){
                $xmlobj = simplexml_load_string($weather);
                if(!empty($xmlobj)){
                    $dw = fixweatherimg((string)$xmlobj->Weather->status1);
                    $nw = fixweatherimg((string)$xmlobj->Weather->status2);
                    $wt = getfullwearhertxt($dw["descp"],$nw["descp"]);

                    $jsonobj = array('ht'=>(string)$xmlobj->Weather->temperature1,
                        'lt'=>(string)$xmlobj->Weather->temperature2,
                        'wt'=>$wt,
                        'dw'=>$dw["descp"],
                        'nw'=>$nw["descp"],
                        'di'=>$dw["img"],
                        'ni'=>$nw["img"]);
                    $ret["weather".($i+1)] =myjsonencode($jsonobj);
                    $ret["updatetime"] =date("Y-m-d H:i:s");
                }
            }
        }
    }
    catch(Exception $e){
        unset($xmlobj);
        unset($jsonobj);
    }
    unset($xmlobj);
    unset($jsonobj);
    return $ret;
}

//xml,english
function getyahooapi($commoncityid,$channel){
    $ret = array("cityid"=>$commoncityid,"channelid"=>$channel->channelid,"updatetime"=>"1000-01-01 00:00:00","weather1"=>"",
        "weather2"=>"","weather3"=>"","weather4"=>"","weather5"=>"","weather6"=>"");
    $tplurl = 'http://weather.yahooapis.com/forecastrss?u=c&p=';
//    $url = $tplurl."CHXX0116";
    $url = $tplurl.$channel->channelcity;
    $weather = myclient($url,true);
    if($weather==null){
        return $ret;
    }
    $p = xml_parser_create();

    xml_parse_into_struct($p, $weather, $vals, $index);
    xml_parser_free($p);
    $updatetime = $vals[$index['LASTBUILDDATE'][0]]['value'];
    if($updatetime != ""){
        $ld=new DateTime($updatetime);
        $ret["updatetime"] =$ld->format("Y-m-d H:i:s");
    }

    $weekindex = $index['YWEATHER:FORECAST'];
    $i = 1;
    foreach($weekindex as $k=>$v){
        $txt = $vals[$v]['attributes']['TEXT'];
        $isrange = is_int(strpos($txt,'/'));
        if($isrange==true){
            $wlist = explode("/",$txt);
            $dw = fixweatherimg(str_replace("AM ","",$wlist[0]));
            $nw = fixweatherimg(str_replace("PM ","",$wlist[1]));
        }else{
            $dw = fixweatherimg($txt);
            $nw = $dw;
        }

        $wt = getfullwearhertxt($dw['descp'],$nw['descp']);
        $jsonobj = array('ht'=>$vals[$v]['attributes']['HIGH'],
            'lt'=>$vals[$v]['attributes']['LOW'],
            'wt'=>$wt,
            'dw'=>$dw['descp'],
            'nw'=>$nw['descp'],
            'di'=>$dw['img'],
            'ni'=>$nw['img']);
        $ret["weather$i"] =myjsonencode($jsonobj);
        $i++;
    }
    unset($xmlobj);
    unset($jsonobj);
    return $ret;
}

function getfullwearhertxt($dw,$nw){
    if($dw == $nw){
        return $dw;
    }else{
        return $dw."转".$nw;
    }
}


//xml,chinese pingyin
function getbaiduapi($commoncityid,$channel){
    $ret = array("cityid"=>$commoncityid,"channelid"=>$channel->channelid,"updatetime"=>"1000-01-01 00:00:00","weather1"=>"",
        "weather2"=>"","weather3"=>"","weather4"=>"","weather5"=>"","weather6"=>"");
    $ak = "B89aaa253bef417d6675f9146842aeb4";
    $url = "http://api.map.baidu.com/telematics/v2/weather?ak=$ak&location=$channel->channelcity";
    try{
        //    $url = $tplurl."上海";
        $weather = myclient($url,true);
        if($weather==null){
            return $ret;
        }
        $xmlobj = simplexml_load_string($weather);
        $replacestr = array("http://api.map.baidu.com/images/weather/day/",
            "http://api.map.baidu.com/images/weather/night/",
            ".png");
        $i =1;
        foreach ($xmlobj->results->result as $result){
            $hasrealtime = is_int(strpos($result->date,'实时'));
            if($hasrealtime == True){
                $ret["updatetime"] =date("Y-m-d H:i:s");
            }
            $isrange = is_int(strpos((string)$result->temperature,'~'));
            $htemp = "";
            $ltemp = "";
            if($isrange==True){
                $templist = explode(" ~ ",$result->temperature);
                $htemp = gettempnumber($templist[0]);
                $ltemp = gettempnumber($templist[1]);
            }else{
                $ltemp = gettempnumber((string)$result->temperature);
                if($hasrealtime == True){
                    $arrobj = explode("实时：",$result->date);
                    $temp = gettempnumber($arrobj[1]);
                    if((int)$temp>=(int)($ltemp)){
                        $htemp = $temp;
                    }else{
                        $htemp = $ltemp;
                        $ltemp = $temp;
                    }
                }else{
                    $htemp = $ltemp;
                }
            }

            $dw = fixweatherimg(str_replace($replacestr,"",$result->dayPictureUrl));
            $nw = fixweatherimg(str_replace($replacestr,"",$result->nightPictureUrl));
            $wt = (string)$result->weather;
            if($dw["descp"]=="没有数据"){
                $dw["descp"] = $wt;
            }
            if($nw["descp"]=="没有数据"){
                $nw["descp"] = $wt;
            }
            $jsonobj = array('ht'=>$htemp,'lt'=>$ltemp,
                'wt'=>$wt,
                'dw'=>$dw["descp"],
                'nw'=>$nw["descp"],
                'di'=>$dw["img"],
                'ni'=>$nw["img"]);
            $ret["weather$i"] =myjsonencode($jsonobj);
            $i++;
        }
        unset($weather);
        unset($xmlobj);
        unset($jsonobj);
    }
    catch(Exception $e) {
        unset($weather);
        unset($xmlobj);
        unset($jsonobj);
    }
    return $ret;
}

//xml,english
function getmsnapi($commoncityid,$channel){
    $ret = array("cityid"=>$commoncityid,"channelid"=>$channel->channelid,"updatetime"=>"1000-01-01 00:00:00","weather1"=>"",
        "weather2"=>"","weather3"=>"","weather4"=>"","weather5"=>"","weather6"=>"");
    $url = "http://weather.msn.com/data.aspx?weadegreetype=C&wealocations=wc:$channel->channelcity";
//    $url = $tplurl."CHXX0129";
    $weather = myclient($url,true);
    if($weather==null){
        return $ret;
    }
    $xmlobj = simplexml_load_string($weather);

    if(array_key_exists("current",$xmlobj->weather)){
        $attr = $xmlobj->weather->current->attributes();
        $updatetime = (string)$attr->date." ".(string)$attr->observationtime;
        $ret["updatetime"] = $updatetime;
    }
    if(array_key_exists("forecast",$xmlobj->weather)){
        $i = 1;
        foreach ($xmlobj->weather->forecast as $forecast){
            $attr = $forecast->attributes();
            $txt = (string)$attr->skytextday;
            $isrange = is_int(strpos($txt,'/'));
            if($isrange==true){
                $wlist = explode("/",$txt);
                $dw = fixweatherimg(str_replace("AM ","",$wlist[0]));
                $nw = fixweatherimg(str_replace("PM ","",$wlist[1]));
            }else{
                $dw = fixweatherimg($txt);
                $nw = $dw;
            }
            $wt = getfullwearhertxt($dw["descp"],$nw["descp"]);
            $jsonobj = array('ht'=>(string)$attr->high,'lt'=>(string)$attr->low,
                'wt'=>$wt,
                'dw'=>$dw["descp"],
                'nw'=>$nw["descp"],
                'di'=>$dw["img"],
                'ni'=>$nw["img"]);
            $ret["weather$i"] =myjsonencode($jsonobj);
            $i++;
        }
    }
    return $ret;
}

//xml,chinese
function get360api($commoncityid,$channel){
    $ret = array("cityid"=>$commoncityid,"channelid"=>$channel->channelid,"updatetime"=>"1000-01-01 00:00:00","weather1"=>"",
        "weather2"=>"","weather3"=>"","weather4"=>"","weather5"=>"","weather6"=>"");
    $url = "http://cdn.weather.hao.360.cn/api_weather_info.php?app=hao360&code=$channel->channelcity";
//    $url = $tplurl."101010400";
    $replacestr = array("callback(",");");
    try{
        $weather = myclient($url,true);
        if($weather==null){
            return $ret;
        }
        $weather = str_replace($replacestr,"",$weather);
        $jsonobj = json_decode($weather);
        $ret["updatetime"] =date("Y-m-d H:i:s",$jsonobj->time);
        $i = 1;
        foreach ($jsonobj->weather as $w){
            if(!empty($w->info)){
                $day = $w->info->day;
                $night = $w->info->night;
                $dw = fixweatherimg($day[1]);
                $nw = fixweatherimg($night[1]);
                $wt = getfullwearhertxt($dw["descp"],$nw["descp"]);
                $jsonobj = array('ht'=>$day[2],'lt'=>$night[2],
                    'wt'=>$wt,
                    'dw'=>$dw["descp"],
                    'nw'=>$nw["descp"],
                    'di'=>$dw["img"],
                    'ni'=>$nw["img"]);
                $ret["weather$i"] =myjsonencode($jsonobj);
            }
            $i++;
        }
    }
    catch(Exception $e){

    }
    return $ret;
}

//callback function todo
function getsogouapi($commoncityid,$channel){
    $ret = array("cityid"=>$commoncityid,"channelid"=>$channel->channelid,"updatetime"=>"1000-01-01 00:00:00","weather1"=>"",
        "weather2"=>"","weather3"=>"","weather4"=>"","weather5"=>"","weather6"=>"");
    $url = "http://123.sogou.com/get123.php?block=wt&ver=v32&city=CN$channel->channelcity";
//    $url = $tplurl."110100";
    $replacestr = array("callback(",");");
    $weather = myclient($url,true);
    if($weather==null){
        return $ret;
    }
    $weather = str_replace($replacestr,"",$weather);
    $weather = json_decode($weather);
    return $ret;
}

function myclient($url,$again){
    $ch  = curl_init();
    curl_setopt($ch, CURLOPT_ENCODING, 'utf8');
    curl_setopt($ch, CURLOPT_URL, $url);
    if($again == true){
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    }else{
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
    $retInfo = curl_exec($ch);
    if (curl_errno ( $ch )) {
        curl_close ( $ch ); // 关闭CURL会话
        if($again == true){
            return myclient($url,false); //失败后再做一次尝试
        }else{
            echo($url);
            echo("\r\n");
            return null;
        }
//        die('Errno' . curl_error ( $ch )) ;
    }
    curl_close($ch);
    return $retInfo;
}

function gettempnumber($temp){
    $replacelist = array("(",")","℃","~");
    return str_replace($replacelist,"",$temp);
}

function myjsonencode($jsonobj){
//    urlencode

    $jsonobj["wt"] = urlencode($jsonobj["wt"]);
    $jsonobj["dw"] = urlencode($jsonobj["dw"]);
    $jsonobj["nw"] = urlencode($jsonobj["nw"]);
    $code = json_encode($jsonobj);
////    $utf8string = html_entity_decode(preg_replace("/U\+([0-9A-F]{4})/", "&#x\\1;", $string), ENT_NOQUOTES, 'UTF-8');
//    return preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2',
//    'UTF-8', pack('H4', '\\1'))", $code);

    return $code;
}

function checkemptyweather($weather){
    if(empty($weather)){
        return false;
    }
    if(!is_array($weather)){
        return false;
    }
    if(array_key_exists("weather1",$weather) == false){
        return false;
    }

    if($weather["weather1"]=="" && $weather["weather2"]=="" && $weather["weather3"]==""
        && $weather["weather4"]=="" && $weather["weather5"]=="" && $weather["weather6"]==""){
        return false;
    }
    return true;
}

function updateweather($list,$db){
    if(empty($list)){
        return;
    }
    if(!is_array($list)){
        return;
    }
    $sql = "INSERT INTO `u_weather_bk` (`channelid`, `cityid`, `updatetime`, `weather1`, `weather2`, `weather3`, `weather4`, `weather5`, `weather6`) VALUES";
    $isexit = false;
    foreach($list as $weather){
        if($isexit==false){
            $sql = $sql."(".$weather["channelid"].",'".$weather["cityid"]."','".$weather["updatetime"]."','".urldecode($weather["weather1"])."','".
                urldecode($weather["weather2"])."','".urldecode($weather["weather3"])."','".urldecode($weather["weather4"])."','".
                urldecode($weather["weather5"])."','".urldecode($weather["weather6"])."')";
            $isexit = true;
        }else{
//            $sql = $sql.",(".$weather["channelid"].",'".$weather["cityid"]."','".$weather["updatetime"]."','".$weather["weather1"]."','".
//                $weather["weather2"]."','".$weather["weather3"]."','".$weather["weather4"]."','".
//                $weather["weather5"]."','".$weather["weather6"]."')";
            $sql = $sql.",(".$weather["channelid"].",'".$weather["cityid"]."','".$weather["updatetime"]."','".urldecode($weather["weather1"])."','".
                urldecode($weather["weather2"])."','".urldecode($weather["weather3"])."','".urldecode($weather["weather4"])."','".
                urldecode($weather["weather5"])."','".urldecode($weather["weather6"])."')";
        }
    }
    if($isexit==true){
        $sql = $sql.";";
        $db->mysqlquery($sql);
    }
}

function cleartable($db){
    $sql = "truncate table `u_weather_bk`;";
    $db->mysqlquery($sql);
}

function mergeweather($db){
    $sql = "call mergeweather();";
    $db->mysqlquery($sql);
}

function getcityinfo($db){
    $sql = "SELECT * FROM uniqlo.u_weathercity";

    return $db->mysqlfetch($sql);
}

function getchannelinfo($db){
    $sql = "SELECT id FROM uniqlo.u_weatherchannel where channelsort <>99;";
    return $db->mysqlfetch($sql);
}

function checkchannel($channelid,$channelinfo){
    foreach($channelinfo as $channel){
        if ($channel["id"] == $channelid){
            return true;
        }
    }
    return false;
}


function fixweatherimg($weather){
    $imglist = array(
        array("like"=>array("晴","qing","qin","sunny","sun","sunny","fair","clear","clearing"),"descp"=>"晴","img"=>"0"),
        array("like"=>array("多云","duoyun","partlycloudy","clouds","cloudy"),"descp"=>"多云","img"=>"1"),
        array("like"=>array("阴","yin","ying","cloudy"),"descp"=>"阴","img"=>"2"),
        array("like"=>array("阵雨","zhenyu","zhengyu","shower","showers"),"descp"=>"阵雨","img"=>"3"),
        array("like"=>array("雷阵雨","雷雨","leizhenyu","leizhengyu","leiyu","thundershower","thunderstorm","thunder"),"descp"=>"雷阵雨","img"=>"4"),
        array("like"=>array("雷阵雨伴有冰雹","leizhenyubanbingbao","thunderstormwithhail","hail"),"descp"=>"雷阵雨伴有冰雹","img"=>"5"),
        array("like"=>array("雨夹雪","yujiaxue","sleet"),"descp"=>"雨夹雪","img"=>"6"),
        array("like"=>array("小雨","xiaoyu","yu","lightrain","sprinkles"),"descp"=>"小雨","img"=>"7"),
        array("like"=>array("中雨","zhongyu","zhonyu","rain","moderaterain"),"descp"=>"中雨","img"=>"8"),
        array("like"=>array("大雨","dayu","heavyrain","bigrain"),"descp"=>"大雨","img"=>"9"),
        array("like"=>array("暴雨","baoyu","rainstorm","tempest","hurricane","storm"),"descp"=>"暴雨","img"=>"10"),
        array("like"=>array("大暴雨","dabaoyu","rainstorm","tempest","hurricane","storm","downpour"),"descp"=>"大暴雨","img"=>"11"),
        array("like"=>array("特大暴雨","tedabaoyu","rainstorm","tempest","hurricane","storm","downpour","torrentialrain"),"descp"=>"特大暴雨","img"=>"12"),
        array("like"=>array("阵雪","zhenxue","zhengxue","snow","showerysnow","snowshowers"),"descp"=>"阵雪","img"=>"13"),
        array("like"=>array("小雪","xiaoxue","snow","flurries","flurry","lightsnow","scouther"),"descp"=>"小雪","img"=>"14"),
        array("like"=>array("中雪","zhongxue","moderatesnow"),"descp"=>"中雪","img"=>"15"),
        array("like"=>array("大雪","daxue","heavysnow"),"descp"=>"大雪","img"=>"16"),
        array("like"=>array("暴雪","baoxue","blizzard"),"descp"=>"暴雪","img"=>"17"),
        array("like"=>array("雾","wu","fog","mist","finespray","brume","reek"),"descp"=>"雾","img"=>"18"),
        array("like"=>array("冻雨","dongyu","donyu","icerain","freezingrain"),"descp"=>"冻雨","img"=>"19"),
        array("like"=>array("沙尘暴","shachenbao","shachengbao","sandstorm","duststorm"),"descp"=>"沙尘暴","img"=>"20"),
        array("like"=>array("小雨-中雨","小雨到中雨","小雨转中雨","小到中雨","","",""),"descp"=>"小雨转中雨","img"=>"21"),
        array("like"=>array("中雨-大雨","中雨到大雨","中雨转大雨","中到大雨","","",""),"descp"=>"中雨转大雨","img"=>"22"),
        array("like"=>array("大雨-暴雨","大雨到暴雨","大雨转暴雨","大到暴雨","","",""),"descp"=>"大雨转暴雨","img"=>"23"),
        array("like"=>array("暴雨-大暴雨","暴雨到大暴雨","暴雨转大暴雨","暴到大暴雨","","",""),"descp"=>"暴雨转大暴雨","img"=>"24"),
        array("like"=>array("大暴雨-特大暴雨","大暴雨到特大暴雨","大暴雨转特大暴雨","大暴到特大暴雨","","",""),"descp"=>"大暴雨转特大暴雨","img"=>"25"),
        array("like"=>array("小雪-中雪","小雪到中雪","小雪转中雪","小到中雪","","",""),"descp"=>"小雪转中雪","img"=>"26"),
        array("like"=>array("中雪-大雪","中雪到大雪","中雪转大雪","中到大雪","","",""),"descp"=>"中雪转大雪","img"=>"27"),
        array("like"=>array("大雪-暴雪","大雪到暴雪","大雪转暴雪","大到暴雪","","",""),"descp"=>"大雪转暴雪","img"=>"28"),
        array("like"=>array("浮尘","fuchen","fucheng","dust","smoke","flyash","ash","floatingdust","surfacedust"),"descp"=>"浮尘","img"=>"29"),
        array("like"=>array("扬沙","yangsha","blowingsand"),"descp"=>"扬沙","img"=>"30"),
        array("like"=>array("强沙尘暴","qiangshachengbao","qiangshachenbao","strongsandstorm","strongduststorm"),"descp"=>"强沙尘暴","img"=>"31"),
        array("like"=>array("霾","mai","haze"),"descp"=>"霾","img"=>"29")
    );
    $weather = strtolower($weather);
    $replacelist = array("am ","pm ","few "," lately"," late"," early","mostly","scattered"," ");

    $weather = str_replace($replacelist,"",$weather);

    foreach($imglist as $img){
        if(in_array($weather,$img["like"])==true){
            return $img;
        }
    }
    return array("descp"=>"没有数据","img"=>"53");
}
?>
