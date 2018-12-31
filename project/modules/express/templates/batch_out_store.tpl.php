<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<style type="text/css">
.import_file {display:hidden}
</style>
<div class="pad-lr-10">
		<div class="explain-col">
            <div>说明：本功能为<b>出库批量</b>操作。<br>批量导出功能：把已支付待出库的快递单号批量导出,默认导出三个月内的，最多不能超过一年。<br>批量导出快递单中物品供比对使用</div>
	</div>

<form name="myform" action="" method="post">
        <div class="explain-col">
导出时间选择: <?php echo form::date('start_time',$start_time)?>至<?php echo form::date('end_time',$end_time)?>

        </div>
<div class="table-list">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
            <input name="dosubmit" type="hidden" value="dosubmit">
            <input type="submit" name="export" class="button" value="批量导出待出库快递" />
            <input type="submit" name="export_express_goods_detail" class="button" value="批量导出快递单中物品" />
		</td>
		</tr>
    </tbody>
</table>

</div>

</div>
</form>
<form name="myform" action="" method="post" enctype="multipart/form-data">
<div class="table-list">
        <div class="explain-col">
            <div>批量导入：把导出的快递单号补上出库单号后上传，完成批量出库操作。<br/>注意：程序会忽略第一行（第一行为标题）</div>

    </div>
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
            <input type="file" name="express_data">
            <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
            <input name="dosubmit" type="hidden" value="dosubmit">
            <input type="submit" name="import" class="button" value="批量导入待出库快递" />
		</td>
		</tr>
    </tbody>
</table>

</div>

</div>
</form>
</div>
</body>
</html>
