<?php

/**
 * 预定
 *
 */
class BookController extends BooksController
{
    /**
     * 我的预定
     */
    public function actionBookList()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);
        
        $book = array();
        $user = new UserUC();

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        $res = $user->getBookList($user_id);
        $result = json_decode($res, true);
        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $book = $result['data'];
            }
        }

        $this->render('bookList', array(
            'book' => $book,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 预定操作
     */
    public function actionBookOperate()
    {
        $user = new UserUC();
        //获取参数 商户id,来源
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
            //获取商户信息
            $merchant = $user->getMerchantWithId($encrypt_id);
            $merchant_id = $merchant->id;
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        //初始化页面
        //人数
        $arr_people_num = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30);
        //日期
        $now_year = date('Y');
        $now_month = date('m');
        $arr_month = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $now_day = date('d');
        $arr_day = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
        $arr_time = array("08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30");
        //门店信息
        $get_store_id = isset($_GET['store_id']) ? $_GET['store_id'] : null;

        $store = array();
        $res = $user->getOnlineStore($merchant_id, $get_store_id);
        $result = json_decode($res, true);
        if ($result['status'] == ERROR_NONE) {
            if (!empty($result['data'])) {
                $store = $result['data'];
            }
        }

        if (isset($_POST['Book']) && !empty($_POST['Book'])) {
            $post = $_POST['Book'];
            $store_id = $post['store'];
            $book_name = $post['family_name'];
            $phone_num = $post['phone_num'];
            $people_num = $post['people_num'];
            $time = $now_year . '-' . $post['month'] . '-' . $post['day'] . ' ' . $post['time'] . ':00';
            $sex = $post['sex'];
            $remark = $post['remark'];

            $res = $user->bookOperate($user_id, $store_id, $book_name, $people_num, $time, $phone_num, $sex, $remark);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $record_id = $result['data'];
                    $this->redirect(Yii::app()->createUrl('mobile/book/book/bookWait', array(
                        'record_id' => $record_id,
                        'encrypt_id' => $encrypt_id
                    )));
                }
            }
        }

        $this->render('bookOperate', array(
            'store' => $store,
            'people_num' => $arr_people_num,
            'month_list' => $arr_month,
            'day_list' => $arr_day,
            'time_list' => $arr_time,
            'year' => $now_year,
            'month' => $now_month,
            'day' => $now_day,
            'store_id' => $get_store_id,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 等待预定界面
     */
    public function actionBookWait()
    {
        $user = new UserUC();
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        if (isset($_GET['record_id'])) {
            $record_id = $_GET['record_id'];

            $res = $user->getBookDetail($record_id);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $book = $result['data'];
                }
            } else {
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }
        } else {
            Yii::app()->user->setFlash('error', '读取失败');
        }

        $this->render('bookWait', array(
            'book' => $book,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 预定详情
     */
    public function actionBookDetail()
    {
        $user = new UserUC();

        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        if (isset($_GET['record_id'])) {
            $record_id = $_GET['record_id'];

            $res = $user->getBookDetail($record_id);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $book = $result['data'];
                }
            } else {
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }
        } else {
            Yii::app()->user->setFlash('error', '读取失败');
        }

        $this->render('bookDetail', array(
            'book' => $book,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 取消预订
     */
    public function actionBookCancel()
    {
        $user = new UserUC();

        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        if (isset($_GET['record_id'])) {
            $record_id = $_GET['record_id'];

            $res = $user->bookCancel($record_id);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $id = $result['data'];
                    $this->redirect(Yii::app()->createUrl('mobile/book/book/bookDetail', array(
                        'record_id' => $id,
                        'encrypt_id' => $encrypt_id
                    )));
                }
            }
        } else {
            Yii::app()->user->setFlash('error', '读取失败');
        }
    }
}