/**
 * Created by jack on 14-6-17.
 */

jQuery(function($) {

    var UserCenter;
    UserCenter = {
        changePWBtn: $('.mini-changepw-btn'),
        exitUserCenter : $('.center_btn6'),
        collocationNum : $('#lbl_collection'),
        change_yf :$('.change_yf'),
        youhui_icon : $('#youhui_icon'),
        init : function(){
            //设置昵称
            $('#lblnick').text('您');
            //设置收藏件数
            $.post(getIsCollUrl,function(data){
                if(data['code'] > 0){
                    var collflag = data['collflag'];
                    if(collflag == 1){
                        UserCenter.youhui_icon.removeClass('youhui_icon');
                        UserCenter.youhui_icon.addClass('youhui_icon_block');
                    }
                    else{
                        //如果收藏超过10套则弹出消息提示框：您的收藏已超过10套，请点击‘确定’领取优惠券
                        var cnum =  UserCenter.collocationNum.text();
                        if(cnum > 10){
                            alert('您的收藏已超过10套，请点击‘确定’领取优惠券');
                        }
                    }
                }else{

                    return false;
                }
            });

        },
        //或跌搭配
        bindCollections : function(){
            var page = UserCenter.change_yf.data('page');
            $.post(getCollDataUrl,{page:page},function(data){
                if(data['code'] > 0){

                }else{

                    return false;
                }
            });
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