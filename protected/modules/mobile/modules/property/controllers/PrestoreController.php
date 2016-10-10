<?php
class PrestoreController extends PropertyController{
    public function init()
    {
        parent::init();
        $encrypt_id = $this->getEncryptId();
        $this->checkLogin($encrypt_id);
    }

    /**
     * Ô¤´æ½ð¶î³äÖµ
     */
    public function actionAddPrestoreMoney() {
        $user_id =$this->getUserId();
        $encrypt_id = $this->getEncryptId();

        if(isset($_POST['money']) && $_POST['money']) {
            $info['money'] = $_POST['money'];
            $prestoreOrderC = new PrestoreOrderC();
            $result = json_decode($prestoreOrderC->addPrestoreOrder($info, $encrypt_id, $user_id));
            if ($result->status == APPLY_CLASS_SUCCESS) {
                //È¥Ö§¸¶
                $this->redirect(Yii::app()->createUrl('mobile/pay/wyPrestoreOrderPay', array('order_id' => $result->data->id, 'encrypt_id' => $encrypt_id)));
            }
        }
        $this->render('addPrestoreMoney');
    }

    /**
     * Ô¤´æ¼ÇÂ¼
     */
    public function actionPrestoreRecord() {
        $prestoreOrderC = new PrestoreOrderC();
        $user_id =$this->getUserId();
        $encrypt_id = $this->getEncryptId();
        $list = array();
        $result = json_decode($prestoreOrderC -> getPrestoreOrderList($user_id),true);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $list = $result['data'];
        }

        $this -> render('prestoreRecord', array(
            'list' => $list,
            'encrypt_id' => $encrypt_id
        ));
    }
}