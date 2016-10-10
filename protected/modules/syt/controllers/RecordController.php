<?php
/**
 * 预定管理
 */
class RecordController extends sytController
{
    /**
     * 预定管理
     */
    public function actionBookRecord()
    {
        $ret        = new RecordC();
        $operatorId = Yii::app()->session['operator_id'];
        $end = '';
        $start = '';
        $record = '';
        $count  = '';
        $wait   = '';
        $accept = '';
        $refuse = '';
        $arrive = '';
        $cancel = '';
        $today  = '';
        if (isset($_GET['Time']) && !empty($_GET['Time'])) {
        	$tmp = explode(" - ", $_GET['Time']);
        	$start = $tmp[0];
        	$end = $tmp[1];
        }
        //预定状态搜索
        $status     = isset($_GET['status']) ? $_GET['status'] : '';        
        $time       = isset($_GET['time']) ? $_GET['time'] : '';
        $phone      = isset($_GET['phone']) ? $_GET['phone'] : '';
        $model      = $ret->BookRecordList($operatorId,$status,$time,$start,$end,$phone);
        $book       = json_decode($model,true);
        if($book['status'] == ERROR_NONE)
        {
            $record = $book['data'];            
        } else {
            $record = '';            
        }
        //计算条数
        $s   = $ret->Sum($operatorId);
        $sum = json_decode($s,true);
        if($sum['status'] == ERROR_NONE)
        {
            $count  = $sum['count'];
            $wait   = $sum['wait'];
            $accept = $sum['accept'];
            $refuse = $sum['refuse'];
            $arrive = $sum['arrive'];
            $cancel = $sum['cancel'];
            $today  = $sum['today'];
        } else {            
            $count  = '';
            $wait   = '';
            $accept = '';
            $refuse = '';
            $arrive = '';
            $cancel = '';
            $today  = '';
        }
        $this->render('bookRecord',array('record'=>$record,'count'=>$count,'wait'=>$wait,'accept'=>$accept,'refuse'=>$refuse,'arrive'=>$arrive,'cancel'=>$cancel,'today'=>$today,'pages' => $ret->page));
    }
    
    /***
     * 已接单
     */
    public function actionAccept()
    {
        $accept     = isset($_GET['accept']) ? $_GET['accept'] : '';
        $id         = isset($_GET['id']) ? $_GET['id'] : '';
        $ret        = new RecordC();
        $rat        = $ret->Accept($accept,$id);
        $bookrecord = json_decode($rat,true);
        if($bookrecord['status'] == ERROR_NONE)
        {
            $url = Yii::app()->createUrl('syt/record/bookRecord');
            echo "<script>alert('修改成功');window.location.href='$url'</script>";
        } else {
            $url = Yii::app()->createUrl('syt/record/bookRecord');
            echo "<script>alert('修改失败');window.location.href='$url'</script>";
        }
    }
    
    /**
     * 添加预定
     */
    public function actionAddRecord()
    {        
        $operatorId = Yii::app()->session['operator_id'];
        $flag = 0;
        if(isset($_POST) && $_POST)
        {
            if(empty($_POST['book_time']))
            {
                Yii::app() -> user->setFlash('book_time','请选择预约时间');
                $flag = 1;
            }            
            if(empty($_POST['name']))
            {
                Yii::app() -> user->setFlash('name','请输入姓名');
                $flag = 1;
            }
            if(empty($_POST['tel']))
            {
                Yii::app() -> user->setFlash('tel','请输入电话');
                $flag = 1;
            }
            if(empty($_POST['sex']))
            {
                Yii::app() -> user->setFlash('sex','请选择性别');
                $flag = 1;
            }
            if(empty($_POST['sum']))
            {
                Yii::app() -> user->setFlash('sum','请输入人数');
                $flag = 1;
            }
            if(empty($_POST['remark']))
            {
                Yii::app() -> user->setFlash('remark','请输入备注');
                $flag = 1;
            }
            if (!preg_match('/13[0-9]{9}|15[0|1|2|3|5|6|7|8|9]\d{8}|18[0|5|6|7|8|9]\d{8}/',$_POST['tel']) && !preg_match('/^(0?(([1-9]\d)|([3-9]\d{2}))-?)?\d{7,8}$/',$_POST['tel'])) {
            	Yii::app() -> user->setFlash('tel','请输入正确的电话或手机号');
                $flag = 1;
            }
            if($flag == 0)
            {
                $booktime = $_POST['book_time'];
                $time     = $_POST['book_time'];
                $name     = $_POST['name'];
                $tel      = $_POST['tel'];
                $sex      = $_POST['sex'];
                $sum      = $_POST['sum'];
                $remark   = $_POST['remark'];
                $ret = new RecordC();
                $rat = $ret->AddRecord($operatorId,$booktime,$time,$name,$tel,$sex,$sum,$remark);
                $add = json_decode($rat,true);
                if($add['status'] == ERROR_NONE)
                {
                    $url = Yii::app()->createUrl('syt/Record/bookRecord');
                    echo "<script>alert('添加成功');parent.location.href='$url'</script>";
                } else {
                    $url = Yii::app()->createUrl('syt/Record/addRecord');
                    echo "<script>alert('添加失败');window.location.href='$url'</script>";
                }
            }
        }
        $this->render('addRecord');
    }
}

