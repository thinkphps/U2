<?php
class SendcityAction extends Action{
	public function sendpro(){
	  header("Content-type: text/html; charset=utf-8");
	  header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
	  $callback=$_GET['callback'];
	  $pro = trim($this->_request('proname'));
	  $city = trim($this->_request('cityname'));
	  $pro = iconv('gbk','utf-8',$pro);
	  $city = iconv('gbk','utf-8',$city);
	  if(is_int(strpos($pro,'省'))){
      $proarr = explode('省',$pro);
      $pro = $proarr[0];
	  }else if(is_int(strpos($pro,'市'))){
      $proarr = explode('市',$pro);
      $pro = $proarr[0];
	  }
	  //$_SESSION[$ip.'pro'] = $pro;
	  //$_SESSION[$ip.'cityn'] = $city;
      cookie('pro',$pro);
	  cookie('cityn',$city);
	  $arr = array('type'=>1);
	  $re = json_encode($arr);
	  echo $callback."($re)"; 
	}
}