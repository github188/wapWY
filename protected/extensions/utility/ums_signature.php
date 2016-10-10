<?php

//if php version < 5.4.0
if(!function_exists('hex2bin')) {
	function hex2bin($hex) {
		$n = strlen($hex);
		$bin = "";
		$i = 0;
		while($i < $n) {
			$a = substr($hex, $i, 2);
			$c = pack("H*", $a);
			if ($i == 0) {
				$bin = $c;
			} else {
				$bin .= $c;
			}
			$i+=2;
		}
		return $bin;
	}
}

//银商RSA公钥
define("RSA_PUBLIC_KEY_UMS", "BF1F167D413A9F77550AD25B2E0105809695EF45995EADA8B969870B03572E34ABE1467D2226C61D7ECA4D28FF3E8CF23A1753BF4B27708ADAAF5B522100162EAE6AA4FF1354F8CCC2EF6E57F58D835EAD9EA63F798A304857B1F8C96BA80023D44F116F776746D36BA823F366BD1B648B3114076C869FBB3A0315A8743D7C8A11DC49180C2245C31F4D83CBB9938A9BB8D2C04A1B5CB99460A0CB4F0C2075E21BCB3FE7AE97D5BE25420ABFE0531B6BFDB8C878D99A7734F6ACD6F7532AD0C35FB180B22BBA7DEC79293EF36315CD3AB83C3581B30082F214F3BC6C3A2C15C77E97F483F8CF167139AC380F7C6235AB39C7A90702AB5A85A87BBFF34034EFD1");

/**
 * 银商验签类
 *
 */
class UmsSignature {
	/**
	 * 验签
	 *
	 * @param string $data
	 * @param string $sign
	 * @param string $public
	 *        	公钥文件//验签公钥文件应为全民捷付提供的公钥文件
	 * @return bool 验签状态
	 */
	public function verify($data, $sign) {
		//获取公钥文件
		$mod = RSA_PUBLIC_KEY_UMS;
		$pem = $this->public2file($mod);
		//检查公钥可用性
		$p = openssl_pkey_get_public ( $pem );
		//数据验签
		$verify = openssl_verify ( $data, hex2bin ( $sign ), $p );
		openssl_free_key ( $p );
		return $verify > 0;
	}
	
	/**
	 * 还原公钥
	 *
	 * @param string $mod
	 * @param string $exp
	 * @return string 字符串形式的公钥文件
	 */
	private function public2file($mod, $exp = '010001') {
		$key=base64_encode ( hex2bin ( "30820122300D06092A864886F70D01010105000382010F003082010A0282010100{$mod}0203{$exp}" ) );
		$pem = chunk_split($key,64,"\n");
		$pem = "-----BEGIN PUBLIC KEY-----\n".$pem."-----END PUBLIC KEY-----\n";
		return $pem;
	}
	
}