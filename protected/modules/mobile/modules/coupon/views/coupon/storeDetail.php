<head>
    <title>门店详情</title>
</head>

<body>

<div class="storeD">
    <div class="weui_cells_title">
        <h3><?php echo $store['name'] ?></h3>
        <p><?php echo $store['branch_name'] ?></p>
    </div>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">位置</label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="weui_panel_access" onclick="tz()">
                    <span class="weui_panel_ft"><?php echo $store['address'] ?></span>
                </p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">电话</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p>
                    <a href="tel:187583623222" class="cBlue">
                        <?php echo $store['telephone'] ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function tz() {
        window.location.href = 'http://api.map.baidu.com/marker?location=<?php echo $store['lat'];?>,<?php echo $store['lng'];?>&title=<?php echo $store['address']?>&content=<?php echo $store['name']?>&output=html';
    }
</script>

</body>
