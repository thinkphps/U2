<?php
Vendor('QQ_PHP_SDK_V3.0.9/OpenApiV3');
class Qqapi {
    protected $accessToken;
    protected $appid;
    protected $appkey;
    public function __construct($pid,$apk,$callback){
        $this->appid = $pid;
        $this->appkey = $apk;
    }
    public function fetchLoginUrl($callback_url){
        $url = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id='. $this->appid .'&redirect_uri='. urlencode($callback_url) .'&scope=get_user_info,get_info';
        return $url;
    }
    public function fetchAccessToken($params, $callback_url) {

        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . $this->appid. "&redirect_uri=" . urlencode($callback_url)
            . "&client_secret=" . $this->appkey . "&code=" . $params["code"];

        $response = $this->get_url_contents($token_url);

        if (strpos($response, "callback") !== false)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if (isset($msg->error))
            {
                throw new Exception($msg->error_description, $msg->error);
            }
        }

        $params = array();
        parse_str($response, $params);
       return $params;
    }

    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;

        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
            . $this->accessToken;

        $str  = $this->get_url_contents($graph_url);
        if (strpos($str, "callback") !== false)
        {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($str);
        if (isset($user->error))
        {
            throw new Exception($user->error_description, $user->error);
        }

        $this->openid = $user->openid;
        return $user->openid;
    }

    public function fetchUserInfo()
    {
        $get_user_info = "https://graph.qq.com/user/get_user_info?"
            . "access_token=" . $this->accessToken
            . "&oauth_consumer_key=" . $this->appid
            . "&openid=" . $this->openid
            . "&format=json";
        $info = $this->get_url_contents($get_user_info);
        $info = json_decode($info, true);
        return $info;
    }

    protected function do_post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);

        curl_close($ch);
        return $ret;
    }

    protected function get_url_contents($url)
    {
        if (ini_get("allow_url_fopen") == "1")
            return file_get_contents($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result =  curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}