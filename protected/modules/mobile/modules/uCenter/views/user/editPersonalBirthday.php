<head>
    <title>个人信息</title>
</head>

<body class="logo">
<?php echo CHtml::beginForm(); ?>
<section class="mid_con register">
    <div class="tel">
        <span>
            <select name="Birthday[year]">
                <?php foreach ($arr_year as $y) {
                    if ($y == $year) {
                        echo '<option value="' . $y . '" selected=selected>' . $y . '</option>';
                    } else {
                        echo '<option value="' . $y . '">' . $y . '</option>';
                    }
                } ?>
            </select>年
        </span>
        <span>
            <select name="Birthday[month]">
                <?php foreach ($arr_month as $m) {
                    if ($m == $month) {
                        echo '<option value="' . $m . '" selected=selected>' . $m . '</option>';
                    } else {
                        echo '<option value="' . $m . '">' . $m . '</option>';
                    }
                } ?>
            </select>月
        </span>
        <span>
            <select name="Birthday[day]">
                <?php foreach ($arr_day as $d) {
                    if ($d == $day) {
                        echo '<option value="' . $d . '" selected=selected>' . $d . '</option>';
                    } else {
                        echo '<option value="' . $d . '">' . $d . '</option>';
                    }
                } ?>
            </select>日
        </span>
    </div>
</section>

<div class="btn">
    <input type="submit" value="确定" class="btn_com" style="width:100%">
</div>

<?php if (Yii::app()->user->hasFlash('error')) { ?>
    <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
<?php } ?>
</section>
<?php echo CHtml::endForm(); ?>
</body>


