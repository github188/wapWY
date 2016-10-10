<script type="text/javascript">
	$(document).ready(main_obj.list_init);
	function sumChange() {
		$("input[name=sum]").val($("input[name=sum]").val().replace(/\D/gi, ""));
	}
</script>
<?php $form = $this->beginWidget('CActiveForm')?>
<div class="kkfm_r_inner" id="pop" style="width:520px">
    <div class="contant" style="text-align:left">
        <div class="filed">
            <span class="label"><em class="red">*</em>预订时间</span>
            <span class="text">
                <input id="d2a25" type="text" class="Wdate" name="book_time" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'%y-%M-%d %H:%m:%s'})"/> 
            <span class="text1 red">
                <?php if (Yii::app()->user->hasFlash('book_time')) {
                   echo Yii::app()->user->getFlash('book_time');
                }?>
            </span>             	
            </span>            
         </div>
      	 <div class="filed">
            <span class="label"><em class="red">*</em>预订人姓名</span>
            <span class="text">
               <input type="text" class="txt" name='name' placeholder="预订人姓名">
            </span>
            <span class="text1 red">
                <?php if (Yii::app()->user->hasFlash('name')) {
                   echo Yii::app()->user->getFlash('name');
                }?>
            </span>
         </div>
         <div class="filed">
            <span class="label"><em class="red">*</em>电话</span>
            <span class="text">             	
               <input type="text" class="txt" name='tel'  placeholder="电话或手机号码">
            </span>
            <span class="text1 red">
                <?php if (Yii::app()->user->hasFlash('tel')) {
                   echo Yii::app()->user->getFlash('tel');
                }?>
            </span>
         </div>
         <div class="filed">
            <span class="label"><em class="red">*</em>性别</span>
            <span class="text">             	
                <select name='sex'>
                    <option value="<?php echo SEX_MALE?>">男</option>
                    <option value="<?php echo SEX_FEMALE?>">女</option>
                </select>                 
            </span> 
            <span class="text1 red">
                <?php if (Yii::app()->user->hasFlash('sex')) {
                   echo Yii::app()->user->getFlash('sex');
                }?>
            </span>
         </div>
         <div class="filed">
            <span class="label"><em class="red">*</em>人数</span>
            <span class="text">             	
               <input type="text" class="txt" name='sum'  placeholder="人数" onkeyup="sumChange()">
            </span>
            <span class="text1 red">
                <?php if (Yii::app()->user->hasFlash('sum')) {
                   echo Yii::app()->user->getFlash('sum');
                }?>
            </span>
         </div>
          <div class="filed">
            <span class="label"><em class="red">*</em>备注</span>
            <span class="text">             	
               <input type="text" class="txt" name='remark'  placeholder="备注">                
            </span>
            <span class="text1 red">
                <?php if (Yii::app()->user->hasFlash('remark')) {
                   echo Yii::app()->user->getFlash('remark');
                }?>
            </span>
         </div>
         <div class="filed">
             <span class="label"></span>
             <span class="text">
                 <?php echo CHtml::submitButton('添加',array('class'=>'btn_com_blue'))?>              	
             </span>
         </div>
    </div>
</div>
    </div>
<?php $form = $this->endWidget()?>
