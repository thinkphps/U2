/**
 * Created by jack on 14-4-8.
 */
;(function($, window, document,undefined) {

    var pageElement = {
        $btnBuy : $('.buy_btn')
        ,$divBuys : $('.buy_btns')
        ,$btnExpansion : $('.syj_btn_expansion')    //右边浮动收缩模特按钮
        ,$divSyj : $('.syj')
    }

    var DressingRoomDetail = function(){

    }

    DressingRoomDetail.prototype = {
        init : function(){
            this.showbaiyiModel();
            this.elementEvent();
        },
        showbaiyiModel : function(){
            /***新的百衣搭配间****/
            var touchid= 854;
            var key="8f1a6e3f182904ad22170f56c890e533";
            loadMymodel(touchid,key);
        },
        callDressingFunction : function(){

        },
        //隐藏显示空间
        objShowOrHide : function(obj){
            if(obj.is(':hidden')){
                obj.show();
            }
            else{
                obj.hide();
            }
        },
        //百衣试衣间回调函数
        beu_getallclothes : function(o){
            //清空购买列表
//            cabnet.buybtns.find("ul").html("");
//            var barcode = "";
//            var uqcode = "";
//            for(var i in o){
//                barcode = o[i].barcode;
//                uqcode = barcode.substring(0,8);
//                //使用uq号去页面中查找衣服的名称、购买地址，如果找不到，则调用接口从数据库中取
//                var img = cabnet.net.find("img[uq='"+ o[i].barcode.substring(0,8) +"']");
//                if(img.length > 0){
//                    var title = img.attr("alt");
//                    if(title.length > 8){
//                        title = title.substring(0,15) + "...";
//                    }
//                    //将衣服信息增加到购买列表
//                    cabnet.buybtns.find("ul").append($('<li title="'+ img.attr("alt") +'"><a barcode="'+barcode+'" uqrcode="'+ uqcode +'" target="_blank" href="'+ img.attr("url") +'" >'+ title +'</a></li>'));
//                    //将衣服颜色缩略图设置为选中状态
//                    cabnet.colorImg.find("li a[barcode='"+ barcode +"']").parent().addClass("pro-selected");
//                }
//                else{
//
//                }
//            }
        },
        elementEvent : function(){
            var _this = this;
            //点击模特图，将模特身上的衣服穿到白衣的模特身上
            $('#sfid').on('click','img',this.callDressingFunction);

            pageElement.$btnExpansion.on('click',function(){
                _this.objShowOrHide(pageElement.$divSyj);
            });

            pageElement.$btnBuy.on('click',function(){
                _this.objShowOrHide(pageElement.$divBuys);
            });


        }
    }

    var drd = new DressingRoomDetail();
    drd.init();


})(jQuery, window, document);