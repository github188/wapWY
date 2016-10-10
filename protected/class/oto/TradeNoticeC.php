<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');

include_once $_SERVER['DOCUMENT_ROOT'].'/protected/class/wechat/Wechat.php';

/**
 * 微信收款通知表
 */
class TradeNoticeC extends mainClass
{
    private static $_instance = NULL;
    
    /**
     * 单例模式
     */
    public static function getInstance(){
        if (!self::$_instance instanceof self){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 添加微信收款通知对象
     * @param string $store_id
     * @param string $avatar
     * @param string $nickname
     * @param string $wechat_open_id
     */
    public function addTradeNoticeUser($store_id,$wechat_open_id){
        $result = array ();
        try {
            if (!empty($store_id) && !empty($wechat_open_id)){
                $transaction = Yii::app()->db->beginTransaction();
                //根据门店id 查找通知对象是否存在
                $notice_user = TradeNoticeUser::model()->find('wechat_open_id =:wechat_open_id and store_id =:store_id and flag =:flag',array(
                    'wechat_open_id'=> $wechat_open_id,
                    ':store_id' => $store_id,
                    ':flag' => FLAG_NO
                ));
                /*
                //根据门店id,获取商户id
                $store_ob = Store::model()->findByPk($store_id);
                $merchant_id = $store_ob->merchant_id;
                //根据商户id和wechat openid 查找用户
                $user_ob = User::model() -> find('wechat_id =:wechat_id and merchant_id =:merchant_id',array(
                    ':wechat_id' => $wechat_open_id,
                    ':merchant_id' => $merchant_id
                ));
                if (empty($user_ob)){
                    throw new Exception('查找用户信息失败');
                }
                $avatar = $user_ob->wechat_headimgurl;
                if (empty($avatar)){    //头像为空
                    $avatar = GJ_STATIC_IMAGES.'face_man.png';
                    if (!empty($user_ob->wechat_sex)){
                        if ($user_ob->wechat_sex == '女'){
                            $avatar = GJ_STATIC_IMAGES.'face_woman.png';
                        }
                    }
                }
                $nickname = $user_ob->wechat_nickname;
                */
                
                //使用玩券公众号获取用户信息（头像，昵称）
                $wechat_appid = 'wxec84afe11d9da7c4';
                $wechat_appsecret = 'd41af6b0d7efdfb5fb925db13b205533';
                //获取access_token
                $wechat = new WechatC();
                $access_token = Wechat::getAccesstoken($wechat_appid, $wechat_appsecret);
                
                //获取微信用户信息
                $user_info_api = $wechat->getUserInfos($access_token, $wechat_open_id);
                $user_info_arr = json_decode($user_info_api, true);
                $nickname = $user_info_arr['nickname']; //昵称
                $avatar = $user_info_arr['headimgurl']; //头像
                
                if (!empty($notice_user)){//通知对象存在
                    $notice_user['avatar'] = $avatar;
                    $notice_user['nickname'] = $nickname;
                    if (!$notice_user->update()){
                        throw new Exception('修改失败');
                    }
                }else{//创建新通知对象 
                    $model = new TradeNoticeUser();
                    $model -> store_id = $store_id;
                    $model -> wechat_open_id = $wechat_open_id;
                    $model -> avatar = $avatar;
                    $model -> nickname = $nickname;
                    $model -> create_time = date("Y-m-d H:i:s");
                    if ($model->save ()) {
                        $result ['data']['id']=$model->id;
                    }else{
                        throw new Exception('保存失败');
                    }
                }
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['errMsg'] = ''; // 错误信息
                $transaction -> commit();
            }else{
                throw new Exception('参数为空');
            }
        }catch (Exception $e){
            $transaction->rollback(); //如果操作失败, 数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    /**
     * 删除收款通知对象数据
     * @param unknown $id
     * @throws Exception
     * @return string
     */
    public function deleteTradeNoticeUser($id){
        $result = array();
        try {
            $model = TradeNoticeUser::model()->findByPk($id);
            if (empty($model)){
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('删除的数据不存在');
            }
            //修改删除标识
            $model['flag'] = FLAG_YES;
            
            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = '';
            }else {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return  json_encode($result);
    }
    
    /**
     * 收款通知对象列表
     */
    public function getTradeNoticeList($store_id = NULL,$storeId_arr = NULL){
        $result = array();
        try {
            if (!empty($store_id) || !empty($storeId_arr)){
                $criteria = new CDbCriteria();
                $criteria->addCondition("flag = :flag");
                $criteria->params[':flag'] = FLAG_NO;
                if (empty($storeId_arr)){
                    $criteria->addCondition("store_id = :store_id");
                    $criteria->params[':store_id'] = $store_id;
                }else{
                    $criteria->addInCondition('store_id',$storeId_arr);
                }
                $criteria->order = 'create_time DESC';
                //分页
                $count = TradeNoticeUser::model()->count($criteria);
                $pages = new CPagination($count);
                $pages -> pageSize = Yii::app()->params['perPage'];
                $pages->applyLimit($criteria);
                
                $model = TradeNoticeUser::model()->findAll($criteria);
                
                if (!empty($model)){
                    $data = array();
                    $storeC = $this->getStoreC();
                    $i = 1;
                    foreach ($model as $key => $val){
                        $data['list'][$key]['index'] = $i++;
                        $data['list'][$key]['id'] = $val['id'];
                        $data['list'][$key]['store_id'] = $val['store_id'];
                        $data['list'][$key]['avatar'] = $val['avatar'];
                        $data['list'][$key]['nickname'] = $val['nickname'];
                        $data['list'][$key]['wechat_open_id'] = $val['wechat_open_id'];
                        $data['list'][$key]['create_time'] = $val['create_time'];
                        $storedetail = json_decode($storeC->getStoreDetails($val['store_id']),true);
                        if ($storedetail['status'] == ERROR_NONE){
                            $data['list'][$key]['store_name'] = $storedetail['data']['name'];
                        }
                    }
                }else{
                    $data['list'] = array();
                }
                $this->page = $pages;
                $result['count'] = $count;
                $result['data'] = $data;        //数据
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = '';         //错误信息
            }
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    /**
     * 获取微信带参二维码ticket
     * $store_id 场景id,32位非0整型,门店id
     */
    public function getQrcodeTicket($access_token,$store_id){
        $data = array(
            "expire_seconds" => 2592000,    //二维码有效时间:临时最多30天2592000
            "action_name" => "QR_SCENE",    //二维码类型:临时
            "action_info" => array(
                "scene" => array(
                    "scene_id" => $store_id
                )
            )
        );
        $data_str = json_encode($data);
        $get_ticket_url = WECHAT_API_URL."qrcode/create?access_token=".$access_token;
        $ch = curl_init($get_ticket_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_str))
        );
        $result = json_decode(curl_exec($ch),true);   //获取ticket和取码url
        if (curl_errno($ch) || !empty($result['errcode'])){
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        $ticket = $result['ticket'];
        return $ticket;
    }
    
    /**
     * 获取门店通知对象数量
     */
    public function getStoreNoticeCount($store_id){
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition("store_id = :store_id");
            $criteria->params[':store_id'] = $store_id;
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag'] = FLAG_NO;
            $count = TradeNoticeUser::model()->count($criteria);
            $result['count'] = $count;        //数据
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = '';         //错误信息
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    public function getStoreC(){
        return StoreC::getInstance();
    }

}