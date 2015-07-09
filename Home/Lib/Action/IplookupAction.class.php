<?php
class IplookupAction extends Action{
      public function IpArea(){
		$format = trim($this->_get('format'));
		if($format=='js'){
			$ip = get_client_ip();
			$str = getIPLoc_sina($ip,1);
			echo "var remote_ip_info=".$str.";";
		}
	  }
}