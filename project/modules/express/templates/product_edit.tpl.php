<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>member_common.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
  $(document).ready(function() {
	var ok1=false;
    var ok2=false;
    var ok3=false;
    var ok4=false;
    if ( $('#bar_code').length==0) { ok1=true;}
    $('#bar_code').blur(function(){
     if($(this).val().length >= 1 && $(this).val().length <=200 && $(this).val()!=''){
     	$.ajax({
            type:"GET",
            url:"/index.php?m=express&c=product&a=public_checkebarcode_ajax"+"&bar_code="+$(this).val(),
            dataType:"json",
            success:function(data){
                if( data == "1" ) {
		                ok1=true;
					} else {
		               alert('条形码错误');
					}
            },
            error:function(jqXHR){
                alert('系统错误，请刷新页面重试');
            }
        });
      
     }else{
     	alert('请输入条形码');
     }
    });
    $('#country_goods_name').blur(function(){
     if($(this).val().length >= 1 && $(this).val().length <=200 && $(this).val()!=''){
     	 ok2=true; 
     }else{
     	alert('请输入日文名');
     }
    });

    $('#uprice').blur(function(){
     if($(this).val().length >= 1 && $(this).val().length <=200 && $(this).val()!=''){
     	 ok3=true; 
     }else{
     	alert('请输入单价');
     }
    });
    $('#uweight').blur(function(){
     if($(this).val().length >= 1 && $(this).val().length <=200 && $(this).val()!=''){
     	 ok4=true; 
     }else{
     	alert('请输入毛重');
     }
    });

    $('#dosubmit').click(function(){
 
     if(ok1 && ok2 && ok3 && ok4){
      $('#myform').submit();
     }else{
     	alert('请补充内容');
      	return false;
     }
    });

  });
</script>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="?m=express&c=product&a=edit" method="post" id="myform">
	<input type="hidden" name="info[id]" id="id" value="<?php echo $info['id']?>"></input>
	
<fieldset>
	
	<table width="100%" class="table_form">
		<tr>
			<td width="80">条形码<font color="red">(必填)</font></td> 
			<td><?php if(isset($info['bar_code'])) {echo $info['bar_code'];}else{ ?>
				<input type="text" name="info[bar_code]" id="bar_code" value="" class="input-text"></input>
			<?php  }?>
			</td>
		</tr>
		<tr>
			<td>状态<font color="red">(必填)</font></td> 
			<td>
				<?php echo form::radio(array('1'=>'有效', '0'=>'无效'), (int)$info['status'], 'name="info[status]"', '');?>
			</td>
		</tr>
		<tr>
			<td>日文名<font color="red">(必填)</font></td> 
			<td>
				<input type="text" name="info[country_goods_name]" id="country_goods_name" value="<?php echo $info['country_goods_name']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>中文名</td> 
			<td>
				<input type="text" name="info[ch_name]" id="ch_name" value="<?php echo $info['ch_name']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>英文名</td> 
			<td>
				<input type="text" name="info[en_name]" id="en_name" value="<?php echo $info['en_name']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>特殊商品<font color="red">(必填)</font></td> 
			<td>
				<?php echo form::radio(array('1'=>'特殊', '0'=>'否'), (int)$info['is_special'], 'name="info[is_special]"', '');?>
			</td>
		</tr>
		<tr>
			<td>类目</td> 
			<td>
				<input type="text" name="info[pcategory]" id="pcategory" value="<?php echo $info['pcategory']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>子类目</td> 
			<td>
				<input type="text" name="info[scategory]" id="scategory" value="<?php echo $info['scategory']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>规格</td> 
			<td>
				<input type="text" name="info[goodsmodel]" id="goodsmodel" value="<?php echo $info['goodsmodel']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>英文品牌名</td> 
			<td>
				<input type="text" name="info[productname]" id="productname" value="<?php echo $info['productname']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>计量单位</td> 
			<td>
				<input type="text" name="info[measur_unit]" id="measur_unit" value="<?php echo $info['measur_unit']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>单价(日元)<font color="red">(必填)</font></td> 
			<td>
				<input type="text" name="info[uprice]" id="uprice" value="<?php echo $info['uprice']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>毛重(g)<font color="red">(必填)</font></td> 
			<td>
				<input type="text" name="info[uweight]" id="uweight" value="<?php echo $info['uweight']; ?>" class="input-text"></input>
			</td>
		</tr>
		<tr>
			<td>生产国</td> 
			<td>
				<input type="text" name="info[product_area]" id="product_area" value="<?php echo $info['product_area']; ?>" class="input-text"></input>
			</td>
		</tr>

	</table>
</fieldset>
<div class="bk15"></div>

    <input name="dosubmit" id="dosubmit" type="button"  value="<?php echo L('submit')?>" class="dialog">
</form>
</div>
</div>
</body>
<script language="JavaScript">
function dosubmit(){

}

</script>
</html>