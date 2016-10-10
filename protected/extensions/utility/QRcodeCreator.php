<?php
define('QRCODE_SYSTEM_PATH',UPLOAD_SYSTEM_PATH.'qrcode/'); //二维码图片物理路径

class QRcodeCreator {
	/** 画圆角
	* @param $radius 圆角位置
	* @param $color_r 色值0-255
	* @param $color_g 色值0-255
	* @param $color_b 色值0-255
	* @return resource 返回圆角
	*/
	function get_lt_rounder_corner($radius, $color_r, $color_g, $color_b) {
		// 创建一个正方形的图像
		$img = imagecreatetruecolor($radius, $radius);
		// 图像的背景
		$bgcolor = imagecolorallocate($img, $color_r, $color_g, $color_b);
		$fgcolor = imagecolorallocate($img, 0, 0, 0);
		imagefill($img, 0, 0, $bgcolor);
		// $radius,$radius：以图像的右下角开始画弧
		// $radius*2, $radius*2：已宽度、高度画弧
		// 180, 270：指定了角度的起始和结束点
		// fgcolor：指定颜色
		imagefilledarc($img, $radius, $radius, $radius * 2, $radius * 2, 180, 270, $fgcolor, IMG_ARC_PIE);
		// 将弧角图片的颜色设置为透明
		imagecolortransparent($img, $fgcolor);
	
		return $img;
	}
	
	/**
	 * 自动换行
	 */
	// 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
	function autowrap($fontsize, $angle, $fontface, $string, $width) {
		$content = "";
		$sum = 0;//记录换行的次数
		// 将字符串拆分成一个个单字 保存到数组 letter 中
		for ($i=0;$i<mb_strlen($string);$i++) {
			$letter[] = mb_substr($string, $i, 1);
		}
		foreach ($letter as $l) {
			$teststr = $content." ".$l;
			$testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
			// 判断拼接后的字符串是否超过预设的宽度
			if ((($testbox[2] > $width) && ($content !== ""))) {
				$content .= "\n";
				$sum+=1;
			}
			$content .= $l;
		}
		return $content.'_'.$sum;
	}
	
	/**
	 * 获取文字图片的宽度
	 * @param unknown $fontsize
	 * @param unknown $angle
	 * @param unknown $fontface
	 * @param unknown $string
	 * @return number
	 */
	function getFontImageWidth($fontsize, $angle, $fontface, $string) {
		$box = imagettfbbox($fontsize, $angle, $fontface, $string);
		//左下角 X 位置
		$left = $box[0];
		//右下角 X 位置
		$right = $box[2];
		$width = $right - $left;
		
		return $width;
	}
	
	/**
	 * 创建微信扫码图片
	 * @param unknown $data 			二维码数据
	 * @param unknown $string			文字内容
	 * @param unknown $branch_string	副文字内容
	 * @param unknown $bg_path			背景图路径
	 * @param unknown $logo_path		logo路径
	 */
	public function createWxQrcode($data, $string, $branch_string, $bg_path, $logo_path) {
		//引入phpqrcode库文件
		Yii::import('application.extensions.qrcode.*');
		include('phpqrcode.php');
		
		//开始创建二维码
		$outfile = QRCODE_SYSTEM_PATH.uniqid().mt_rand(10000, 99999).'.png'; //临时文件存储路径
		//创建一个二维码文件: 二维码数据内容，输出图片文件，纠错等级，图片大小，空白区域大小
		QRcode::png($data, $outfile, QR_ECLEVEL_H, 10.2, 0);
		
		$qr = imagecreatefromstring(file_get_contents($outfile));
		$qr_width = imagesx($qr);
		$qr_height = imagesy($qr);
		//对二维码加logo
		if ($logo_path !== FALSE) {
			$logo = imagecreatefromstring(file_get_contents($logo_path));
			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);
			$logo_qr_width = $qr_width / 5;
			$scale = $logo_width / $logo_qr_width;
			$logo_qr_height = $logo_height / $scale;
			$from_width = ($qr_width - $logo_qr_width) / 2;
			imagecopyresampled($qr, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
			imagedestroy($logo);
		}
		//imagepng($qr, $outfile);
		
		//添加文字
		mb_internal_encoding("UTF-8"); // 设置编码
		//读取背景大图
		$img = imagecreatefromstring(file_get_contents($bg_path));
		$img_width = imagesx($img);
		$img_height = imagesy($img);
		//计算图片的宽度,二维码图片宽度的3倍
		//$img_width = $qr_width * 3;
		//计算图片的高度，二维码图片高度的1.5倍
		//$img_height = $qr_height * 1.5;
		//先生成一张大的图片，然后依次把二维码和文字信息合并到大图中
		//$img = imagecreatetruecolor($img_width, $img_height); //创建大图
		//$color = imagecolorallocate($img, 255, 255, 255); //图片背景白色
		//imagefill($img, 0, 0, $color); //颜色填充
		//文字信息合并到大图
		$color1 = imagecolorallocate($img, 35, 172, 56); //文字颜色
		$font_width = $img_width; //文字宽度，等于图片宽度
// 		$text = QRcodeCreator::autowrap(80, 0, dirname(__FILE__).'/boldfont.ttc', $string, $font_width);
// 		$text = explode('_', $text);
// 		$tmp = $text['0'];
		//获取文字图片的宽度
		$font_img_width1 = QRcodeCreator::getFontImageWidth(56, 0, dirname(__FILE__).'/msyhbd.ttf', $string);
		$font_img_width2 = QRcodeCreator::getFontImageWidth(32, 0, dirname(__FILE__).'/msyhbd.ttf', $branch_string);
		//计算x坐标
		$x1 = ($img_width - $font_img_width1) / 2;
		$x2 = ($img_width - $font_img_width2) / 2.05;
		//设置y坐标
		$y1 = 510;
		$y2 = $y1 + 80;
		//图片，字体大小，角度，x坐标，y坐标，颜色，字体文件，字符串内容
		imagettftext($img, 56, 0, $x1, $y1, $color1, dirname(__FILE__).'/msyhbd.ttf', $string); //主门店名
		if (!empty($branch_string)) {
			imagettftext($img, 32, 0, $x2, $y2, $color1, dirname(__FILE__).'/msyhbd.ttf', $branch_string); //分店名
		}
		
		//二维码图片合并到大图
// 		$qr = imagecreatefrompng($outfile);
// 		$w1 = imagesx($qr);
// 		$h1 = imagesy($qr);
		//计算x坐标
		$x = ($img_width - $qr_width) / 2;
		//计算y坐标
		$y = $y2 + 40;
		imagecopy($img, $qr, $x, $y, 0, 0, $qr_width, $qr_height);
		
		/*
		//文字信息合并到大图
		$color1 = imagecolorallocate($img, 0, 0, 255);
		$text_height = imagefontheight(15);
		$font_width = imagesx($qr) - 40;
		$text = QRcodeCreator::autowrap('80', 0, dirname(__FILE__).'/boldfont.ttc', $string, $font_width);
		$text = explode('_', $text);
		$tmp = $text['0'];
		imagettftext($img, 14, 0, 20, $h, $color1, dirname(__FILE__).'/boldfont.ttc', $tmp);
		//文字中行的间距
		$h = $h + 22 * ($text['1'] + 1);
		*/
		//把图片大小的高度更改为实际的高度
		//$image_p = imagecreatetruecolor($img_width, $img_height);
		//imagecopyresampled($image_p, $img, 0, 0, 0, 0, $img_width, $img_height, $img_width, $img_height);
		//输出图片到服务器
		imagepng($img, $outfile);
		//销毁
		//imagedestroy($image_p);
		imagedestroy($img);
		imagedestroy($qr);
		//删除临时文件
		//unlink($outfile);
		
		return basename($outfile);
	}
	
	/**
	 * 保存支付宝二维码图片
	 * @param unknown $url
	 * @return string
	 */
	public function saveAliQrcode($url) {
		$outfile = QRCODE_SYSTEM_PATH.uniqid().mt_rand(10000, 99999).'.jpg'; //临时文件存储路径
		
// 		$ch = curl_init ();  
//         curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );  
//         curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );  
//         curl_setopt ( $ch, CURLOPT_URL, $url );  
//         ob_start ();  
//         curl_exec ( $ch );  
//         $return_content = ob_get_contents ();  
//         ob_end_clean ();
// 		$tp = @fopen(basename($outfile), 'a');
// 		fwrite($tp, $return_content);
// 		fclose($tp);
		
		//开始捕捉
		ob_start();
		readfile($url);
		$img = ob_get_contents();
		ob_end_clean();
		//保存到服务器
		$file = fopen($outfile, "a");
		fwrite($file, $img);
		fclose($file);
		
		return basename($outfile);
	}
	
	/**
	 * 二维码文件下载
	 * @param unknown $file_name
	 */
	static public function download($file_name) {
		//文件物理路径
		$file = QRCODE_SYSTEM_PATH.$file_name;
		if (!file_exists($file)) {
			exit();
		}
		//设置header信息，用于浏览器识别下载文件
		header("Content-type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".filesize($file));
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		
		//解决图片无法打开的错误
		ob_clean();
		flush();
		
		//输出文件
		readfile($file);
	}
	
	
}