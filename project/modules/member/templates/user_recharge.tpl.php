<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="searchform" action="" method="get" >
<input type="hidden" value="member" name="m">
<input type="hidden" value="apay" name="c">
<input type="hidden" value="user_recharge" name="a">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
				

				<select name="type">
					<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>>姓名</option>
					<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>><?php echo L('uid')?></option>
					<option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>><?php echo L('email')?></option>

					<option value='6' <?php if(isset($_GET['type']) && $_GET['type']==6){?>selected<?php }?>>英文名</option>
				</select>
				
				<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
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
			<th align="left"><?php echo L('uid')?></th>
			<th align="left"><?php echo L('username')?></th>
			<th align="left">英文名</th>
			<th align="left"><?php echo L('email')?></th>

			<th align="left">余额</th>
			<th align="left">充值金额</th>
			<th align="left"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($memberlist_arr)){
	foreach($memberlist_arr as $k=>$v) {
?>
    <tr>
		<td align="left"><?php echo $v['userid']?></td>
		<td align="left"><?php echo $v['username']?></td>
		<td align="left"><?php echo $v['enname']?></td>
		<td align="left"><?php echo $v['email']?></td>
		<td align="left"><?php echo $v['amount']?></td>
		<td align="left"><input value="0" type="number" id="charge_money_<?php echo $v['userid']?>" min="1" max="10" style="width:100px" /></td>
		<td align="left">
			<input type="button" class="button"  value="充 值" onclick="user_recharge(<?php echo $v['userid']?>,this)"/>
		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>
<form style="display:none" id="form_pay" action="/index.php?m=member&c=apay&a=do_recharge&pc_hash=<?php echo $_SESSION['pc_hash'];?>"  method="POST">
	<input type="hidden"  name="money" id="hid_money" >
	<input type="hidden"  name="member_id" id="hid_member_id" >
	<input type="hidden"  name="pay_no" id="hid_pay_no" >
</form>
<div class="btn">


</div>

<div id="pages"><?php echo $pages?></div>
</div>

</div>
<script type="text/javascript">
<!--
function user_recharge(id,obj) {
	money  = $("#charge_money_"+id).val();
	var reg = /^[1-9][0-9]*$/;
	if (!reg.test(money) ) {
		alert("请输入正确的金额");
		return false;
	}
	$("#hid_money").val(money);
	$("#hid_member_id").val(id);
	if (confirm("账户："+id+",充值金额为"+money)) {
		$(obj).attr("disabled","true");
		$.ajax({
            async: false,
            type: "POST",
            url:'/index.php?m=member&c=apay&a=get_recharge_no&pc_hash=<?php echo $_SESSION["pc_hash"];?>',
            data:{money:money,member_id:id},
            dataType: "json",
            success: function (data) {
            	if (data.pay_no) {
            		$("#hid_pay_no").val(data.pay_no);
					$("#form_pay").submit();
					return true;
            	}else{
            		alert("充值失败");
            		return false;
            	}
              }
        });
        

	}

}


//-->
</script>
</body>
</html>