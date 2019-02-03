<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>

<div class="pad-10">
<div class="common-form">

<div class="bk15"></div>
<div class="pad-lr-10">


<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
				快递编号：
				<input name="expressno" id="expressno" type="text" onkeypress="return EnterTextBox(1);" placeholder="输入快递编号"  class="input-text" />
				
				<input type="button"  class="button" onclick="searchExpress()" value="&nbsp;&nbsp;搜索快递&nbsp;&nbsp; " />
				
				&nbsp;&nbsp;&nbsp;&nbsp;
				商品条形码：
				<input name="bar_code" id="bar_code" type="text" onkeypress="return EnterTextBox(2);"  placeholder="输入商品条形码" class="input-text" />
				
				<input type="button" class="button" onclick="addProduct()" value="&nbsp;&nbsp;入库商品&nbsp;&nbsp; " />
				

		</div>
		</td>
		</tr>
    </tbody>
</table>

<div class="bk15"></div>


<form name="myform" action="?m=express&c=manage&a=scan_in_store" method="post" >
<input type="hidden" name="dosubmit" value="1">
<input type="hidden" name="expressno" id="myexpressno" value="">
<input type="hidden" name="opt" id="myopt" value="1">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left" >快递编号</th>
			<th align="left" >商品条形码</th>
			<th align="left" width="150">商品名</th>
			<th align="left">数量</th>
			<th align="left">入库数量</th>
		</tr>
	</thead>
<tbody id="tbody">
    
</tbody>
</table>
<div class="btn">
<label>快递单称重：<input name="weight" id="weight" type="text"  class="input-text" />(kg)</label>&nbsp; 
<input type="button" class="button"  value=" 确认入库 " onclick="instore()" />
&nbsp; &nbsp; 
问题订单备注：<input name="cause" id="cause" type="text"  class="input-text" /></label>
<input type="button" class="button"  value=" 转为问题订单 " onclick="problem()" />
</div>

</div>
</form>
</div>
<script type="text/javascript">
<!--
var td_html = "<tr><td align=\"left\" >%expressno% </td><td align=\"left\" id=\"goods_%bar_code%\" >%bar_code% %new%</td>"
td_html += "<td align=\"left\" >%goodsname%</td><td align=\"left\" >%num%</td>"
td_html += "<td align=\"left\"  >%hidden%</td></tr>"
var hiddenForm1 = "<input type=\"hidden\" name=\"goods[%idx%][id]\" value=\"%id%\"><input type=\"number\" name=\"goods[%idx%][num]\" id=\"rnum_%bar_code%\" value=\"0\">" 
var hiddenForm2 = "<input type=\"hidden\" name=\"product[%idx%][bar_code]\" value=\"%bar_code%\"><input type=\"number\" name=\"product[%idx%][num]\" id=\"rnum_%bar_code%\" value=\"1\">" 
var idx=0;
var expressno;

function EnterTextBox(where) {
     if (event.keyCode == 13) {
     	if (where=="1") {
     		searchExpress()
     	}
     	if (where=="2") {
     		addProduct()
     	}

     }
 }

function searchExpress() {

	expressno = $("#expressno").val();
	$("#expressno").attr("disabled",true);

	if (expressno=='') {
		alert("请输入快递");
		$("#expressno").removeAttr("disabled");
		return false;
	}
 		$.ajax({
             type: "GET",
             url: "?m=express&c=manage&a=public_getexpress_ajax&expressno="+expressno,
             data: "{expressno:"+expressno+"}",
             dataType: "json",
             success: function(data){
               if (data.code==1) {
               	  alert(data.msg)
               } else {
               		$("#myexpressno").val(expressno);
					createHtml(data.info)
               }
             }
        });
    $("#expressno").removeAttr("disabled");      
}
function addProduct() {
	var bar_code = $("#bar_code").val();
	$("#bar_code").attr("disabled",true);
	if (bar_code=="") {
		alert("请输入条形码");
		$("#bar_code").removeAttr("disabled");
		return false;
	}
	if ($("#rnum_"+bar_code).length>0) {
		oldnum = $("#rnum_"+bar_code).val();
		oldnum = parseInt(oldnum,10);
		$("#rnum_"+bar_code).val(oldnum+1);
		$("#num_"+bar_code).val(oldnum+1);
		$("#bar_code").removeAttr("disabled");
		return true;
	}
	$.ajax({
             type: "GET",
             url: "?m=express&c=manage&a=public_getproduct_ajax&bar_code="+bar_code,
             data: "{bar_code:"+bar_code+"}",
             dataType: "json",
             success: function(data){
               if (data.code==1) {
               	  alert(data.msg)
               } else {
					createLine(data.info);

					return true;
               }
             }
        });
	$("#bar_code").removeAttr("disabled");
}
function instore() {
	$("#myopt").val("1");
	weight=$("#weight").val();
	if(expressno=='') {
		alert("内容错误，无法完成提交");
		return false;
	} else  if(weight=='') {
		alert("填写重量");
		return false;
	} else if (confirm("入库操作将会使用‘入库数量’覆盖当前数量，确定入库？")) {
		myform.submit();
	}
}
function problem() {
	$("#myopt").val("-1");
	cause=$("#cause").val();
	if(expressno=='') {
		alert("内容错误，无法完成提交");
		return false;
	} else if (cause=="") {
		alert("请填写问题备注");
		return false;
	} else {
		myform.submit();
	}
}

function createHtml(data) {
	var html;
	myexpressno = $("#myexpressno").val();
	$.each(data.goods_list, function(index,value){
	     html+=td_html.replace(/%hidden%/g, hiddenForm1); 
	     html =html.replace(/%new%/g, "<font></font>");
	     html= html.replace(/%expressno%/g, myexpressno);
	     html= html.replace(/%bar_code%/g, value.bar_code);
	     html= html.replace(/%goodsname%/g, value.goodsname);
	     html= html.replace(/%num%/g, value.num);
	     html= html.replace(/%idx%/g, idx);
	     html= html.replace(/%id%/g, value.id);
	     idx++;
	});
	html =html.replace(/%new%/g, "<font></font>");
	$("#tbody").append(html);
}

function createLine(data) {
	var html;
	myexpressno = $("#myexpressno").val();
	html =td_html.replace(/%hidden%/g, hiddenForm2);
	 html= html.replace(/%expressno%/g, myexpressno);
	 html =html.replace(/%new%/g, "<font color=\"red\">New</font>");
	 html= html.replace(/%bar_code%/g, data.bar_code);
	 html= html.replace(/%goodsname%/g, data.ch_name);
	 html= html.replace(/%num%/g, 0);
	 html= html.replace(/%idx%/g, idx);

	 idx++;

	$("#tbody").append(html);
}



//-->
</script>
</body>
</html>