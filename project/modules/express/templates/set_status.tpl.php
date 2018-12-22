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
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
			<th>入库快递单号</th>
			<th>快递描述</th>
			<th>重量</th>
			<th>支付金额</th>
			<th>出库快递单号</th>
			<th>物流状态操作</th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($lists)){
	foreach($lists as $info){
?>   
	<tr>
	<td align="center"><?php echo $info['expressno']?></td>
	<td align="center"><?php echo $info['detail']?></td>
	<td align="center"><?php echo $info['weight']?></td>
	<td align="center"><?php echo $info['pay_money']?></td>
    <td align="center"><?php echo $info['send_no'] ?></td>
	<td align="center">
<?php
        $time_node_arr = unserialize($info['time_node']);
        if (!is_array($time_node_arr)) {
            $time_node_arr = [];
        }
        foreach ($nodes as $val) {
            $name = $val['name'];
            $value = $val['value'];
            if (isset($time_node_arr[$value])) {
                echo '['.$name.L('icon_unlock').']';
            } else {
                echo '['.$name.'<a href="javascript:void(0);" onclick="set_status(this,\''.$info['expressno'].'\',\''.$value.'\''.')">'.L('icon_locked').'</a>]';
            }
        }
    ?>
	</td>
	</tr>
<?php 
	}
}
?>
</tbody>
    </table>
 <div id="pages"><?php echo $pages?></div>
</div>
<script type="text/javascript">
<!--
    function set_status(obj,expressno,node){
        $.get("index.php?m=express&c=manage&a=set_status&expressno="+expressno+"&node="+node+"&pc_hash="+"<?php echo $_SESSION['pc_hash']?>",function(data){
            if (data=="1") {
                obj.innerHTML = "<font color='red'>√</font>";
            } else {
                window.top.art.dialog({
                    content:'请按顺序完成这些状态',
                    cancelVal: '关闭',
                    cancel: true
                });
            }
        });
    }

//-->
</script>
</body>
</html>
