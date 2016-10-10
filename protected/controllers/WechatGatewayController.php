<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/class/wechat/Wechat.php';

class WechatGatewayController extends Controller
{

    public function init()
    {

    }

    public function actionIndex()
    {
        //跟踪http记录
        $encrypt_id = $_GET["account"];
        $merchant = Merchant::model()->find('encrypt_id=:encrypt_id', array(':encrypt_id' => $encrypt_id));

        $wechatObj = new wechatCallbackapiTest();

        if (isset($_GET['echostr'])) {
            $merchant_token = $merchant['wechat_token'];
            $wechatObj->valid($merchant_token);
        } else {
            //获取参数
            $id = $merchant->id;
            $wechatObj->responseMsg($id);
        }

    }
}

// function traceHttp(){
// 	logger("REMOTE_ADDR:".$_SERVER["REMOTE_ADDR"].((strpos($_SERVER["REMOTE_ADDR"], "101.226"))?" FROM WeiXin":" Unknown IP"));
// 	logger("QUERY_STRING:".$_SERVER["QUERY_STRING"]);
// }

// function logger($content){
// 	file_put_contents("log.html", date('Y-m-d H:i:s  ').$content."<br>", FILE_APPEND);
// }

class wechatCallbackapiTest
{
    public function valid($merchant_token)
    {
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature($merchant_token)) {
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature($merchant_token)
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $merchant_token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function responseMsg($id)
    {
        //判断当前 商户是否授权第三方 授权第三方则直接不处理 交由第三方接口处理
        $merchant = Merchant::model()->findByPk($id);
        if (isset($merchant->wechat_thirdparty_authorizer_if_auth) && $merchant->wechat_thirdparty_authorizer_if_auth == 2) {
            exit('success');
        }
        $wechat = new WechatC();
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {   //关键词回复
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);     //接收到的文字
            $postMsgType = $postObj->MsgType;         //接受的的类型
            $eventKey = $postObj->EventKey;         //菜单点击接受到的文字 
//             $time = time();
//             //文字信息XML
//             $textTpl = "<xml>
//                         <ToUserName><![CDATA[%s]]></ToUserName>
//                         <FromUserName><![CDATA[%s]]></FromUserName>
//                         <CreateTime>%s</CreateTime>
//                         <MsgType><![CDATA[%s]]></MsgType>
//                         <Content><![CDATA[%s]]></Content>
//                         <FuncFlag>0</FuncFlag>
//                         </xml>";

            $contentStr = "";  //初始化回复信息

            if ($postMsgType == "text") {   //接收到文字信息

                $word_obj = $wechat->getReplyKeyWord($id, $keyword);

                if (!empty($word_obj)) {   //判断关键词列表存在该词
                    if (!empty($word_obj['content'])) {  //判断回复类型：文字信息
                        $contentStr = $word_obj['content'];

                        $resultStr = $this->transmitText($postObj, $contentStr);
                    } else {  //回复类型：图文信息
                        $id = $word_obj['material_id'];
                        $contentStr = $this->getMaterial($id, $fromUsername);     //回复图文消息内容

                        $resultStr = $this->transmitNews($postObj, $contentStr);
                    }

                    echo $resultStr;
                } else {                  //关键词列表不存在该词,判断是否回复首次关注的返回信息
                    $first = $wechat->getFirstMsg($id);
                    if (!empty($first)) {
                        if (!empty($first['content'])) {    //判断回复类型：文字信息
                            $contentStr = $first['content'];
                            $resultStr = $this->transmitText($postObj, $contentStr);
                        } else {          //回复类型：图文信息
                            $id = $first['material_id'];
                            $contentStr = $this->getMaterial($id, $fromUsername);
                            $resultStr = $this->transmitNews($postObj, $contentStr);
                        }
                        echo $resultStr;
                    }
                }
            }

            if ($postMsgType == "event") {   //首次关注及菜单按钮点击事件
                if ($postObj->Event == "subscribe") {     // 首次关注信息回复

                    //获取用户信息
                    $userC = new UserC();
                    $result = json_decode($userC->saveWechatFansInfo($id, $fromUsername));
                    if ($result->status == ERROR_NONE) {
                        //成功获取用户信息并保存
                        Yii::log($result->data . 'kkkk', 'warning');
                    }
                    //扫描门店二维码关注,添加收款通知对象
                    if (isset($postObj->EventKey) && !empty($postObj->EventKey)) {
                        $tradeNoticeC = new TradeNoticeC();
                        $arr = explode("_", trim($postObj->EventKey));
                        $store_id = $arr['1'];
                        $Notice_res = json_decode($tradeNoticeC->addTradeNoticeUser($store_id, $fromUsername), true);
                    }
                    $first = $wechat->getFirstMsg($id);
                    if (!empty($first)) {
                        if (!empty($first['content'])) {    //判断回复类型：文字信息
                            $contentStr = $first['content'];
                            $resultStr = $this->transmitText($postObj, $contentStr);
                        } else {          //回复类型：图文信息
                            $id = $first['material_id'];
                            $contentStr = $this->getMaterial($id, $fromUsername);
                            $resultStr = $this->transmitNews($postObj, $contentStr);
                        }
                        echo $resultStr;
                    }

                    //关注+支付，赠送红包
                    $model = TempWechatOpenid::model()->find('openid =:openid and flag=:flag',
                        array(
                            ':openid' => $fromUsername,
                            ':flag' => FLAG_NO
                        ));
                    if (!empty($model)) {
                        $if_get = $model['if_get'];
                        if ($if_get == 1) {
                            $packet = new Packet();
                            $openid = $fromUsername;
                            $send = $packet->wxpacket($openid);
                            $result = $send->result_code;
                            if ($result == 'SUCCESS') {
                                $userC->changeIfget($openid);
                            }
                        }
                    }

                }
                //已关注,修改收款通知对象
                if ($postObj->Event == "SCAN") {

                    //获取用户信息
                    $userC = new UserC();
                    $result = json_decode($userC->saveWechatFansInfo($id, $fromUsername));
                    if ($result->status == ERROR_NONE) {
                        //成功获取用户信息并保存
                    }

                    if (isset($postObj->EventKey) && !empty($postObj->EventKey)) {
                        $tradeNoticeC = new TradeNoticeC();
                        $arr = explode("_", 'key_' . $postObj->EventKey);
                        $store_id = $arr['1'];
                        $Notice_res = json_decode($tradeNoticeC->addTradeNoticeUser($store_id, $fromUsername), true);
                    }
                }
                //取消关注事件推送
                if ($postObj->Event == "unsubscribe") {
                    $userC = new UserC();
                    $result = json_decode($userC->cancelWechatSubscribe($id, $fromUsername));
                }

                if ($postObj->Event == "CLICK") {         // 菜单点击事件
                    $menu = $wechat->getMenuClickMsg($id, $postObj->EventKey);

//             		//保存数据到数据魔方
//             		$statistics = new StatisticsForm();
//         			$statistics->updateMenu($menu->id,$id);

                    //回复
                    if (!empty($menu)) {
                        if ($menu->type == WQ_MENU_TYPE_WORD) {        //回复类型：文字信息$menu
                            $contentStr = $menu->content;
                            $resultStr = $this->transmitText($postObj, $contentStr);
                        } elseif ($menu->type == WQ_MENU_TYPE_PHOTO) {       //回复类型：图文消息
                            $id = $menu->content;
                            $contentStr = $this->getMaterial($id, $fromUsername);            //回复图文消息内容
                            $resultStr = $this->transmitNews($postObj, $contentStr);
                        } elseif ($menu->type == WQ_MENU_TYPE_SYSTEM || $menu->type == WQ_MENU_TYPE_WWW) {            //回复链接网址
                            $contentStr = $menu->content;
                            $resultStr = $this->transmitInternet($postObj, $contentStr);
                        }
                    }
                    echo $resultStr;
                }
//             	if ($postObj -> Event == "VIEW"){          //  回复链接网址
//             		$menu = Menu::model()->find('user_id=:user_id and menu_name=:menu_name', array(':user_id'=>$id, ':menu_name'=>$postObj->EventKey));
//             		if (!empty($menu)){
//             			if ()
//             		}
//             	}
                //卡券审核事件类型：卡券通过审核
                if ($postObj->Event == "card_pass_check") {
                    $cardId = $postObj->CardId;
                    $card_check = $postObj->Event;
                    $cardCouponsC = new CardCouponsC();
                    $cardCouponsC->editCheckStatus($cardId, $card_check);
                }

                //卡券审核事件类型：卡券通过审核
                if ($postObj->Event == "card_not_pass_check") {
                    $cardId = $postObj->CardId;
                    $card_check = $postObj->Event;
                    $cardCouponsC = new CardCouponsC();
                    $cardCouponsC->editCheckStatus($cardId, $card_check);
                }

                //卡券领取事件推送
                if ($postObj->Event == "user_get_card") {
                    $cardId = $postObj->CardId;
                    $code = $postObj->UserCardCode;
                    $friendUserName = $postObj->FriendUserName;
                    $IsGiveByFriend = $postObj->IsGiveByFriend;
                    $OldUserCardCodee = $postObj->OldUserCardCode;
                    $OuterId = $postObj->OuterId;
                    $fromUsername = $postObj->FromUserName;
                    Yii::log('card_id:' . $cardId, 'warning');
                    Yii::log('code:' . $code, 'error');
                    Yii::log('outerId:' . $OuterId, 'error');

                    $userUC = new MobileUserUC();
                    $userUC->changeCode($cardId, $code, $friendUserName, $IsGiveByFriend, $OldUserCardCodee, $OuterId, $fromUsername);
                }

                //买单事件推送
                if ($postObj->Event == "user_pay_from_pay_cell") {
                    $cardId = $postObj->CardId;
                    $fromUsername = $postObj->FromUserName;
                    $fromUsername = (array)$fromUsername;
                    if ($cardId == 'pXZ1iwxqENBrV-z7WtUvpPKaLeRQ') {
                        $post = array();
                        $post['touser'] = array($fromUsername[0], 'oXZ1iw8sVWBBd_AAAAAAAAAAAAAA', 'oXZ1iw8sVWBBd_BBBBBBBBBBBBBB');
                        $post['wxcard']['card_id'] = 'pXZ1iw9nDxhXQNsVE-JNIr_PwLIM';
                        $post['msgtype'] = 'wxcard';
                        $wechatc = new WechatC();

                        //获取token
                        $appid = 'wxec84afe11d9da7c4';
                        $secret = 'd41af6b0d7efdfb5fb925db13b205533';
                        $accessToken = Wechat::getAccessToken($appid, $secret);


                        $re = $wechatc->massSendGroud(
                            $accessToken,
                            urldecode(json_encode($post))
                        );
                        Yii::log('jiji' . $re . ' ' . json_encode($post), 'warning');
                    }
                }
            }
        }
    }


    //图文消息格式处理
    private function getMaterial($id, $fromUsername)
    {
        $wechat = new WechatC();
        $material = $wechat->getMaterial($id);

//     	$fp = fopen("log.txt", "a");
//     	flock($fp, LOCK_EX);
//     	fwrite($fp, 'dsaf'.json_encode($material));
//     	flock($fp, LOCK_UN);
//     	fclose($fp);

        foreach ($material as $key => $value) {
            $contentStr[] = array("Title" => $value['title'],
                "Description" => $value['abstract'],
                "PicUrl" => IMG_GJ_LIST . $value['cover_img'],
                "Url" => $value['link_content'] . '&source=wechat' . '&fromUsername=' . $fromUsername);
        }
        return $contentStr;
    }

    //菜单点击事件 回复文字信息
    private function transmitText($object, $content, $funcFlag = 0)
    {
        $textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>%d</FuncFlag>
					</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $funcFlag);
        return $resultStr;
    }

    //菜单点击事件 回复链接网址
    private function transmitInternet($object, $content, $funcFlag = 0)
    {
        $textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[view]]></MsgType>
						<Url><![CDATA[%s]]></Url>
						<FuncFlag>%d</FuncFlag>
					</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $funcFlag);
        return $resultStr;
    }

    //菜单点击事件回复 图文消息
    private function transmitNews($object, $arr_item, $funcFlag = 0)
    {
        //首条标题28字，其他标题39字
        if (!is_array($arr_item))
            return;

        $itemTpl = " <item>
			         	<Title><![CDATA[%s]]></Title>
			         	<Description><![CDATA[%s]]></Description>
			         	<PicUrl><![CDATA[%s]]></PicUrl>
			         	<Url><![CDATA[%s]]></Url>
    				 </item> ";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
				    	<ToUserName><![CDATA[%s]]></ToUserName>
				    	<FromUserName><![CDATA[%s]]></FromUserName>
				    	<CreateTime>%s</CreateTime>
				    	<MsgType><![CDATA[news]]></MsgType>
				    	<Content><![CDATA[]]></Content>
				    	<ArticleCount>%s</ArticleCount>
				    	<Articles>$item_str</Articles>
				    	<FuncFlag>%s</FuncFlag>
				    </xml>";

        $resultStr = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item), $funcFlag);
        return $resultStr;
    }


}