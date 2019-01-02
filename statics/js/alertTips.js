/*
* @Author: Marte
* @Date:   2018-12-29 15:12:33
* @Last Modified by:   Marte
* @Last Modified time: 2019-01-02 15:25:22
*/

'use strict';
// window.onload=function(){
    window.alertTips=function(title,txt){

        //创建遮罩层
        var tipsLayer=document.createElement("div");
        tipsLayer.className="alertTips-layer";

        //创建弹窗窗口
        var alertBox=document.createElement("div");
        alertBox.className="alertTips-alert-box";

        //创建窗口里的内容
        var alertContent='<div class="alertTips-top-box"><span>'+title+'</span><a href="javascript:void(0);" onclick="doOk();"></a></div>';
        alertContent+='<div class="alertTips-center-box">'+txt+'</div>';
        alertContent+='<div class="alertTips-bottom-box"><button class="bg-blue" onclick="doOk();">确定</button></div>';
        alertBox.innerHTML=alertContent;


        document.body.appendChild(tipsLayer);
        document.body.appendChild(alertBox);

        // this.isModel=true; //模态框显示
    　　document.getElementsByTagName("body")[0].style.cssText='height:100%;overflow:hidden;'; //设置此样式取消滚动条

        //隐藏提示框函数
    　　document.doOk=function(){
            tipsLayer.parentNode.removeChild(alertBox);
            tipsLayer.parentNode.removeChild(tipsLayer);

            // this.isModel=false;//模态框消失
        　　document.getElementsByTagName("body")[0].style.cssText='';//滚动条恢复
        }
    }
// }

//分页统一使用，获取页码部分宽度 居中用
$(function () {
    var w=0;
    $(".pages-pay-l a").each(function() {
        w+=(parseInt($(this).width()) + 4);
        console.log(w);
    });
    w+=14;
    $(".pages-pay-l").width(w);
});