<?php
include_once(realpath(dirname(__FILE__).'/../../../../protected/config').'/constant.php');
include_once(realpath(dirname(__FILE__).'/../../../../protected/class/config').'/constant.php');
class sytController extends CController
{
	public $layout='main';
	public $menu=array();
	public $breadcrumbs=array();
	
	/**
	 * 保存当前请求地址
	 * (non-PHPdoc)
	 * @see CController::afterAction()
	 */
	protected function afterAction($action) {
		$id = strtolower($action->getId());
		if ($id == 'pay' || 
			$id == 'tradinglist' || 
			$id == 'charge' || 
			$id == 'memberstoredlist' || 
			$id == 'bookrecord' || 
			$id == 'editpwd' || 
			$id == 'printoperat') {
			$url = Yii::app()->request->getUrl();
			$arr = explode("?", $url);
			$url = $arr[0];
			Yii::app()->session['lastRequestUrl'] = $url;
		}
	}
        
        /*
        * 导出excel 方法
        */
       protected function excel($data,$title){
           //导出数据到excel
           include_once 'PHPExcel.php';
           include_once 'PHPExcel/Reader/Excel2007.php';
           include_once 'PHPExcel/Reader/Excel5.php';
           include_once 'PHPExcel/IOFactory.php';
           $excel = new PHPExcel();
           $objWriter = new PHPExcel_Writer_Excel5($excel);
           $excel -> setActiveSheetIndex(0);
           $objActSheet = $excel -> getActiveSheet();
           $objActSheet -> setTitle($title);
           $ascii = ord('A');

           foreach($data as $k => $v){
               $i = 0;
               foreach($v as $val){
                   $objActSheet -> setCellValue(chr($ascii + $i).($k + 1),$val);
                   $objActSheet -> getRowDimension(0) -> setRowHeight(30);
                   $objActSheet -> getRowDimension($i + 1) -> setRowHeight(80);
                   $objActSheet -> getStyle(chr($ascii + $i).($k + 1))->getAlignment()->setWrapText(true);
                   $i++;
                   $objActSheet -> getColumnDimension(chr($ascii + $i + 1)) -> setWidth(12);
               }
           }
           $objActSheet -> getColumnDimension('C') -> setWidth(40);

           $filename = date('YmdHis').'.xls';
           header('Pragma: public');
           header('Expires: 0');
           header('Cache-Control:must-revalidate, post-check=0, pre-check=0');
           header('Content-Type:application/force-download');
           header('Content-Type:application/vnd.ms-execl');
           header('Content-Type:application/octet-stream');
           header('Content-Type:application/download');
           header('Content-Disposition:attachment;filename="'.$filename.'"');
           header('Content-Transfer-Encoding:binary');
           $objWriter -> save('php://output');
       }

       
       /**
        * 判断用户是否登录isGuest
        * 未登录则跳转到登录页面
        */
       public function init(){
       	if(!isset(Yii::app() -> session['operator_id']) || empty(Yii::app() -> session['operator_id'])){
       		echo("<script>parent.location.href='".Yii::app() -> createUrl('syt/auth/login')."'</script>");
       		Yii::app() -> end();
       	}
       	return;
       }

}