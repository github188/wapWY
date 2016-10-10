<?php
class IndexController extends WapController{
    /**
     * 用户中心首页
     */
    public function actionIndex() {
        $this->render('index');
    }

}