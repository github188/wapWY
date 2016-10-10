<?php
/**
 * 会员管理
 */
include_once(dirname(__FILE__).'/../mainClass.php');
class UserRule extends mainClass
{
    //会员卡管理列表
    /**
     * merchantId 商户id
     */
    public function GetMembershipCardList($merchantId)
    {
        $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
        $flag = 0;
        if(isset($merchantId) && empty($merchantId))
        {
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';  
                $flag = 1;
        }
        if($flag == 0)
        {
            $criteria = new CDbCriteria();
            $criteria->order = 'id desc';
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $usergrade = UserGrade::model()->findall($criteria);
            if(!empty($usergrade))
            {
                $data = array();
                foreach ($usergrade as $key => $value) 
                {   
                    $data[$key]['points_rule']          = $value['points_rule'];
                    $data[$key]['id']                   = $value['id'];
                    $data[$key]['name']                 = $value['name'];
                    $data[$key]['membership_card_name'] = $value['membership_card_name'];
                    $data[$key]['discount']             = $value['discount'];
                    $data[$key]['discount_illustrate']  = $value['discount_illustrate'];
                    $data[$key]['membercard_img']       = $value['membercard_img'];
                } 
                $result['status'] = ERROR_NONE;
                $result['data']   = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; 
            }
        }
        return json_encode($result);
    }
    
    //修改会员卡
    /**
     * $id   会员卡id
     * $merchantId  商户id
     * $membershipCardName 会员卡名称
     * $membercard_img 会员卡背景图
     */
    public function EditMembershipCard($id,$merchantId,$membershipCardName='',$membercard_img='')
    {
        $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
        $flag = 0;
        if(isset($merchantId) && empty($merchantId))
        {
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';  
                $flag = 1;
        }
        if(isset($id) && empty($id))
        {
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';  
                $flag = 1;
        }
        if($flag == 0)
        {
            $userGrade = UserGrade::model()->find('id=:id and merchant_id=:merchant_id',array(':id'=>$id,':merchant_id'=>$merchantId));
            if(!empty($userGrade)) 
            {
                $userGrade -> membership_card_name = $membershipCardName;
                if(isset($membercard_img) && !empty($membercard_img)){
                	$userGrade -> membercard_img = $membercard_img;
                }
                if($userGrade -> update()){
                	$result['status'] = ERROR_NONE; 
                }else{
                	$result['status'] = ERROR_SAVE_FAIL;
                	$result['errMsg'] = '保存失败';
                }    
                               
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; 
            }
        }
        return json_encode($result);
    }
    
    //返回会员信息
    /**
     * $id   会员卡id
     * $merchantId  商户id
     */
    public function Back($id,$merchantId)
    {
        $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
        $flag = 0;
        if(isset($merchantId) && empty($merchantId))
        {
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';  
                $flag = 1;
        }
        if(isset($id) && empty($id))
        {
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';  
                $flag = 1;
        }
        if($flag == 0)
        {
            $userGrade = UserGrade::model()->find('id=:id and merchant_id=:merchant_id',array(':id'=>$id,':merchant_id'=>$merchantId));
            if(!empty($userGrade))
            {
                $data = array();
                $data['membership_card_name'] = isset($userGrade -> membership_card_name) ? $userGrade -> membership_card_name : '';
                $data['name']                 = isset($userGrade -> name) ? $userGrade -> name : '';
                $result['status'] = ERROR_NONE;
                $result['data']   = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }
    
    //输入手机号查找会员等级并显示信息
    /**
     * tel 手机号
     * 
     */
    public function UserSearch($tel)
    {
        $user = User::model()->find('account=:account',array(':account'=>$tel));
        $data = array();
        if($user)
        {
            $data['name'] = $user->name;
            $usergrade    = UserGrade::model()->find('id=:id and flag=:flag',array(':id'=>$user->membershipgrade_id,':flag'=>FLAG_NO));
            $data['grade_name'] = $usergrade->name;
            $result['status'] = ERROR_NONE;
            $result['data']   = $data;
        } else {
            $result['status'] = ERROR_NO_DATA;
            $data['errMsg'] = '无此数据';
            $result['data']   = $data;
        }
        return json_encode($result);
    }
    
}