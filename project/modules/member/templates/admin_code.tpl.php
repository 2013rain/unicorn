<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>


<div class="pad_10">
<div class="table-list">

    <table width="50%" cellspacing="0">
        <thead>
		<tr>
		<th width="50%">邀请码</th>
		<th width="50%" align="left" >生成时间</th>
		</tr>
        </thead>
        <tbody>
 <tr>
<td width="50%" align="center"><?php echo $info['code']?></td>
<td width="50%" ><?php echo $info['create_time'] ?></td>
</tr>
</tbody>
</table>

</div>
</div>
</body>
</html>
<script type="text/javascript">
<!--
	function edit(id, name) {
		window.top.art.dialog({title:'<?php echo L('edit')?>--'+name, id:'edit', iframe:'?m=admin&c=admin_manage&a=edit&userid='+id ,width:'500px',height:'400px'}, 	function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;
		var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
	}

function card(id) {
	window.top.art.dialog({title:'<?php echo L('the_password_card')?>', id:'edit', iframe:'?m=admin&c=admin_manage&a=card&userid='+id ,width:'500px',height:'400px'}, 	'', function(){window.top.art.dialog({id:'edit'}).close()});
}
//-->
</script>