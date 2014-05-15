/**
 * Created with JetBrains WebStorm.
 * User: jack.wu
 * Date: 14-02-26
 * Time: 上午11:40
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($){

    var weather = {
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
                arr = ['周日','周一','周二','周三','周四','周五','周六'],
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
        ajax:function(code,option){
            var that = this,
                city = option.city;
            var subindex = option.subindex ? option.subindex:0;
            var subid = option.subid ? option.subid : 0;
            var shopid = option.shopid ? option.shopid : 0;
            option.baiduerjiid = option.baiduerjiid ? option.baiduerjiid:-1;//百度地图上的select二级选择值（直辖市）
            //调用接口，天气信息
            $.post(getweatherurl,{id:code,subindex:subindex,subid:subid,shopid:shopid,baiduerjiid:option.baiduerjiid},function(data){
                that.setText(data,option);
            });

        },
        setText:function(info,option){

            var time  = this.time();
            var index = option.index || 1;
            var num = option.second ? 0 : index * 2 - 1
            var arrIndex;
            var avg = 10;
            var weatherinfo = info['weather' + index];
            //设置背景图片
            this.setBackground(weatherinfo.wt);
            if(info['weather' + index].lt != null){
                avg = Math.ceil( (parseInt(weatherinfo.lt) + parseInt(weatherinfo.ht)) / 2);
            }

            $('#nio-img').attr({'title': weatherinfo.wt, 'class': 'nio-' + weatherinfo.di});

            $('#nio-kv').css('background-image', 'url('+option.imgpath+'/images/index/uniqlo-bg/'+ weatherinfo.di + '.jpg)');
            $('#nio-city').text(option.city).attr('title', option.city);
            $('#nio-date').text(time[index - 1].year + '年' + time[index - 1].month + '月' + time[index - 1].date + '日');
            $('#nio-day').text(time[index - 1].day).attr('title', time[index - 1].day);
            $('#nio-wea').text(weatherinfo.wt);
            $('#nio-low').html(weatherinfo.lt + '&deg;');
            $('#nio-high').html(weatherinfo.ht + '&deg;');
            /*kimi*/
            $('#nio-city').text(info.cityname);
            $('#cinpinyin').text(info.cbn);
            if(!option.shopid){
                if(!info.sname && !info.tradetime){
                    var tv = '暂时还没有店铺信息，请选择其他地区';
                }else{
                    var tv = info.sname+'<br>'+info.tradetime;
                }
                $('#shopid').html(tv);
            }
            $.weather.shopid = option.shopid;
            var str = '<option value="0">请选择</option>';
            var scid = {};
            $.each(info.plist,function(pin,pv){
                if(pv.sel==1){
                    var psel = "selected='selected'";
                }
                str+="<option value='"+pv.region_id+"' "+psel+">"+pv.local_name+"</option>";
            });
            $('#le1').html(str);

            var str2 = '<option value="0">请选择</option>';
            $.each(info.plist,function(pin,pv){
                if(pv.baidusel==1){
                    var sel = "selected='selected'";
                    scid.selpid = pv.region_id;
                }
                str2+="<option value='"+pv.region_id+"' "+sel+">"+pv.local_name+"</option>";
            });
            $('#spid').html(str2);

            var str3 = '<option value="0">请选择</option>';
            if(info.clist){
                $.each(info.clist,function(pin,pv){
                    if(pv.sel==1){
                        var csel = "selected='selected'";
                        scid.selcid = pv.region_id;
                    }
                    str3+="<option value='"+pv.region_id+"' "+csel+">"+pv.local_name+"</option>";
                });
                $('#scid').html(str3);
                if(info.isp){//直辖市
                    var str4 = '<option value="0">请选择</option>';
                    var sel2 = "selected='selected'";
                    str4+="<option value='"+info.indexcity.region_id+"' "+sel2+">"+info.indexcity.local_name+"</option>";
                    $('#le2').html(str4);
                }else{
                    var str4 = '<option value="0">请选择</option>';
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
                //var jsonpurl = baseurl+"index.php/Indexnew/getcity?callback=jsonpBaiduCity2&pid="+scid.selpid+"&cid="+scid.selcid+"&baiduid=2&shopid="+option.shopid+"&baiduerjiid="+info.baiduerjiid;
                $.post(sendurl+'mini.php/API/getcity',{pid:scid.selpid,cid:scid.selcid,baiduid:2,shopid:option.shopid,baiduerjiid:info.baiduerjiid},function(data,status){
                 $('#ddlShop').html('<option value="0">请选择</option>');
                 if(!data.shopid){
                 $('#a_shopinfo').html('您附近的优衣库门店');
                 }
                 var str = '<option value="0">请选择</option>';
                 if(data.clist){
                 $('#emptymsg').html('');
                 $.each(data.clist,function(pin,pv){
                 if(pv.sel == 1){
                 var sel =  "selected='selected'";
                 }
                 str+="<option value='"+pv.sname+'<br>'+pv.tradetime+"' "+sel+">"+pv.sname+"</option>";
                 });
                 $('#ddlShop').html(str);
                 }else{
                 if(data.baiduerjiid>0){
                 $('#emptymsg').html('本地区暂无优衣库门店，请选择其他地区');
                 }
                 }
                 });
            }
            /*kimi*/
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

            $('#nio-tip').html(this.tips[arrIndex]);

            //天气图标
            $('#title_day0').attr({'title': info['weather1'].wt, 'class': 'nio-' + (parseInt(info['weather1'].di) + 1)});
            $('#title_day1').attr({'title': info['weather2'].wt, 'class': 'nio-' + (parseInt(info['weather2'].di) + 1)});
            $('#title_day2').attr({'title': info['weather3'].wt, 'class': 'nio-' + (parseInt(info['weather3'].di) + 1)});
            $('#title_day3').attr({'title': info['weather4'].wt, 'class': 'nio-' + (parseInt(info['weather4'].di) + 1)});
            $('#title_day4').attr({'title': info['weather5'].wt, 'class': 'nio-' + (parseInt(info['weather5'].di) + 1)});

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
                $("#main_con").addClass("water5")
            }
            else if(str.indexOf("大雨") >= 0 || str.indexOf("暴雨") >= 0 ||
                str.indexOf("雷雨") >= 0 || str.indexOf("冰雹") >= 0 ){
                $("#main_con").addClass("water4")
            }
            else if(str.indexOf("雨") >= 0){
                $("#main_con").addClass("water3")
            }
            else if(str == "晴"){
                $("#main_con").addClass("water1")
            }
            else if(str == "阴"){
                $("#main_con").addClass("water6")
            }
            else{
                $("#main_con").addClass("water2")
            }
        },
        removeBackgroundClass:function(){
            for(var i = 1;i<=6;i++){
                if($("#main_con").hasClass("water"+ i)){
                    $("#main_con").removeClass("water"+ i);
                    break;
                }
            }
        },
        init : function(option){
            option = option || {}
            var city = option.city
            //if(city === remote_ip_info['city'] && this[city]) return this.setText(this[city], option);
            //city = remote_ip_info['city'] = city || remote_ip_info['city'];
            for (var i = 0, len = citys.length; i < len; i ++) {
                if(citys[i].n === city) break;
            }
            option.city = city
            if(citys[i]){
                this.ajax(citys[i].i, option);
            }
            else {
                option.city = '上海';
                this.ajax(101020100, option);
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

        if(city !== '请选择'){
            if(province !== '台湾省'){
                if( province === '香港' || province === '澳门'){
                    city = province;
                } else {
                    city = city.slice(0, (temp === '区' ? -2 : -1));
                }
            }
            $.uniqlo.lid = 0;
            $.uniqlo.bid = 0;
            $.weather.nextpage = 0;
            $('#nio-tip').text('正在加载天气数据，请稍等...').attr('title', '正在加载天气数据，请稍等...');
            H.map.centerAndZoom(city, 11);
            weather.init({'city' : city, 'province': prov,imgpath : window.imgpath,'subindex':'1',
                callback: function(city, temper, info){
                    //kimi
                    var avg = getavg(temper.high,temper.low);
                    $.weather.avg = avg;
                    getgoods($.weather.avg,$.weather.sex,0,0,$.uniqlo.fid,$.uniqlo.zid,0,0);
                    //往index传城市
                    restart_autochange();
                    sendcity(city,info.city);
                }
            });
            //kimi
            that.hide();
            $.uniqlo.index.togClass($.uniqlo.index.week.find('li').first(), 'mini-checked')

        } else alert('请选择城市！');
        return false;

    }).on('click', 'a.mini-city-close', function(){
            location.hide();
        });
});