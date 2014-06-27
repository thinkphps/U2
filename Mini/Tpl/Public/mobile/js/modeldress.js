/**
 * Created by jack on 14-4-8.
 */

var pageElement = {
    BarcodeList : []
    ,IsHide : 0
    ,ChildrenType : 4
    ,Ischanged : 0
    ,$btnBuy : $('.buy_btn')
    ,$divBuys : $('.buy_btns')
    ,$btnExpansion : $('.syj_btn_expansion')    //右边浮动收缩模特按钮
    ,$divSyj : $('.syj')
    ,$ulgender : $('#ulgender')
    ,$watercontainer : $('#watercontainer')
    ,dressByBarcode:function(barcode,gender){
        $('#beubeu_loadImg').show();
        $('#baby_fitting_room').hide();
        Model.DressingByBarcode(barcode,gender);
        if(pageElement.$divSyj.is(':hidden')){
            pageElement.$btnExpansion.click();
        }
    }
    ,dressByBarcodeList:function(suitInfo){
        $('#beubeu_loadImg').show();
        $('#baby_fitting_room').hide();
        get_baiyi_dp(suitInfo[0],suitInfo[1]);
        if(pageElement.$divSyj.is(':hidden')){
            pageElement.$btnExpansion.click();
        }
    },
    currentColRelayout : function($proInfo,changeType){
        var leftPx = $proInfo.position().left
            ,topPx = $proInfo.position().top
            ,addHeight = $proInfo.find('.product_color').outerHeight();
        pageElement.$watercontainer.children('.productinfo').each(function(){
            var thisposition =  $(this).position();
            if(thisposition.left == leftPx && thisposition.top > topPx){
                $(this).css('top',thisposition.top + addHeight * changeType);
            }
        });

        $('#watercontainer').height($('#watercontainer').height() + addHeight * changeType);
    },
    clearAllSelected : function(){
        $('#watercontainer .pro-selected').removeClass('pro-selected');

        $('#watercontainer .select').removeClass('select');

        $('#watercontainer .product_color').hide();

        $('#watercontainer .product_gender a').removeData('select_barcode');
        $('#watercontainer').waterfall('reLayout');
    },
    clearSelected :function(){
        var barcodeList = this.BarcodeList;

        $('.pro-selected').each(function(){
            if($.inArray($(this).data('barcode'),barcodeList) < 0){
                $(this).removeClass('pro-selected');
            }
        });

        $('.product_color').each(function(){
            if(!$(this).is(':hidden')){
                if($(this).find('.pro-selected').length == 0){
                    $(this).hide();
                    var $tryon = $(this).parent().find('.tryon');
                    if($tryon.hasClass('select')){
                        $tryon.removeClass('select');
                        pageElement.currentColRelayout($($(this).parent().parent()[0]),-1);
                    }
                    $(this).parent().find('.product_gender a').removeData('select_barcode');
                }
            }
        });
    },
    getUrlParam :function(name){
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r!=null) return unescape(r[2]); return -1; //返回参数值
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
    }
};

//白衣回调函数
function Model_loadok_callback(){
    var suitid = pageElement.getUrlParam('suitid');
    if( suitid != -1){
        var sex = pageElement.getUrlParam('gender');
        var gender = pageElement.getGenderValue(sex);
        get_baiyi_dp(suitid,gender);
    }
}

(function($, window, document,undefined) {

    var ModelDress = function(){
    }

    ModelDress.prototype = {
        init : function(){
            var _this = this;
            _this.elementEvent();
            _this.showbaiyiModel(_this.addClothesBySuitID);
//            setTimeout(function(){
//                _this.addClothesBySuitID();
//            },1500);

        },
        showbaiyiModel : function(callback){
            /***新的百衣搭配间****/
            var touchid= 854;
            var key="8f1a6e3f182904ad22170f56c890e533";
            loadMymodel(touchid,key);
            Model.CurrClothesCallback = this.beu_getallclothes;
            $('.beubeu_btns').css({'left':'0em','top':'16.3em'});
            $('.beubeu_btns3').css('left','-0em').css('top','13em');
            $('#showmodelhead').css({'top':'14em','left':'0em'});
            $('#beubeu_shoes').css({'top':'13em','left':'4em'});
            $('#beubeu_loading2').css({'height':'24em','left':'5em'});
            $('#beu_showimg').css({'position':'absolute','top':'-1em'});
            $('#show_csw').css({'left':'17.9em','top':'15.9em'});
            $('#beubeu_copy_c').css({'left':'6.5em','top':'17.5em'});
            $('#beubeu_copy_lw').css({'left':'2.4em','top':'2.3em'});
            $('#beu_img').css({'width':'28em','height':'34em'});
            $('.beubeu_list').css({'top':'12em','left':'-1em'});
            $('#beubeu_copy_c').css({'left':'2em'});
            $('#beubeu_copy_lw').css({'top':'366px'});

            if ( callback ) {
                callback.call(this);
            }
        },

        addClothesBySuitID : function(){
            var _this = this;
            var suitid = pageElement.getUrlParam('suitid');
            if( suitid != -1){
                var sex = pageElement.getUrlParam('gender');
                var gender = pageElement.getGenderValue(sex);

                //根据首页迁移过来的性别显示背景
                pageElement.$ulgender.find('a').removeClass('select');
                if(sex==1){
                    pageElement.$ulgender.find('a:eq(0)').addClass('select');
                    $('.changjing1').css("background","url("+imgpath+"/images/my_yyg_bg1.jpg) center 0 no-repeat");
                }else if(sex==2){
                    pageElement.$ulgender.find('a:eq(1)').addClass('select');
                    $('.changjing1').css("background","url("+imgpath+"/images/my_yyg_bg2.jpg) center 0 no-repeat");
                }else if(sex==3 || sex == 4){
                    pageElement.$ulgender.find('a:eq(2)').addClass('select');
                    $('.changjing1').css("background","url("+imgpath+"/images/my_yyg_bg0.jpg) center 0 no-repeat");
                }
                _mini.getSuits();
                pageElement.$btnExpansion.click();
            }
        },
        callDressingFunction : function(){
            var $img = $(this).find('img')
            pageElement.dressByBarcodeList($img.data('detail'));
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
            var barcode = '';
            var strLi = '';
            var title = '';
            pageElement.Ischanged = 1;
            pageElement.BarcodeList = [];
            if(o.length > 0){
                for(var i in o){
                    barcode = o[i].barcode;
                    title = o[i].name;
                    pageElement.BarcodeList.push(barcode);
//                    strLi += '<li data-title="'+ title +'" ><a target="_blank" href="#" data-barcode="'+ barcode +'" title="'+ title+'"> '+ title+'</a></li>'
                    strLi += '<li data-title="'+ title +'" ><div class="buyurl">';
                    //strLi += '<span title="'+title+'" data-barcode="'+barcode+'">'+title+'</span>'
                    strLi += '<span title="" data-barcode="'+barcode+'"></span>'
                    strLi +='</div></li>'
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
        dressing :function(barcode,gender,sex,isud,$wrapper_box,$colorLi){
            $('#beubeu_loadImg').show();
            $('#baby_fitting_room').hide();
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

            }
            else{
                pageElement.dressByBarcode(barcode,gender);
                $colorLi.parent().find("li").removeClass("pro-selected");
                $colorLi.addClass('pro-selected');

            }

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
                    colorHtml += 'data-num_iid="'+ colors[i].num_iid+'" ';
                    colorHtml += 'data-uqcode="'+ colors[i].uq +'" ';
                    colorHtml += 'data-colorid="' + colors[i].colorid + '" ';
                    colorHtml += 'data-imgurl="'+ imgurl +'"> ';
                    colorHtml += '</a><span>'+ title +'</span>';
                    colorHtml += '<i>已选中</i></li>';
                }
            }
            return colorHtml;
        },
        setBuyInfo : function(){
            pageElement.$divBuys.find('span').each(function(){
                var $this = $(this);
                //使用barcode去数据库中取回商品编号
                $.post(getJackNumiidUrl,{'item_bn':$this.data('barcode').substring(0,8)},function(data){
                    if( data.code == 1){
                        var clothesInfo = data.data
                        $this.data('buy_url',clothesInfo.detail_url);
                        if(clothesInfo.num<=0){
                        $this.html('<font style="color:red;font-weight:bold;">已售罄</font>'+clothesInfo.title);
                        $this.attr('title','(已售罄)'+clothesInfo.title);
                       }else{
                        $this.html(clothesInfo.title);
                        $this.attr('title',clothesInfo.title);
                        }
                    }
                });
            });
            pageElement.Ischanged = 0;
        },

        elementEvent : function(){
            var _this = this;

            //点击模特图，将模特身上的衣服穿到白衣的模特身上
            $('#sfid').on('click','.model',_this.callDressingFunction);

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
                //如果当前选中的是婴儿，则将现在搭配间的衣服增加到购买列表
                if($('#beubeu_loadImg').is(':hidden')){
                    var $buyUl =  pageElement.$divBuys.find('ul');
                    $buyUl.html('');
                    var strLi = '';
                    if($('#single').is(':hidden')){
                        var $topImg = $('#tops_bottoms img');
                        strLi += '<li data-title="'+ $topImg.data('title') +'" ><div class="buyurl">';
                        strLi += '<span title="'+title+'" data-buy_url="'+$topImg.data('prourl')+'">'+ $topImg.data('title')+'</span>'
                        strLi +='</div></li>'
                        var $bottomsImg = $('#bottoms img');
                        strLi += '<li data-title="'+ $bottomsImg.data('title') +'" ><div class="buyurl">';
                        strLi += '<span title="'+title+'" data-buy_url="'+$bottomsImg.data('prourl')+'">'+ $bottomsImg.data('title')+'</span>'
                        strLi +='</div></li>'
                    }else{
                        var $myImg =  $('#single img');
                        strLi += '<li data-title="'+ $myImg.data('title') +'" ><div class="buyurl">';
                        strLi += '<span title="'+title+'" data-buy_url="'+$myImg.data('prourl')+'">'+ $myImg.data('title')+'</span>'
                        strLi +='</div></li>'
                    }
                    $buyUl.append(strLi);
                }else{
                    if(pageElement.Ischanged == 1){
                        _this.setBuyInfo();
                    }
                }
                _this.objShowOrHide(pageElement.$divBuys);
            });

            pageElement.$divSyj.on('mouseenter','.buyurl',function(){
                $(this).css({'background':'#999','color':'#fff'});
            }).on('mouseleave','.buy_btns .buyurl',function(){
                $(this).css({'background':'#fff','color':'#666'});
            }).on('click','.buy_btns span',function(){
                if($(this).data('buy_url')){
                window.open( $(this).data('buy_url'));
                }
            });

            pageElement.$divSyj.on('mouseleave',function(){
                pageElement.$divBuys.hide();
            });

            //点击衣服调用试穿按钮功能
           /* pageElement.$watercontainer.on('click','.product_inf',function(){
                $(this).parent().find('.tryon').click();
            });
            //点击衣服调用试穿按钮功能
            pageElement.$watercontainer.on('click','.product_img',function(){
                $(this).parent().parent().find('.tryon').click();
            });

            //点击衣服 显示价格、库存等详细信息
            pageElement.$watercontainer.on('click','.wrapper_box img',function(){
                //如果已显示价格图层，就隐藏
                var $productInfo = $(this).parent().parent().find('.product_inf');
               if($productInfo.is(':hidden')){
                   $productInfo.show();
               }else{
                   $productInfo.hide();
               }
            });*/

            pageElement.$watercontainer.on('click','.product_inf',function(){
                //如果已显示价格图层，就隐藏
                var $productInfo = $(this);
                if($productInfo.is(':hidden')){
                    $productInfo.show();
                }else{
                    $productInfo.hide();
                }
            });

            //鼠标移出隐藏色块
            /*pageElement.$watercontainer.on('mouseleave','.wrapper_box',function(){
                var $this = $(this);
//                $(this).find('.product_inf').hide();
                var $color_img = $this.find('.color-img');
                var $tryon =  $(this).find('.tryon');
                var gender = $tryon.data('gendertype');
                if(gender == 5){
                    var isud = $tryon.data('isud');
                    if(isud == 1){
                        if(!$('#tops_bottoms').is(':hidden')){
                            if($tryon.data('imgurl') != $('#tops_bottoms img').attr('src')){
                                $tryon.removeClass('select');
                            }
                        }
                    }else if(isud == 2){   //如果是下装，则将图片显示到下部
                        if(!$('#bottoms').is(':hidden')){
                            if($tryon.data('imgurl') != $('#bottoms img').attr('src')){
                                $tryon.removeClass('select');
                            }
                        }
                    }
                    else{
                        if(!$('#single').is(':hidden')){
                            if($tryon.data('imgurl') != $('#single img').attr('src')){
                                $tryon.removeClass('select');
                            }
                        }
                    }
                }else{
                    pageElement.IsHide = 0;
                    if( $color_img.find('.pro-selected').length == 0){
                        $this.find('.product_color').hide();
                        if($tryon.hasClass('select')){
                            $tryon.removeClass('select');
                            $tryon.data('selected',0);
                            pageElement.currentColRelayout($($this.parent()[0]),-1);
                        }
                    }
                }
            });*/

            //点击试穿按钮，显示颜色
            $('#watercontainer').on('click','.tryon',function(){
                var $this = $(this);
                if(!$this.hasClass('select')){
                    pageElement.IsHide = 1;
                    var $wrapper_box = $this.parent();
                    var gender = $this.data('gendertype');
                    $this.addClass('select');
                    if( gender == 5){
                        var isud = $this.data('isud');
                        $('#beubeu_loadImg').hide();
                        $('#baby_fitting_room').show();
                        var imgUrl = $wrapper_box.find('.product_img').attr('src');
                        //isud：1为上装2为下装3为配饰4为套装5为内衣6为婴幼儿
                        //如果是上装则将图片显示到上部
                        var title = $wrapper_box.find('h3 a').text();
                        var purl = $wrapper_box.find('h3 a').attr('href');
                        $this.data('imgurl',imgUrl);
                        if(isud == 1){
                            $('#tops_bottoms').show();
                            $('#single').hide();
                            if($('#bottoms img').attr('src').length > 2 ){
                                $('#bottoms').show();
                            }
                            $('#tops_bottoms img').attr('src',imgUrl).data({'prourl':purl,'title':title});
                            $('#single img').attr('src','#').removeData('prourl').removeData('title');;
                        }else if(isud == 2){   //如果是下装，则将图片显示到下部
                            $('#bottoms').show();
                            $('#single').hide();
                            if($('#tops_bottoms img').attr('src').length > 2 ){
                                $('#bottoms').show();
                            }
                            $('#bottoms img').attr('src',imgUrl).data({'prourl':purl,'title':title});
                            $('#single img').attr('src','#').removeData('prourl').removeData('title');;
                        }
                        else{
                            $('#tops_bottoms,#bottoms').hide();
                            $('#tops_bottoms img,#bottoms img').attr('src','#').removeData('prourl').removeData('title');
                            $('#single').show();

                            $('#single img').attr('src',imgUrl).data({'prourl':purl,'title':title});
                        }

                        pageElement.$watercontainer.find('.tryon').each(function(){
                            if($(this).hasClass('select')){
                                if($(this).data('imgurl') != $('#tops_bottoms img').attr('src') && $(this).data('imgurl') != $('#bottoms img').attr('src')
                                    &&  $(this).data('imgurl') != $('#single img').attr('src')){
                                    $(this).removeClass('select');
                                }
                            }
                        });

                        if(pageElement.$divSyj.is(':hidden')){
                            pageElement.$btnExpansion.click();
                        }
                    }
                    else{

                        var $colorImg = $wrapper_box.find('.color-img');
                        $colorImg.html('').append(_this.getColorsHtml(eval($this.data('colors'))));
                        $wrapper_box.find('.product_color').show();
                        pageElement.currentColRelayout($($wrapper_box.parent()[0]),1);

                    }
//                $this.data('selected',1);
                }else{
                    //再次点击
                    $this = $(this).parent();
                    var $color_img = $this.find('.color-img');
                    var $tryon =  $(this).parent().find('.tryon');
                    var gender = $tryon.data('gendertype');
                    if(gender == 5){
                        var isud = $tryon.data('isud');
                        if(isud == 1){
                            if(!$('#tops_bottoms').is(':hidden')){
                                if($tryon.data('imgurl') != $('#tops_bottoms img').attr('src')){
                                    $tryon.removeClass('select');
                                }
                            }
                        }else if(isud == 2){   //如果是下装，则将图片显示到下部
                            if(!$('#bottoms').is(':hidden')){
                                if($tryon.data('imgurl') != $('#bottoms img').attr('src')){
                                    $tryon.removeClass('select');
                                }
                            }
                        }
                        else{
                            if(!$('#single').is(':hidden')){
                                if($tryon.data('imgurl') != $('#single img').attr('src')){
                                    $tryon.removeClass('select');
                                }
                            }
                        }
                    }else{
                        pageElement.IsHide = 0;
                        if( $color_img.find('.pro-selected').length == 0){
                            $this.find('.product_color').hide();
                            if($tryon.hasClass('select')){
                                $tryon.removeClass('select');
                                $tryon.data('selected',0);
                                pageElement.currentColRelayout($($this.parent()[0]),-1);
                            }
                        }
                    }
                }
            });

            //点击色块将衣服添加到模特身上
            $('#watercontainer').on("click",".color-img li",function(){
                var $colorImg = $(this).parent();
                var $proInfo = $(this).find('a');
                var $wrapper_box =  $colorImg.parent().parent().parent();
                var barcode = $proInfo.data('uqcode') + $proInfo.data('colorid');
                var gender = $proInfo.data('gender');
                var isud = $proInfo.data('isud');
                var sex = $wrapper_box.find('.tryon').data('gendertype');
                //kimi
                var $cselected = $('#watercontainer').find('li.pro-selected').children('a');
                if($cselected.data('uqcode')+$cselected.data('colorid')!=barcode || $cselected.data('num_iid')==$proInfo.data('num_iid')){
                _this.dressing(barcode,gender,sex,isud,$wrapper_box,$(this));
               }else{
                    $('#watercontainer').find('li.pro-selected').parent().parent().parent().hide();
                    $('#watercontainer').find('li.pro-selected').parent().parent().parent().siblings('dl').find('a.select').removeClass('select');
                    $('#watercontainer').find('li.pro-selected').removeClass("pro-selected");
                    $(this).addClass('pro-selected');
                }
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
            //性格选择层中移出时隐藏该层
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