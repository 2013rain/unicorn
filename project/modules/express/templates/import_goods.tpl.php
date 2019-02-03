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


<form name="myform" action="" method="post" enctype="multipart/form-data" onsubmit="return checkSize();">
<div class="table-list">
        <div class="explain-col">
            <div>注意：程序会忽略第一行（第一行为标题）,上传文件尽量不要超过3M</div>

    </div>
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
            <input type="file" name="express_data" id="express_data">
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728" />
            <input name="import" type="hidden" value="1">
            <input type="submit"  class="button" id="mysub" value="批量导入商品" />
		</td>
		</tr>
    </tbody>
</table>

</div>

</form>
</div>
<script type="text/javascript">
var maxsize = 3145728;//2M
var errMsg = "上传的附件文件不能超过3M！！！";
var tipMsg = "您的浏览器暂不支持计算上传文件的大小，确保上传文件不要超过3M，建议使用IE、FireFox、Chrome浏览器。";

var  browserCfg = {};
var ua = window.navigator.userAgent;
if (ua.indexOf("MSIE")>=1){
browserCfg.ie = true;
}else if(ua.indexOf("Firefox")>=1){
browserCfg.firefox = true;
}else if(ua.indexOf("Chrome")>=1){
browserCfg.chrome = true;
}
function checkSize() {
    var obj_file = document.getElementById("express_data");
    if(obj_file.value==""){
      alert("请先选择上传文件");
      return false;
     }
     var filesize = 0;
     if(browserCfg.firefox || browserCfg.chrome ){
      filesize = obj_file.files[0].size;
     }else if(browserCfg.ie){
      var obj_img = document.getElementById('tempimg');
      obj_img.dynsrc=obj_file.value;
      filesize = obj_img.fileSize;
     }else{
      alert(tipMsg);
       return false;
     }
     if(filesize==-1){
      alert(tipMsg);
      return false;
     }else if(filesize>maxsize){
      alert(errMsg);
      return false;
    }else{
        $("#mysub").val("正在上传");
        $("#mysub").attr({"disabled":"true"});
      return true;
    }
   }
  
</script>
</body>
</html>
