/**
 * Created with JetBrains WebStorm.
 * User: Mos,jack.wu
 * Date: 13-5-30
 * Time: ����11:40
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($){

    var weather = {
        currentOption : "",
        tips : [
            '��ע�����£��˴����䡢<a href="http://uniqlo.tmall.com/search.htm?keyword=%B1%B3%D0%C4" target="__blank">����</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%C1%AC%D2%C2%C8%B9" target="__blank">����ȹ</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%B6%CC%BF%E3" target="__blank">�̿�</a>�п㡢����<a href="http://uniqlo.tmall.com/search.htm?keyword=T%D0%F4" target="__blank">T��</a>',
            '��ʱ�����ڻ����������<a href="http://uniqlo.tmall.com/search.htm?keyword=%C1%AC%D2%C2%C8%B9" target="__blank">����ȹ</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%C6%DF%B7%D6%BF%E3" target="__blank">7�ֿ�</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=T%D0%F4" target="__blank">T��</a>��<a href="http://uniqlo.tmall.com/search.htm?keyword=%B1%B3%D0%C4" target="__blank">����</a>',
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
            var that = this,
                city = option.city;
                var subindex = option.subindex ? option.subindex:0;
                var subid = option.subid ? option.subid : 0;
                var shopid = option.shopid ? option.shopid : 0;
            option.baiduerjiid = option.baiduerjiid ? option.baiduerjiid:-1;//�ٶȵ�ͼ�ϵ�select����ѡ��ֵ��ֱϽ�У�
            //���ýӿڣ�������Ϣ
            that.currentOption = option;
            var jsonpurl = baseurl+"index.php/Indexnew/GetWeatherByCityID?callback=weatherJsonpCallback&id="+code+"&subindex="+subindex+"&subid="+subid+'&shopid='+shopid+'&baiduerjiid='+option.baiduerjiid;
            jsonpFcuntion(jsonpurl);
        },
        setText : function(info, option){
            var time  = this.time();
            var index = option.index || 1;
            var arrIndex;
            var avg = 10;
            var weatherinfo = info['weather' + index];
            //���ñ���ͼƬ
            this.setBackground(weatherinfo.wt);
//            this.setBackground("����תС��");
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
            $('#cinpinyin').text(info.cbn);
            $('#nio-tip').html(this.tips[arrIndex]);
            if(!option.shopid){
                if(!info.sname && !info.tradetime){
                 var tv = '��ʱ��û�е�����Ϣ';
                }else{
                var tv = info.sname+'<br>'+info.tradetime;
                }
            $('#shopid').html(tv);
            }
            $.weather.shopid = option.shopid;
            var str = '<option value="0">��ѡ��</option>';
            var scid = {};
            $.each(info.plist,function(pin,pv){
                if(pv.sel==1){
                    var psel = "selected='selected'";
                }
                str+="<option value='"+pv.region_id+"' "+psel+">"+pv.local_name+"</option>";
            });
            $('#le1').html(str);

            var str2 = '<option value="0">��ѡ��</option>';
            $.each(info.plist,function(pin,pv){
                if(pv.baidusel==1){
                    var sel = "selected='selected'";
                    scid.selpid = pv.region_id;
                }
                str2+="<option value='"+pv.region_id+"' "+sel+">"+pv.local_name+"</option>";
            });
            $('#spid').html(str2);

            var str3 = '<option value="0">��ѡ��</option>';
            if(info.clist){
                $.each(info.clist,function(pin,pv){
                    if(pv.sel==1){
                        var csel = "selected='selected'";
                        scid.selcid = pv.region_id;
                    }
                    str3+="<option value='"+pv.region_id+"' "+csel+">"+pv.local_name+"</option>";
                });
                $('#scid').html(str3);
                if(info.isp){//ֱϽ��
                    var str4 = '<option value="0">��ѡ��</option>';
                    var sel2 = "selected='selected'";
                    str4+="<option value='"+info.indexcity.region_id+"' "+sel2+">"+info.indexcity.local_name+"</option>";
                    $('#le2').html(str4);
                }else{
                    var str4 = '<option value="0">��ѡ��</option>';
                    $.each(info.clist,function(pin,pv){
                        if(pv.sel==1){
                            var sel2 = "selected='selected'";
                        }
                        str4+="<option value='"+pv.region_id+"' "+sel2+">"+pv.local_name+"</option>";
                    });
                    $('#le2').html(str4);
                }
                scid.selpid = scid.selpid?scid.selpid:-1;
                scid.selcid = scid.selcid?scid.selcid:-1;

                var jsonpurl = baseurl+"index.php/Indexnew/getcity?callback=jsonpBaiduCity2&pid="+scid.selpid+"&cid="+scid.selcid+"&baiduid=2&shopid="+option.shopid+"&baiduerjiid="+info.baiduerjiid;
                jsonpFcuntion(jsonpurl);
            }
            //kimi
            //����ͼ��
            $('#title_day0').attr({'title': info['weather1'].wt, 'class': 'nio-' + (parseInt(info['weather1'].di) + 1)});
            $('#title_day1').attr({'title': info['weather2'].wt, 'class': 'nio-' + (parseInt(info['weather2'].di) + 1)});
            $('#title_day2').attr({'title': info['weather3'].wt, 'class': 'nio-' + (parseInt(info['weather3'].di) + 1)});
            $('#title_day3').attr({'title': info['weather4'].wt, 'class': 'nio-' + (parseInt(info['weather4'].di) + 1)});
            $('#title_day4').attr({'title': info['weather5'].wt, 'class': 'nio-' + (parseInt(info['weather5'].di) + 1)});

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
            $('#l_day0').html(info['weather1'].lt);
            $('#l_day1').html(info['weather2'].lt);
            $('#l_day2').html(info['weather3'].lt);
            $('#l_day3').html(info['weather4'].lt);
            $('#l_day4').html(info['weather5'].lt);

            //��������
            $('#name_day0').html(info['weather1'].wt);
            $('#name_day1').html(info['weather2'].wt);
            $('#name_day2').html(info['weather3'].wt);
            $('#name_day3').html(info['weather4'].wt);
            $('#name_day4').html(info['weather5'].wt);



            this[option.city] = info['cityname'];
            var temper = {low: weatherinfo.lt,
                high: weatherinfo.ht};
            if(typeof option.callback == 'function'){
                option.callback(option.province, temper, info);
            }
        },
        setBackground : function(str){
            this.removeBackgroundClass();
            if(str.indexOf("ѩ") > 0){
                $("#div_header").addClass("dr_header_bg5")
                $("#div_main").addClass("dr_main_con_bg5")
            }
            else if(str.indexOf("����") > 0 || str.indexOf("����") > 0 ||
                str.indexOf("����") > 0 || str.indexOf("����") > 0 ){
                $("#div_header").addClass("dr_header_bg4")
                $("#div_main").addClass("dr_main_con_bg4")
            }
            else if(str.indexOf("��") > 0){
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
//            if(city === remote_ip_info['city'] && this[city]) return this.setText(this[city], option);
//            city = remote_ip_info['city'] = city || remote_ip_info['city'];
            for (var i = 0, len = citys.length; i < len; i ++) {
                if(citys[i].n === city) break;
            }
            option.city = city
            if(citys[i]) this.ajax(citys[i].i, option);
            else {
                option.city = '�Ϻ�';
                this.ajax(101020100, option);
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

    $.weather = weather;

    /*== mini-city ==*/

    if(typeof data != 'undefined'){
        var sel = new LinkageSelect({data : data});
        sel.bind('.linkageselect .level_1');
        sel.bind('.linkageselect .level_2');
        sel.bind('.linkageselect .level_3');
    }
    var location = $('.mini-city');
    $('.change_city').on('click', function(){
        location.toggle();
    });

    location.submit(function(){
        var that = $(this),
            option = that.find('option:checked'),
            province = option.first().text(),
            prov = province,
            city = option.last().text(),
            temp = city.slice(-1);

        if(city !== '��ѡ��'){
            if(province !== '̨��ʡ'){
                if( province === '���' || province === '����'){
                    city = province;
                } else {
                    city = city.slice(0, (temp === '��' ? -2 : -1));
                }
            }
            $('#nio-tip').text('���ڼ����������ݣ����Ե�...').attr('title', '���ڼ����������ݣ����Ե�...');
            $.pron = prov;
            H.map.centerAndZoom(city, 11);
            weather.init({'city' : city, 'province': prov,imgpath : window.imgpath,'subindex':'1',
                callback: function(city, temper, info){
                    var avg = getavg(temper.high,temper.low);
                    $.weather.avg = avg;
                    avg = avg?avg:0;
                    $.weather.occasion = $.weather.occasion?$.weather.occasion:0;
                    $.weather.sex = $.weather.sex?$.weather.sex:0;
                    $.weather.set = $.weather.set?$.weather.set:0;
                    if($.weather.sex.toString() == "0" && $.weather.set.toString() != "1" ||($.weather.occasion.toString() == "0" && $.weather.sex == undefined )){
                        getSuits();
                    }else{

                        var jsonpurl = "http://uniqlo.bigodata.com.cn/u1_5/index.php/Index/getgood?callback=jsonpCallback4&tem="+avg+"&pro="+city+'&cid='+$.weather.occasion+'&sid='+$.weather.sex+'&tid='+$.weather.set;
                        jsonpFcuntion(jsonpurl);
                        $('#suits-container').html('');
                        $("#suits-container").hide();
                    }
                    //��mimi������
                    sendcity(city,info.city);
                }
            });
            that.hide();
            $.uniqlo.index.togClass($.uniqlo.index.week.find('li').first(), 'mini-checked')

        } else alert('��ѡ����У�');
        return false;

    }).on('click', 'a.mini-city-close', function(){
            location.hide();
        });

});