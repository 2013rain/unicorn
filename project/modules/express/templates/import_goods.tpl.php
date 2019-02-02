<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<style type="text/css">
.import_file {display:hidden}
</style>
<div class="pad-lr-10">
		<div class="explain-col">
            <div>说明：本功能为<b>批量导入商品</b>操作。<br>
                必填项：条形码、状态、特殊商品、日文名、价格（日元）、毛重（g）<br>
            </div>
	   </div>
       <div class="explain-col">
     
            <a href="?m=express&c=product&a=import_goods&template=1"   class="button" target="_blank" >下载商品模板文件</a>
       </div>


<form name="myform" action="" method="post" enctype="multipart/form-data">
<div class="table-list">
        <div class="explain-col">
            <div>注意：程序会忽略第一行（第一行为标题）</div>

    </div>
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
            <input type="file" name="express_data">
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728" />
            <input name="import" type="hidden" value="1">
            <input type="submit"  class="button" value="批量导入商品" />
		</td>
		</tr>
    </tbody>
</table>

</div>

</form>
</div>
</body>
</html>
