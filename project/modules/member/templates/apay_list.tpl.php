<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="searchform" action="" method="get" >
<input type="hidden" value="member" name="m">
<input type="hidden" value="apay" name="c">
<input type="hidden" value="apay_list" name="a">

<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
				快递单号：
				<input name="express_no" type="text" value="<?php echo $express_no; ?>" class="input-text" />
				&nbsp;&nbsp;
				<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			
			<th align="left">logid</th>
			<th align="left"><?php echo L('username')?></th>
			<th align="left">英文名</th>
			<th align="left">快递单号</th>
			<th align="left">支付状态</th>
			<th align="left">金额</th>
			<th align="left">支付时间</th>
			<th align="left">支付宝交易号</th>

		</tr>
	</thead>
<tbody>
<?php
	if(is_array($tradelist_arr)){
	foreach($tradelist_arr as $k=>$v) {
?>
    <tr>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><?php echo $v['username']?></td>
		<td align="left"><?php echo $v['enname']?></td>
		<td align="left"><?php echo $v['express_no']?></td>
		<td align="left"><?php  if($v['status']=='1'){echo '已支付';} else {echo '待支付'; } ?></td>
		<td align="left"><?php echo $v['amount']?></td>
		<td align="left"><?php echo $v['finish_time']?></td>
		<td align="left"><?php echo $v['outer_order_no']?></td>
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

</body>
</html>