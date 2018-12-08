<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="searchform" action="" method="get" >
<input type="hidden" value="member" name="m">
<input type="hidden" value="member" name="c">
<input type="hidden" value="search" name="a">
<input type="hidden" value="879" name="menuid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
				
							
				<select name="status">
					<option value='0' <?php if(isset($_GET['status']) && $_GET['status']==0){?>selected<?php }?>><?php echo L('status')?></option>
					<option value='1' <?php if(isset($_GET['status']) && $_GET['status']==1){?>selected<?php }?>><?php echo L('lock')?></option>
					<option value='2' <?php if(isset($_GET['status']) && $_GET['status']==2){?>selected<?php }?>><?php echo L('normal')?></option>
				</select>
				
				<select name="type">
					<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>>姓名</option>
					<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>><?php echo L('uid')?></option>
					<option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>><?php echo L('email')?></option>
					<option value='4' <?php if(isset($_GET['type']) && $_GET['type']==4){?>selected<?php }?>><?php echo L('regip')?></option>
					<option value='5' <?php if(isset($_GET['type']) && $_GET['type']==5){?>selected<?php }?>><?php echo L('nickname')?></option>
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

<form name="myform" action="?m=member&c=member&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('userid[]');"></th>
			<th align="left"></th>
			<th align="left"><?php echo L('uid')?></th>
			<th align="left"><?php echo L('username')?></th>
			<th align="left">英文名</th>
			<th align="left"><?php echo L('nickname')?></th>
			<th align="left"><?php echo L('email')?></th>

			<th align="left"><?php echo L('regip')?></th>
			<th align="left"><?php echo L('lastlogintime')?></th>

			<th align="left"><?php echo L('point')?></th>
			<th align="left"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($memberlist)){
	foreach($memberlist as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['userid']?>" name="userid[]"></td>
		<td align="left"><?php if($v['islock']) {?><img title="<?php echo L('lock')?>" src="<?php echo IMG_PATH?>icon/icon_padlock.gif"><?php }?></td>
		<td align="left"><?php echo $v['userid']?></td>
		<td align="left"><?php echo $v['username']?><a href="javascript:member_infomation(<?php echo $v['userid']?>, '<?php echo $v['modelid']?>', '')"><?php echo $member_model[$v['modelid']]['name']?><img src="<?php echo IMG_PATH?>admin_img/detail.png"></a></td>
		<td align="left"><?php $v['enname']?></td>
		<td align="left"><?php echo new_html_special_chars($v['nickname'])?></td>
		<td align="left"><?php echo $v['email']?></td>

		<td align="left"><?php echo $v['regip']?></td>
		<td align="left"><?php echo format::date($v['lastdate'], 1);?></td>

		<td align="left"><?php echo $v['point']?></td>
		<td align="left">
			<a href="javascript:edit(<?php echo $v['userid']?>, '<?php echo $v['username']?>')">[<?php echo L('edit')?>]</a>
		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>

<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>


</div>

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit').L('member')?>《'+name+'》',id:'edit',iframe:'?m=member&c=member&a=edit&userid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}

function checkuid() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

function member_infomation(userid, modelid, name) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=member&c=member&a=memberinfo&userid='+userid+'&modelid='+modelid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}

//-->
</script>
</body>
</html>