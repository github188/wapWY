<?php
/**
 * Created by PhpStorm.
 * User: nb-lt
 * Date: 2016/3/4
 * Time: 14:59
 */
class QuestionController extends UCenterController
{

    /*
     * 问卷调查
     */
    public function actionSurvey()
    {
    	$merchant = new  MerchantC();
        if(!empty($_POST)) {
            $merchant_id = 13;
            $flag = 1;
            if (!isset($_POST['question1']) || empty($_POST['question1'])) {
                Yii::app()->user->setFlash('question1_error', '请选择答案');
                $flag = 2;
            } else {
                $question1 = $_POST['question1'];
            }
            if (!isset($_POST['question2']) || empty($_POST['question2'])) {
                Yii::app()->user->setFlash('question2_error', '请选择答案');
                $flag = 2;
            } else {
                $question2 = $_POST['question2'];
            }
            if (!isset($_POST['question3']) || empty($_POST['question3'])) {
                Yii::app()->user->setFlash('question3_error', '请选择答案');
                $flag = 2;
            } else {
                $question3 = $_POST['question3'];
            }
            if (!isset($_POST['question4']) || empty($_POST['question4'])) {
                Yii::app()->user->setFlash('question4_error', '请选择答案');
                $flag = 2;
            } else {
                $question4 = $_POST['question4'];
            }
            if (!isset($_POST['question5']) || empty($_POST['question5'])) {
                Yii::app()->user->setFlash('question5_error', '请填写内容');
                $flag = 2;
            } else {
                $question5 = $_POST['question5'];
            }
            if (!isset($_POST['question6']) || empty($_POST['question6'])) {
                Yii::app()->user->setFlash('question6_error', '请填写内容');
                $flag = 2;
            } else {
                $question6 = $_POST['question6'];
            }
            if (!isset($_POST['question7']) || empty($_POST['question7'])) {
                Yii::app()->user->setFlash('question7_error', '请选择答案');
                $flag = 2;
            } else {
                $question7 = $_POST['question7'];
            }
            if (!isset($_POST['question8']) || empty($_POST['question8'])) {
                Yii::app()->user->setFlash('question8_error', '请填写内容');
                $flag = 2;
            } else {
                $question8 = $_POST['question8'];
            }
            if (!isset($_POST['question9']) || empty($_POST['question9'])) {
                Yii::app()->user->setFlash('question9_error', '请选择答案');
                $flag = 2;
            } else {
                $question9 = $_POST['question9'];
            }
            if (!isset($_POST['branch_company']) || empty($_POST['branch_company'])) {
                Yii::app()->user->setFlash('branch_company_error', '请填写公司名称');
                $flag = 2;
            } else {
                $branch_company = $_POST['branch_company'];
            }
            if (!isset($_POST['contacts']) || empty($_POST['contacts'])) {
                Yii::app()->user->setFlash('contacts_error', '请填写联系人姓名');
                $flag = 2;
            } else {
                $contacts = $_POST['contacts'];
            }
            if (!isset($_POST['tel']) || empty($_POST['tel'])) {
                Yii::app()->user->setFlash('tel_error', '请填写联系电话');
                $flag = 2;
            } else {
                if (!empty($_POST['area_code'])) {
                    $tel = $_POST['area_code'].'-'.$_POST['tel'];
                    //正则表达式判断电话号码
                    if (!preg_match("/^0\d{2,3}-\d{7,8}$/",$tel)) {
                        $flag = 2;
                        Yii::app()->user->setFlash('tel_error', '请输入正确的联系电话');
                    }

                }else {
                    $tel = $_POST['tel'];
                    //正则表达式判断手机
                    if (!preg_match("/^[1][34578][0-9]{9}$/",$tel)) {
                        $flag = 2;
                        Yii::app()->user->setFlash('tel_error', '请输入正确的联系电话');
                    }
                    $check_tel = json_decode($merchant -> checkTel($tel));
                    if($check_tel -> status == ERROR_NONE){
                    	if($check_tel -> if_repeat == 1){
                    		$flag = 2;
                    		Yii::app()->user->setFlash('tel_error', '该手机号已参加过该调查');
                    	}
                    }
                }
            }
            
            if ($flag == 1) {

                $re = json_decode($merchant->saveQuestion($merchant_id, $branch_company, $contacts, $tel, $question1, $question2, $question3, $question4, $question5, $question6, $question7, $question8, $question9));
                if ($re->status == ERROR_NONE) {
                    $url = Yii::app()->createUrl('uCenter/question/survey');
                    echo "<script>alert('保存成功');window.location.href='$url'</script>";
                } else {
                    $url = Yii::app()->createUrl('uCenter/question/survey');
                    echo "<script>alert('保存失败');window.location.href='$url'</script>";
                }
            }
//            else {
//                $url = Yii::app()->createUrl('uCenter/question/survey');
//                echo "<script>alert('请完整填写调查问卷！');window.location.href='$url'</script>";
//            }
        }
        $this->render('survey');
    }
}