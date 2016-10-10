<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/4/28
 * Time: 14:49
 */
class AuthorityC extends mainClass
{
    /**
     * @param $id
     * @return mixed
     * 获取岗位名称
     */
    public function postName($id){
        $result = Post::model()->findByPk($id);
        return $result = json_decode(CJSON::encode($result), TRUE);
    }
}