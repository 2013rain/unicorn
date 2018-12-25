<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = $show_header = 1; 
include $this->admin_tpl('header', 'admin');
?>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    </div>
</div>
<div class="pad-lr-10">
<form name="myform" action="" method="post" id="myform">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
<?php
if ($status >= 3 && $status < 7) {
    echo '<th width="6%" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall(\'expressid[]\');"></th>';
}
?>
			<th width="12%" align="center">入库快递单号</th>
			<th width="12%" align="center">快递描述</th>
			<th width='8%' align="center">重量</th>
			<th width="10%" align="center">支付金额</th>
			<th align="center" width="13%">出库快递单号</th>
<?php
if ($status >= 3 && $status < 7) {
    echo '<th width="28%" align="center">操作</th>';
}
?>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($lists)){
	foreach($lists as $info){
?>   
	<tr>
<?php
if ($status >= 3 && $status < 7) {
    echo '<td align="center"><input type="checkbox" name="expressid[]" value="'.$info['id'].'"></td>';
}
?>
	<td align="center"><?php echo $info['expressno']?></td>
	<td align="center"><?php echo $info['detail']?></td>
	<td align="center"><?php echo $info['weight']?></td>
	<td align="center"><?php echo $info['pay_money']?></td>
	<td align="center"><?php echo $info['send_no']?></td>
<?php
        if ($status >= 3 && $status < 7) {
?>
	<td align="center">
    <a href="index.php?m=express&c=manage&a=set_next_status&id=<?php echo $info['id']?>&s=<?php echo $status;?>&pc_hash=<?php echo $_SESSION['pc_hash']?>">进入下一订单状态</a>
    </td>
<?php
        }
?>
	</tr>
<?php 
	}
}
?>
</tbody>
    </table>
<?php
if ($status >= 3 && $status < 7) {
?>
    <div class="btn"><label for="check_box">选择全部/取消</label>
        <input name="submit" type="submit" class="button" value="进入到下一个状态" onClick="return confirm('确定设置为下一个状态么')">&nbsp;&nbsp;</div>  
<?php
}
?>
</div>
 <div id="pages"><?php echo $pages?></div>
 <input type="hidden" name="s" value="<?php echo $status;?>">
<input type="hidden" name="dosubmit" value="1">
</form>
</div>
<script type="text/javascript">
<!--

//-->
</script>
</body>
</html>
