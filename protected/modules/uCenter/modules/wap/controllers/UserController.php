<?php
class UserController extends WapController{
    public $layout='user';
    /**
     * 会员登录
     */
    public function actionLogin() {
//        spl_autoload_unregister(array('YiiBase','autoload'));
//        include($_SERVER['DOCUMENT_ROOT'].'/protected/class/mainClass.php');
//        spl_autoload_register(array('YiiBase','autoload'));
        if (isset($_POST['account']) && $_POST['account'] && isset($_POST['pwd']) && $_POST['pwd']) {
            $model = new userClass();
            $rs = $model->login('13095920688', '96e79218965eb72c92a549dd5a330112');
            if ($rs == OPERATE_RESULT_ERROR_NULL) {
                Yii::app()->session['userId'] = $rs['data']['id'];
            }
        }
        $this->render('login');
    }

    /**
     * 会员注册
     */
    public function actionRegister() {

    }

}