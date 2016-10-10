<?php

/**
 * Created by PhpStorm.
 * User: nb-lt
 * Date: 2016/6/28
 * Time: 10:49
 * 小区保修
 */
class RepairController extends PropertyController
{
    public function init()
    {
        parent::init();
        $encrypt_id = $this->getEncryptId();
        $this->checkLogin($encrypt_id);
    }

    /**
     * 报修记录
     */
    public function actionRepairRecord()
    {
        $reportRepairRecordC = new ReportRepairRecordC();
        $list = array();
        $user_id = $this->getUserId();
        $encrypt_id = $this->getEncryptId();

        $result = json_decode($reportRepairRecordC->getReportRepairRecordList($user_id), true);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $list = $result['data'];
        }

        $this->render('repairRecord', array(
            'list' => $list,
            'user_id' => $user_id,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 小区报修
     */
    public function actionRepair()
    {

        $reportRepairRecordC = new ReportRepairRecordC();
        $user_id = $this->getUserId();
        $encrypt_id = $this->getEncryptId();

        $user = new MobileUserUC();
        $re = json_decode($user->getMerchant($encrypt_id));
        $merchant_id = $re->data->id;

        //微信上传图片
        $wxImage = new WechatJS();
        $resultWxImage = $wxImage->WxUploadImage('', $encrypt_id);
        $res = json_decode(($resultWxImage),true);
        $signPackage = $res;

        //获取表单提交数据
        if (isset($_POST) && $_POST) {
            $info = $_POST;

            //调用addRepairRecord（）方法
            $result = $reportRepairRecordC->addReportRepairRecord($info, $user_id, $merchant_id);

            //保存成功，则页面重定向
            if ($result['status'] == APPLY_CLASS_SUCCESS) {
                $this->redirect($this->createUrl('Ucenter/Index', array(
                    'encrypt_id' => $encrypt_id
                )));
            } else {
                Yii::app()->user->setFlash('error', $result['errMsg']);
            }
        }

        $this->render('repair', array(
            'user_id' => $user_id,
            'encrypt_id' => $encrypt_id,
            'signPackage' => $signPackage
        ));
    }

    /**
     * 维修反馈
     */
    public function actionRepairComment()
    {

        $encrypt_id = $this->getEncryptId();
        $reportrepairrecordC = new ReportRepairRecordC();

        $post = $_POST;

        $result = json_decode($reportrepairrecordC->addRemark($post), true);

        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $this->redirect($this->createUrl('Ucenter/Center', array('encrypt_id' => $encrypt_id)));
        } else {
            Yii::app()->user->setFlash('error', $result['errMsg']);
            $this->redirect($this->createUrl('Repair/RepairRecord', array('encrypt_id' => $encrypt_id)));
        }
    }







    /**
     * 重画所有图片
     */
    public function actionImgageChange()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1500M');

        $dir1 = dirname(__FILE__) . '/../../../../../upload/images/gj/source/'; //获取该路径下所有（图片）文件
//         echo $dir1;exit;
        $dir2 = scandir($dir1); //scandir()函数返回一个数组，其中包含指定路径中的文件和目录。
        foreach ($dir2 as $key => $v) {
            if ($v != '.' && $v != '..') {
                $filenames = scandir($dir1 . $v);
                foreach ($filenames as $j) {
                    if ($j != '.' && $j != '..') {
                        $all_file[$v][] = $j;
                    }
                }
            }
        }
        $thumb = '600@600,300@300,250@250,150@150,125@125,80@80'; //重画图片尺寸
        $folder = IMG_WAP_WY;

        foreach ($all_file as $key1 => $v1) {
            $date_folder = $key1;
            $basePath = dirname(__FILE__) . '/../../../../../upload/images/' . $folder . '/';
            $thumbs = '';

            $temp1 = explode(',', $thumb);
            foreach ($temp1 as $key2 => $v2) {
                $temp2 = explode('@', $v2);
                $temp3 = explode('*', $temp2[1]);
                $thumbs[] = array(
                    'folder' => $temp2[0],
                    'path' => $basePath . $temp2[0] . '/' . $date_folder . '/',
                    'width' => $temp3[0],
                    'height' => isset($temp3[1]) ? $temp3[1] : '',
                );

                $this->createFolder($date_folder,$folder,$thumbs); //调用createFolder()方法生成文件夹，存放重画后的图片
            }

            $save_path = $basePath . "source/$date_folder/";
            $file_names = $v1;
            foreach ($file_names as $v3) {
                $this->HandleThumbs($save_path, $v3, $thumbs); //调用HandleThumbs()方法生成缩略图
            }
        }
    }

    /**
     * @param $date_folder
     * @param $path
     * @param $thumbs
     * 生成文件夹
     */
    public function createFolder($date_folder, $path, $thumbs)
    {
        ini_set('gd.jpeg_ignore_warning', 1);
        $basePath = dirname(__FILE__) . '/../../../../../upload/images/';
        $paths = explode('/', $path);

        $folder = '';
        foreach ($paths as $k => $v) {
            $folder .= "/$v/";
            if (!file_exists($basePath . $folder)) {
                mkdir($basePath . $folder, 0777);
            }
        }

        if ($thumbs) {
            foreach ($thumbs as $k => $v) {
                $curFolder = "{$v['folder']}/";

                if (!file_exists($basePath . $folder . $curFolder)) {
                    mkdir($basePath . $folder . $curFolder, 0777);
                    if (!file_exists($basePath . $folder . $curFolder . $date_folder)) {
                        mkdir($basePath . $folder . $curFolder . $date_folder, 0777);
                    }
                } else {
                    if (!file_exists($basePath . $folder . $curFolder . $date_folder)) {
                        mkdir($basePath . $folder . $curFolder . $date_folder, 0777);
                    }
                }
            }
        }
        return;
    }

    /**
     * @param $path
     * @param $fileName
     * @param $thumbs
     * 生成缩略图
     */
    public function HandleThumbs($path,$fileName,$thumbs){
        $newFile = substr($fileName,0,strpos($fileName,"."));
        if(is_array($thumbs)){
            foreach($thumbs as $key => $val){
                $this->imgResize($path.$fileName,$newFile,$val['width'],$val['height'],$val['path'],$val['watermark']); //调用imgResize()方法，规定图片类型
            }
        }
    }

    /**
     * @param $oriFilePath
     * @param $newFile
     * @param $maxWidth
     * @param $maxHeight
     * @param $newPath
     * @param $watermark
     * @return mixed
     * 规定图片格式类型
     */
    public function imgResize($oriFilePath,$newFile,$maxWidth,$maxHeight,$newPath, $watermark) {
        $info = getimagesize ( $oriFilePath );
        switch ($info [2]) {
            case 1 :
                $im = imagecreatefromgif ( $oriFilePath );
                break;
            case 2 :
                $im = imagecreatefromjpeg ( $oriFilePath );
                break;
            case 3 :
                $im = imagecreatefrompng ( $oriFilePath );
                break;
            default :
                $array_error ['file_name'] = $oriFilePath;
                $array_error ['if_eoor'] = "fail";
                $array_error ['file_no'] = $info [2];
                $array_error ['file_type'] = $info ['mime'];
                return $array_error;
        }
        if (! $im) {
            $array_error ['file_name'] = $oriFilePath;
            $array_error ['if_eoor'] = "fail";
            $array_error ['file_no'] = $info [2];
            $array_error ['file_type'] = $info ['mime'];
            return $array_error;
        }
        $width = imagesx ( $im );
        $height = imagesy ( $im );

        $waterOriImg = '';

        if (($maxWidth && $width > $maxWidth) || ($maxHeight && $height > $maxHeight)) {
            $RESIZEWIDTH = false;
            $RESIZEHEIGHT = false;
            if ($maxWidth && $width > $maxWidth) {
                $widthratio = $maxWidth / $width;
                $RESIZEWIDTH = true;
            }
            if ($maxHeight && $height > $maxHeight) {
                $heightratio = $maxHeight / $height;
                $RESIZEHEIGHT = true;
            }
            if ($RESIZEWIDTH && $RESIZEHEIGHT) {
                if ($widthratio < $heightratio) {
                    $ratio = $widthratio;
                } else {
                    $ratio = $heightratio;
                }
            } elseif ($RESIZEWIDTH) {
                $ratio = $widthratio;
            } elseif ($RESIZEHEIGHT) {
                $ratio = $heightratio;
            }
            if ($ratio > 0) {
                $newwidth = $width * $ratio;
                $newheight = $height * $ratio;
            } else {
                $newwidth = $maxWidth;
                $newheight = $maxHeight;
            }
            if (function_exists ( "imagecopyresampled" )) {
                $newim = imagecreatetruecolor ( $newwidth, $newheight );
                imagecopyresampled ( $newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
            } else {
                $newim = imagecreate ( $newwidth, $newheight );
                imagecopyresized ( $newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
            }
            if ($info [2] == 1){
                $waterOriImg = $newPath . $newFile . '.gif';
                imagejpeg ( $newim, $waterOriImg);
            }
            else if ($info [2] == 2){
                $waterOriImg = $newPath . $newFile . '.jpg';
                imagejpeg ( $newim, $waterOriImg);
            }
            else if ($info [2] == 3){
                $waterOriImg = $newPath . $newFile . '.png';
                imagejpeg ( $newim, $waterOriImg);
            }

            ImageDestroy ( $newim );
        } else {
            if ($info [2] == 1){
                $waterOriImg = $newPath . $newFile . '.gif';
                imagejpeg ( $im, $waterOriImg);
            }elseif ($info [2] == 2){
                $waterOriImg = $newPath . $newFile . '.jpg';
                imagejpeg ( $im, $waterOriImg);
            }elseif($info [2] == 3){
                $waterOriImg = $newPath . $newFile . '.png';
                imagejpeg( $im, $waterOriImg);
            }
        }

        if($watermark){
            createWater($waterOriImg, $watermark, 9);
        }

        $array_error ['file_name'] = $oriFilePath;
        $array_error ['if_eoor'] = "pass";
        $array_error ['file_no'] = $info [2];
        $array_error ['file_type'] = $info ['mime'];
        return $array_error;
    }

    /**
     * 从微信下载读取图片
     */
    public function actiondownloadWxImg($server_id){

        $token = $this->getApiToken();
        //根据微信JS接口上传了图片,会返回上面写的images.serverId（即media_id），填在下面即可
        $str = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=$token&media_id=$server_id";
        //获取微信“获取临时素材”接口返回来的内容（即刚上传的图片）
        $a = file_get_contents($str);
        //__DIR__指向当前执行的PHP脚本所在的目录
        echo __DIR__;
        //以读写方式打开一个文件，若没有，则自动创建
        $img_url = __DIR__."/1.jpg";
        $resource = fopen( $img_url, 'w+');
        //将图片内容写入上述新建的文件
        fwrite($resource, $a);
        //关闭资源
        fclose($resource);
        return json_encode($img_url);

    }

    /**
     * @param bool $forceRefresh
     * @return mixed
     * 私有方法请求得到accessToken
     */
    private function getApiToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxd9edf097f820e733&secret=f6cc31d0d17d6eba6094ed697c4e9e37";
        $result = $this->callApi($url);
        $accessToken = $result['access_token'];
        return $accessToken;
    }

    /**
     * @param $url
     * @return mixed
     * 请求资源
     */
    protected function callApi($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }

}