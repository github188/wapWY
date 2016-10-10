<?php
// error_reporting(E_ALL);
// define('UPLOAD_SYSTEM_PATH', 'aa');

include_once $_SERVER['DOCUMENT_ROOT'].'/protected/extensions/wxopen/wxBizMsgCrypt.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/protected/components/Component.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/protected/config/config.php';

class WechatController extends CController
{
    
    private $wxBizMsg;
    public $textTpl;
    public $newsItemTpl;
    public $newsTpl;
    public $urlTpl;
    
    function __construct()
    {
        $this -> wxBizMsg = new WXBizMsgCrypt(Component::getToken(), Component::getAesKey(), Component::getAppId());
        $this -> textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[text]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                             </xml>";
        $this -> newsItemTpl = "<item>
            			         	<Title><![CDATA[%s]]></Title>
            			         	<Description><![CDATA[%s]]></Description>
            			         	<PicUrl><![CDATA[%s]]></PicUrl>
            			         	<Url><![CDATA[%s]]></Url>
                				</item>";
        $this -> newsTpl = "<xml>
        				    	<ToUserName><![CDATA[%s]]></ToUserName>
        				    	<FromUserName><![CDATA[%s]]></FromUserName>
        				    	<CreateTime>%s</CreateTime>
        				    	<MsgType><![CDATA[news]]></MsgType>
        				    	<ArticleCount>%s</ArticleCount>
        				    	<Articles>%s</Articles>
			                </xml>";
        $this -> urlTpl = "<xml>
    						<ToUserName><![CDATA[%s]]></ToUserName>
    						<FromUserName><![CDATA[%s]]></FromUserName>
    						<CreateTime>%s</CreateTime>
    						<MsgType><![CDATA[view]]></MsgType>
    						<Url><![CDATA[%s]]></Url>
    					</xml>";
    }
    
    
    public function actionIndex()
    {
        echo 'index';
    }
    
    public function actionTest()
    {
        $merchant_id = Yii::app()->session['merchant_id'];
        if(!isset($merchant_id) || empty($merchant_id))
        {
            echo "<script>alert('请登录后访问！');window.location.href='/mCenter/auth/Login';</script>";
            exit('请登录后访问！');
        }
        
        $merchant = Merchant::model()->findByPk($merchant_id);
        $access_token = Wechat::getTokenByMerchant($merchant);
        
        echo '当前登录商户的accesstoken：'.$access_token;
        if($merchant->wechat_thirdparty_authorizer_if_auth == 2)
        {
            echo '(授权方式的auth_access_token，AppID为：'.$merchant->wechat_thirdparty_authorizer_appid.')';
        }
        else 
        {
            echo '(默认AppId，Secret方式的access_token，AppId为：'.$merchant->wechat_subscription_appid.')';
        }
        echo '<br />';
        
        
    }
    
    
    //授权事件接收方法，以及微信服务器ticket`
    public function actionReceive()
    {
        $xml = file_get_contents("php://input");
        if($xml){
            Yii::log('[receive something...]=>'.'['.$xml.'] from '.Yii::app()->request->getUrl(), 'info', 'wechat.receive');
            $res = $this -> dealReceive($_GET, $xml);
        } else {
            die('非法访问！');
        }
    }
    
    //对上一步 授权事件接收方法，以及微信服务器ticket 进行处理
    private function dealReceive($param, $xmlData)
    {
        //给微信服务器应答
        echo 'success';
        
        $xml = simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA);
        /*
         * 接收到的解密数据示例
         *  <xml>
         *  <AppId><![CDATA[wx4de0ac035e5784ae]]></AppId>
         *  <Encrypt><![CDATA[eWiG3FkEfr30ZmkYZRtXIG9ak7AbTYMVU+zxBxigJKvPV/FcvY/R++yD4PuU0MhD5yRbdX0rUZ+mMnasEoKpPbFuNGYi7qjRF+JQBIbhezAnwhPw7mID79ytJrf6qh2eb67byXt2gnHAqUDPYofy9z0zvabQO0cr8giy2ru/AliwW6Ih64FDrOULB+zIqBLVi1+3+zksERYuZ78PpoOa4roXvraPtjGbpC26STvQ+QgAsOuuddKb+hK8cgrpcqc3bms4SUPDxTKTnEiAq1rwmS8I2tMzWEkLxoW157f8GrqMiJWMWQfuMblcbXvJIjbzZeiGKTtQWOeFjw5ARUithIvAFYoGsjNO0sY0mq5XsSmg+W2esI0KFyM0eGGQyMQcqxk+o6tX2JQGnqID5Y6mT1bhnNUGZTKEPI2caPhu86QoITMyDt1bHK79mofwkJlDJyflTHMWcIr3m1MymEpoyg==]]></Encrypt>
         *  </xml>
        */
        $appId = $xml -> AppId;
//         $encrypt = $xml -> Encrypt;
        
        $timestamp = isset($param['timestamp']) ? $param['timestamp'] : '';
        $nonce = isset($param['nonce']) ? $param['nonce'] : '';
        $msgSignature = isset($param['msg_signature']) ? $param['msg_signature'] : '';
        
        $msg = '';
        //解密
        $errCode = $this->_decryptMsg($param, $xmlData, $msg);
        // 成功,缓存
        if ($errCode == 0) {
        
            $xml = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            $InfoType = $xml -> InfoType;
            //component_verify_ticket 每十分钟推送的ticket，
            //unauthorized授权成功取消授权，authorized授权成功，updateauthorized更新授权
            //授权和更新授权相同 使用authorized
        
            /**
             *
             * 1、推送component_verify_ticket协议
             *  <xml>
             *  <AppId>第三方平台appid</AppId>
             *  <CreateTime>1413192605 </CreateTime>
             *  <InfoType>component_verify_ticket</InfoType>
             *  <ComponentVerifyTicket>Ticket内容</ComponentVerifyTicket>
             *  </xml>
             *
             * 9、推送授权相关通知
             * 当公众号对第三方平台进行授权、取消授权、更新授权后，微信服务器会向第三方平台方的授权事件接收URL
             * （创建第三方平台时填写）推送相关通知。
             * POST数据示例（取消授权通知）
             *  <xml>
             *  <AppId>第三方平台appid</AppId>
             *  <CreateTime>1413192760</CreateTime>
             *  <InfoType>unauthorized</InfoType>
             *  <AuthorizerAppid>公众号appid</AuthorizerAppid>
             *  </xml>
             *
             * POST数据示例（授权成功通知）
             *  <xml>
             *  <AppId>第三方平台appid</AppId>
             *  <CreateTime>1413192760</CreateTime>
             *  <InfoType>authorized</InfoType>
             *  <AuthorizerAppid>公众号appid</AuthorizerAppid>
             *  <AuthorizationCode>授权码（code）</AuthorizationCode>
             *  <AuthorizationCodeExpiredTime>过期时间</AuthorizationCodeExpiredTime>
             *  </xml>
             *
             *  POST数据示例（授权更新通知）
             *  <xml>
             *  <AppId>第三方平台appid</AppId>
             *  <CreateTime>1413192760</CreateTime>
             *  <InfoType>updateauthorized</InfoType>
             *  <AuthorizerAppid>公众号appid</AuthorizerAppid>
             *  <AuthorizationCode>授权码（code）</AuthorizationCode>
             *  <AuthorizationCodeExpiredTime>过期时间</AuthorizationCodeExpiredTime>
             *  </xml>
             */
            //当$InfoType为updateauthorized或authorized时，赋值为authorized
            //$InfoType == 'updateauthorized' && $InfoType = 'authorized';
            switch ($InfoType)
            {
                case 'component_verify_ticket'://获取得到component_verify_ticket
                    $ticket = (String) $xml -> ComponentVerifyTicket;
                    //时效性10分钟进行缓存
                    Yii::app() -> memcache -> set('component_verify_ticket', $ticket, 600);
                    break;
                case 'authorized'://得到用户授权事件，后去放到一张授权表里
                    //处理
                    $authorizer_appid = $xml -> AuthorizerAppid;
                    $authorization_code = $xml -> AuthorizationCode;
                    
                    $time = date('Y年m月d日  H:i:s',time());
                    $msg = '#############################';
                    $msg .= '['.$time.'] 用户授权 authorizer_appid:'.$authorizer_appid.' authorization_code:'.$authorization_code;
                    $msg .= '#############################';
                    
                    //判断缓存中存不存在当前公众号的授权access_token，存在清除防止失效
                    $key = $authorizer_appid.'_auth_access_token';
                    $authorizer_access_token = Yii::app()->memcache->get($key);
                    if(isset($authorizer_access_token) && !empty($authorizer_access_token))
                    {
                        Yii::app()->memcache->delete($key);
                    }
                    
                    Yii::log($msg, 'info', 'wechat.auth');
        
                    break;
                case 'updateauthorized'://更新授权
                    //处理
                    $authorizer_appid = $xml -> AuthorizerAppid;
                    $authorization_code = $xml -> AuthorizationCode;
                    
                    $time = date('Y年m月d日  H:i:s',$timestamp);
                    $msg = '#############################';
                    $msg .= '['.$time.'] 用户更新授权 authorizer_appid:'.$authorizer_appid.' authorization_code:'.$authorization_code;
                    $msg .= '#############################';
                    Yii::log($msg, 'info', 'wechat.updateauth');
        
                    $merchant = Merchant::model() -> find('wechat_thirdparty_authorizer_appid=:wechat_thirdparty_authorizer_appid', array(':wechat_thirdparty_authorizer_appid'=>$authorizer_appid));;
                    $merchant -> wechat_thirdparty_authorizer_refresh_time = date('Y-m-d H:i:s');
                    
                    //判断缓存中存不存在当前公众号的授权access_token，存在清除防止失效
                    $key = $authorizer_appid.'_auth_access_token';
                    $authorizer_access_token = Yii::app()->memcache->get($key);
                    if(isset($authorizer_access_token) && !empty($authorizer_access_token))
                    {
                        Yii::app()->memcache->delete($key);
                    }
                    
                    $merchant -> save();
        
                    break;
                case 'unauthorized'://得到用户取消授权事件
                    //处理
                    $authorizer_appid = $xml -> AuthorizerAppid;
                    
                    $time = date('Y年m月d日  H:i:s',$timestamp);
                    $msg = '#############################';
                    $msg .= '['.$time.'] 用户取消授权 authorizer_appid:'.$authorizer_appid;
                    $msg .= '#############################';
                    Yii::log($msg, 'info', 'wechat.unauth');
        
                    $merchant = Merchant::model() -> find('wechat_thirdparty_authorizer_appid=:wechat_thirdparty_authorizer_appid', array(':wechat_thirdparty_authorizer_appid'=>$authorizer_appid));
        
                    $merchant -> wechat_thirdparty_authorizer_if_auth = 3;//3取消授权
                    $merchant -> wechat_thirdparty_authorizer_cancel_time = date('Y-m-d H:i:s');
                    $suffix = '_auth_access_token';
                    
                    //判断缓存中存不存在当前公众号的授权access_token，存在清除防止失效
                    $key = $authorizer_appid.'_auth_access_token';
                    $authorizer_access_token = Yii::app()->memcache->get($key);
                    if(isset($authorizer_access_token) && !empty($authorizer_access_token))
                    {
                        Yii::app()->memcache->delete($key);
                    }
                    
                    $merchant -> save();
                    break;
            }
            return true;
        } else {
            echo $errCode;
            Yii::log('<<<不好啦，解密失败！错误码：'.$errCode.'>>>', 'error', 'wechat');
            return false;
        }
    }
    
    //公众号消息与事件接收方法
    public function actionEventMsg()
    {
        //对应公众号的appid,除去'/'
        $auth_appid = trim($_GET['appid'], '/');
        
        $timestamp = empty($_GET['timestamp']) ? '' : trim($_GET['timestamp']);
        $nonce = empty($_GET['nonce']) ? '' : trim($_GET['nonce']);
        $msg_signature = empty($_GET['msg_signature']) ? '' : trim($_GET['msg_signature']);
        
        $param = array(
            'timestamp'=>$timestamp,
            'nonce'=>$nonce,
            'msg_signature'=>$msg_signature,
        );
        
        $encryptMsg = file_get_contents('php://input');
        
        //检测参数是否完整,后期应对微信服务器IP进行检测
        if (empty($auth_appid) || empty($timestamp) || empty($nonce) || empty($msg_signature) || empty($encryptMsg))
        {
            exit('参数错误，非法访问！');
        }
        
        $msg = '';
        $errCode = $this->_decryptMsg($param, $encryptMsg, $msg);  //封装解密函数
        
        if($errCode == 0) //解密成功
        {
            //获取消息类型
            $xml = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            Yii::log('$msg:'.$msg, 'info', 'wechat.eventMsg');
            
            $MsgType = $xml -> MsgType;
            $Content = $xml -> Content;
            $FromUserName = $xml -> FromUserName;
            $ToUserName = $xml -> ToUserName;
            $CreateTime = $xml -> CreateTime;
            
            //测试原始公众号账号
            $tUserName = 'gh_3c884a361561';
            switch ($MsgType)
            {
                case 'event':
                    //事件类型
                    $event = $xml -> Event;
                    $eventKey = $xml -> EventKey;
                    
                    //1、模拟粉丝触发专用测试公众号的事件,待测试
                    if($FromUserName == $tUserName || $ToUserName == $tUserName){
                        $contentStr = $event.'from_callback';
                        $resultStr = sprintf($this -> textTpl, $FromUserName, $tUserName, time(), $contentStr);
                        $encryptMsg = '';
                        $errCode = $this->_encryptMsg($param, $resultStr, $encryptMsg); //封装加密函数
                        echo $encryptMsg;
                        break;
                    }
                    
                    switch ($event)
                    {
                        /***************************  微信认证事件推送开始  *************************/
//                         case 'qualification_verify_success':    //1.资质认证成功（此时立即获得接口权限）
                            
//                             break;
                            
//                         case 'qualification_verify_fail':       //2.资质认证失败
                        
//                             break;
                            
//                         case 'naming_verify_success':           //3.名称认证成功（即命名成功）
                        
//                             break;
                            
//                         case 'naming_verify_fail':              //4.名称认证失败（这时虽然客户端不打勾，但仍有接口权限）
                        
//                             break;
                            
//                         case 'annual_renew':                    //5.年审通知
                        
//                             break;
                            
//                         case 'verify_expired':                  //6.认证过期失效通知
                        
//                             break;
                        /***************************  微信认证事件推送结束  *************************/
                        
                        /***************************  接收事件推送开始  *************************/
                        case 'subscribe':                       //用户关注
                            $this -> dealSubscribe($param, $auth_appid, $xml);
                            break;
                            
                        case 'unsubscribe':                     //用户取消关注
                            $this -> dealUnSubscribe($param, $auth_appid, $xml);
                            break;
                        
                        case 'SCAN':                            //用户已关注时的事件推送
                            $this -> dealScan($param, $auth_appid, $xml);
                            break;
                        
                        case 'CLICK':                           //点击菜单拉取消息时的事件推送
                            $this -> dealClick($param, $auth_appid, $xml);
                            break;
                            
//                         case 'VIEW';                            //点击菜单跳转链接时的事件推送
//                            $this -> dealView($param, $auth_appid, $xml);
//                             break;
                            
//                         case 'LOCATION':                        //上报地理位置事件
//                             $this -> dealLocation($param, $auth_appid, $xml);
//                             break;
                        /***************************  接收事件推送结束  *************************/
                        
                        /***************************  卡券事件开始  *************************/
                        case 'card_pass_check':                 //审核通过事件推送
                            $this->dealCardCheck($param, $auth_appid, $xml);
                            break;
                            
                        case 'card_not_pass_check':             //审核不通过事件推送
                            $this->dealCardCheck($param, $auth_appid, $xml);
                            break;
                            
                        case 'user_get_card':                   //领取事件推送
                            $this -> dealUserGetCard($param, $auth_appid, $xml);
                            break;
                            
//                         case 'user_del_card':                   //删除事件推送
//                             $this -> dealUserDelCard($param, $auth_appid, $xml);
//                             break;
                            
//                         case 'user_consume_card':               //核销事件推送
//                             $this -> dealUserConsumeCard($param, $auth_appid, $xml);
//                             break;
                            
//                         case 'user_pay_from_pay_cell':          //买单事件推送
//                             $this -> dealUserPayFromPayCell($param, $auth_appid, $xml);
//                             break;
                            
//                         case 'user_view_card':                  //进入会员卡事件推送
//                             $this -> dealUserViewCard($param, $auth_appid, $xml);
//                             break;
                            
//                         case 'user_enter_session_from_card':    //从卡券进入公众号会话事件推送
//                             $this -> dealUserEnterSessionFromCard($param, $auth_appid, $xml);
//                             break;
                            
//                         case 'update_member_card':              //会员卡内容更新事件
//                             $this -> dealUpdateMemberCard($param, $auth_appid, $xml);
//                             break;
                            
//                         case 'card_sku_remind':                 //库存报警事件
//                             $this -> dealCardSkuRemind($param, $auth_appid, $xml);
//                             break;
                            
//                         case 'card_pay_order':                  //券点流水详情事件
//                             $this -> dealCardPayOrder($param, $auth_appid, $xml);
//                             break;
                            
                        /***************************  卡券事件结束  *************************/
                        
                        /***************************  摇一摇事件通知开始  *************************/
//                         case 'ShakearoundUserShake':
//                             $this -> dealShakearoundUserShake($param, $auth_appid, $xml);
//                             break;
                            
                        /***************************  摇一摇事件通知开始  *************************/
                        default:                                //Other...
                            break;
                    }
                    
                    break;
                case 'text':
                    
                    //2、模拟粉丝发送文本消息给专用测试公众号
                    if($Content == 'TESTCOMPONENT_MSG_TYPE_TEXT')
                    {
                        $contentStr = 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
                        $resultStr  = sprintf($this -> textTpl, $FromUserName, $tUserName, time(), $contentStr);
                        //加密
                        $encryptMsg = '';
                        $errCode = $this->_encryptMsg($param, $resultStr, $encryptMsg);
                        echo $encryptMsg;
                        break;
                    }
                    //3、模拟粉丝发送文本消息给专用测试公众号
                    if(strpos($Content, 'QUERY_AUTH_CODE:') !== false)
                    {
                        $ticket = trim(str_replace('QUERY_AUTH_CODE:', '', $Content),'$');
                        Yii::log('$ticket:'.$ticket, 'info', 'wechat');
                    
                        $res = json_decode(Component::getApiQueryAuth($ticket), true);
                    
                        $authorizer_access_token = $res['authorization_info']['authorizer_access_token'];
                        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$authorizer_access_token;
                    
                        Yii::log('$authorizer_access_token:'.$authorizer_access_token, 'info', 'wechat');
                    
                        Yii::log('Openid$FromUserName:'.$FromUserName, 'info', 'wechat');
                    
                        $data = array(
                            'touser'=>$FromUserName.'',
                            'msgtype'=>'text',
                            'text'=>array(
                                'content'=>$ticket.'_from_api',
                            ),
                        );
                        $res = https_post_data($url, $data);
                    
                        Yii::log('$end:'.$res, 'info', 'wechat');
                        break;
                    }
                    //不做任何处理
//                     echo 'success';break;
                    $this -> dealTextMsg($param, $auth_appid, $xml);
                    break;
            }
        
        }
        else //解密失败
        {
            echo $errCode;
            Yii::log('<<<不好啦，解密失败！错误码：'.$errCode.'>>>', 'error', 'wechat');
            exit;
        }
    }
    
    //处理关注事件
    private function dealSubscribe($param, $auth_appid, $xmlObj)
    {
        $FromUserName = $xmlObj -> FromUserName;
        $ToUserName = $xmlObj -> ToUserName;
        
        $userC = new UserC();
        $wechatC = new WechatC();
        $merchant = Merchant::model() -> find('wechat_thirdparty_authorizer_appid=:auth_id', array(':auth_id'=>$auth_appid));
        
		$result = json_decode($userC -> saveWechatFansInfo($merchant -> id, $FromUserName));
        if (isset($xmlObj->EventKey) && ! empty($xmlObj->EventKey)) {
            $tradeNoticeC = new TradeNoticeC();
            $arr = explode("_", trim($xmlObj->EventKey));
            $store_id = $arr['1'];
            $Notice_res = json_decode($tradeNoticeC->addTradeNoticeUser($store_id, $FromUserName), true);
        }
        $first = $wechatC->getFirstMsg($merchant -> id);
        if (!empty($first)) {
            if (!empty($first['content'])) {    //判断回复类型：文字信息
                $contentStr = $first['content'];
                $resultStr = sprintf($this->textTpl, $FromUserName, $ToUserName, time(), $contentStr);
            }else {          //回复类型：图文信息
                $material_id = $first['material_id'];
                $articles = $this->getMaterialToArticleXML($material_id, $FromUserName, $articleCount);
                
                $resultStr = sprintf($this->newsTpl, $FromUserName, $ToUserName, time(), $articleCount, $articles);
            }
            $errCode = $this->_encryptMsg($param, $resultStr, $encryptMsg);
            echo $encryptMsg;
        }
    }
    
    //处理取消关注事件
    private function dealUnSubscribe($param, $auth_appid, $xmlObj)
    {
        $FromUserName = $xmlObj -> FromUserName;
        $userC = new UserC();
        $merchant = Merchant::model() -> find('wechat_thirdparty_authorizer_appid=:auth_id', array(':auth_id'=>$auth_appid));
        
        $result = json_decode($userC -> cancelWechatSubscribe($merchant -> id, $FromUserName));
    }
    
    //处理文本消息事件
    private function dealTextMsg($param, $auth_appid, $xmlObj)
    {
        $FromUserName = $xmlObj -> FromUserName;
        $Content = trim($xmlObj -> Content);
        $ToUserName = $xmlObj -> ToUserName;
        
        $wechatC = new WechatC();
        $merchant = Merchant::model() -> find('wechat_thirdparty_authorizer_appid=:auth_appid', array(':auth_appid'=>$auth_appid));
        $word_obj = $wechatC -> getReplyKeyWord($merchant -> id, $Content);
        
        Yii::log('merchant_id:'.$merchant->id.',Content'.$Content, 'info', 'wechat.textMsg');
        
        if (!empty($word_obj)) {   //判断关键词列表存在该词
            if (!empty($word_obj['content'])) {  //判断回复类型：文字信息
                $contentStr = $word_obj['content'];

                $resultStr = sprintf($this -> textTpl, $FromUserName, $ToUserName, time(), $contentStr);
                Yii::log('$resultStr:'.$resultStr, 'info', 'wechat.textMsg.resultStr');
            }else {  //回复类型：图文信息
                $material_id = $word_obj['material_id'];
                
                $articles = $this -> getMaterialToArticleXML($material_id, $FromUserName, $articleCount);
                
                $resultStr = sprintf($this -> newsTpl, $FromUserName, $ToUserName, time(), $articleCount, $articles);
                Yii::log('$resultStr:'.$resultStr, 'info', 'wechat.textMsg.resultStr');
            }
            $errCode = $this->_encryptMsg($param, $resultStr, $encryptMsg);
            Yii::log('$encryptMsg:'.$encryptMsg, 'info', 'wechat.textMsg.encryptMsg');
            echo $encryptMsg;
        }else {                  //关键词列表不存在该词,判断是否回复首次关注的返回信息
            $first = $wechatC -> getFirstMsg($merchant -> id);
            if (!empty($first)) {
                if (!empty($first['content'])) {    //判断回复类型：文字信息
                    $contentStr = $first['content'];
                    Yii::log('contentStr：'.$contentStr, 'info', 'wechat.c');
                    $resultStr = sprintf($this -> textTpl, $FromUserName, $ToUserName, time(), $contentStr);
                    Yii::log('$resultStr:'.$resultStr, 'info', 'wechat.textMsg.resultStr');
                }else {          //回复类型：图文信息
                    $material_id = $first['material_id'];
                    $articles = $this -> getMaterialToArticleXML($material_id, $FromUserName, $articleCount);
                
                    $resultStr = sprintf($this -> newsTpl, $FromUserName, $ToUserName, time(), $articleCount, $articles);
                    Yii::log('$resultStr:'.$resultStr, 'info', 'wechat.textMsg.resultStr');
                }
                $errCode = $this->_encryptMsg($param, $resultStr, $encryptMsg);
                Yii::log('$encryptMsg:'.$encryptMsg, 'info', 'wechat.textMsg.encryptMsg');
                echo $encryptMsg;
            }
        }
    }
    
    //已关注,修改收款通知对象
    private function dealScan($param, $auth_appid, $xmlObj)
    {
        $FromUsername = $xmlObj->FromUserName;
        if(isset($xmlObj -> EventKey) && !empty($xmlObj -> EventKey)){
            $tradeNoticeC = new TradeNoticeC();
            $store_id = $xmlObj -> EventKey;
            $Notice_res = json_decode($tradeNoticeC -> addTradeNoticeUser($store_id, $FromUsername),true);
             
            Yii::log($Notice_res['errMsg'].'ppp','warning');
        }
    }
    
    //点击自定义菜单事件
    private function dealClick($param, $auth_appid, $xmlObj)
    {
        $wechatC = new WechatC();
        $merchant = Merchant::model() -> find('wechat_thirdparty_authorizer_appid=:auth_appid', array(':auth_appid'=>$auth_appid));
        $menu = $wechatC -> getMenuClickMsg($merchant -> id, $xmlObj -> EventKey);
        
        $FromUserName = $xmlObj->FromUserName;
        $ToUserName = $xmlObj->ToUserName;
        
        //回复
        if (!empty($menu)) {
            if ($menu->type == WQ_MENU_TYPE_WORD) {        //回复类型：文字信息$menu
                $contentStr = $menu->content;
                $resultStr = sprintf($this->textTpl, $FromUserName, $ToUserName, time(), $contentStr);
                
            }elseif ($menu->type == WQ_MENU_TYPE_PHOTO){       //回复类型：图文消息
                $material_id = $menu->content;
                $articles = $this -> getMaterialToArticleXML($material_id, $FromUserName, $articleCount);
                
                $resultStr = sprintf($this -> newsTpl, $FromUserName, $ToUserName, time(), $articleCount, $articles);
            }elseif ($menu->type == WQ_MENU_TYPE_SYSTEM || $menu->type == WQ_MENU_TYPE_WWW){            //回复链接网址
                $contentStr = $menu->content;
                $resultStr = sprintf($this->urlTpl, $FromUserName, $ToUserName, time(), $contentStr);
            }
        }
        $errCode = $this->_encryptMsg($param, $resultStr, $encryptMsg);
        echo $encryptMsg;
    }
    
    //卡券审核事件类型：卡券通过未通过审核公用
    private function dealCardCheck($param, $auth_appid, $xmlObj)
    {
        $cardId = $xmlObj -> CardId;
        $card_check = $xmlObj -> Event;
        $cardCouponsC = new CardCouponsC();
        $cardCouponsC -> editCheckStatus($cardId,$card_check);
    }
    
    //卡券领取事件推送
    private function dealUserGetCard($param, $auth_appid, $xmlObj)
    {
        $cardId = $xmlObj -> CardId;
        $code = $xmlObj -> UserCardCode;
        $friendUserName = $xmlObj -> FriendUserName;
        $IsGiveByFriend = $xmlObj -> IsGiveByFriend;
        $OldUserCardCodee = $xmlObj -> OldUserCardCode;
        $OuterId = $xmlObj -> OuterId;
        $FromUsername = $xmlObj -> FromUserName;
        $userC = new UserUC();
        $userC->changeCode($cardId, $code, $friendUserName, $IsGiveByFriend, $OldUserCardCodee, $OuterId, $FromUsername);
    }
    
    /**
     * 获取图文素材到图文itemsxml
     * @param unknown $material_id
     * @param unknown $FromUserName
     * @param unknown $count
     * @return string
     */
    private function getMaterialToArticleXML($material_id, $FromUserName, &$count=0)
    {
        $wechat = new WechatC();
        $materials = $wechat->getMaterial($material_id);
        $count = count($materials);
        
        $articleItems = '';
        
        foreach ($materials as $key => $value){
            $item = sprintf($this->newsItemTpl, $value['title'], $value['abstract'], IMG_GJ_LIST.$value['cover_img'], $value['link_content'].'&source=wechat'.'&fromUsername='.$FromUserName);
            $articleItems .= $item;
        }
        return $articleItems;
    }
    
    /**
     * 封装加密
     * @param array $param 加密所需参数数组msg_signature,timestamp,nonce
     * @param string $msgXml 所需加密的xml明文
     * @param string $encryptMsg 加密后的xml密文
     * @return number
     */
    private function _encryptMsg($param=array(), $msgXml='', &$encryptXml='')
    {
        $errCode = $this->wxBizMsg->encryptMsg($msgXml, $param['timestamp'], $param['nonce'], $encryptXml);
        return $errCode;
    }
    
    /**
     * 封装解密
     * @param array $param 解密所需参数数组msg_signature,timestamp,nonce
     * @param string $encryptXml 所需解密的xml密文
     * @param string $msgXml 解密后的xml明文
     */
    private function _decryptMsg($param=array(), $encryptXml='', &$msgXml='')
    {
        $xml = simplexml_load_string($encryptXml, 'SimpleXMLElement', LIBXML_NOCDATA);
    
        //组合postData xml
        $from_xml = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($from_xml, $xml->Encrypt);
    
        $errCode = $this->wxBizMsg->decryptMsg($param['msg_signature'], $param['timestamp'], $param['nonce'], $from_xml, $msgXml);
        return $errCode;
    }
    
}
