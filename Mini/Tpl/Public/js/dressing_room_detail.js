/**
 * Created by jack on 14-4-8.
 */

$(function(){
    $(".syj_btn").draggable({
        drag:function(event, ui){
            var mleft =  $("#beubeu_loadImg").width();
            if(ui.position.left<mleft){
                $(".syj").css('margin-left',mleft+'px');
            }else{
                $(".syj").css('margin-left','');
            }
        },
        stop: function( event, ui ) {
            if(ui.position.top<0){
                $(".syj_btn").animate({'top':'50px'}, 400);
            }

            if(ui.position.top>$(window).height()-$(".syj_btn").height()){
                var mtop = $(window).height()-$(".syj_btn").height();
                $(".syj_btn").animate({'top':mtop + 'px'}, 400);
            }

            if(ui.position.left<0){
                $(".syj_btn").animate({'left':'10px'}, 400);
            }
            if(ui.position.left>$(window).width()-$(".syj_btn").width()){
                var mleft = $(window).width()-$(".syj_btn").width() - 10;
                $(".syj_btn").animate({'left':mleft + 'px'}, 400);
            }
        }
    });

    var jsonpurl = sendurl +"mini.php/API/getshopinfo";
    //获取店铺信息
    $.post(jsonpurl,{},function(data,status){
        H.initData(data);
    },'json');
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
    })

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

    $.uniqlo.index.week.on('click', 'li', function(){                  // 首页天气切换

        $.uniqlo.lid = 0;
        $.uniqlo.bid = 0;
        $.weather.nextpage = 0;
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
    getSuits : function(){
        var gender = $('#ulgender li').siblings().children('a.select').parent('li').data('gender');
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
                        for(var i = 0 ;i < deflength;i++){
                            if(i<4){
                                var show = "style='display:block;'";
                            }else{
                                var show = "style='display:none;'";
                            }
                            str += "<div class=\"model\" "+show+"><div style='width: 180px;height: 180px;margin-top: 150px'><img class='imgrd' data-detail='"+data.def[i].detail_url+"' src='/"+data.def[i].pic_url+"' width=\"180\" height=\"180\" /></div></div>";
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
            $.post(styleurl,{sid:gender,fid:fid},function(data,status){
                if(data){
                    if(data.def){
                        var deflength = data.def.length,str = "";
                        if(deflength && deflength>4){
                            $('#btn-mask').addClass('none')
                        }else{
                            $('#btn-mask').removeClass('none');
                        }
                        for(var i = 0 ;i < deflength;i++){
                            if(i<4){
                                var show = "style='display:block;' onmouseover='modeltip($(this),1," + gender +");' onmouseout='modeltip($(this),0," + gender +");'";
                            }else{
                                var show = "style='display:none;' onmouseover='modeltip($(this),1," + gender +");' onmouseout='modeltip($(this),0," + gender +");'";
                            }
                            if(gender==1){
                                str += "<div class=\"model\" "+show+"><div class='model_try2 none'></div><div style='width: 400px;height: 533px;margin-left: -70px'><img class='imgrd' data-detail='"+data.def[i].detail+"' src='"+data.def[i].suitImageUrl+"' width=\"400\" height=\"533\" /></div></div>";
                            }
                            if(gender==2){
                                str += "<div class=\"model\" "+show+"><div class='model_try2_man none'></div><div style='width: 400px;height: 533px;margin-left: -70px'><img class='imgrd' data-detail='"+data.def[i].detail+"' src='"+data.def[i].suitImageUrl+"' width=\"400\" height=\"533\" /></div></div>";
                            }
                            if(gender==3){
                                str += "<div class=\"model\" "+show+"><div class='model_try2_child none'></div><div style='width: 400px;height: 533px;margin-left: -70px'><img class='imgrd' data-detail='"+data.def[i].detail+"' src='"+data.def[i].suitImageUrl+"' width=\"400\" height=\"533\" /></div></div>";
                            }
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
        var sid = $('#cateu2 li').siblings().children('a.select').parent('li').data('gender');
        var fid = $('#cstyle2 li').siblings().children('a.select').data('suitstyle');
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
                //上下装
                var ustr = '',dstr='';
                $.each(data.u,function(i,name){
                    ustr+="<li class='upclothes zleft' la='"+name.id+"'><a href='javascript:;'>"+name.name+"</a></li>";
                });
                $.each(data.d,function(i,name){
                    dstr+="<li class='doclothes zright' la='"+name.id+"'><a href='javascript:;'>"+name.name+"</a></li>";
                });
                $('#alluid').nextAll('li').remove();
                $('#alluid').after(ustr);
                $('#alldid').nextAll('li').remove();
                $('#alldid').after(dstr);
            }
        },'json');
    },
    left : [],
    right : [],
    showStyleMask2 : function(gender){

        if(gender == 4){
            $('#style-mask2').removeClass('children-style-mask2');
            $('#style-mask2').removeClass('male-style-mask2');
            $('#style-mask2').addClass('baby-style-mask2').show();
            $('.style-children_mask').hide();
        }else if(gender == 3){
            $('#style-mask2').removeClass('baby-style-mask2');
            $('#style-mask2').removeClass('male-style-mask2');
            $('#style-mask2').addClass('children-style-mask2').show();
            $('.style-children_mask').show();
        }
        else if(gender == 2){
            $('#style-mask2').removeClass('baby-style-mask2');
            $('#style-mask2').removeClass('children-style-mask2');
            $('#style-mask2').addClass('male-style-mask2').show();
            $('.style-children_mask').hide();
        }
        else{
            $('#style-mask2').removeClass('baby-style-mask2');
            $('#style-mask2').removeClass('children-style-mask2');
            $('#style-mask2').removeClass('male-style-mask2');
            $('#style-mask2').hide();
            $('.style-children_mask').hide();
        }
    },
    getProductInfo : function(data){
        var _this = this;
        var strHtml = '';
        var color = 'h_orange';
        var pushid = 3,pushid2 = 2;
        $.each(data.da,function(p,v){
            if(data.count>=4){
                pushid = 3;
            }else{
                pushid = data.count-1;
            }
            if(data.count>=4){
                pushid2 = 2;
            }else{
                pushid2 = data.count-2;
            }
            if(v.first==1 && p==0){
                strHtml+= v.ad;
            }
            if(v.first==1 && p==pushid2){
                strHtml+= v.ad;
            }
            if(v.first==1 && p==pushid){
                strHtml+= v.cb;
            }
            if(v.type==1){
                color = 'h_pink';
            }else if(v.type==2){
                color = 'h_blue';
            }else if(v.type == 3 || v.type==4 || v.type == 5){
                color = 'h_orange';
            }
            if(p != 0 && p != pushid && p != pushid2){
                strHtml += '<div class="productinfo"><div class="wrapper_box"><a href="javascript:;">';
                strHtml += '<img class="product_img" src="http://uniqlo.bigodata.com.cn/' + v.pic_url + '" /></a>';
                strHtml += '<dl><dt><a href="javascript:;" class="tryon" data-colors="'+ JSON.stringify(v.products).replace(/\"/g,"'") +'" ';
                strHtml +=  'data-gendertype="'+ v.type +'"><i></i>试穿</a></dt>';
                strHtml += '<dd><a href="javascript:;" class="btn_ym" data-id="'+ v.num_iid+'"><i></i>已买</a></dd>';
                strHtml += '<dd><a href="javascript:;" class="btn_xh" data-id="'+ v.num_iid+'"><i></i>喜欢</a></dd></dl>';
                //颜色
                strHtml += '<div class="product_color none"><h5>请选择颜色</h5><dl class="sale-colors"><ul class="color-img"></ul></dl></div>';
                strHtml += '<div class="product_gender none"><h5>请选择性别</h5><ul>';
                strHtml += '<li><a href="javascript:;"  data-gender="15581" >男童</a></li>';
                strHtml += '<li><a href="javascript:;"  data-gender="15583">女童</a></li>';
                strHtml += '</ul></div>';
                strHtml += '<h3 class="'+color+'"><a href="'+ v.detail_url +'" target="_blank">'+ v.title+'</a></h3>';
                strHtml += '<div class="product_inf none"><div class="inf_top"></div>';
                strHtml += '<div class="inf_con"><p class="price"><span>￥</span>'+ v.price+'</p>';
                strHtml += '<p class="stock">剩余库存<span>'+ v.num+'</span>件</p>';
                strHtml += '<div class="inf_xx"><p>'+ v.title +'</p></div></div>';
                strHtml += '<div class="inf_bom"><a href="javascript:;" class="select"></a></div></div></div></div>';
            }
        });
        return strHtml;
    }
}



$('#watercontainer').on('click','.btn_xh',function(){      //喜欢
    var num_iid = $(this).data('id');
    $(this).toggleClass('select');
    if($(this).hasClass('select')){
        addbuy(num_iid,1,1)//添加
    } else {
        addbuy(num_iid,1,0)//取消
    }
});
$('#watercontainer').on('click','.btn_ym',function(){    //购买
    var num_iid = $(this).data('id');
    $(this).toggleClass('select');
    if($(this).hasClass('select')){
        addbuy(num_iid,2,1)//添加
    } else {
        addbuy(num_iid,2,0)//取消
    }
});
$('#watercontainer').on('click','#cldata',function(){         //右侧已收藏
    $.weather.nextpage = 0;
    $(this).addClass('select');
    $('#buydata').removeClass('select');
    $.uniqlo.lid = 1;
    $.uniqlo.bid = 0;
    $.uniqlo.kid = 0
    getgoods(0,0,1,0,0,0,0,0);
});

$('#watercontainer').on('click','#buydata',function(){         //右侧已购买
    $.weather.nextpage = 0;
    $(this).addClass('select');
    $('#cldatas').removeClass('select');
    $.uniqlo.bid = 1;
    $.uniqlo.lid = 0;
    $.uniqlo.kid = 0;
    getgoods(0,0,0,1,0,0,0,0);
});
$('#watercontainer').on('click','#keybutton',function(){          //右侧keyword
    $.weather.nextpage = 0;
    var keyword = $('#keywordid').val();
    $(this).addClass('select');
    $('#cldatas').removeClass('select');
    $.uniqlo.bid = 0;
    $.uniqlo.lid = 0;
    $.uniqlo.kid = 1;
    if(keyword){
        getgoods(0,0,0,0,0,0,1,0,keyword);
    }
});

function getgoods(tem,sid,lid,bid,fid,zid,kid,loadmore,keyword){
    $.post(goodurl,{
        tem : tem,
        sid : sid,
        lid : lid,
        bid : bid,
        fid : fid,
        zid : zid,
        kid : kid,
        page : $.weather.nextpage,
        keyword : keyword
    },function(data,status){
        if(data){
            if(data.code==1){
                var strProInfo = _mini.getProductInfo(data);
                $.weather.nextpage = data.nextpage;
                if(!loadmore){
                    $('#watercontainer').html(strProInfo);

                    var opt={

                        getResource:function(index,render){
                            //index为已加载次数,render为渲染接口函数,接受一个dom集合或jquery对象作为参数。通过ajax等异步方法得到的数据可以传入该接口进行渲染，如 render(elem)

                            var html = getgoods($.weather.avg,$.weather.sex,$.uniqlo.lid,$.uniqlo.bid,$.uniqlo.fid,$.uniqlo.zid,$.uniqlo.kid,1);
                            html = $.weather.str;
                            return $(html);

                        },

                        auto_imgHeight:true,
                        column_width : 228,
                        insert_type:2,
                        cell_selector : '.productinfo',
                        max_column_num:4
                    }

                    $('#watercontainer').waterfall(opt);
                }
                else{
                    $.weather.str = strProInfo;
                }
            }else{
                $.weather.str = '';
                $('.product_more').html('');
            }
        }
    },'json');
}
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
//取出添加到大配件衣服的tag信息
function adddapei(id,po){
    if(id>0){
        $.post(addda,{id:id,po:po},function(data,status){
            $(po).siblings('.mini-cab-slide').find('ul').html(data);
            $.uniqlo.cabSlider();
        });
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
                            $('#getTimeCode').css('display','inline');
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
                                    $('#getTimeCode').css('display','none');
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
            $('#getTimeCode2').css('display','inline');
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
                    $('#getTimeCode2').css('display','none');
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

function fleshVerify(){
    //重载验证码
    var time = new Date().getTime();
    document.getElementById('verifyImg').src= '__APP__/Login/verify/'+time;
}

function modeltip(e,act,gender){
    if(act==1){
        if(gender == 2){
            e.find('.model_try2_man').each(function(){ $(this).removeClass('none');});
        }else if(gender==3){
            e.find('.model_try2_child').each(function(){ $(this).removeClass('none');});
        }else{
            e.find('.model_try2').each(function(){ $(this).removeClass('none');});
        }

    }else{
        if(gender == 2){
            e.find('.model_try2_man').each(function(){ $(this).addClass('none');});
        }else if(gender==3){
            e.find('.model_try2_child').each(function(){ $(this).addClass('none');});
        }else{
            e.find('.model_try2').each(function(){ $(this).addClass('none');});
        }
    }

}

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