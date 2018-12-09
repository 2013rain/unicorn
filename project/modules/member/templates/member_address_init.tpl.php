<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>

<form name="myform" id="myform" action="?m=member&c=member_address&a=init" method="post" onsubmit="check();return false;">
<div class="pad-lr-10">
<div class="table-list">

<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left" width="30px"><input type="checkbox" value="" id="check_box" onclick="selectall('addressid[]');"></th>
			<th align="left">member_id</th>
			<th>账号</th>
			<th>联系人</th>
			<th>身份证</th>
			<th>手机号</th>
			<th>邮箱</th>
			<th>省份</th>
			<th>市</th>
			<th>区域</th>
			<th>邮编</th>
			<th>身份证人头照</th>
			<th>身份证国徽照</th>
		</tr>
	</thead>
<tbody>
<?php
	foreach($member_address_list as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="addressid[]"></td>
		<td align="left"><?php echo $v['member_id']?></td>
		<td align="center"><?php echo $v['username']?></th>
		<td align="center"><?php echo $v['consignee']?></td>
		<td align="center"><?php echo $v['idcard']?></td>
		<td align="center"><?php echo $v['mobile']?></td>
		<td align="center"><?php echo $v['email']?></td>
		<td align="center"><?php echo $v['province']?></td>
		<td align="center"><?php echo $v['city']?></td>
		<td align="center"><?php echo $v['area']?></td>
		<td align="center"><?php echo $v['zipcode']?></td>
		<td align="center" onclick="javascript:edit('<?php echo $v["idfronturl"]?>');">查看图片</td>
		<td align="center" onclick="javascript:edit('<?php echo $v["idbackurl"]?>');">查看图片</td>
    </tr>
<?php
	}
?>
</tbody>
 </table>

<div class="btn"><label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="审核拒绝"  onclick="document.myform.action='?m=member&c=member_address&a=audit_refuse'" />
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=member&c=member_address&a=audit_access'" value="审核通过"/>
</div> 
<div id="pages"><?php echo $pages?></div>
</div>
</div>
</form>
<div id="PC__contentHeight" style="display:none">160</div>
<script language="JavaScript">
<!--
function edit(src) {
	if (src=="") {
		return false;
	}
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'',id:'edit',iframe:src,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;return true;}, function(){window.top.art.dialog({id:'edit'}).close()});
}


//-->
</script>
</body>
</html>