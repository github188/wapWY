<?php
    $browser=strtolower(browser());
    $filename='玩券收银台';
    if($browser=='firefox'){
     $filename=urldecode($filename);
    }else{
     $filename=urlencode($filename);
    }
    $url='http://syt.51wanquan.com/';
    $Shortcuts='[InternetShortcut]
    URL='.$url.'
    IconFile=http://syt.51wanquan.com/favicon.ico
    IconIndex=123
    IDList=
    [{000214A0-0000-0000-C000-000000000046}]
    Prop3=19,2';
    Header('Content-type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$filename.'.url;');
    echo $Shortcuts;

    function browser(){
        $user_agent=$_SERVER['HTTP_USER_AGENT'];
        if(false!==strpos($user_agent,'MSIE 9.0')){
         return 'IE9';
        }
        if(false!==strpos($user_agent,'MSIE 8.0')){
         return 'IE8';
        }
        if(false!==strpos($user_agent,'MSIE 7.0')){
         return 'IE7';
        }
        if(false!==strpos($user_agent,'MSIE 6.0')){
         return 'IE6';
        }
        if(false!==strpos($user_agent,'Firefox')){
         return 'Firefox';
        }
        if(false!==strpos($user_agent,'Chrome')){
         return 'Chrome';
        }
        if(false!==strpos($user_agent,'Safari')){
         return 'Safari';
        }
        if(false!==strpos($user_agent,'Opera')){
         return 'Opera';
        }
        if(false!==strpos($user_agent,'360SE')){
         return '360SE';
        }
    }
?>
