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
        init : function(){
            //设置昵称
            $('#lblnick').text('您');
            //设置收藏件数

            //如果收藏超过10套则弹出消息提示框：您的收藏已超过10套，请点击‘确定’领取优惠券
            var cnum =  UserCenter.collocationNum.text();
            if(cnum > 10){
                alert('您的收藏已超过10套，请点击‘确定’领取优惠券');
            }
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

});