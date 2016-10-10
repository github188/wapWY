<body>
<div class="q_header">
    <img src="<?php echo USER_STATIC_IMAGES ?>q2.jpg">
</div>
<div class="q_wrap">
    <div class="q_content">
        <?php echo CHtml::beginForm(Yii::app()->createUrl('uCenter/question/survey'), 'post');?>
        <div class="q_contact">
            <div class="q_contact_item">
                <label>分公司</label>
                <input name="branch_company" type="text" value="<?php if(isset($_POST['branch_company']) && !empty($_POST['branch_company'])){echo $_POST['branch_company'];}?>">
                <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('branch_company_error')) {
                        echo Yii::app()->user->getFlash('branch_company_error');
                    }?>
                </span>
            </div>
            <div class="q_contact_item">
                <label>联系人</label>
                <input name="contacts" type="text" value="<?php if(isset($_POST['contacts']) && !empty($_POST['contacts'])){echo $_POST['contacts'];}?>">
                <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('contacts_error')) {
                        echo Yii::app()->user->getFlash('contacts_error');
                    }?>
                </span>
            </div>
            <div class="q_contact_item">
                <label>电话</label>
                <input name="tel" type="text" value="<?php if(isset($_POST['tel']) && !empty($_POST['tel'])){echo $_POST['tel'];}?>">
                <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('tel_error')) {
                        echo Yii::app()->user->getFlash('tel_error');
                    }?>
                </span>
            </div>
        </div>
        <div class="q_detail">
            <span>注册</span>
            <div class="q_answer">
                <p>1、目前，支付宝和微信支付注册流程是否顺利？</p>
                <div class="q_check">
                    <span><input type="radio" value="1" <?php if(isset($_POST['question1']) && !empty
                            ($_POST['question1']) && $_POST['question1']==1){echo 'checked';}?> name="question1"><label>是</label></span>
                    <span><input type="radio" value="2" <?php if(isset($_POST['question1']) && !empty
                            ($_POST['question1']) && $_POST['question1']==2){echo 'checked';}?> name="question1"><label>否</label></span>
                    <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('question1_error')) {
                        echo Yii::app()->user->getFlash('question1_error');
                    }?>
                    </span>
                </div>
            </div>
            <div class="q_answer">
                <p>2、注册流程是否清晰明确？</p>
                <div class="q_check">
                    <span><input type="radio" value="1" <?php if(isset($_POST['question2']) && !empty
                            ($_POST['question2']) && $_POST['question2']==1){echo 'checked';}?> name="question2"><label>是</label></span>
                    <span><input type="radio" value="2" <?php if(isset($_POST['question2']) && !empty
                            ($_POST['question2']) && $_POST['question2']==2){echo 'checked';}?> name="question2"><label>否</label></span>
                    <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('question2_error')) {
                        echo Yii::app()->user->getFlash('question2_error');
                    }?>
                    </span>
                </div>
            </div>
            <div class="q_answer">
                <p>3、注册中，供应商协助是否及时？</p>
                <div class="q_check">
                    <span><input type="radio" value="1" <?php if(isset($_POST['question3']) && !empty
                            ($_POST['question3']) && $_POST['question3']==1){echo 'checked';}?> name="question3"><label>是</label></span>
                    <span><input type="radio" value="2" <?php if(isset($_POST['question3']) && !empty
                            ($_POST['question3']) && $_POST['question3']==2){echo 'checked';}?> name="question3"><label>否</label></span>
                    <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('question3_error')) {
                        echo Yii::app()->user->getFlash('question3_error');
                    }?>
                    </span>
                </div>
            </div>
        </div>
        <div class="q_detail">
            <span>对账</span>
            <div class="q_answer">
                <p>1、供应商对账人及联系方式是否知晓？</p>
                <div class="q_check">

                    <span><input type="radio" value="1" <?php if(isset($_POST['question4']) && !empty
                        ($_POST['question4']) && $_POST['question4']==1){echo 'checked';}?> name="question4"><label>是</label></span>
                    <span><input type="radio"  value="2" <?php if(isset($_POST['question4']) && !empty
                            ($_POST['question4']) && $_POST['question4']==2){echo 'checked';}?> name="question4"><label>否</label></span>
                    <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('question4_error')) {
                        echo Yii::app()->user->getFlash('question4_error');
                    }?>
                    </span>
                </div>
            </div>
            <div class="q_answer">
                <p>2、对账中，存在哪些问题？</p>
                <div class="q_check">
                    <textarea placeholder="请输入您发现的问题" name="question5"><?php if(isset($_POST['question5']) && !empty($_POST['question5'])){echo $_POST['question5'];}?></textarea>
                    <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('question5_error')) {
                        echo Yii::app()->user->getFlash('question5_error');
                    }?>
                    </span>
                </div>
            </div>
            <div class="q_answer">
                <p>3、对对账流程有哪些建议？</p>
                <div class="q_check">
                    <textarea placeholder="请填写您的建议" name="question6"><?php if(isset($_POST['question6']) && !empty($_POST['question6'])){echo $_POST['question6'];}?></textarea>
                    <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('question6_error')) {
                        echo Yii::app()->user->getFlash('question6_error');
                    }?>
                    </span>
                </div>
            </div>
        </div>
        <div class="q_detail">
            <span>收银台</span>
            <div class="q_answer">
                <p>1、对收银台功能是否熟悉了解？</p>
                <div class="q_check">
                    <span><input type="radio" value="1" <?php if(isset($_POST['question7']) && !empty
                            ($_POST['question7']) && $_POST['question7']==1){echo 'checked';}?> name="question7"><label>是</label></span>
                    <span><input type="radio" value="2" <?php if(isset($_POST['question7']) && !empty
                            ($_POST['question7']) && $_POST['question7']==2){echo 'checked';}?> name="question7"><label>否</label></span>
                    <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('question7_error')) {
                        echo Yii::app()->user->getFlash('question7_error');
                    }?>
                    </span>
                </div>
            </div>
            <div class="q_answer">
                <p>2、对收银台功能是否有其他建议？</p>
                <div class="q_check">
                    <textarea placeholder="请填写您的建议" name="question8"><?php if(isset($_POST['question8']) && !empty($_POST['question8'])){echo $_POST['question8'];}?></textarea>
                    <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('question8_error')) {
                        echo Yii::app()->user->getFlash('question8_error');
                    }?>
                    </span>
                </div>
            </div>
            <div class="q_answer">
                <p>3、对支付宝和微信支付是否使用习惯？</p>
                <div class="q_check">
                    <span><input type="radio" value="1" <?php if(isset($_POST['question9']) && !empty
                            ($_POST['question9']) && $_POST['question9']==1){echo 'checked';}?> name="question9"><label>是</label></span>
                    <span><input type="radio" value="2" <?php if(isset($_POST['question9']) && !empty
                            ($_POST['question9']) && $_POST['question9']==2){echo 'checked';}?> name="question9"><label>否</label></span>
                    <span class="text1 red">
                    <?php if (Yii::app()->user->hasFlash('question9_error')) {
                        echo Yii::app()->user->getFlash('question9_error');
                    }?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="q_footer">
    <input type="submit" value="提交">
</div>
<?php echo CHtml::endForm();?>
</body>

