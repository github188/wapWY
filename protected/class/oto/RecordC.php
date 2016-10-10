<?php
/**
 * 预定管理
 */
include_once(dirname(__FILE__).'/../mainClass.php');
class RecordC extends mainClass
{
    //预定列表
    /**
     * operatorId  操作员号
     * status  预约状态
     * time   今日时间
     * start  开始时间
     * end   结束时间
     */
    public function BookRecordList($operatorId,$status='',$time='',$start='',$end='',$phone='')
    {
        //返回结果
        $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
        $flag   = 0;
        if(isset($operatorId) && empty($operatorId))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数operatorid缺失';
            $flag = 1;
        }
        if($flag == 0)
        {
            $criteria = new CDbCriteria();
            $criteria -> order = 'id desc';
            //按时间搜索
            if(!empty($start) && !empty($end))
            {
                $criteria->addBetweenCondition('book_time', $start, $end);
            }  
            //今日预约
            if(!empty($time) && $time == 'today')
            {
                $criteria->addBetweenCondition('book_time', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'));
            }
            //按手机号搜索
            if(!empty($phone))
            {
                $criteria->addCondition("book_information like '%$phone%'");
            }
            if(!empty($status))
            {
                //预定状态搜索
                switch ($status)
                {
                    case BOOK_RECORD_STATUS_WAIT:
                        $criteria->addCondition('status=:status');
                        $criteria -> params [':status'] = $status;
                        break;
                    case BOOK_RECORD_STATUS_ACCEPT:
                        $criteria->addCondition('status=:status');
                        $criteria -> params [':status'] = $status;
                        break;  
                    case BOOK_RECORD_STATUS_REFUSE:
                        $criteria->addCondition('status=:status');
                        $criteria -> params [':status'] = $status;
                        break;
                    case BOOK_RECORD_STATUS_ARRIVE:
                        $criteria->addCondition('status=:status');
                        $criteria -> params [':status'] = $status;
                        break;
                    case BOOK_RECORD_STATUS_CANCEL:
                        $criteria->addCondition('status=:status');
                        $criteria -> params [':status'] = $status;
                        break;
                }  
            }
            $operator = Operator::model()->find('id=:id',array(':id'=>$operatorId));
            $storeid  = $operator->store_id;
            $criteria->addCondition('store_id=:store_id');
            $criteria -> params [':store_id'] = $storeid;
            //分页
            $pages = new CPagination(BookRecord::model()->count($criteria));
            $pages->pageSize = Yii::app() -> params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;
            
            $bookrecord = BookRecord::model()->findall($criteria);
            if($bookrecord)
            {                
                $data   = array();                
                foreach ($bookrecord as $key => $value) 
                {
                    $data[$key]['book_time']        = $value['book_time'];
                    $data[$key]['cancel_time']      = $value['cancel_time'];
                    $data[$key]['create_time']      = $value['create_time'];
                    $data[$key]['deal_time']        = $value['deal_time'];
                    $data[$key]['arrive_time']      = $value['arrive_time'];
                    $data[$key]['book_information'] = $value['book_information'];
                    $data[$key]['status']           = $value['status'];
                    $data[$key]['remark']           = $value['remark'];
                    $data[$key]['id']               = $value['id'];                   
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
    
    //计算条数
    /**
     * $operatorId  操作id
     */
    public function Sum($operatorId)
    {
        //返回结果
        $result = array('status'=>1,'errMsg'=>'null','data'=>'null','wait'=>'null','accept'=>'null','refuse'=>'null','arrive'=>'null','cancel' => 'null');
        $flag   = 0;
        if(isset($operatorId) && empty($operatorId))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数operatorid缺失';
            $flag = 1;
        }
        if($flag == 0)
        {
            $criteria = new CDbCriteria();
            $operator = Operator::model()->find('id=:id',array(':id'=>$operatorId));
            $storeid  = $operator->store_id;
            $criteria->addCondition('store_id=:store_id');
            $criteria -> params [':store_id'] = $storeid;
            $bookrecord = BookRecord::model()->findall($criteria);
            if($bookrecord)
            {
                $criteria1 = new CDbCriteria();
                $criteria1->addCondition('store_id=:store_id');
                $criteria1 -> params [':store_id'] = $storeid;
                $count  = BookRecord::model()->count($criteria1);
                $data   = array();
                $wait   = 0;
                $accept = 0;
                $refuse = 0;
                $arrive = 0;
                $cancel = 0;
                $today  = 0;
                foreach ($bookrecord as $key => $value) 
                {
                    
                    if($value['status'] == BOOK_RECORD_STATUS_WAIT)
                    {
                        $wait = $wait + 1;
                    }
                    if($value['status'] == BOOK_RECORD_STATUS_ACCEPT)
                    {
                        $accept = $accept + 1;
                    }
                    if($value['status'] == BOOK_RECORD_STATUS_REFUSE)
                    {
                        $refuse = $refuse + 1;
                    }
                    if($value['status'] == BOOK_RECORD_STATUS_ARRIVE)
                    {
                        $arrive = $arrive + 1;
                    }
                    if($value['status'] == BOOK_RECORD_STATUS_CANCEL)
                    {
                        $cancel = $cancel + 1;
                    }
                    if($value['book_time'] >= date('Y-m-d 00:00:00') && $value['book_time'] <= date('Y-m-d 23:59:59'))
                    {
                        $today = $today + 1;
                    }
                }
                $result['status'] = ERROR_NONE;                 
                $result['count']  = $count;
                $result['wait']   = $wait;
                $result['accept'] = $accept;
                $result['refuse'] = $refuse;
                $result['arrive'] = $arrive;
                $result['cancel'] = $cancel;
                $result['today']  = $today;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }  
        }
        return json_encode($result);
    }


    //改状态
    /**
     * id   预约订单id
     * accept  预约状态
     */
    public function Accept($accept,$id)
    {
        //返回结果
        $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
        $flag   = 0;
        if(isset($accept) && empty($accept))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数accept缺失';
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
            $bookrecord = BookRecord::model()->find('id=:id',array(':id'=>$id));
            if($bookrecord != $accept)
            {
                //待确认
                if($accept == BOOK_RECORD_STATUS_WAIT)
                {
                    $bookrecord->status = BOOK_RECORD_STATUS_WAIT;
                }
                //已接单
                if($accept == BOOK_RECORD_STATUS_ACCEPT)
                {
                    $bookrecord->status = BOOK_RECORD_STATUS_ACCEPT;
                }
                //已拒单
                if($accept == BOOK_RECORD_STATUS_REFUSE)
                {
                    $bookrecord->status = BOOK_RECORD_STATUS_REFUSE;
                }
                //已到店
                if($accept == BOOK_RECORD_STATUS_ARRIVE)
                {
                    $bookrecord->status = BOOK_RECORD_STATUS_ARRIVE;
                }
                //已取消
                if($accept == BOOK_RECORD_STATUS_CANCEL)
                {
                    $bookrecord->status = BOOK_RECORD_STATUS_CANCEL;
                }
                if($bookrecord->update())
                {
                    $result['status'] = ERROR_NONE;
                }
            } else {
                $result['status'] = ERROR_REQUEST_FAIL;
                $result['errMsg'] = '状态已更改';
            }
        }
        return json_encode($result);
    }
    
    //添加预定
    /**
     * $operatorId  操作id
     * booktime   预约时间
     * time       时间
     * name       姓名
     * tel        电话
     * sex        性别
     * sum       人数
     * remark    备注
     */
    public function AddRecord($operatorId,$booktime,$time,$name,$tel,$sex,$sum,$remark)
    {
        //返回结果
        $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
        $flag   = 0;
        if(isset($operatorId) && empty($operatorId))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数operatorid缺失';
            $flag = 1;
        }
        if(isset($booktime) && empty($booktime))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数booktime缺失';
            $flag = 1;
        }
        if(isset($time) && empty($time))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数time缺失';
            $flag = 1;
        }
        if(isset($name) && empty($name))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数name缺失';
            $flag = 1;
        }
        if(isset($tel) && empty($tel))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数tel缺失';
            $flag = 1;
        }
        if(isset($sex) && empty($sex))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数sex缺失';
            $flag = 1;
        }
        if(isset($sum) && empty($sum))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数sum缺失';
            $flag = 1;
        }
        if(isset($remark) && empty($remark))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数remark缺失';
            $flag = 1;
        }
        if($flag == 0)
        {
            $operator = Operator::model()->find('id=:id',array(':id'=>$operatorId));
            if($operator)
            {
                $bookrecord                   = new BookRecord();
                $bookrecord->store_id         = $operator->store_id;
                $bookrecord->book_time        = $booktime;
                $bookrecord->create_time      = new CDbExpression('now()');
                $bookrecord->book_information = $name.'@'.$tel.'@'.$sex.'@'.$sum.'@'.$time;
                $bookrecord->operator_id      = $operatorId;
                $bookrecord->remark           = $remark;
                if($bookrecord->save())
                {
                    $result['status'] = ERROR_NONE;
                } else {
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
}

