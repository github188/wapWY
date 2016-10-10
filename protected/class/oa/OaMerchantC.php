<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/5/12
 * Time: 16:05
 */
class OaMerchantC extends mainClass
{
    /**
     * @return string
     * 获取开通时间
     */
    public function findListStartTime(){
        $date = CHtml::listData(Merchant::model()->findAll(), 'id', 'gj_start_time');
        return json_encode($date);
    }

    /**
     * @return string
     * 获取到期时间
     */
    public function findListEndTime(){
        $date = CHtml::listData(Merchant::model()->findAll(), 'id', 'gj_end_time');
        return json_encode($date);
    }
}