<?php
@require_once "oauth2.php";
class Packet{
    /**
     * 微信红包
     * 
     */		
	public function wxpacket($openid){
		$wxapi = new Wxapi();
		$res = $wxapi->pay($openid);
		return $res;
	}
}
?>