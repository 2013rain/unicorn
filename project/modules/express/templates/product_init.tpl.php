<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">

<div class="bk15"></div>
<div class="pad-lr-10">
<form name="searchform" action="" method="get" >
<input type="hidden" value="express" name="m">
<input type="hidden" value="product" name="c">
<input type="hidden" value="init" name="a">

<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
				
				条形码：
				<input name="barcode" type="text" value="<?php if(isset($_GET['barcode'])) {echo $_GET['barcode'];}?>" class="input-text" />
				日文名：
				<input name="jpname" type="text" value="<?php if(isset($_GET['jpname'])) {echo $_GET['jpname'];}?>" class="input-text" />
				中文名：
				<input name="zhname" type="text" value="<?php if(isset($_GET['zhname'])) {echo $_GET['zhname'];}?>" class="input-text" />
	</div>
		</td>
		</tr>
    </tbody>
</table>

<input type="submit" name="search" class="button" value=" <?php echo L('search')?> " />
<input type="button" onclick="javascript:edit(0, '新增商品')" class="button" value=" 新增商品 " />

</form>
<div class="bk15"></div>


<form name="myform" action="?m=express&c=product&a=delete" method="post" onsubmit="return checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('productid[]');"></th>
			<th align="left">id</th>
			<th align="left">条形码</th>
			<th align="left" width="150">日文名</th>
			<th align="left" width="150">中文名</th>
			<th align="left">状态</th>
			<th align="left">是否特殊</th>
			<th align="left">规格型号</th>

			<th align="left">计量单位</th>
			<th align="left">单价</th>

			<th align="left">毛重</th>
			<th align="left">生产国</th>
			<th align="left" width="150">录入时间</th>
			<th align="left">操作</th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($productlist)){
	foreach($productlist as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="productid[]"></td>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><?php echo $v['bar_code']?></td>
		<td align="left"><?php echo $v['country_goods_name']?></td>
		<td align="left"><?php echo $v['ch_name']?></td>
		<td align="left"><?php if ($v["status"]==1){echo "有效";}else {echo "无效";} ?></td>
		<td align="left"><?php if ($v["is_special"]==1){echo "特殊";}else {echo "普通";} ?></td>

		<td align="left"><?php echo $v['goodsmodel'];?></td>
		<td align="left"><?php echo $v['measur_unit'];?></td>
		<td align="left"><?php echo $v['uprice']?></td>
		<td align="left"><?php echo $v['uweight']?></td>
		<td align="left"><?php echo $v['product_area']?></td>
		<td align="left"><?php echo $v['create_time']?></td>
		<td align="left">
			<a href="javascript:edit(<?php echo $v['id']?>, '编辑商品')">[<?php echo L('edit')?>]</a>
			<a >[<?php echo L('delete')?>]</a>
		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>

<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label>&nbsp; 
<input type="submit" class="button" name="dosubmit" value=" 删 除 " onclick="return confirm('确定批量删除？')" />


</div>

<div id="pages"><?php echo $pages?></div>
</div>
</form>

</div>
<script type="text/javascript">
<!--
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:name,id:'edit',iframe:'?m=express&c=product&a=edit&id='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}

function checkuid() {
	var ids='';
	$("input[name='productid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});

	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}


//-->
</script>
</body>
</html>