<?php
class SendcityAction extends Action{
	public function sendpro(){
	  header("Content-type: text/html; charset=utf-8");
	  $pro = trim($this->_post('proname'));
	  $city = trim($this->_post('cityname'));
	  if(is_int(strpos($pro,'省'))){
      $proarr = explode('省',$pro);
      $pro = $proarr[0];
	  }else if(is_int(strpos($pro,'市'))){
      $proarr = explode('市',$pro);
      $pro = $proarr[0];
	  }
	  $_SESSION['pro'] = $pro;
	  $_SESSION['cityn'] = $city;
	}
}