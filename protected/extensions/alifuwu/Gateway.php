<?php
class Gateway {
	public function verifygw($is_sign_success) {
		$biz_content = HttpRequest::getRequest ( "biz_content" );
		$as = new AlipaySign ();
		$xml = simplexml_load_string ( $biz_content );
		// print_r($xml);
		$EventType = ( string ) $xml->EventType;
		// echo $EventType;
		if ($EventType == "verifygw") {
			
			require(dirname(__FILE__) . '/AopConfig.php');
			
			// global $aliconfig;
			// print_r ( $aliconfig );
			if ($is_sign_success) {
				$response_xml = "<success>true</success><biz_content>" . $as->getPublicKeyStr ( $AopConfig ['merchant_public_key_file'] ) . "</biz_content>";
			} else { // echo $response_xml;
				$response_xml = "<success>false</success><error_code>VERIFY_FAILED</error_code><biz_content>" . $as->getPublicKeyStr ( $AopConfig ['merchant_public_key_file'] ) . "</biz_content>";
			}
			
			$return_xml = $as->sign_response ( $response_xml, $AopConfig ['charset'], $AopConfig ['merchant_private_key_file'] );

			writeLog ( "response_xml: " . $return_xml );
			echo $return_xml;
			exit ();
		}
	}
}