/**
 * Created with JetBrains WebStorm.
 * User: Mos,jack.wu
 * Date: 13-5-30
 * Time: 上午11:40
 * To change this template use File | Settings | File Templates.
 */
//jQuery(function($){

var weather = {
    currentOption : "",
    cityName : "",
    cityCode : '',
    tips : [
        '请注意防暑降温，宜穿短袖、<a href="http://uniqlo.tmall.com/search.htm?keyword=%B1%B3%D0%C4" target="__blank">背心</a>、<a href="http://uniqlo.tmall.com/search.htm?keyword=%C1%AC%D2%C2%C8%B9" target="__blank">连衣裙</a>、<a href="http://uniqlo.tmall.com/search.htm?keyword=%B6%CC%BF%E3" target="__blank">短裤</a>中裤、薄型<a href="http://uniqlo.tmall.com/search.htm?keyword=T%D0%F4" target="__blank">T恤</a>',
        '午时避免在户外久留，穿<a href="http://uniqlo.tmall.com/search.htm?keyword=%C1%AC%D2%C2%C8%B9" target="__blank">连衣裙</a>、<a href="http://uniqlo.tmall.com/search.htm?keyword=%C6%DF%B7%D6%BF%E3" target="__blank">7分裤</a>、<a href="http://uniqlo.tmall.com/search.htm?keyword=T%D0%F4" target="__blank">T恤</a>',
        '<a href="http://uniqlo.tmall.com/search.htm?keyword=%D5%EB%D6%AF%C9%C0" target="__blank">针织衫</a>+<a href="http://uniqlo.tmall.com/search.htm?keyword=%C5%A3%D7%D0%BF%E3" target="__blank">牛仔裤</a>，<a href="http://uniqlo.tmall.com/search.htm?keyword=%B9%A4%D7%B0%BF%E3" target="__blank">工装裤</a>，或<a href="http://uniqlo.tmall.com/search.htm?keyword=%C1%AC%D2%C2%C8%B9" target="__blank">连衣裙</a>是不错的选择',
        '白天穿长袖<a href="http://uniqlo.tmall.com/search.htm?keyword=%B3%C4%C9%C0" target="__blank">衬衫</a>+<a href="http://uniqlo.tmall.com/search.htm?keyword=%D5%EB%D6%AF%C9%C0" target="__blank">针织衫</a>，配<a href="http://uniqlo.tmall.com/search.htm?keyword=%C5%A3%D7%D0%BF%E3" target="__blank">牛仔裤</a>，晚上加件外套吧',
        '<a href="http://uniqlo.tmall.com/search.htm?keyword=%C3%C0%C0%FB%C5%AB" target="__blank">美利奴</a>毛衣、混混纺/羊毛/<a href="http://uniqlo.tmall.com/search.htm?keyword=%D1%F2%C8%DE%C9%C0" target="__blank">羊绒衫</a>、<a href="http://uniqlo.tmall.com/search.htm?keyword=%B7%E7%D2%C2" target="__blank">风衣</a>、连帽<a href="http://uniqlo.tmall.com/search.htm?keyword=%C7%D1%BF%CB" target="__blank">茄克</a>赶紧穿起来',
        '<a href="http://uniqlo.tmall.com/search.htm?keyword=%D3%F0%C8%DE%B7%FE" target="__blank">羽绒服</a>或羊毛混纺短<a href="http://uniqlo.tmall.com/search.htm?keyword=%B4%F3%D2%C2" target="__blank">大衣</a>，内配精纺<a href="http://uniqlo.tmall.com/search.htm?keyword=%C3%C0%C0%FB%C5%AB" target="__blank">美利奴</a>毛衣+<a href="http://uniqlo.tmall.com/search.htm?keyword=%CE%A7%BD%ED" target="__blank">围巾</a>',
        '宜穿厚<a href="http://uniqlo.tmall.com/search.htm?keyword=%D3%F0%C8%DE%B7%FE" target="__blank">羽绒服</a>、<a href="http://uniqlo.tmall.com/search.htm?keyword=%D2%A1%C1%A3%C8%DE" target="__blank">摇粒绒</a>外套+羽绒背心，配上<a href="http://uniqlo.tmall.com/search.htm?keyword=%CE%A7%BD%ED" target="__blank">围巾</a>和<a href="http://uniqlo.tmall.com/search.htm?keyword=%CA%D6%CC%D7" target="__blank">手套</a>'
    ],
    time : function(){
        var
            now = new Date(),
            year = now.getFullYear(),
            month = now.getMonth(),
            date = now.getDate(),
            day = now.getDay(),
            arr = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
            second = new Date(year, month, date + 1),
            third  = new Date(year, month, date + 2),
            forth  = new Date(year, month, date + 3),
            fifth  = new Date(year, month, date + 4),
            sixth  = new Date(year, month, date + 5);

        return [
            {
                day : arr[day],
                year: year,
                month: month + 1,
                date: date
            },
            {
                day : arr[second.getDay()],
                year: second.getFullYear(),
                month:second.getMonth() + 1,
                date: second.getDate()
            },
            {
                day : arr[third.getDay()],
                year: third.getFullYear(),
                month:third.getMonth() + 1,
                date: third.getDate()
            },
            {
                day : arr[forth.getDay()],
                year: forth.getFullYear(),
                month:forth.getMonth() + 1,
                date: forth.getDate()
            },
            {
                day : arr[fifth.getDay()],
                year: fifth.getFullYear(),
                month:fifth.getMonth() + 1,
                date: fifth.getDate()
            },
            {
                day : arr[sixth.getDay()],
                year: sixth.getFullYear(),
                month:sixth.getMonth() + 1,
                date: sixth.getDate()
            }
        ]
    },
    format : function(n){
        return (n < 10 ? '0' : '') + n;
    },
    ajax : function(code, option){
        var that = this;
        //调用接口，天气信息
        that.currentOption = option;
        var jsonpurl = baseurl+"index.php/Indexnew/getcitywerther?callback=weather.weatherJsonpCallback&id="+code;
        this.jsonpFcuntion(jsonpurl);
    },
    showTips : function(newstorre,sname,tradetime,shopid,isMapChange){

        if(isMapChange == 0){
            //kimi判断是否有新店开张
            if(newstorre){
                $('.preferential_1').remove();
                $('#tablink1').remove();
                $('#scrollDiv').prepend('<li class="preferential_1" style="display:none;"><i></i><a href="http://a1761.oadz.com/link/C/1761/727/dbSAtIqGPkyXTaxXq7gPysYowUc_/p020/0/http://uniqlo.bigodata.com.cn/u2/mini.php" target="__blank">'+newstorre+'</a></li>');
                $('.preferential_side_bar').prepend("<li class=\"current\" id=\"tablink1\" onmouseover=\"easytabs('1', '1');\" onfocus=\"easytabs('1','1');\" onclick=\"return false;\"></li>");

            }else{
                $('.preferential_1').remove();
                $('#tablink1').remove();
            }
            if(!sname && !tradetime){
                var tv = '暂时还没有店铺信息，请选择其他地区';
            }else{
                var tv = '<span id="tipshopid" data-shopid="'+shopid+'">'+sname+'</span><br>'+tradetime;
            }
            $('#shopid').html(tv);
            stop_autochange();
            var lilength = $('#scrollDiv').children().length;
            slength = 3-lilength+1;
            counter = 3-lilength;
            loadtabs[0] = 3-lilength+1;
            do {
                a = 0;
                b = 1;
                easytabs(b, loadtabs[a]);
                a++;
                b++;
            } while (b <= menucount);
            if (autochangemenu != 0) {
                start_autochange();
            }
            $('#a_shopinfo').html('您附近的优衣库门店');
            $('#shopInfo').show();
            $('#a_shopinfo2').hide();
        }
    },
    setText : function(info, option){
        var time  = this.time();
        var index = option.index || 1;
        var arrIndex;
        var avg = 10;
        var weatherinfo = info['weather' + index];
        //设置背景图片
        this.setBackground(weatherinfo.wt);
        if(info['weather' + index].lt != null){
            avg = Math.ceil( (parseInt(weatherinfo.lt) + parseInt(weatherinfo.ht)) / 2);
        }
        //气温降4度
        if(avg >14){
            if(avg >= 24) arrIndex = 0
            else if(avg >= 19) arrIndex = 1
            else arrIndex = 2
        } else {
            if(avg >= 11) arrIndex = 3
            else if(avg >= 6) arrIndex = 4
            else if(avg >= 1) arrIndex = 5
            else arrIndex = 6
        }

        //kimi
        $('#nio-city').text(info.cityname);
        $('#cinpinyin').text(info.pinyin);
        $('#nio-tip').html(this.tips[arrIndex]);

        //kimi
        //天气图标
        $('#title_day0').attr({'title': info['weather1'].wt, 'class': 'nio-' + (parseInt(info['weather1'].di) )});
        $('#title_day1').attr({'title': info['weather2'].wt, 'class': 'nio-' + (parseInt(info['weather2'].di) )});
        $('#title_day2').attr({'title': info['weather3'].wt, 'class': 'nio-' + (parseInt(info['weather3'].di) )});
        $('#title_day3').attr({'title': info['weather4'].wt, 'class': 'nio-' + (parseInt(info['weather4'].di) )});
        $('#title_day4').attr({'title': info['weather5'].wt, 'class': 'nio-' + (parseInt(info['weather5'].di) )});

        //星期几
        $('#week_day0').text(time[0].day);
        $('#week_day1').text(time[1].day);
        $('#week_day2').text(time[2].day);
        $('#week_day3').text(time[3].day);
        $('#week_day4').text(time[4].day);

        //最高温
        $('#h_day0').html(info['weather1'].ht);
        $('#h_day1').html(info['weather2'].ht);
        $('#h_day2').html(info['weather3'].ht);
        $('#h_day3').html(info['weather4'].ht);
        $('#h_day4').html(info['weather5'].ht);

        //最低温
        $('#l_day0').html(info['weather1'].lt+'℃');
        $('#l_day1').html(info['weather2'].lt+'℃');
        $('#l_day2').html(info['weather3'].lt+'℃');
        $('#l_day3').html(info['weather4'].lt+'℃');
        $('#l_day4').html(info['weather5'].lt+'℃');

        //文字描述
        $('#name_day0').html(info['weather1'].wt);
        $('#name_day1').html(info['weather2'].wt);
        $('#name_day2').html(info['weather3'].wt);
        $('#name_day3').html(info['weather4'].wt);
        $('#name_day4').html(info['weather5'].wt);

        $('.weather').show();

        this[option.city] = info['cityname'];
        var temper = {low: weatherinfo.lt,
            high: weatherinfo.ht};
        if(typeof option.callback == 'function'){
            option.callback(option.province, temper, info);
        }
    },
    setBackground : function(str){
        this.removeBackgroundClass();
        if(str.indexOf("雪") >= 0){
            $("#div_header").addClass("dr_header_bg5")
            $("#div_main").addClass("dr_main_con_bg5")
        }
        else if(str.indexOf("大雨") >= 0 || str.indexOf("暴雨") >= 0 ||
            str.indexOf("雷雨") >= 0 || str.indexOf("冰雹") >= 0 ){
            $("#div_header").addClass("dr_header_bg4")
            $("#div_main").addClass("dr_main_con_bg4")
        }
        else if(str.indexOf("雨") >= 0){
            $("#div_header").addClass("dr_header_bg3")
            $("#div_main").addClass("dr_main_con_bg3")
        }
        else if(str == "晴"){
            $("#div_header").addClass("dr_header_bg1")
            $("#div_main").addClass("dr_main_con_bg1")
        }
        else if(str == "阴"){
            $("#div_header").addClass("dr_header_bg6")
            $("#div_main").addClass("dr_main_con_bg6")
        }
        else{
            $("#div_header").addClass("dr_header_bg2")
            $("#div_main").addClass("dr_main_con_bg2")
        }
    },
    jsonpFcuntion : function(url){
        var JSONP=document.createElement("script");
        JSONP.type="text/javascript";
        JSONP.src=url;
        document.getElementsByTagName("head")[0].appendChild(JSONP);
    },
    weatherJsonpCallback : function(data){
        var isMapChange = this.currentOption.isMapChange ? this.currentOption.isMapChange:0;
        this.showTips(data.newstore,data.sname,data.tradetime,data.shopid,isMapChange);
        this.setText(data,this.currentOption);
    },
    removeBackgroundClass:function(){
        for(var i = 1;i<=6;i++){
            if($("#div_header").hasClass("dr_header_bg"+ i) && $("#div_main").hasClass("dr_main_con_bg"+ i)){
                $("#div_header").removeClass("dr_header_bg"+ i);
                $("#div_main").removeClass("dr_main_con_bg"+ i);
                break;
            }
        }
    }
    ,
    temp : function(str){
        var first, second, low = 0, high = 0;
        if(str){
            str = str.split('~');
            first = parseInt(str[0], 10);
            second = parseInt(str[1], 10);
            low = Math.min(first, second);
            high = Math.max(first, second);
        }
        return {
            l  : low + '&deg;',
            h : high + '&deg;',
            av : Math.ceil((low + high) / 2),
            low: low,
            high : high
        }
    },
    init : function(option){
        var city = option.city
        for (var i = 0, len = citys.length; i < len; i ++) {
            if(citys[i].n === city) break;
        }
        option.city = city
        if(citys[i]){
            this.ajax(citys[i].i, option);
            this.cityCode = citys[i].i;
            this.cityName = city;
        }
        else {
            option.city = '上海';
            this.ajax(101020100, option);
            this.cityCode = '101020100';
            this.cityName = '上海';
        }
    }
};

//weather.init();

/*=================================*/
// $.weather.init({
// 		city : city,
// 		callback: function(city, temper, info){
// 			// city 城市名
// 			// temper: {
//			// 	low: 最低气温
//			// 	high: 最高气温
//			// }
// 			// info 整条info数据
// 		}
// })
/*=================================*/

//    $.weather = weather;

/*== mini-city ==*/



//});