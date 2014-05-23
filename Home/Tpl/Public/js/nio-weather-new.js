/**
 * Created with JetBrains WebStorm.
 * User: Mos,jack.wu
 * Date: 13-5-30
 * Time: ����11:40
 * To change this template use File | Settings | File Templates.
 */
//jQuery(function($){

var weather = {
    currentOption : "",
    cityName : "",
    cityCode : '',
    tips : [
        '��ע�����£��˴����䡢<a href="http://uniqlo.tmall.com/search.htm?keyword=%B1%B3%D0%C4" target="__blank">����</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%C1%AC%D2%C2%C8%B9" target="__blank">����ȹ</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%B6%CC%BF%E3" target="__blank">�̿�</a>�п㡢����<a href="http://uniqlo.tmall.com/search.htm?keyword=T%D0%F4" target="__blank">T��</a>',
        '��ʱ�����ڻ����������<a href="http://uniqlo.tmall.com/search.htm?keyword=%C1%AC%D2%C2%C8%B9" target="__blank">����ȹ</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%C6%DF%B7%D6%BF%E3" target="__blank">7�ֿ�</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=T%D0%F4" target="__blank">T��</a>',
        '<a href="http://uniqlo.tmall.com/search.htm?keyword=%D5%EB%D6%AF%C9%C0" target="__blank">��֯��</a>+<a href="http://uniqlo.tmall.com/search.htm?keyword=%C5%A3%D7%D0%BF%E3" target="__blank">ţ�п�</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%B9%A4%D7%B0%BF%E3" target="__blank">��װ��</a>����<a href="http://uniqlo.tmall.com/search.htm?keyword=%C1%AC%D2%C2%C8%B9" target="__blank">����ȹ</a>�ǲ����ѡ��',
        '���촩����<a href="http://uniqlo.tmall.com/search.htm?keyword=%B3%C4%C9%C0" target="__blank">����</a>+<a href="http://uniqlo.tmall.com/search.htm?keyword=%D5%EB%D6%AF%C9%C0" target="__blank">��֯��</a>����<a href="http://uniqlo.tmall.com/search.htm?keyword=%C5%A3%D7%D0%BF%E3" target="__blank">ţ�п�</a>�����ϼӼ����װ�',
        '<a href="http://uniqlo.tmall.com/search.htm?keyword=%C3%C0%C0%FB%C5%AB" target="__blank">����ū</a>ë�¡�����/��ë/<a href="http://uniqlo.tmall.com/search.htm?keyword=%D1%F2%C8%DE%C9%C0" target="__blank">������</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%B7%E7%D2%C2" target="__blank">����</a>����ñ<a href="http://uniqlo.tmall.com/search.htm?keyword=%C7%D1%BF%CB" target="__blank">�ѿ�</a>�Ͻ�������',
        '<a href="http://uniqlo.tmall.com/search.htm?keyword=%D3%F0%C8%DE%B7%FE" target="__blank">���޷�</a>����ë��Ķ�<a href="http://uniqlo.tmall.com/search.htm?keyword=%B4%F3%D2%C2" target="__blank">����</a>�����侫��<a href="http://uniqlo.tmall.com/search.htm?keyword=%C3%C0%C0%FB%C5%AB" target="__blank">����ū</a>ë��+<a href="http://uniqlo.tmall.com/search.htm?keyword=%CE%A7%BD%ED" target="__blank">Χ��</a>',
        '�˴���<a href="http://uniqlo.tmall.com/search.htm?keyword=%D3%F0%C8%DE%B7%FE" target="__blank">���޷�</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%D2%A1%C1%A3%C8%DE" target="__blank">ҡ����</a>����+���ޱ��ģ�����<a href="http://uniqlo.tmall.com/search.htm?keyword=%CE%A7%BD%ED" target="__blank">Χ��</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%CA%D6%CC%D7" target="__blank">����</a>'
    ],
    time : function(){
        var
            now = new Date(),
            year = now.getFullYear(),
            month = now.getMonth(),
            date = now.getDate(),
            day = now.getDay(),
            arr = ['������','����һ','���ڶ�','������','������','������','������'],
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
        //���ýӿڣ�������Ϣ
        that.currentOption = option;
        var jsonpurl = baseurl+"index.php/Indexnew/getcitywerther?callback=weather.weatherJsonpCallback&id="+code;
        this.jsonpFcuntion(jsonpurl);
    },
    showTips : function(newstorre,sname,tradetime,shopid,isMapChange){

        if(isMapChange == 0){
            //kimi�ж��Ƿ����µ꿪��
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
                var tv = '��ʱ��û�е�����Ϣ����ѡ����������';
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
            $('#a_shopinfo').html('�����������¿��ŵ�');
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
        //���ñ���ͼƬ
        this.setBackground(weatherinfo.wt);
        if(info['weather' + index].lt != null){
            avg = Math.ceil( (parseInt(weatherinfo.lt) + parseInt(weatherinfo.ht)) / 2);
        }
        //���½�4��
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
        //����ͼ��
        $('#title_day0').attr({'title': info['weather1'].wt, 'class': 'nio-' + (parseInt(info['weather1'].di) )});
        $('#title_day1').attr({'title': info['weather2'].wt, 'class': 'nio-' + (parseInt(info['weather2'].di) )});
        $('#title_day2').attr({'title': info['weather3'].wt, 'class': 'nio-' + (parseInt(info['weather3'].di) )});
        $('#title_day3').attr({'title': info['weather4'].wt, 'class': 'nio-' + (parseInt(info['weather4'].di) )});
        $('#title_day4').attr({'title': info['weather5'].wt, 'class': 'nio-' + (parseInt(info['weather5'].di) )});

        //���ڼ�
        $('#week_day0').text(time[0].day);
        $('#week_day1').text(time[1].day);
        $('#week_day2').text(time[2].day);
        $('#week_day3').text(time[3].day);
        $('#week_day4').text(time[4].day);

        //�����
        $('#h_day0').html(info['weather1'].ht);
        $('#h_day1').html(info['weather2'].ht);
        $('#h_day2').html(info['weather3'].ht);
        $('#h_day3').html(info['weather4'].ht);
        $('#h_day4').html(info['weather5'].ht);

        //�����
        $('#l_day0').html(info['weather1'].lt+'��');
        $('#l_day1').html(info['weather2'].lt+'��');
        $('#l_day2').html(info['weather3'].lt+'��');
        $('#l_day3').html(info['weather4'].lt+'��');
        $('#l_day4').html(info['weather5'].lt+'��');

        //��������
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
        if(str.indexOf("ѩ") >= 0){
            $("#div_header").addClass("dr_header_bg5")
            $("#div_main").addClass("dr_main_con_bg5")
        }
        else if(str.indexOf("����") >= 0 || str.indexOf("����") >= 0 ||
            str.indexOf("����") >= 0 || str.indexOf("����") >= 0 ){
            $("#div_header").addClass("dr_header_bg4")
            $("#div_main").addClass("dr_main_con_bg4")
        }
        else if(str.indexOf("��") >= 0){
            $("#div_header").addClass("dr_header_bg3")
            $("#div_main").addClass("dr_main_con_bg3")
        }
        else if(str == "��"){
            $("#div_header").addClass("dr_header_bg1")
            $("#div_main").addClass("dr_main_con_bg1")
        }
        else if(str == "��"){
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
            option.city = '�Ϻ�';
            this.ajax(101020100, option);
            this.cityCode = '101020100';
            this.cityName = '�Ϻ�';
        }
    }
};

//weather.init();

/*=================================*/
// $.weather.init({
// 		city : city,
// 		callback: function(city, temper, info){
// 			// city ������
// 			// temper: {
//			// 	low: �������
//			// 	high: �������
//			// }
// 			// info ����info����
// 		}
// })
/*=================================*/

//    $.weather = weather;

/*== mini-city ==*/



//});