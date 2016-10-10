<head>
    <title>门店列表</title>
</head>

<body>

<div class="storeList">
    <div class="weui_cells_title">所有门店</div>
    <div class="weui_cells store_lists">
        <?php foreach ($stores as $v) { ?>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <h3 onclick="store_detail(<?php echo $v['id'] ?>)">
                        <span></span>
                        <?php echo $v['name'] . $v['branch_name'] ?>
                    </h3>
                    <p><?php echo $v['address'] ?></p>
                </div>
                <div class="weui_cell_ft">
                    <a href="tel:<?php echo $v['telephone'] ?>" class="phone"></a>
                </div>
            </div>
        <?php } ?>
    </div>
    <input type="hidden" id="current_page" value="2">
    <a href="javascript:;" class="refreshMore">加载更多...</a>
</div>

<script>
    function store_detail(id) {
        window.location.href = '<?php echo Yii::app()->createUrl('mobile/coupon/coupon/StoreDetail') ?>?id=' + id + '&encrypt_id=' + '<?php echo $encrypt_id ?>';
    }

    var flag = true;

    $('.refreshMore').click(function () {
        var current_page = $('#current_page').val();
        var qcode = '<?php echo $qcode ?>';
        var encrypt_id = '<?php echo $encrypt_id ?>';
        if (flag) {
            flag = false;
            $.ajax({
                url: '<?php echo Yii::app()->createUrl('mobile/coupon/coupon/StoreList') ?>',
                data: {
                    current_page: current_page,
                    qcode: qcode,
                    encrypt_id: encrypt_id
                },
                dataType: "json",
                type: 'post',
                async: false,
                success: function (data) {
                    if (data.data.status == <?php echo ERROR_NONE ?> && data.data.lists.length > 0) {
                        var stores = data.data.lists;
                        for (var i = 0; i < stores.length; i++) {
                            if (stores[i]['branch_name'] == null) {
                                stores[i]['branch_name'] = '';
                            }
                            $('.store_lists').append("<div class='weui_cell'>" +
                                "<div class='weui_cell_bd weui_cell_primary'>" +
                                "<h3 onclick='store_detail(" + stores[i]['id'] + ")'><span></span>" + stores[i]['name'] + stores[i]['branch_name'] +
                                "</h3>" +
                                "<p>" + stores[i]['address'] + "</p>" +
                                "</div>" +
                                "<div class='weui_cell_ft'>" +
                                "<a href='tel:" + stores[i]['telephone'] + "' class='phone'></a>" +
                                "</div>" +
                                "</div>");
                        }

                        $('#current_page').val(parseInt(current_page) + 1);
                        flag = true;
                    } else {
                        $('.refreshMore').html('没有更多门店了');
                        $('#current_page').val(2);
                    }
                }
            });
        }
    });
</script>

</body>

