/**
 * Created by jack on 14-6-17.
 */

jQuery(function($) {

    var UserCenter;
    UserCenter = {
        changePWBtn: $('.mini-changepw-btn'),
        exitUserCenter : $('.center_btn6'),
        collocationNum : $('#lbl_collection'),
        change_yf :$('#btnchange'),
        youhui_icon : $('#youhui_icon'),
        centor_con : $('#conter_con'),
        del_sc_btn : $('.del_sc_btn'),
        init : function(){
            UserCenter.bindCollections(0);
        },
        //或跌搭配
        bindCollections : function(pageOffset){
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
                if(collflag == 1){
                    UserCenter.youhui_icon.removeClass('youhui_icon');
                    UserCenter.youhui_icon.addClass('youhui_icon_block');
//                    UserCenter.youhui_icon.hide();
                }
                else{
                    //如果收藏超过10套则弹出消息提示框：您的收藏已超过10套，请点击‘确定’领取优惠
                    if(cnum > 10){
                        alert('您的收藏已超过10套，请点击‘确定’领取优惠券');
                    }
                }
                UserCenter.change_yf.data(data['page']);
                UserCenter.showColections(data['def']);

            });
        },
        //生成搭配html
        showColections : function(list){
            var strHtml = '';
            for(var i = 0;i<list.length;i++){
                strHtml += '<div class="model">';
                strHtml += '<div class="model_yj">';
                strHtml += '<img src="'+ tmplPath +'/images/yj/yj_women.png" />';
                strHtml += '<img src="'+ list[i].pic_body + '" />';
                strHtml += '</div>';
                var detiles = list[i].detail;
                strHtml += '<ul>';
                for(var j = 0;j<detiles.length;j++){
                    strHtml += '<li><a href="#"><img src="__TMPL__Public/images/yffff.png"  /></a></li>'
                }
                strHtml += '</ul>';
                strHtml += '<a href="javascript:;" data-id="'+ list[i].id+'" class="del_sc_btn"></a>'
                strHtml += '</div>';
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
        $('.my_yyg_title,#sfid').show();
        $('.user_center').hide();
    });
    //衣服换一组显示
    UserCenter.change_yf.on('click',function(){
        UserCenter.bindCollections(0);
    });
    //删除一套收藏
    UserCenter.centor_con.on('click','.del_sc_btn',function(){
        var cid = $(this).data('id');
        $.post(delBeubenCollUrl,{id:cid},function(data){
            if(data['code'] == '1'){
               UserCenter.bindCollections(-1);
            }
        });
    });
    //点击优惠券修改状态
    UserCenter.youhui_icon.on('click',function(){
        if( $(this).hasClass('youhui_icon')){
            $.post(setCollFlagUrl,function(data){
                if(data['code'] > 0){
                    UserCenter.youhui_icon.removeClass('youhui_icon');
                    UserCenter.youhui_icon.addClass('youhui_icon_block');
                }else{

                    return false;
                }
            });
        }
    });
});