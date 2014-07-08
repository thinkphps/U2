/**
 * Created by jack on 14-6-17.
 */

jQuery(function($) {

    var UserCenter;
    UserCenter = {
        changePWBtn: $('#btnWardrobe'),
        exitUserCenter : $('.center_btn6'),
        collocationNum : $('#lbl_collection'),
        change_yf :$('#btnchange'),
        youhui_icon : $('#youhui_icon'),
        centor_con : $('#conter_con'),
        del_sc_btn : $('.del_sc_btn'),
        btn_changepwd : $('#btn_changepwd'),
        changepwd : $('#changepwd'),
        ulMenu : $('#ul_menu'),
        changeName : $('#change_name'),
        btnChangeName :$('#btn_changename'),
        myWardrobe : $('#my_wardrobe'),
        btnChangetbname :$('#btn_changetbname'),
        changeTBname : $('#changetbname'),
        txtUsername :$('#txtusername'),
        txttbname :$('#txttbname'),
        userinfo : $('#userinfo'),
        btnUserInfo :$('#btn_userinfo'),
        youhui_msg : $('#youhui_msg'),
        init : function(){
            UserCenter.bindCollections(0);
        },
        //或跌搭配
        bindCollections : function(pageOffset){
            $('ul.detail_sub_nav').css('z-index',1);
            $('#utuichu').hide();
            var page =  parseInt(UserCenter.change_yf.data('page'));
            page += pageOffset;
            $.post(getCollDataUrl,{page:page},function(data){
                var nick = data['uname'];
                if(nick == '') nick = '您';
                //设置昵称
                $('#lblnick').text(nick);
                var cnum =  data['collcount'];
                UserCenter.collocationNum.text(cnum);
                var collflag = data['collflag'];
                $('#txttbname').val(data['tname']);
                if(collflag == 1){
                    UserCenter.youhui_icon.data('isreceive',1);
//                    UserCenter.youhui_icon.removeClass('youhui_icon');
//                    UserCenter.youhui_icon.addClass('youhui_icon_block');
//                    UserCenter.youhui_icon.hide();
                }
                else{
                    UserCenter.youhui_icon.data('isreceive',0);
                    //如果收藏超过10套则弹出消息提示框：您的收藏已超过10套，请点击‘确定’领取优惠
                    if(cnum >= 10){
                        UserCenter.youhui_icon.show();
                        if(pageOffset!=-1){
                        alert('您的收藏已超过10套，请点击‘确定’领取优惠券');
                         }
                    }
                    else{
                        //UserCenter.youhui_icon.hide();
                    }
                }
                UserCenter.change_yf.data('page',data['page']);
                UserCenter.showColections(data['def']);

            });
        },
        //生成搭配html
        showColections : function(list){
            var strHtml = '';
            if( list != null){
                for(var i = 0;i<list.length;i++){
                    strHtml += '<div class="model">';
                    var gender = list[i].gender;
                    strHtml += '<div class="model_yj" data-suitid="'+ list[i].suitID +'" data-gender="'+ gender +'">';
                    if( gender == '15478'){
                        strHtml += '<img src="'+ tmplPath +'/images/yj/yj_men.png"  />';
                    }else if(gender == '15474'){
                        strHtml += '<img src="'+ tmplPath +'/images/yj/yj_women.png"  />';
                    }else{
                        strHtml += '<img src="'+ tmplPath +'/images/yj/yj_chr.png"  />';
                    }

                    strHtml += '<img src="'+ list[i].pic_clothes + '" />';
                    strHtml += '<div class="model_del none" data-id="'+ list[i].id+'" ></div></div>';
                    var detiles = list[i].detail;
                    strHtml += '<ul>';
                    for(var j = 0;j<detiles.length;j++){
                        strHtml += '<li><a href="'+ detiles[j].detail_url +'" target="_blank"><img src="'+ rootPath + '/'+detiles[j].pic_url +'"  /></a></li>'
                    }
                    strHtml += '</ul>';
                    strHtml += '<a href="javascript:;" data-id="'+ list[i].id+'" class="del_sc_btn"><span class="none"></span></a>'
                    strHtml += '</div>';
                }
            }
           UserCenter.centor_con.html(strHtml);
        }
    }

    $.UserCenter = UserCenter;
    UserCenter.changePWBtn.on('click',function(){
        $('.my_yyg_title,#sfid').hide();
        $('.user_center').show();
        UserCenter.init();
    });
    UserCenter.exitUserCenter.on('click',function(){
        $('ul.detail_sub_nav').css('z-index',105);
        UserCenter.change_yf.data('page',0);
        UserCenter.myWardrobe.click();
        $('.my_yyg_title,#sfid').show();
        $('.user_center').hide();
        $('#utuichu').show();
    });
    //衣服换一组显示
    UserCenter.change_yf.on('click',function(){
        UserCenter.bindCollections(0);
    });
    //删除一套收藏
    UserCenter.centor_con.on('click','.del_sc_btn',function(event){
        var cid = $(this).data('id');
        $.post(delBeubenCollUrl,{id:cid},function(data){
            if(data['code'] == '1'){
               UserCenter.bindCollections(-1);
            }
        });
        event.stopPropagation();
    });

    UserCenter.centor_con.on('mouseleave','.model_yj',function(){
        $(this).find('.model_del').hide();
    });

    UserCenter.centor_con.on('mouseenter','.model_yj',function(){
        $(this).find('.model_del').show();
    });

    UserCenter.centor_con.on('mouseleave','.del_sc_btn',function(){
        $(this).find('span').hide();
    });

    UserCenter.centor_con.on('mouseenter','.del_sc_btn',function(){
        $(this).find('span').show();
    });

    //点击优惠券修改状态
    UserCenter.youhui_icon.on('click',function(){
        var isreceive = UserCenter.youhui_icon.data('isreceive');
        var colnum = UserCenter.collocationNum.text();
        if(colnum >= 10){
            if( isreceive != 1){
                $.post(setCollFlagUrl,function(data){
                    if(data['code'] > 0){
                        //UserCenter.youhui_icon.removeClass('youhui_icon');
                        UserCenter.youhui_icon.data('isreceive',1);
    //                    UserCenter.youhui_icon.addClass('youhui_icon_block');
                        $('#youhui_msg').removeClass('none');
                        $('#youhui_msg').show();
                    }else{

                        return false;
                    }
                });
            }else{
                $('#youhui_msg').removeClass('none');
                $('#youhui_msg').show();//目前没有优惠信息，有的话这两句要删掉
                //alert('您已领取优惠券！');
            }
        }else{
            alert('收藏满10套方可领取优惠券！');
        }
    });
    UserCenter.youhui_msg.on('click',function(){
       if(!$(this).hasClass('none')){
           $('#youhui_msg').addClass('none');
           $('#youhui_msg').hide();
       }
    });
    //账户信息
    UserCenter.btnUserInfo.on('click',function(){
        //UserCenter.ulMenu.find('li').removeClass('select');
        $(this).siblings('li').removeClass('select');
        if(!$(this).hasClass('select')){
        $(this).addClass('select');
        $('div[name=user_l_box]').hide();
        UserCenter.userinfo.show();
        $.post(getUserInfoUrl,function(data){
            console.log(data);
            if(data['code'] > 0){
                var info = data['result'];
                $('#showuserinfo').val(info.user_name);
                $('#showmobile').val(info.mobile);
                $('#shoutbname').val(info.taobao_name);
            }else{
                return false;
            }
        });
    }else{
            $(this).removeClass('select');
            UserCenter.userinfo.hide();
            UserCenter.myWardrobe.addClass('select');
     }
    });

    //我的衣柜
    UserCenter.myWardrobe.on('click',function(){
        UserCenter.ulMenu.find('li').removeClass('select');
        $(this).addClass('select');
        $('div[name=user_l_box]').hide();
    });

    //修改密码
    UserCenter.btn_changepwd.on('click',function(){
        $(this).siblings('li').removeClass('select');
        if(!$(this).hasClass('select')){
        $(this).addClass('select');
        $('div[name=user_l_box]').hide();
        UserCenter.changepwd.show();
       }else{
            $(this).removeClass('select');
            UserCenter.changepwd.hide();
            UserCenter.myWardrobe.addClass('select');
      }
    });

    //修改账号
    UserCenter.btnChangeName.on('click',function(){
        UserCenter.ulMenu.find('li').removeClass('select');
        $(this).addClass('select');
        $('div[name=user_l_box]').hide();
        UserCenter.changeName.show();
    });

    //关联淘宝账号
    UserCenter.btnChangetbname.on('click',function(){
        $(this).siblings('li').removeClass('select');
        $('#changetbname_msg').html('');
        if(!$(this).hasClass('select')){
        $(this).addClass('select');
        $('div[name=user_l_box]').hide();
        UserCenter.changeTBname.show();
        }else{
            $(this).removeClass('select');
            UserCenter.changeTBname.hide();
            UserCenter.myWardrobe.addClass('select');
        }
    });

    $('#btn_change_name').on('click',function(){
        //提交修改账户
        var username = UserCenter.txtUsername.val();
        if(username.length > 0){
            $.post(changeNameUrl,{uname:username},function(data){
                if(data['code'] == '1'){
                    $('#lblnick').text(username);
                    UserCenter.txtUsername.val('');
                    $('#changename_msg').html('账户修改成功！');
                }
            });
        }else{
            $('#changename_msg').html('账户不能为空！');
        }
    });

    $('#btn_change_tbname').on('click',function(){
        //提交修改淘宝账号
        var tbname = UserCenter.txttbname.val();
        if(tbname.length > 0){
            $.post(changeTaoNameUrl,{taobao_name:tbname},function(data){
                if(data['code'] == '1'){
                    UserCenter.txttbname.val('');
                    $('#lblusername').text(tbname);
                    $('#txttbname').val(data['tname']);
                    $('#changetbname_msg').html('淘宝登录名已关联成功！');
                }
                else{
                    $('#changetbname_msg').html(data['msg']);
                }
            });
        }else{
            $('#changetbname_msg').html('淘宝登录名不能为空！');
        }
    });

    //穿衣
    UserCenter.centor_con.on('click','.model_yj',function(){
        var $model_yj = $(this);
        var gender = $model_yj.data('gender');
        var suitid = $model_yj.data('suitid');
        get_baiyi_dp_bystr(suitid);
        if(pageElement.$divSyj.is(':hidden')){
            pageElement.$btnExpansion.click();
        }
    });
});