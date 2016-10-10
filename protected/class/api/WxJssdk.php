<?php
include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * 微信jssdk数据库相关操作
 */
class WxJssdk extends mainClass{

    
    public function getJsapiTicket($merchant_id){   
        $merchant = Merchant::model()->findByPk($merchant_id);
//         $cmd = Yii::app()->db->createCommand();
// 		$cmd->select('*');
// 		$cmd->from(array('wq_merchant'));
// 		$cmd->where(array(
// 				'AND',
// 				'id = :merchant_id',
// 		));
// 		$cmd->params = array(
// 			':merchant_id' => $merchant_id,
// 		);
//     	$merchant = $cmd->queryRow();
		return $merchant;
    }
    
    public function getWxAccessToken($merchant_id){
        $merchant = Merchant::model()->findByPk($merchant_id);
//         $cmd = Yii::app()->db->createCommand();
// 		$cmd->select('*');
// 		$cmd->from(array('wq_merchant'));
// 		$cmd->where(array(
// 				'AND',
// 				'id = :merchant_id',
// 		));
// 		$cmd->params = array(
// 			':merchant_id' => $merchant_id,
// 		);
//     	$merchant = $cmd->queryRow();
		return $merchant;
    }
    
    /*
     * 保存微信jsapiticket 有效时间7200秒
     */
    public function setJsapiTicket($user_id, $jsapi_ticket_json)
    {
            $result = array();
            try {
                    $model = Merchant::model()->findByPk($user_id);
                    if (empty($model)) {
                            $result['status'] = ERROR_NO_DATA;
                            throw new Exception('读取商户信息出错');
                    }
                    $model['jsapi_ticket_json'] = $jsapi_ticket_json;

                    if ($model->save()) {
                            $result['status'] = ERROR_NONE; //状态码
                            $result['errMsg'] = ''; //错误信息
                            $result['data'] = '';
                    }else {
                            $result['status'] = ERROR_SAVE_FAIL; //状态码
                            $result['errMsg'] = '数据保存失败'; //错误信息
                            $result['data'] = '';
                    }
            } catch (Exception $e) {
                    $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
                    $result['errMsg'] = $e->getMessage(); //错误信息
            }
            return json_encode($result);
    }

    /*
     * 保存微信accesstoken 有效时间7200秒
     */

    public function setWxAccessToken($user_id, $access_token_json)
    {
            $result = array();
            try {
                    $model = Merchant::model()->findByPk($user_id);
                    if (empty($model)) {
                            $result['status'] = ERROR_NO_DATA;
                            throw new Exception('读取商户信息出错');
                    }
                    $model['access_token_json'] = $access_token_json;

                    if ($model->save()) {
                            $result['status'] = ERROR_NONE; //状态码
                            $result['errMsg'] = ''; //错误信息
                            $result['data'] = '';
                    }else {
                            $result['status'] = ERROR_SAVE_FAIL; //状态码
                            $result['errMsg'] = '数据保存失败'; //错误信息
                            $result['data'] = '';
                    }
            } catch (Exception $e) {
                    $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
                    $result['errMsg'] = $e->getMessage(); //错误信息
            }
            return json_encode($result);
    }
}