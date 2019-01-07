<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="searchform" action="" method="get" >
<input type="hidden" value="member" name="m">
<input type="hidden" value="apay" name="c">
<input type="hidden" value="charge_list" name="a">

<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
				时间: <?php echo form::date('start_time',$start_time)?>至<?php echo form::date('end_time',$end_time)?>
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
			
			<th align="left">会员id</th>
			<th align="left">会员姓名</th>
			<th align="left">英文名</th>
			<th align="left">充值时间</th>
			<th align="left">金额</th>
			<th align="left">充值人</th>

		</tr>
	</thead>
<tbody>
<?php
	if(is_array($tradelist_arr)){
	foreach($tradelist_arr as $k=>$v) {
?>
    <tr>
		<td align="left"><?php echo $v['member_id']?></td>
		<td align="left"><?php echo $v['username']?></td>
		<td align="left"><?php echo $v['enname']?></td>
		<td align="left"><?php echo $v['finish_time']?></td>
		<td align="left"><?php echo $v['amout']?></td>
		<td align="left"><?php echo $v['adminname']?></td>
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