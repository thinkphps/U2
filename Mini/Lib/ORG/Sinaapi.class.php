<?php
Vendor('weibo-phpsdk-v2-2013-02-20/saetv2');
class Sinaapi {
	protected $auth;
    protected $appid;
    protected $appkey;
	protected $accessToken;
	protected $client;
	public function __construct($pid,$apk) {
		$this->appid = $pid;
		$this->appkey = $apk;
		$this->auth = new SaeTOAuthV2( $this->appid , $this->appkey );
	}
	//获取授权地址
	public function fetchAuthUrl($callback_url) {
		return $this->auth->getAuthorizeURL( $callback_url );
	}
	public function fetchAccessToken($params, $callback_url) {
		$keys = array();
		$keys['code'] = $params['code'];
		$keys['redirect_uri'] = $callback_url;
		return $this->auth->getAccessToken( 'code', $keys ) ;
	}

	public function setAccessToken($accessToken) {
		$this->accessToken = $accessToken;
		$this->client = new SaeTClientV2( $this->appid, $this->appkey, $accessToken['access_token'] ); 
	}
	
	public function setweibo($content){
		$res = $this->client->update($content);
		//error_log(print_r(,1),3,'./123.txt');
	}
	public function setweibopic($t,$p){
		$res = $this->client->upload( $t, $p );
		return $res;
	}
	
	public function showuser(){
		$uid = $this->client->get_uid();
		$uid = $uid['uid'];
		$info = $this->client->show_user_by_id($uid);
		return $info;
	}
	
	public function followers_by_id(){
		$uid = $this->client->get_uid();
		$uid = $uid['uid'];
		return $this->client->followers_by_id($uid,0,50);
		//return $this->client->followers_by_id($userid,$cursor,$count);
	}
	public function follow_by_id(){
		$this->client->follow_by_id(2868316492);
	}
	public function getuid(){
	   $uid = $this->client->get_uid();
	   return $uid['uid'];
	}
	
	public function user_timeline($uid){
		$weibo = $this->client->user_timeline_by_id($uid);
		return $weibo;
	}
	
	public function getFeeds($nickname){
		$weibos = $this->client->user_timeline_by_name($nickname);
		return $weibos;		
	}
    
	public function sedcomments($id , $comment){
    $comments = $this->client->send_comment($id , $comment);
	return $comments;
	}
}