/**
 * Created by jack on 14-4-8.
 */

var pageElement = {
    BarcodeList : []
    ,IsHide : 0
    ,ChildrenType : 3
    ,$btnBuy : $('.buy_btn')
    ,$divBuys : $('.buy_btns')
    ,$btnExpansion : $('.syj_btn_expansion')    //右边浮动收缩模特按钮
    ,$divSyj : $('.syj')
    ,dressByBarcode:function(barcode,gender){
        Model.DressingByBarcode(barcode,gender);
        if(pageElement.$divSyj.is(':hidden')){
            pageElement.$btnExpansion.click();
        }
    }
    ,dressByBarcodeList:function(barcodeList){
        //穿套装的时候先清空模特
        Model.Empty();
        for(var i = 0;i < barcodeList.length;i++){
            Model.DressingByBarcode(barcodeList[i].item_bn,barcodeList[i].sex);
        }
        if(pageElement.$divSyj.is(':hidden')){
            pageElement.$btnExpansion.click();
        }
    },
    clearAllSelected : function(){
        $('#watercontainer .pro-selected').removeClass('pro-selected');

        $('#watercontainer .select').removeClass('select');

        $('#watercontainer .product_color').hide();

        $('#watercontainer .product_gender a').removeData('select_barcode');
    },
    clearSelected :function(){
        var barcodeList = this.BarcodeList;

        $('.pro-selected').each(function(){
            if($.inArray($(this).data('barcode'),barcodeList) < 0){
                $(this).removeClass('pro-selected');
//                if($(this.data))
            }
        });

        $('.product_color').each(function(){
            if(!$(this).is(':hidden')){
                if($(this).find('.pro-selected').length == 0){
                    $(this).hide();
                    $(this).parent().find('.tryon').removeClass('select');
                    $(this).parent().find('.product_gender a').removeData('select_barcode');
                }
            }
        });

    }
};

(function($, window, document,undefined) {

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
            var img = $(this).find('img')
            pageElement.dressByBarcodeList($(img.get(0)).data('detail'));
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
            pageElement.BarcodeList = [];
            if(o.length > 0){
                for(var i in o){
                    barcode = o[i].barcode;
                    title = o[i].name;
                    pageElement.BarcodeList.push(barcode);
//                if(title.length > 8){
//                    title = title.substring(0,15) + "...";
//                }
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

                pageElement.clearSelected();

            }else{
                //如果当前模特身上没有衣服，则清空页面上所有点击后
                if(pageElement.IsHide == 0){
                    pageElement.clearAllSelected();
                }
                else{
                    pageElement.clearSelected();
                }
            }
        }
        ,
        dressing :function(barcode,gender,sex,$wrapper_box,$colorLi){
            //如果性别为3：童装,则显示性别选择div
            if(sex == pageElement.ChildrenType ){
                $wrapper_box.find('.product_color').hide();
                var $product_gender = $wrapper_box.find('.product_gender');
                $product_gender.show();
                $product_gender.find('li a').each(function(){
                    $(this).removeClass('select');
                    if($(this).data('select_barcode') == barcode){
                        $(this).addClass('select');
                    }
                    $(this).data('barcode',barcode);
                })

            }else{
                pageElement.dressByBarcode(barcode,gender);
//                $colorLi.data('barcode',barcode);
//                $(this).data('prevBarcode',$colorImg.find('pro-selected').data('barcode'));
                $colorLi.parent().find("li").removeClass("pro-selected");
                $colorLi.addClass('pro-selected');
            }
        },
        clothingexisting : function(barcode){
            var bool = false;
            var item = cabnet.buybtns.find("ul li a[barcode='"+barcode+"']");
            if(item.length > 0){
                bool = true;
            }
            return bool;
        },
        getColorsHtml : function(colors){
            var colorHtml = '';
            var title = '';
            var imgurl = '';
            if(colors != null){
                for(var i = 0; i < colors.length; i++){
                    title =  colors[i].colorid + ' ' + colors[i].colorname;
                    imgurl =    '/' +  colors[i].colorcode.substring(0,colors[i].colorcode.lastIndexOf(".")) + ".jpg";
                    colorHtml += '<li data-barcode="'+ colors[i].uq + colors[i].colorid +'" title="'+title+'"><a href="javascript:;" data-colorid="'+ colors[i].colorid + '" ';
                    colorHtml += 'style="background:url('+ imgurl +') center no-repeat;background-size:cover;" ';
                    colorHtml += 'data-gender="'+ colors[i].gender+'" ';
                    colorHtml += 'data-uqcode="'+ colors[i].uq +'" ';
                    colorHtml += 'data-colorid="' + colors[i].colorid + '" ';
                    colorHtml += '</a><span>'+ title +'</span>';
                    colorHtml += '<i>已选中</i></li>';
                }
            }
            return colorHtml;
        },
        elementEvent : function(){
            var _this = this;

            //点击模特图，将模特身上的衣服穿到白衣的模特身上
            $('#sfid').on('click','.model',_this.callDressingFunction);

            pageElement.$btnExpansion.on('click',function(){
                if($(this).hasClass('ondrag')){
                    $(this).removeClass('ondrag');
                    return;
                }else{
                    _this.objShowOrHide(pageElement.$divSyj);
                    var $parentDiv = $(this).parent();
                    if($parentDiv.hasClass('select')){
                        $parentDiv.removeClass('select');
                    }
                    else{
                        $parentDiv.addClass('select');
                    }
                }
            });

            pageElement.$btnBuy.on('click',function(){
                _this.objShowOrHide(pageElement.$divBuys);
            });

            pageElement.$divSyj.on('mouseleave',function(){
                pageElement.$divBuys.hide();
            });

            $('.sc_btn').on('click',function(){
//                console.log(pageElement.modelClothesList);
                var arr   = Model.GetAllClothesCallback();
                for(var key in arr){
                    alert(arr[key].barcode);
                }
            });

            //鼠标移动到衣服上显示价格、库存等详细信息
            $('#watercontainer').on('mouseenter','.wrapper_box img',function(){
                $(this).parent().parent().find('.product_inf').show();
            });
            //鼠标移出隐藏价格、库存等详细信息
            $('#watercontainer').on('mouseleave','.wrapper_box',function(){
                var $this = $(this);
                $(this).find('.product_inf').hide();
                var $color_img = $this.find('.color-img');
                var $tryon =  $(this).find('.tryon');
                pageElement.IsHide = 0;
                $tryon.data('selected',0);
//                var selectBarcode = $children_gender.find('a').data('select_barcode');
                if( $color_img.find('.pro-selected').length == 0){
                    $this.find('.product_color').hide();
                    if($tryon.hasClass('select')){
                        $tryon.removeClass('select');
                    }
                }
            });

            //点击试穿按钮，显示颜色
            $('#watercontainer').on('click','.tryon',function(){
                pageElement.IsHide = 1;
                var $this = $(this);
                var $wrapper_box = $this.parent().parent().parent();
                var $colorImg = $wrapper_box.find('.color-img');
                $colorImg.html('').append(_this.getColorsHtml(eval($this.data('colors'))));
                $this.data('selected',1);
                $this.addClass('select');
                $wrapper_box.find('.product_color').show();
            });

            //点击色块将衣服添加到模特身上
            $('#watercontainer').on("click",".color-img li",function(){
                var $colorImg = $(this).parent();
                var $proInfo = $(this).find('a');
                var $wrapper_box =  $colorImg.parent().parent().parent();
                var barcode = $proInfo.data("uqcode") + $proInfo.data('colorid');
                var gender = $proInfo.data("gender");
                var sex = $wrapper_box.find('.tryon').data('gendertype');
                _this.dressing(barcode,gender,sex,$wrapper_box,$(this));
            });

            //点击男童、女童穿衣上身
            $('#watercontainer').on("click",".product_gender li",function(){
                var $wrapper_box = $(this).parent().parent().parent();
                var $a_gender = $(this).find('a');
                $(this).parent().find('li a').removeClass('select').removeData('select_barcode');
                $a_gender.addClass('select');
                var barcode = $a_gender.data("barcode");
                var gender = $a_gender.data("gender");
                pageElement.dressByBarcode(barcode,gender);
                $a_gender.data('select_barcode',barcode);
                $wrapper_box.find('.color-img li').each(function(){
                    if($(this).data('barcode') == barcode){
                        $wrapper_box.find(".color-img li").removeClass("pro-selected");
                        $(this).addClass('pro-selected');
                        return;
                    }
                });
            });
            //行性格选择层中移出时隐藏该层
            $('#watercontainer').on("mouseleave",".product_gender",function(){
                var $this = $(this);
                var $product_color = $this.parent().find('.product_color');
                $this.hide();
                $product_color.show();
            });
        }
    }

    var modelDress = new ModelDress();
    modelDress.init();


})(jQuery, window, document);