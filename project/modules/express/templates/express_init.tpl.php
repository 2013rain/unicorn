<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="table-list">
<fieldset>
	<legend><?php echo L('wait_for_put_in')?></legend>
	<table width="100%" cellspacing="0">
        <thead>
                <tr>
                <th width="10%"><?php echo L('storage')?></th>
                <th width="10%" align="left" ><?php echo L('express_no')?></th>
                <th width="10%" align="left" ><?php echo L('express_summary')?></th>
                <th width="10%"  align="left" ><?php echo L('create_time')?></th>
                <th width="20%"  align="left" ><?php echo L('opreate')?></th>
                </tr>
        </thead>
	<tbody>
	<?php
	if(is_array($infos)){
		foreach($infos as $info){
	?>
	<tr>
	<td width="10%" align="center"><?php echo $info['userid']?></td>
	<td width="10%" ><?php echo $info['username']?></td>
	<td width="10%" ><?php echo $roles[$info['roleid']]?></td>
	<td width="10%" ><?php echo $info['lastloginip']?></td>
	<td width="20%"  ><?php echo $info['lastlogintime'] ? date('Y-m-d H:i:s',$info['lastlogintime']) : ''?></td>
	<td width="15%"><?php echo $info['email']?></td>
	<td width="10%"  align="center"><?php echo $info['realname']?></td>
	<td width="15%"  align="center">
	<a href="javascript:edit(<?php echo $info['userid']?>, '<?php echo new_addslashes($info['username'])?>')"><?php echo L('edit')?></a> |
	<?php if(!in_array($info['userid'],$admin_founders)) {?>
	<a href="javascript:confirmurl('?m=admin&c=admin_manage&a=delete&userid=<?php echo $info['userid']?>', '<?php echo L('admin_del_cofirm')?>')"><?php echo L('delete')?></a>
	<?php } else {?>
	<font color="#cccccc"><?php echo L('delete')?></font>
	<?php } ?> | <a href="javascript:void(0)" onclick="card(<?php echo $info['userid']?>)"><?php echo L('ht_card')?></a>
	</td>
	</tr>
	<?php
		}
	}
	?>
	</tbody>
	</table>
</fieldset>
<div class="bk15"></div>
<form name="myform" action="" method="get">
<input type="hidden" name="m" value="member" />
<input type="hidden" name="c" value="member" />
<input type="hidden" name="a" value="search" />
<fieldset>
	<legend><?php echo L('member_search')?></legend>
<div class="bk10"></div>
<form name="searchform" action="" method="get" >
<input type="hidden" value="member" name="m">
<input type="hidden" value="member" name="c">
<input type="hidden" value="search" name="a">
<input type="hidden" value="879" name="menuid">
<table width="100%" class="table_form contentWrap">
		<tr>
			<td width="120"><?php echo L('regtime')?></td> 
			<td>
				<?php echo form::date('start_time', $start_time)?>-
				<?php echo form::date('end_time', $end_time)?>
			</td>
		</tr>
		<tr>
			<td width="120"><?php echo L('member_group')?></td> 
			<td>
				<?php echo form::select($grouplist, $_GET['groupid'], 'name="groupid"', L('nolimit'))?>
			</td>
		</tr>
		<tr>
			<td width="120"><?php echo L('status')?></td> 
			<td>
				<select name="status">
					<option value='0' <?php if(isset($_GET['status']) && $_GET['status']==0){?>selected<?php }?>><?php echo L('nolimit')?></option>
					<option value='1' <?php if(isset($_GET['status']) && $_GET['status']==1){?>selected<?php }?>><?php echo L('lock')?></option>
					<option value='2' <?php if(isset($_GET['status']) && $_GET['status']==2){?>selected<?php }?>><?php echo L('normal')?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="120"><?php echo L('type')?></td> 
			<td>
				<select name="type">
					<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>><?php echo L('username')?></option>
					<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>><?php echo L('uid')?></option>
					<option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>><?php echo L('email')?></option>
					<option value='4' <?php if(isset($_GET['type']) && $_GET['type']==4){?>selected<?php }?>><?php echo L('regip')?></option>
				</select>
				<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
			</td>
		</tr>
		<tr>
			<td width="120"><?php echo L('amount')?></td> 
			<td>
				<input name="amount_from" type="text" value="" class="input-text" size="4"/>-
				<input name="amount_to" type="text" value="" class="input-text" size="4"/>
			</td>
		</tr>
		<tr>
			<td width="120"><?php echo L('point')?></td> 
			<td>
				<input name="point_from" type="text" value="" class="input-text" size="4"/>-
				<input name="point_to" type="text" value="" class="input-text" size="4"/>
			</td>
		</tr>

</table>
<div class="bk15"></div>
<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
</form>

</fieldset>
</form>

</div>
</div>
</body>
</html>
