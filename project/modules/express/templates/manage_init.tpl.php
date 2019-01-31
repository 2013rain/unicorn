<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">

<div class="bk15"></div>
<div class="pad-lr-10">
<form name="searchform" action="" method="get" >
<input type="hidden" value="express" name="m">
<input type="hidden" value="manage" name="c">
<input type="hidden" value="init" name="a">

<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
				
				主运单号：
				<input name="major_no" id="major_no" type="text" value="<?php if(isset($_GET['major_no'])) {echo $_GET['major_no'];}?>" class="input-text" />
				快递编号：
				<input name="expressno" id="expressno" type="text" value="<?php if(isset($_GET['expressno'])) {echo $_GET['expressno'];}?>" class="input-text" />
		</div>
		</td>
		</tr>
    </tbody>
</table>

<input type="submit" name="search" class="button" value="&nbsp;&nbsp;<?php echo L('search')?>&nbsp;&nbsp; " />
&nbsp;&nbsp;
<?php if(!empty($express_list)) { ?>
<input type="button" onclick="javascript:down()" class="button" value=" 按条件导出快递商品 " />
<?php } ?>
</form>
<div class="bk15"></div>



<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			
			<th align="left">id</th>
			<th align="left">主运单号</th>
			<th align="left" >快递编号</th>
			<th align="left" >账户id</th>
			<th align="left" width="150">快递描述</th>
			<th align="left">重量</th>
			<th align="left">支付金额</th>
			<th align="left">出库快递单号</th>
			<th align="left">当前状态</th>

		</tr>
	</thead>
<tbody>
<?php
	if(is_array($express_list)){
	foreach($express_list as $k=>$v) {
?>
    <tr>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><?php echo trim($v['major_no'])?></td>
		<td align="left"><?php echo trim($v['expressno'])?></td>
		<td align="left"><?php echo $v['userid']?></td>
		<td align="left"><?php echo $v['detail']?></td>
		<td align="left"><?php echo $v['weight']?></td>
		<td align="left"><?php echo $v['pay_money']?></td>
		<td align="left"><?php echo trim($v['send_no'])?></td>
		<td align="left"><?php if($v['status']=='2' && $v['pay_status']=='1') {echo '已支付';} else { echo $express_status[$v['status']]; }?></td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>

<div id="pages"><?php echo $pages?></div>
</div>


</div>
<script type="text/javascript">
<!--
function down() {
	major_no = $("#major_no").val()
	expressno = $("#expressno").val()
	if (major_no==""&&expressno=="") {
		alert("条件不能为空");
		return false;
	}
	pc_hash = "<?php echo htmlspecialchars($_GET['pc_hash']); ?>" ;
	this.location.href="?m=express&c=manage&a=init&down=1&major_no="+major_no+"&expressno="+expressno+"&pc_hash="+pc_hash;
}


//-->
</script>
</body>
</html>