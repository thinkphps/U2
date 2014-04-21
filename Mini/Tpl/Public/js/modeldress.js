/**
 * Created by jack on 14-4-8.
 */

var pageElement = {
        $btnBuy : $('.buy_btn')
        ,$divBuys : $('.buy_btns')
        ,$btnExpansion : $('.syj_btn_expansion')    //右边浮动收缩模特按钮
        ,$divSyj : $('.syj')
        ,BarcodeList : []
        ,dressByBarcode:function(barcode,gender){
            Model.DressingByBarcode(barcode,gender);
            if(pageElement.$divSyj.is(':hidden')){
                pageElement.$btnExpansion.click();
            }
        }
        ,dressByBarcodeList:function(barcodeList){
            for(var i = 0;i < barcodeList.length;i++){
                Model.DressingByBarcode(barcodeList[i].item_bn,barcodeList[i].sex);
            }
            if(pageElement.$divSyj.is(':hidden')){
                pageElement.$btnExpansion.click();
            }
        }
    }

;(function($, window, document,undefined) {

    var ModelDress = function(){
//        this.ModelClothesList = []
    }

    ModelDress.prototype = {
        init : function(){
            this.showbaiyiModel();
            this.elementEvent();
            this.addClothesBySuitID();
        },
        showbaiyiModel : function(){
            /***新的百衣搭配间****/
            var touchid= 854;
            var key="8f1a6e3f182904ad22170f56c890e533";
            loadMymodel(touchid,key);
            Model.CurrClothesCallback = this.beu_getallclothes;
        },
        getUrlParam :function(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.search.substr(1).match(reg);  //匹配目标参数
            if (r!=null) return unescape(r[2]); return -1; //返回参数值
        },
        addClothesBySuitID : function(){
            var _this = this;
            var suitid = this.getUrlParam('suitid');
            if( suitid != -1){
                var gender = _this.getGenderValue(this.getUrlParam('gender'));

                $.post(getCidItembnUrl,{suitid:suitid},function(data){
                    var code = data.code;
                    if( code == 1){
                        var barcodeList = data.data;
                        for(var i = 0;i<barcodeList.length;i++){
                            Model.DressingByBarcode(barcodeList[i].barcode,gender);
                        }
                        pageElement.$btnExpansion.click();

                    }
                }).success(function(){
                    console.log(1);
                    console.log(pageElement.modelClothesList);
                });
            }
        },
        getGenderValue : function(gender){
            var genderValue = 15474;
            if(gender == 1){
                genderValue = 15474;
            }
            else if(gender == 2){
                genderValue = 15478;
            }
            else if(gender == 3){
                genderValue = 15583;
            }
            else if(gender == 4){
                genderValue = 15581;
            }
            else{
                genderValue = 15474;
            }
            return genderValue;
        },
        callDressingFunction : function(){
            pageElement.dressByBarcodeList($(this).data('detail'));
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
        addBuyBtns:function(){
            alert(1);
//            for(var barcode in  pageElement.BarcodeList ){
//                console.log(barcode);
//            }
        },
        //百衣试衣间回调函数
        beu_getallclothes : function(o){
            var _this = this;

            //清空购买列表
            var $buyUl =  pageElement.$divBuys.find('ul');
            $buyUl.html('');
            console.log($buyUl.html());
            var barcode = '';
            var strLi = '';
            var title = '';
            pageElement.modelClothesList = [];
            for(var i in o){
                barcode = o[i].barcode;
                title = o[i].name;
//                if(title.length > 8){
//                    title = title.substring(0,15) + "...";
//                }
                pageElement.BarcodeList.push({'barcode':barcode});

                //使用barcode去数据库中取回商品编号
//                $.post(getJackNumiidUrl,{'item_bn':barcode.substring(0,8)},function(data){
//                    console.log(data);
//                    var code = data.code;
//                    if( code == 1){
//                        var clothesInfo = data.data
                        strLi += '<li data-title="'+ title +'" data-numid=""><a href="javascript:;" title="'+ title+'"> '+ title+'</a></li>'
//                        pageElement.modelClothesList.push({'barcode':barcode,'num_iid':data.data});
//                    }
//                });
            }
            $buyUl.append(strLi);
        },

        elementEvent : function(){
            var _this = this;

            //点击模特图，将模特身上的衣服穿到白衣的模特身上
            $('#sfid').on('click','img',this.callDressingFunction);




            pageElement.$btnExpansion.on('click',function(){
                _this.objShowOrHide(pageElement.$divSyj);
                var $parentDiv = $(this).parent();
                if($parentDiv.hasClass('select')){
                    $parentDiv.removeClass('select');
                }
                else{
                    $parentDiv.addClass('select');
                }
            });

            pageElement.$btnBuy.on('click',function(){
                _this.objShowOrHide(pageElement.$divBuys);
            });

            $('.sc_btn').on('click',function(){
//                console.log(pageElement.modelClothesList);

            });


        }
    }

    var modelDress = new ModelDress();
    modelDress.init();


})(jQuery, window, document);