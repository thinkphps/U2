/**
 * Created by jack on 14-4-8.
 */
var gd = {};
$(function(){
    var jsonpurl = sendurl +"mini.php/API/getshopinfo";
    //获取店铺信息
    $.post(jsonpurl,{},function(data,status){
        gd.goods = data;
       // H.initData(data);
    },'json');
    $("#shopInfo,#a_shopinfo,#a_shopinfo2").on("click",function(){
        $("#mapdiv").show();
    })
    $("#closemap").on("click",function(){
        $("#mapdiv").hide();
    });
    $(".select_city").each(function(){
        var s=$(this);
        var z=parseInt(s.css("z-index"));
        var dt=$(this).children("dt");
        var dd=$(this).children("dd");
        var _show=function(){dd.slideDown(200);dt.addClass("cur");s.css("z-index",z+1);};   //展开效果
        var _hide=function(){dd.slideUp(200);dt.removeClass("cur");s.css("z-index",z);};    //关闭效果
        dt.click(function(){dd.is(":hidden")?_show():_hide();});
        dd.find("a").click(function(){dt.html($(this).html());_hide();});     //选择效果（如需要传值，可自定义参数，在此处返回对应的"value"值 ）
        $("body").click(function(i){ !$(i.target).parents(".select_city").first().is(s) ? _hide():"";});
    });

    //左侧风格按钮
    $('.expansion2').on('click',function(){
        if($('.detail_sub_nav').is(':hidden')){
            $('.detail_sub_nav').show();
            $(this).css('left','5em');
        }
        else{
            $('.detail_sub_nav').hide();
            $(this).css('left','0em');
        }
    });

    var miniMask = $('div.mini-mask');
    $("#shopinfo").on("click",function(){
        $("#mapdiv").show();
        miniMask.show();
        H.locateByCity(remote_ip_info);
    });

    $("#closemap").on("click",function(){
        $("#mapdiv").hide();
        miniMask.hide();
    });
    miniMask.on('click',function(){
        $("#mapdiv").hide();
    });


    $('#mini-activate-succ').click(function(){
        $('.mini-activate-succ').hide();
        miniMask.hide();
    })
    window.imgpath = imgpath;
    $.weather.init({
        'subindex':1,
        imgpath : imgpath,
        city : cityn || null,
        callback: function(city, temper, info){
            var avg = getavg(temper.high,temper.low);
            $.weather.avg = avg;
            $.weather.getgurl = goodurl;
            $.pron = provi ? provi : remote_ip_info.province;

            $.uniqlo.index.gender.find('a').first().click()
            //首次加载获取数据
            getgoods(avg);

        }
    });

    $(".syj_btn").draggable({

    });

    //天气左右移动按钮事件
//    var weekLeftNum = 0;
//    $('.left_btn').on('click',function(){
//        if(weekLeftNum > 0){
//            var left = $('#ulweek').position().left;
//            $('#ulweek').css('left', left += 135);
//            weekLeftNum -= 1;
//        }
//    });
//
//    $('.right_btn').on('click',function(){
//        if(weekLeftNum < 3){
//            var left = $('#ulweek').position().left;
//            $('#ulweek').css('left', left -= 135);
//            weekLeftNum += 1;
//        }
//    });


    $.uniqlo.index.week.on('click', '.li_day', function(){                  // 首页天气切换
        if(!$(this).hasClass('w_select')){
        $.uniqlo.lid = 0;
        $.uniqlo.bid = 0;
        $.weather.nextpage = 0;
        $.uniqlo.kid = 0;
        $.weather.str = '';
        var that = $(this)
        $.uniqlo.index.togClass(that, 'w_select')
        $.weather.init({
            index : that.index() + 1,
            city:$('#nio-city').text(),
            imgpath : window.imgpath,
            callback: function(city, temper, info){
                var avg = getavg(temper.high,temper.low);
                $.weather.avg = avg;
                $.weather.nextpage = 0;
                getgoods(avg,$.weather.sex,0,0,$.uniqlo.fid,$.uniqlo.zid,0,0);
            }
        })
		}else{
        $.weather.nextpage = 0;
        $.weather.avg = '';
        getgoods('',$.weather.sex,0,0,$.uniqlo.fid,$.uniqlo.zid,0,0);
        $.uniqlo.index.week.find('.w_select').removeClass('w_select');
		}
    })

    //点击天气红色X，取消温度
    $.uniqlo.index.week.on('click','a',function(){
        $.weather.nextpage = 0;
        $.weather.avg = '';
        getgoods('',$.weather.sex,0,0,$.uniqlo.fid,$.uniqlo.zid,0,0);
        $.uniqlo.index.week.find('.w_select').removeClass('w_select');
        return false;
    });

    $.uniqlo.index.week.find('a').hover(function(){
        $(this).next().show();
    },function(){
        $(this).next().hide()
    });



    /* =============== close popup form =============== David */
    $(function(){
        var allpopform = {
            formregister: $('form.mini-register'),
            formlogin: $('form.mini-login'),
            formfetch: $('form.mini-fetch')
        }

        allpopform.formregister.on('click', 'a.mini-form-close', function(){
            allpopform.formregister.hide();
            miniMask.hide();
        })
        allpopform.formlogin.on('click', 'a.mini-form-close', function(){
            allpopform.formlogin.hide();
            miniMask.hide();
        })
        allpopform.formfetch.on('click', 'a.mini-form-close', function(){
            allpopform.formfetch.hide();
            miniMask.hide();
        })
    })
    /* =============== mini-aside =============== */

    !(function($){
        var aside = {
            msg : $('li.mini-aside-msg'),
            form: $('form.mini-aside-form'),
            formIsOpen: false,
            select: $('div.mini-select'),
            input: $('#mini-select'),
            options: $('ul.mini-options'),
            optionsIsOpen: false,
            succ : $('div.mini-aside-succ'),
            tips: $('div.mini-aside-tips')
        }

        aside.succ.on('click', 'button', function(){
            this.parentNode.style.display = 'none'
        })

        aside.msg.on('click', function(){
            aside.form.trigger(aside.formIsOpen? 'hidden' : 'shown')
        })

        aside.form.on('click', 'a.mini-form-close', function(){
            aside.form.trigger('hidden')
        }).on('click', function(e){
            e.stopPropagation()
        }).on('shown', function(){
            aside.form.show()
            // aside.msg.addClass('mini-msg-checked')
            aside.formIsOpen = true
            aside.select.text('请选择主题')
            aside.input.val('')
        }).on('hidden', function(){
            aside.form.hide()
            // aside.msg.removeClass('mini-msg-checked')
            aside.formIsOpen = false
        })

        aside.select.on('click', function(){
            aside.options.trigger(aside.optionsIsOpen? 'optionsHide' : 'optionsShow')
        })

        aside.options.on('optionsHide', function(){
            aside.options.hide()
            aside.optionsIsOpen = false
        }).on('optionsShow', function(){
            aside.options.show()
            aside.optionsIsOpen = true
        }).on('click', 'li', function(){
            var that = this
            aside.select.text(that.innerHTML)
            aside.input.val(that.id)
            aside.options.trigger('optionsHide')
        })
        //意见反馈

        aside.form.submit(function(event){
            event.preventDefault();
            var minivalue = aside.input.val();
            var convalue = $('#propid').val();
            if(minivalue<=0){
                aside.tips.html('请选择主题').show();
                return false;
            }
            convalue = stripHTML(convalue);
            if(convalue){
                $.post(addpropurl,{cate:minivalue,con:convalue},function(data,status){
                    if(data){
                        aside.succ.show();
                        aside.tips.hide();
                    }
                    aside.input.val('');
                    $('#propid').val('');
                });
            }else{
                aside.tips.html('请填写内容').show();
            }
        });
        aside.succ.on('click', function(){
            aside.succ.hide();
            aside.tips.hide();
            aside.form.hide();
            aside.formIsOpen = false;
        })
    }($))

    /* ============ login && logout ============ */
})
var _mini = {
    timestamp : '',
    getSuits : function(){
        var gender = $('#ulgender li').siblings().children('a.select').parent('li').data('gender'),page = $('#changeid').attr('la');
        this.showStyleMask(gender);
        //下边取数据
        if(gender!=4){
            var fid = $('#ul_index-bar-place li').siblings().children('a.select').parent('li').data('suitstyle');
            fid = fid ? fid : 0;
        }else{
            var fid = 0;
        }
        $('#sfid').addClass('none');
        if(gender==4){
            //婴幼儿
            $.post(styleurl,{tem:$.weather.avg,sid:gender},function(data,status){
                if(data){
                    if(data.def){
                        var deflength = data.def.length,str = "";
                        if(deflength && deflength>4){
                            $('#btn-mask').addClass('none')
                        }else{
                            $('#btn-mask').removeClass('none');
                        }
                        var show='';
                        for(var i = 0 ;i < deflength;i++){
                            if(i==1){
                                var show = "klfg";
                            }
                            str += "<div class=\"model "+show+"\"><div style='width: 180px;height: 180px;margin-top: 150px'><img class='imgrd' data-detail='"+data.def[i].detail_url+"' src='"+rootPath+"/"+data.def[i].pic_url+"' width=\"180\" height=\"180\" /></div></div>";
                        }
                        $('#sfid').html(str);
                        $('#sfid').removeClass('none');
                        var css = {};
                        css['transform']='rotateY(90deg)';
                        $('.imgrd').css(css);
                        setTimeout(function(){
                            $('.imgrd').css({
                                '-webkit-transition': 'all 0.5s ease-in-out',
                                '-moz-transition': 'all 0.5s ease-in-out',
                                '-o-transition': 'all 0.5s ease-in-out',
                                '-ms-transition': 'all 0.5s ease-in-out',
                                'transition': 'all 0.5s ease-in-out'
                            });
                            var css = {};
                            css['transform']='matrix(1, 0, 0, 1, 0, 0)';
                            $('.imgrd').css(css);
                        },100);
                    }else{
                        $('#btn-mask').removeClass('none');
                        $('#sfid').html('');
                    }
                }
            });
        }else{
            $.post(styleurl,{sid:gender,fid:fid,page:page},function(data,status){
                if(data){
                    //if(data.prepage==1 && page==1){
                    $('#changeid').attr('la',data.page);
                     //}
                    if(data.def){
                        var deflength = 2//data.def.length
                            ,str = "";
                        if(data['count'] && data['count']>4){
                            $('#btn-mask').addClass('none')
                        }else{
                            $('#btn-mask').removeClass('none');
                        }
                        for(var i = 0 ;i < deflength;i++){
                            if(i<4){
                                var show = "style='display:block;'";
                            }else{
                                var show = "style='display:none;'";
                            }
//                            if(gender==1){
//                                str += "<div class=\"model\" "+show+"><div class='model_try2 none'></div><div style='width: 400px;height: 533px;margin-left: -70px'><img class='imgrd' data-detail='["+data.def[i].suitID+"," + data.def[i].sex + "]' src='"+data.def[i].suitImageUrl+".400x533.png' width=\"400\" height=\"533\" /></div></div>";
//                            }
//                            if(gender==2){
//                                str += "<div class=\"model\" "+show+"><div class='model_try2_man none'></div><div style='width: 400px;height: 533px;margin-left: -70px'><img class='imgrd' data-detail='["+data.def[i].suitID+"," + data.def[i].sex + "]' src='"+data.def[i].suitImageUrl+".400x533.png' width=\"400\" height=\"533\" /></div></div>";
//                            }
//                            if(gender==3){
//                                str += "<div class=\"model\" "+show+"><div class='model_try2_child none'></div><div style='width: 400px;height: 533px;margin-left: -70px'><img class='imgrd' data-detail='["+data.def[i].suitID+"," + data.def[i].sex + "]' src='"+data.def[i].suitImageUrl+".400x533.png' width=\"400\" height=\"533\" /></div></div>";
//                            }
                            str += '<div class="model"><img src="'+rootPath+'/'+data.def[i].suitImageUrl+'" data-detail="['+data.def[i].suitID+','+ data.def[i].sex +']"></div>';
                        }
                        $('#sfid').html(str);
                        $('#sfid').removeClass('none');
                        var css = {};
                        css['transform']='rotateY(90deg)';
                        $('.imgrd').css(css);
                        setTimeout(function(){
                            $('.imgrd').css({
                                '-webkit-transition': 'all 0.5s ease-in-out',
                                '-moz-transition': 'all 0.5s ease-in-out',
                                '-o-transition': 'all 0.5s ease-in-out',
                                '-ms-transition': 'all 0.5s ease-in-out',
                                'transition': 'all 0.5s ease-in-out'
                            });
                            var css = {};
                            css['transform']='matrix(1, 0, 0, 1, 0, 0)';
                            $('.imgrd').css(css);
                        },100);
                    }else{
                        $('#btn-mask').removeClass('none');
                        $('#sfid').html('');
                    }
                }
            },'json');
        }
    },
    showStyleMask : function(gender){
        $('.syj').hide();

        if(gender == 4){
            $(".syj_btn").hide();
        }else{

            $(".syj_btn").show();
        }
        if(gender == 4){
            for($i=1;$i<11;$i++){
                $('li[data-suitstyle="' + $i +'"]').hide();
            }
        }else if(gender == 3){
            for($i=1;$i<11;$i++){
                if($i==9 || $i==8 || $i==5){
                    $('li[data-suitstyle="' + $i +'"]').hide();
                }else{
                    $('li[data-suitstyle="' + $i +'"]').show();
                }
            }
        }
        else if(gender == 2){
            for($i=1;$i<11;$i++){
                if($i==6 || $i==7){
                    $('li[data-suitstyle="' + $i +'"]').hide();
                }else{
                    $('li[data-suitstyle="' + $i +'"]').show();
                }
            }
        }
        else{
            for($i=1;$i<11;$i++){
                $('li[data-suitstyle="' + $i +'"]').show();
            }
        }
    },
    getGoods : function(){
        var sid = $('#cateu2 li.rw_select').data('gender');
        var fid = $('#cstyle2 li.select').data('suitstyle');
        fid = fid ? fid : 0;
        this.showStyleMask2(sid);
        $.weather.sex = sid;
        $.weather.nextpage = 0;
        $.uniqlo.fid = fid;
        getgoods($.weather.avg,sid,0,0,$.uniqlo.fid,$.uniqlo.zid,0,0);
    },
    getzid : function(sid){
        $.post(midstyleurl,{sid:sid},function(data,status){
            if(data){
                    $('#alluid').addClass('select');
                    $('#alldid').addClass('select');
                    var ustr = '';
                    $.each(data.u,function(i,name){
                        ustr+="<li class='upclothes zleft' la='"+name.id+"'><a href='javascript:;'>"+name.name+"</a></li>";
                    });
                    $('#alluid').nextAll('li').remove();
                    $('#alluid').after(ustr);
            }
        },'json');
    },
    initialization : function(){
        $('#ulweek .w_select').removeClass('w_select');
        $('#li_day0').addClass('w_select');
        $('#cateu2 .rw_select').removeClass('rw_select')
        $('.rw_all a').addClass('select');
        $('#cstyle2 .select').removeClass('select');
        $('.ch_all a').addClass('select');
        $('.left_tj .w_select,.right_tj .w_select').removeClass('w_select');
        $('#alluid a,#alldid a').addClass('w_select');
        $('#style-mask2').removeClass('baby-style-mask2').hide();
        $('#style-mask2').removeClass('children-style-mask2').hide();
        $('#style-mask2').removeClass('male-style-mask2').hide();
        $('.style-children_mask').hide();
    },
    left : [],
    right : [],
    showStyleMask2 : function(gender){
        var $leftstyle = $('#cstyle2').children('li');
        if(gender == 2){
            $.each($leftstyle,function(){
                var $this = $(this);
                var fvalue = $this.data('suitstyle');
                var strClass =$this.attr('class');
                var length = strClass.length;
                if(fvalue==6 ){
                    if($this.hasClass('a2')){
                        $this.removeClass('a2');
                        $this.addClass('a2_0');
                    }
                }
                else if(fvalue == 7){
                    if($this.hasClass('a3')){
                        $this.removeClass('a3');
                        $this.addClass('a3_0');
                    }
                }
                else{
                    if(strClass.substr(length - 2,2)== '_0'){
                        $this.removeClass(strClass);
                        $this.addClass(strClass.substr(0,length-2));
                    }
                }
            });
        }
        else if(gender == 3){
            $.each($leftstyle,function(){
                var $this = $(this);
                var fvalue = $this.data('suitstyle');
                var strClass =$this.attr('class');
                var length = strClass.length;
                if(fvalue == 5 ){
                    if($this.hasClass('a9')){
                        $this.removeClass('a9');
                        $this.addClass('a9_0');
                    }
                }
                else if(fvalue == 8){
                    if($this.hasClass('a4')){
                        $this.removeClass('a4');
                        $this.addClass('a4_0');
                    }
                }
                else if(fvalue == 9){
                    if($this.hasClass('a8')){
                        $this.removeClass('a8');
                        $this.addClass('a8_0');
                    }
                }
                else{
                    if(strClass.substr(length - 2,2)== '_0'){
                        $this.removeClass(strClass);
                        $this.addClass(strClass.substr(0,length-2));
                    }
                }
            });
        }
        else if(gender == 4){
            $.each($leftstyle,function(){
                var $this = $(this);

                var fvalue = $this.data('suitstyle');
                var strClass =$this.attr('class');
                var length = strClass.length;
                if(fvalue!='all'){
                    if(strClass.substr(length - 2,2) != '_0'){
                        $this.removeClass(strClass);
                        $this.addClass(strClass+'_0');
                    }
                }
                else{
                    if(strClass.substr(length - 2,2)== '_0'){
                        $this.removeClass(strClass);
                        $this.addClass(strClass.substr(0,length-2));
                    }
                }
            });
        }
        else{
            $.each($leftstyle,function(){
                var $this = $(this);
                var strClass =$this.attr('class');
                var length = strClass.length;
                if(strClass.substr(length - 2,2)== '_0'){
                    $this.removeClass(strClass);
                    $this.addClass(strClass.substr(0,length-2));
                }
            });
        }
    },
    getProductInfo : function(data){
        var _this = this;
        var strHtml = '';
        var color = 'h_orange';
        var loveCss='',buyCss='';
        $.each(data.da,function(p,v){
            if(v.first==1){
                strHtml+= v.ad;
            }else if(v.first==2){
                strHtml+= v.ad;
            }else  if(v.first==3){
                strHtml+= v.cb;
            }
            else{
                loveCss='';
                buyCss='';
                if(v.type==1){
                    color2 = 'anniu_bgred';
                }else if(v.type==2){
                    color2 = 'anniu_bgblu';
                }else if(v.type == 3 || v.type==4 || v.type == 5){
                    color2 = 'anniu_bgorg';
                }
                if(v.loveid != null && v.loveid != undefined){
                    loveCss = ' select';
                }
                if(v.buyid != null && v.buyid != undefined){
                    buyCss = ' select';
                }
               if(v.num>0){
                 var num_msg = '库存<span class="stock2">'+ v.num+'</span>件',color = 'isnum';
               }else{
                 var num_msg = '<span class="stock3">已售罄</span>',color = 'nonum';
               }
                strHtml += '<div class="productinfo"><div class="wrapper_box"><a href="javascript:;" class="tryon" data-colors="'+ JSON.stringify(v.products).replace(/\"/g,"'") +'" data-gendertype="'+ v.type +'" data-isud="'+ v.isud+'">';
                strHtml += '<img class="product_img" width="200" height="200" src="http://uniqlo.bigodata.com.cn/' + v.pic_url + '" /></a>';
                strHtml += '<dl class="'+color2+'">';
                strHtml += '<dd class="btn_xh'+ loveCss +'" data-id="'+ v.num_iid+'"><a href="javascript:;"  ><i></i><span>喜欢</span></a></dd>';
                strHtml += '<dd class="btn_ym'+ buyCss +'"  data-id="'+ v.num_iid+'"><a href="javascript:;" ><i></i><span>已买</span></a></dd></dl>';
                strHtml += '<dl class="pri_num"><span class="price">￥'+ v.price+'</span>'+num_msg+'</dl>';
                /*strHtml += '<dl><dt><a href="javascript:;" class="tryon" data-colors="'+ JSON.stringify(v.products).replace(/\"/g,"'") +'" ';
                strHtml +=  'data-gendertype="'+ v.type +'" data-isud="'+ v.isud+'"><i></i>';
                if(v.type == 5){
                    strHtml+= '搭配';
                }
                else{
                    if(v.num==0){
                        strHtml+= '搭配';
                    }else{
                        strHtml += '试穿';
                    }
                }
                strHtml += '</a></dt></dl>';*/

                //颜色
                var sty = '';
                if(v.skunum==0 && v.approve_status=='onsale'){
                    sty = 'style="background:url('+tmplPath+'/images/icon3.png) no-repeat scroll 96px 0 / 42px 42px #EEEEEE; padding:10px 8px; overflow:hidden;"';
                }
                strHtml += '<div class="product_color none" '+sty+'><h5>请选择颜色</h5><dl class="sale-colors"><ul class="color-img"></ul></dl></div>';
                strHtml += '<div class="product_gender none"><h5>请选择性别</h5><ul>';
                strHtml += '<li><a href="javascript:;"  data-gender="15581" >男童</a></li>';
                strHtml += '<li><a href="javascript:;"  data-gender="15583">女童</a></li>';
                strHtml += '</ul></div>';
                strHtml += '<div class="product_gender2 none"><h5>请选择性别</h5><ul>';
                strHtml += '<li><a href="javascript:;"  data-gender="15478" >男</a></li>';
                strHtml += '<li><a href="javascript:;"  data-gender="15474">女</a></li>';
                strHtml += '</ul></div>';
                if(v.num>0){
                    strHtml += '<h3 class="'+color+'"><a href="'+ v.detail_url +'&kid=11727_51912_165824_211542" target="_blank">'+ v.title+'</a></h3>';
                    //strHtml += '<div class="product_inf none"><div class="inf_top"></div>';
                    //strHtml += '<div class="inf_con"><p class="price"><span>￥</span>'+ v.price+'</p>';
                    //strHtml += '<p class="stock">剩余库存<span>'+ v.num+'</span>件</p>';
                }else{
                    strHtml += '<h3 class="'+color+'">'+ v.title+'</h3>';
                    //strHtml += '<div class="product_inf none"><div class="inf_top"></div>';
                    //strHtml += '<div class="inf_con"><p class="price">已售罄</p>';
                }
                //strHtml += '<div class="inf_xx"><p>'+ v.title +'</p></div></div>';
                strHtml += '</div></div>';
            }
        });
        return strHtml;
    },
    checkLogin : function(){
        if($('.login').length == 0){
            $('.mini-reg-btn').click();
            return false;
        }
        else{
            return true;
        }
    }
}
$('#watercontainer').on('click','.btn_xh',function(){      //喜欢
    if($('.login').length == 0){
        alert('没有登录');
    }
    else{
        var num_iid = $(this).data('id');
        $(this).toggleClass('select');
        if($(this).hasClass('select')){
            addbuy(num_iid,1,1)//添加
        } else {
            addbuy(num_iid,1,0)//取消
        }
    }
});
$('#watercontainer').on('click','.btn_ym',function(){    //购买
    if($('.login').length == 0){
        alert('没有登录');
    }
    else{
        var num_iid = $(this).data('id');
        $(this).toggleClass('select');
        if($(this).hasClass('select')){
            addbuy(num_iid,2,1)//添加
        } else {
            addbuy(num_iid,2,0)//取消
        }
    }

});
$('#watercontainer').on('click','#cldata',function(){         //右侧已收藏
    //如果没有登录则弹出注册框
    if(_mini.checkLogin()){
        var $this = $(this);
        var lid = 0,bid = 0;
        if( $.uniqlo.bid == 1){
            bid = 1;
        }

        if($this.hasClass('select')){
            $this.removeClass('select');
            lid = 0;
        }else{
            $this.addClass('select');
            lid = 1;
        }
        _mini.initialization();
        $.uniqlo.bid = bid;
        $.uniqlo.lid = lid;
        getgoods(0,0,lid,bid,0,0,0,0);

    }
});

$('#watercontainer').on('click','#buydata',function(){         //右侧已购买

    //如果没有登录则弹出注册框
    if(_mini.checkLogin()){
        var $this = $(this);
        var lid = 0,bid = 0;
        if( $.uniqlo.lid  == 1){
            lid = 1;
        }

        if($this.hasClass('select')){
            $this.removeClass('select');
            bid = 0;
        }else{
            $this.addClass('select');
            bid = 1;
        }
        _mini.initialization();
        $.uniqlo.bid = bid;
        $.uniqlo.lid = lid;
        getgoods(0,0,lid,bid,0,0,0,0);

    }
});

$('#watercontainer').on('click','#keybutton',function(){          //右侧keyword
    $.weather.nextpage = 0;
    var keyword = $('#keywordid').val();
    $(this).addClass('select');
    $('#cldatas').removeClass('select');
    $.uniqlo.bid = 0;
    $.uniqlo.lid = 0;
    $.weather.sex = 0;
    $.uniqlo.fid = 0;
    $.uniqlo.zid = 0;
    $.uniqlo.kid = 1;
    $.uniqlo.minikeyword = keyword;
        getgoods(0,0,0,0,0,0,1,0,keyword);
        _mini.initialization();
        $.uniqlo.index.week.find('.w_select').removeClass('w_select');
    $.each($('#cstyle2').children('li').children('a'),function(){
        var strClass =$(this).attr('class'),length = strClass.length;
        if(strClass.substr(length - 2,2)== '_0'){
            $(this).removeClass(strClass);
            $(this).addClass(strClass.substr(0,length-2));
        }
    })
});
//kimi20140604点击风格
$('.style_btn_group').on('click','a',function(){
    var fsxvalue = $(this).data('fsx');
    if(!$(this).hasClass('noshow')){
    if(fsxvalue==1){
        $('.tag_text').addClass('none'),$('.shaixuan_btn').addClass('none');
        $('.style_btn_group a:eq(1)').removeClass('style_select');
        if($(this).hasClass('style_select')){
            $('.style_btn_group_detail').addClass('none');
            $(this).removeClass('style_select');
        }else{
            $('.style_btn_group_detail').removeClass('none');
            $(this).addClass('style_select');
        }

    }else if(fsxvalue==2){
        $('.shaixuan_btn').removeClass('none');
        $('.style_btn_group_detail').addClass('none');
        $('.style_btn_group a:eq(0)').removeClass('style_select');
        if($(this).hasClass('style_select')){  //影藏
            $('.tag_text').addClass('none'),$('.shaixuan_btn').addClass('none');
            $(this).removeClass('style_select');
        }else{
            $('.tag_text').removeClass('none'),$('.shaixuan_btn').removeClass('none');
            $(this).addClass('style_select');
        }
    }
}
});
$('.shaixuan_btn_con').on('click','a:eq(0)',function(){   //确定
    $('.tag_text').addClass('none'),$('.shaixuan_btn').addClass('none'),$('.style_btn_group a').removeClass('style_select');
    $('#beijingtu').removeClass('quexiao').addClass('xuanz');
    getgoods($.weather.avg,$.weather.sex,0,0,$.uniqlo.fid,$.uniqlo.zid,0,0);
});
$('.shaixuan_btn_con').on('click','a:eq(1)',function(){   //取消
    $('.tag_text').addClass('none'),$('.shaixuan_btn').addClass('none'),$('.style_btn_group a').removeClass('style_select');
    $('#beijingtu').removeClass('xuanz').addClass('quexiao');
    $('.left_tj li').removeClass('select');
    _mini.left.length = 0;
    getgoods($.weather.avg,$.weather.sex,0,0,$.uniqlo.fid,0,0,0);
});
$("#ddlShop").on("change",function(){
    var list = gd.goods;
    for(var i=1;i<list.length;i++){
        if(list[i][0] == $('#ddlShop option:selected').text() ){
            $('#mo-sh').text(list[i][0]);
            $('#mo-shd').text(list[i][1]);
            $('#mo-shc').text(list[i][6]); //电话
            $('#mo-shf').text(list[i][4]); //范围
            $('#mo-sht').html(list[i][5]); //时间
        }
    }
   $('.mo-sinfo').show();
})
document.onkeydown = function(e){
    var ev = document.all ? window.event : e;
    if(ev.keyCode==13) {
        $('#keybutton').click();
    }
}


$('#watercontainer').waterfall({
    itemCls: 'productinfo',
//    prefix: 'productinfo',
    fitWidth: true,
    colWidth: 142,
    gutterWidth: 10,
    gutterHeight: -10,
    align: 'center',
    minCol: 1,
    //maxCol: 4,
    maxPage: -1,
    path: function(page){
        return goodurl +'?page='+ page;
    },
    bufferPixel: -300,
    containerStyle: {
        position: 'relative'
    },
    resizable: true,
    isFadeIn: false,
    isAnimated: true,
    animationOptions: {
    },
    isAutoPrefill: true,
    checkImagesLoaded: true,
    dataType: 'json',
    params: {
    },

//    loadingMsg: 'Loading...',

    state: {
        isDuringAjax: false,
        isProcessingData: false,
        isResizing: false,
        curPage: 1
    },

    // callbacks
    callbacks: {
        renderData: function (data, dataType) {
            var tpl,
                template;
            if(data.code == 1){
                if ( dataType === 'json' ||  dataType === 'jsonp'  ) { // json or jsonp format
                    //如果当前返回的参数和之前的参数不一致则将当前页面中的数据清空
                    if(data.timestamp == _mini.timestamp){
                        $('#watercontainer').waterfall('reLayout');
                        return _mini.getProductInfo(data);
                    }
                    else
                    {
                        $('#waterfall-loading').remove();
                        return "";
                    }

                } else { // html format
                    return data;
                }
            }
            else{
                $('.product_more').hide();
                $('#waterfall-loading').remove();
                $('#watercontainer').waterfall('option', {bufferPixel: 10000,
                    maxPage: -1});
                return "";
            }
        }
    },
    debug: false
});

function getgoods(tem,sid,lid,bid,fid,zid,kid,loadmore,keyword){
    $('.mini-mask').css('height',$(document).height());
    if(keyword == undefined){ keyword = ""}
    _mini.timestamp = new Date().getTime();
    $('#waterfall-loading').remove();
    var oid = $('#gorder option:selected').val(),oid = oid ? oid : 2;
    $('#watercontainer').waterfall('removeItems', $('.productinfo'));
    $('#watercontainer').waterfall('option', {
        params:{ tem : tem,//温度    $.weather.avg
            sid : sid ,//性别id形如1,2,3 all为0 $.weather.sex
            lid : lid,//收藏id  $.uniqlo.lid,
            bid : bid,//$.uniqlo.bid,//购买id
            fid : fid,//,$.uniqlo.fid,//风格id
            zid : zid,//$.uniqlo.zid,//自定义分类
            kid : kid,//$.uniqlo.kid,//快速搜索标记
            keyword : keyword,
            oid : oid,
            timestamp : _mini.timestamp
        },
        state:{curPage:1},
        bufferPixel: -50,
        maxPage: 99999999
    });

}
//排序
$('#watercontainer').on('change','#gorder',function(){
    getgoods($.weather.avg,$.weather.sex,$.uniqlo.lid,$.uniqlo.bid,$.uniqlo.fid,$.uniqlo.zid,$.uniqlo.kid,0,$('#keywordid').val());
});
function delgo(id){
    if(id>0){
//删除cokkie里的数据
        var flagisud = '';
        $.post(isuturl,{id:id},function(data,status){
            if(data['code']==1){
                if(data['isud']==1){
                    flagisud = 'u';
                }else if(data['isud']==2){
                    flagisud = 'd';
                }
                var noiid = getCookie('nologiid'+flagisud);
                if(noiid){
                    var arrnoiid = noiid.split('_'),aalength = arrnoiid.length;
                    var str = '';
                    for(var u=0;u<aalength;u++){
                        if(id!=arrnoiid[u] && arrnoiid[u]){
                            str+=arrnoiid[u]+'_';
                        }
                    }
                    addCookie('nologiid'+flagisud,str);
                }
            }
        });
//删除cokkie里的数据
        $.post(gurl,{id:id},function(data,status){
        });
    }
}
$(window).on('beforeunload', function(){
    addCookie('nologiidu','',-1);
    addCookie('nologiidd','',-1);
})
function addbuy(id,flag,isdel){
    if(id>0){
        $.post(buyurl,{id:id,flag:flag,isdel:isdel},function(data,status){
            if(data){
                if(data.code==0){
                    alert(data.msg);
                }
            }
        },'json');
    }
}

function getavg(high,low){
    //修复气温求平均值没有转换为INT类型的bug
    var avg = Math.ceil((parseInt(low)+parseInt(high))/2);
    return avg;
}
function addwardrobe(id, onFailCallback, onSuccCallback){
    if(id>0){
        //没有登录的时候可以加三件
        if(!luid){
            var flagisud = '';
            $.post(isuturl,{id:id},function(data,status){
                if(data['code']==1){
                    if(data['isud']==1){
                        flagisud = 'u';
                    }else if(data['isud']==2){
                        flagisud = 'd';
                    }

                    var noiid = getCookie('nologiid'+flagisud);
                    if(noiid){
                        var arrnoiid = noiid.split('_'),aalength = arrnoiid.length;
                        if(aalength<=3){
                            var k = 0;
                            for(var i=0;i<aalength;i++){
                                if(arrnoiid[i]){
                                    if(arrnoiid[i]!=id){
                                        k = 1;
                                    }else{
                                        k = 0;
                                        onFailCallback();
                                        break;
                                    }
                                }
                            }
                            if(k==1){
                                addCookie('nologiid'+flagisud,noiid+id+'_',0);
                                onSuccCallback();
                            }
                        }else{
                            $.Register.showMask().register.show();
                        }
                    }else{
                        addCookie('nologiid'+flagisud,id+'_',0);
                        onSuccCallback();
                    }

                }else if(data['code']==-2){
                    onFailCallback();
                }
            });
            //没有登录的时候可以加三件
        }else{
            $.post(addwarurl,{id:id},function(data,status){
                if(data['code'] < 0){
                    //未登录状态收入衣柜
                    if(data['code'] == -1 ){
                        $.Register.showMask().register.show();
                    }else if(data['code'] == -2){
                        onFailCallback();
                    }else if(data['code'] == -3){
                        $('.mini-mask').show();
                        $('.mini-relate').show();
                    }//else if(data['code'] == -4){
                    //$('#relateMobile').val(data['msg']);
                    //$('.mini-mask').show();
                    //$('.mini-activate-notice').show();
                    //}
                }else{
                    onSuccCallback();
                }
            });
        }
    }
}

function addCookie(objName,objValue,objHours){
    var str = objName + "=" + escape(objValue);
    if(objHours > 0){
        var date = new Date();
        var ms = objHours*3600*1000;
        date.setTime(date.getTime() + ms);
        str += "; expires=" + date.toGMTString();
    }
    document.cookie = str;
}

function getCookie(objName){
    var arrStr = document.cookie.split("; ");
    for(var i = 0;i < arrStr.length;i ++){
        var temp = arrStr[i].split("=");
        if(temp[0] == objName) return unescape(temp[1]);
    }
}
function sendcity(pro,city){
    $.post(sendurl+'index.php/Sendcity/sendpro',{proname:pro,cityname:city},function(data,status){

    });
}

function stripHTML(msg)
{
    return msg=msg.replace(/<[^>]*>/g, "");
}

!(function($){
    /*
     $('#user_name').blur(function(){
     var user_name	= $('#user_name').val();
     if(user_name){
     $.post(ckeckuserurl,{user_name:user_name},function(data){
     if(data['code'] < 0){
     $('#msg_error').html(data['msg']);
     return false;
     }
     });
     }
     });

     $('#mobile').blur(function(){
     var mobile	= $('#mobile').val();
     if(mobile){
     $.post(ckeckmobileurl,{mobile:mobile},function(data){
     if(data['code'] < 0){
     $('#msg_error').html(data['msg']);
     return false;
     }
     });
     }
     });

     $('#f_mobile').blur(function(){
     var user_name	= $('#f_user_name').val();
     var mobile	= $('#f_mobile').val();
     if(user_name && mobile){
     $.post(ckeckusermobileurl,{user_name:user_name,mobile:mobile},function(data){
     if(data['code'] < 0){
     $('#f_error_msg').html(data['msg']);
     return false;
     }
     });
     }
     });
     */
    $(".mini-activate-fail").click(function(){
        $('.mini-activate-fail').hide();
        $('.mini-activate').show();
    })
    $(".mini-activate-succ").click(function(){
        window.location.reload();
    })
}($))

function getFuncCode(){
    var mobile		= $('#mobile').val();
    if(!mobile){
        $('#msg_error').html('请填写手机号码');
        return false;
    }else{
        var mobile_reg = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if(!mobile_reg.test(mobile)){
            $('#msg_error').html('手机号码格式错误');
            return false;
        }else{
            $.post(ckeckmobileurl,{mobile:mobile},function(data){
                if(data['code'] < 0){
                    $('#msg_error').html(data['msg']);
                    return false;
                }else{
                    $.post(activephone,{mobile:mobile},function(data){
                        if(data['code'] < 0){
                            $('#msg_error').html(data['msg']);
                            clearInterval($.uniqlo.activePhone);
                            return false;
                        }else{
                            $('#getTimeCode').css('display','inline-flex');
                            $('#getCode').css('display','none');
                            $('#getNextCode').css('display','none');
                            var i=0;
                            $.uniqlo.activePhone = setInterval( function(){
                                i++;
                                if( 180 - i > 0 ){
                                    $('#time_show').html(180-i);
                                }else{
                                    $('#time_show').html(180);
                                }
                                if( i > 179 ){
                                    $('#getTimeCode').css('display','inline-flex');
                                    $('#getNextCode').css('display','inline-block');
                                    clearInterval($.uniqlo.activePhone);
                                }
                            }, 1000);
                        }
                    });
                }
            });
        }
    }
}


function getFuncCode2(){
    var mobile		= $('#userMobileCode').val();
    $.post(activephone,{mobile:mobile},function(data){
        if(data['code'] < 0){
            clearInterval($.uniqlo.activePhone2);
            return false;
        }else{
            $('#getTimeCode2').css('display','inline-flex');
            $('#getCode2').css('display','none');
            $('#getNextCode2').css('display','none');
            var i=0;
            $.uniqlo.activePhone2 = setInterval( function(){
                i++;
                if( 180 - i > 0 ){
                    $('#time_show2').html(180-i);
                }else{
                    $('#time_show2').html(180);
                }
                if( i > 179 ){
                    $('#getTimeCode2').css('display','inline-flex');
                    $('#getNextCode2').css('display','inline-block');
                    clearInterval($.uniqlo.activePhone2);
                }
            }, 1000);
        }
    });
}


function activate_succ(){
    var verCode = $('#verCode').val();
    if(!verCode){
        //alert('验证码错误');return;
        $('.mini-activate-fail').show();
    }else{
        $.post(activesucc,{verCode:verCode},function(data){
            if(data['code'] > 0){
                $('.mini-activate-succ').show();
            }else{
                $('.mini-activate-fail').show();
            }
            $('.mini-activate').hide(); // 始终关闭激活层
        })
    }
}

function do_register(){
    var taobao_name	= $('#taobao_name').val();
    var password	= $('#password').val();
    var re_password = $('#re_password').val();
    var mobile		= $('#mobile').val();
    var c_phone = $('#checkbox-phone').val();
    var c_none = $('#checkbox-none').val();
    var verifying_code = $('#verifying_code').val();
    var isChecked = 1;
    /*
     if(!user_name){
     $('#msg_error').html('请填写用户名');
     return false;
     }else{
     var reg = /^[a-zA-Z0-9-_\u4e00-\u9fa5]{3,25}$/;
     if(!reg.test(user_name)){
     $('#msg_error').html('用户名格式错误');
     return false;
     }else{
     /*
     $.post(ckeckuserurl,{user_name:user_name},function(data){
     if(data['code'] < 0){
     $('#msg_error').html(data['msg']);
     return false;
     }
     });

     }
     }
     */
    if(!mobile){
        $('#msg_error').html('请填写手机号码');
        return false;
    }else{
        var mobile_reg = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if(!mobile_reg.test(mobile)){
            $('#msg_error').html('手机号码格式错误');
            return false;
        }
    }
    if(!password){
        $('#msg_error').html('请输入密码');
        return false;
    }else{
        if(password.length < 6 || password.length > 16){
            $('#msg_error').html('密码格式错误');
            return false;
        }
    }
    if(!re_password){
        $('#msg_error').html('请再次输入密码');
        return false;
    }else{
        if(password.length < 6 || password.length > 16){
            $('#msg_error').html('密码格式错误');
            return false;
        }
    }
    if(password != re_password){
        $('#msg_error').html('您两次输入的密码不一致');
        return false;
    }
    /*
     if($('#checkbox-phone').prop('checked')){
     if(!mobile){
     $('#msg_error').html('请填写手机号码');
     return false;
     }else{
     var mobile_reg = /^1[3|4|5|8][0-9]\d{4,8}$/;
     if(!mobile_reg.test(mobile)){
     $('#msg_error').html('手机号码格式错误');
     return false;
     }else{
     $.post(ckeckmobileurl,{mobile:mobile},function(data){
     if(data['code'] < 0){
     $('#msg_error').html(data['msg']);
     return false;
     }
     });
     }
     }
     }else{
     mobile = '';
     }
     */
    if($('#checkbox-phone').prop('checked')){
        if(!taobao_name){
            $('#msg_error').html('请填写淘宝登录名');
            return false;
        }else{
            if(taobao_name.length < 5 || taobao_name.length > 25){
                $('#msg_error').html('淘宝登录名格式错误');
                return false;
            }
        }
    }else{
        taobao_name = '';
    }
    if($('#js-checked').prop('checked')==false){
        if(!verifying_code){
            $('#msg_error').html('请填写验证码');
            return false;
        }
    }
    if($('#t_agree').prop('checked')==false){
        $('#msg_error').html('请勾选“已阅读隐私条款”');
        return false;
    }
    if($('#js-checked').prop('checked')){
        isChecked = 0;
    }
    $.post(ckeckmobileurl,{mobile:mobile},function(data){
        if(data['code'] < 0){
            $('#msg_error').html(data['msg']);
            return false;
        }else{
            if($('#checkbox-phone').prop('checked')){
                $.post(ckeckuserurl,{user_name:taobao_name},function(data){
                    if(data['code'] < 0){
                        $('#msg_error').html(data['msg']);
                        return false;
                    }else{
                        $.post(registerurl,{taobao_name:taobao_name,password:password,mobile:mobile,verifying_code:verifying_code,isChecked:isChecked,nologiidu:getCookie('nologiidu'),nologiidd:getCookie('nologiidd')},function(data){
                            if(data['code'] > 0){
                                $('.mini-reg-submit').addClass('disabled');
                                $('.mini-reg-submit').attr('disabled','disabled');
                                //$('.mini-register').hide();
                                //$('.mini-activate-notice').show();
                                window.location.reload();
                            }else{
                                $('#msg_error').html(data['msg']);
                                return false;
                            }
                        });
                    }
                });
            }else{
                $.post(registerurl,{taobao_name:taobao_name,password:password,mobile:mobile,verifying_code:verifying_code,isChecked:isChecked,nologiidu:getCookie('nologiidu'),nologiidd:getCookie('nologiidd')},function(data){
                    if(data['code'] > 0){
                        $('.mini-reg-submit').addClass('disabled');
                        $('.mini-reg-submit').attr('disabled','disabled');
                        //$('.mini-register').hide();
                        //$('.mini-activate-notice').show();
                        window.location.reload();
                    }else{
                        $('#msg_error').html(data['msg']);
                        return false;
                    }
                });
            }
        }
    });

}

function do_login(){
    var user_name	= $('#d_user_name').val();
    var password	= $('#d_password').val();
    if(!user_name || !password){
        $('#login_error_msg').html('请输入正确的用户名或密码');
        return false;
    }
    var is_remember_login = 0;
    if($('#remember_login').prop('checked')){
        is_remember_login = 1;
    }
    $.post(loginurl,{user_name:user_name,password:password,is_remember_login:is_remember_login,nologiidu:getCookie('nologiidu'),nologiidd:getCookie('nologiidd')},function(data){
        if(data['code'] > 0){
            //删除没有登录时添加的三件衣服
            addCookie('nologiidu','',-1);
            addCookie('nologiidd','',-1);
            window.location.reload()
        }else{
            $('#login_error_msg').html(data['msg']);
            return false;
        }
    });
}

function do_find_pwd(){
    // var user_name	= $('#f_user_name').val();
    var mobile	= $('#f_mobile').val();
    var verify	= $('#verify').val();
    // if(!user_name){
    // 	$('#f_error_msg').html('请输入正确的淘宝登录名或手机号码');
    // 	return false;
    // }
    if(!mobile){
        $('#f_error_msg').html('请输入正确的手机号码');
        return false;
    }
    /*
     if(!verify){
     $('#f_error_msg').html('请填写验证码');
     return false;
     }
     */
    $.post(findpwdurl,{mobile:mobile,verify:verify},function(data){
        if(data['code'] > 0){
            $('.mini-fetch-submit').addClass('disabled');
            $('.mini-fetch-submit').attr('disabled','disabled');
            $('.mini-fetch-succ').show();
        }else{
            $('#f_error_msg').html(data['msg']);
            return false;
        }
    });
}

function change_pwd(){
    var old_password = $('#c_old_password').val();
    var new_password = $('#c_new_password').val();
    var reg_new_password = $('#c_reg_new_password').val();
    if(old_password.length < 6 || old_password.length > 16){
        $('#pwd_error_msg').html('请输入正确的旧密码');
        return false;
    }
    if(!new_password){
        $('#pwd_error_msg').html('请填写新密码');
        return false;
    }else{
        if(new_password.length < 6 || new_password.length > 16){
            $('#pwd_error_msg').html('密码格式错误');
            return false;
        }else{
            if(old_password == new_password){
                $('#pwd_error_msg').html('新密码不能与旧密码相同');
                return false;
            }
        }
    }

    if(!reg_new_password){
        $('#pwd_error_msg').html('请再次输入密码');
        return false;
    }else{
        if(new_password != reg_new_password){
            $('#pwd_error_msg').html('您两次输入的密码不一致');
            return false;
        }
    }
    $.post(changepwdurl,{old_password:old_password,new_password:new_password},function(data){
        if(data['code'] > 0){
            //$('.mini-change-password').hide();
            $('.mini-change-succ').show();
            $('#pwd_error_msg').html('');
        }else{
            $('#pwd_error_msg').html(data['msg']);
            return false;
        }
    });
}

function do_active(){
    var a_user_name = $('#a_user_name').val();
    var a_mobile = $('#a_mobile').val();
    if(!a_mobile){
        $('#active_mobile_msg').html('请填写手机号码');
        return false;
    }else{
        var mobile_reg = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if(!mobile_reg.test(a_mobile)){
            $('#active_mobile_msg').html('手机号码格式错误');
            return false;
        }
    }

    if($('#activeAgree').prop('checked')==false){
        $('#active_mobile_msg').html('请勾选“已阅读隐私条款”');
        return false;
    }
    $.post(activeurl,{user_name:a_user_name,mobile:a_mobile},function(data){
        if(data['code'] > 0){
            $('.mini-relate').hide();
            $('.mini-relate-notice').show();
            $('#relateMobile').val(a_mobile);
        }else{
            $('#active_mobile_msg').html(data['msg']);
            return false;
        }
    });
}

function do_relate(){
    var relateMobile = $('#relateMobile').val();
    if(relateMobile){
        $.post(relateurl,{mobile:relateMobile},function(data){
            if(data['code'] > 0){
                $('.mini-activate-notice').hide();
                $('.mini-relate-notice').hide();
                $('.mini-activate-succ').show();
            }else{
                $('.mini-activate-notice').hide();
                $('.mini-relate-notice').hide();
                $('.mini-activate').show();
                var i=0;
                $.uniqlo.timerFind1 = setInterval( function(){
                    // 查询数据库 成功？  clearInterval(find)
                    $.post(relateurl,{mobile:relateMobile},function(data){
                        if(data['code'] > 0){
                            $('.mini-activate').hide();
                            $('.mini-activate-succ').show();
                            clearInterval($.uniqlo.timerFind1);
                        }else{
                            i++;
                            if( i > 10 ){
                                $('.mini-activate').hide();
                                $('.mini-activate-timeout').show();
                                clearInterval($.uniqlo.timerFind1);
                            }
                        }
                    });
                }, 3000);
                return false;
            }
        });
    }
}

function do_refresh_relate(){
    var relateMobile = $('#relateMobile').val();
    if(relateMobile){
        $('.mini-activate-timeout').hide();
        $('.mini-activate').show();
        var i=0;
        $.uniqlo.timerFind2 = setInterval( function(){
            // 查询数据库 成功？  clearInterval(find)
            $.post(relateurl,{mobile:relateMobile},function(data){
                if(data['code'] > 0){
                    $('.mini-activate').hide();
                    $('.mini-activate-succ').show();
                    clearInterval($.uniqlo.timerFind2);
                }else{
                    i++;
                    if( i > 10 ){
                        $('.mini-activate').hide();
                        $('.mini-activate-timeout').show();
                        clearInterval($.uniqlo.timerFind2);
                    }
                }
            });
        }, 3000);
    }
}

/*function fleshVerify(){
    //重载验证码
    var time = new Date().getTime();
    document.getElementById('verifyImg').src= '__APP__/Login/verify/'+time;
}*/


$(function(){
    $('.js-checked').change(function(){
        if ($('.js-checked:checked').length == 1) {
            $('.verifying_code').attr('disabled',true)
        }else{
            $('.verifying_code').attr('disabled',false)
        };
    });
    $('.js-click').click(function(e){
        e.preventDefault;
        var idname = $(this).attr('data-id');
        $(idname).show().siblings('.mini-pop').hide();
        $('.mini-mask').show()
    });


})