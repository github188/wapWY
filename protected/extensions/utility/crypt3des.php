<?php
class Crypt3Des {
	protected $model = "ecb"; //模式
	protected $padding = "pkcs5"; //填充方式
	protected $key = "cc3c40af69f269961a04163fd8251fd3"; //3des密钥
	
	/**
	 * Crypt3Des的构造函数
	 * @param string $desKey 密钥
	 * @param string $padding 填充方式，默认：pkcs5，可选pkcs5/pkcs7/null
	 * @param string $model 算法模式，默认：ecb，可选ecb/cbc/cfb/ofb
	 */
	public function __construct($desKey, $padding = "pkcs5", $model = "ecb") {
		$this->key = $desKey;
		$this->padding = $padding;
		$this->model = $model;
	}
	
	/**
	 * 3des加密
	 * @param unknown $input
	 * @return string
	 */
	public function encrypt($input) {
		$size = mcrypt_get_block_size(MCRYPT_3DES, $this->model);
		//选择填充方式
		if ($this->padding == 'pkcs5') {
			$input = $this->pkcs5_pad($input, $size);
		}
		if ($this->padding == 'pkcs7') {
			$input = $this->pkcs7_pad($input);
		}
		
		$key = str_pad($this->key, 24, '0');
		$td = mcrypt_module_open(MCRYPT_3DES, '', $this->model, '');
		$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		@mcrypt_generic_init($td, $key, $iv);
		$data = mcrypt_generic($td, $input);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		//$data = base64_encode ( $data );
		//转化为16进制
		$data = $this->String2Hex($data);
		
		return $data;
	}
	
	/**
	 * 3des解密
	 * @param unknown $encrypted
	 * @return Ambigous <boolean, string>
	 */
	function decrypt($encrypted) { // 数据解密
		//$encrypted = base64_decode ( $encrypted );
		//16进制转化
		$encrypted = $this->Hex2String($encrypted);
		$key = str_pad($this->key, 24, '0');
		$td = mcrypt_module_open(MCRYPT_3DES, '', $this->model, '');
		$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		$ks = mcrypt_enc_get_key_size($td);
		@mcrypt_generic_init($td, $key, $iv);
		$decrypted = mdecrypt_generic($td, $encrypted);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		//选择填充方式
		if ($this->padding == 'pkcs5') {
			$decrypted = $this->pkcs5_unpad($decrypted);
		}
		if ($this->padding == 'pkcs7') {
			$decrypted = $this->pkcs7_unpad($decrypted);
		}
		
		return $decrypted;
	}
	
	/**
	 * pkcs5填充添加
	 * @param unknown $text
	 * @param unknown $blocksize
	 * @return string
	 */
	private function pkcs5_pad($text, $blocksize) {
		$pad = $blocksize - (strlen ( $text ) % $blocksize);
		return $text . str_repeat ( chr ( $pad ), $pad );
	}
	
	/**
	 * pkcs5填充去除
	 * @param unknown $text
	 * @return boolean|string
	 */
	private function pkcs5_unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text )) {
			return false;
		}
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad) {
			return false;
		}
		return substr ( $text, 0, - 1 * $pad );
	}
	/**
	 * pkcs7填充添加
	 * @param unknown $text
	 * @return string
	 */
	private function pkcs7_pad($text) {
		$block_size = mcrypt_get_block_size(MCRYPT_3DES, $this->model );
		$padding_char = $block_size - (strlen($text) % $block_size);
		$text .= str_repeat (chr($padding_char), $padding_char );
		return $text;
	}
	/**
	 * pkcs7填充去除
	 * @param unknown $text
	 * @return unknown|string
	 */
	private function pkcs7_unpad($text) {
		$block_size = mcrypt_get_block_size(MCRYPT_3DES, $this->model );
		$padding_char = substr($text, -1, 1);
		$num = ord($padding_char);
		if ($num > 8) {
			return $text;
		}
		$len = strlen($text);
		for ($i = $len - 1; $i >= $len - $num; $i--) {
			if (ord(substr($text, $i, 1)) != $num) {
				return $text;
			}
		}
		$text = substr($text, 0, -$num);
		return $text;
	}
	
	/**
	 * 字符串转十六进制
	 * @param unknown $string
	 * @return string
	 */
	private function String2Hex($string){
		$hex='';
		for ($i=0; $i < strlen($string); $i++){
			$hex .= dechex(ord($string[$i]));
		}
		return $hex;
	}

	/**
	 * 十六进制转字符串
	 * @param unknown $hex
	 * @return string
	 */
	private function Hex2String($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
}