/**
 * Created with JetBrains WebStorm.
 * User: Mos
 * Date: 13-5-30
 * Time: ����11:40
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($){

var weather = {
	tips : [
		'��ע�����£��˴����䡢���ġ���ȹ���̿㡢����T��',
		'��ʱ�����ڻ������������ȹ/�㡢����װ��T���������޶���',
		'������ɼ+ţ�п�/���п㣬����֯����ȹ�ǲ����ѡ��',
		'���촩�������+������װ����ţ�п㣬���ϼӼ���֯����',
		'����ūë�¡����/��ë/�����������¡���ñ�ѿ˸Ͻ�������',
		'���޷�����ë��Ķ̴��£����侫������ūë��+Χ��',
		'�˴������޷���ҡ��������+���ޱ��ģ�����ñ�Ӻ�����'
	],
	time : function(){
		var
			now = new Date(),
			year = now.getFullYear(),
			month = now.getMonth(),
			date = now.getDate(),
			day = now.getDay(),
			arr = ['����','��һ','�ܶ�','����','����','����','����'],
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
			city = option.city,
			url = "http://query.yahooapis.com/v1/public/yql?callback=?",
			query = "select * from json where url='http://m.weather.com.cn/data/";

		if(this[city]){
			return this.setText(this[city], option);
		}
		$.getJSON(
			url,
			{
				q: query + code + ".html'",
				format: "json"
			},
			function (data, textStatus, jqXHR) {
				if(data && data.query && data.query.results){
					that.setText(data.query.results.weatherinfo, option);
				} else {
					console.warn('��ע�⣬��ѡ�������ݽӿڻ�ȡʧ�ܣ����л������ýӿڣ�')
					that.ajax2(option)
				}
			}
		);
	},
	ajax2: function(option){
		var script = document.createElement('script'), that = this;
		script.type = 'text/javascript'
		script.charset = 'gb2312'
		script.src = 'http://php.weather.sina.com.cn/iframe/index/w_cl.php?code=js&day=4&city=' + option.city
		document.body.appendChild(script)

		setTimeout(function iterator(){
			if(typeof SWther == 'undefined'){
				return setTimeout(iterator, 500)
			}
			makeInfo(SWther.w[option.city], option)
		},500)

		function makeInfo(arr, option){
			var tmp
			var temp = []
			var weather = []

			for(var i = 0; i < arr.length; i ++){
				tmp = arr[i]
				temp.push((tmp.t1 || 0) + '��~' + (tmp.t2 || 0) + '��')
				weather.push(tmp.s1)
			}
			option.second = true

			that.setText({
				temp1: temp[0],
				temp2: temp[1],
				temp3: temp[2],
				temp4: temp[3],
				temp5: temp[4],
				img0: 0,
				weather1: weather[0],
				weather2: weather[1],
				weather3: weather[2],
				weather4: weather[3],
				weather5: weather[4]
			},option)
		}
	},
	setText : function(info, option){

		var time  = this.time();
		var index = option.index || 1;
		var temp = this.temp(info['temp' + index]);
		var num = option.second ? 0 : index * 2 - 1
		var arrIndex
		var dataStr = time[index - 1].year + '��' + time[index - 1].month + '��' + time[index - 1].date + '��'


		$('#nio-img').attr({'title': info['img_title' + num] || info['weather' + index], 'class': 'nio-' + (info['img' + num] || 0)});
        $('#nio-kv').css('background-image', 'url('+option.imgpath+'/images/index/uniqlo-bg/'+ (info['img' + num] || 0) + '.jpg)');
		$('#nio-city').text(option.city).attr('title', option.city);
		$('#nio-date').text(dataStr).attr('title', dataStr);
		$('#nio-day').text(time[index - 1].day).attr('title', time[index - 1].day);
		$('#nio-wea').text(info['weather' + index]);
		$('#nio-low').html(temp.l);
		$('#nio-high').html(temp.h);

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

		$('#nio-tip').text(this.tips[arrIndex]).attr('title', this.tips[arrIndex]);

		$('#nio-day1').text(time[0].day);
		$('#nio-day2').text(time[1].day);
		$('#nio-day3').text(time[2].day);
		$('#nio-day4').text(time[3].day);
		$('#nio-day5').text(time[4].day);
		$('#nio-day6').text(time[5].day);

		$('#nio-tem1').html(this.temp(info.temp1).h);
		$('#nio-tem2').html(this.temp(info.temp2).h);
		$('#nio-tem3').html(this.temp(info.temp3).h);
		$('#nio-tem4').html(this.temp(info.temp4).h);
		$('#nio-tem5').html(this.temp(info.temp5).h);
		$('#nio-tem6').html(this.temp(info.temp6).h);

		this[option.city] = info;
		if(typeof option.callback == 'function'){
			option.callback(option.province, temp, info);
		}
	},
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
		option = option || {}
		var city = option.city
		if(city === remote_ip_info['city'] && this[city]) return this.setText(this[city], option);
		city = remote_ip_info['city'] = city || remote_ip_info['city'];
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
$('.mini-change-btn').on('click', function(){
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
		weather.init({'city' : city, 'province': prov,imgpath : window.imgpath,
 		callback: function(city, temper, info){
      var avg = getavg(temper.high,temper.low);
 			$.weather.avg = avg;
            avg = avg?avg:0;
            $.weather.occasion = $.weather.occasion?$.weather.occasion:0;
            $.weather.sex = $.weather.sex?$.weather.sex:0;
            $.weather.set = $.weather.set?$.weather.set:0;
	var JSONP=document.createElement("script");  
    JSONP.type="text/javascript";  
    JSONP.src="http://uniqlo.bigodata.com.cn/u1_5/index.php/Index/getgood?callback=jsonpCallback4&tem="+avg+"&pro="+city+'&cid='+$.weather.occasion+'&sid='+$.weather.sex+'&tid='+$.weather.set;  
    document.getElementsByTagName("head")[0].appendChild(JSONP); 

			/*$.post($.weather.getgurl,{
			pro : city,
			tem : avg,
		    cid : $.weather.occasion,
		    sid : $.weather.sex,
		    tid : $.weather.set			
			},function(data,status){
			var da = eval("("+data+")");
			if(da){
	if(da.flag1=='p'){
	if($.weather.set==1){
	if(da.ustr){
	$('#upc').html(da.ustr);
	}
	if(da.dstr){
	$('#downc').html(da.dstr);
	}
	}else{
	$('#upc').html(da.ustr);
	$('#downc').html(da.dstr);	
	}
	}
	if(da.flag=='t'){
     $('#taoz').html(da.tstr);
     $.uniqlo.kvSlider();
	}
	if($.weather.set==1){
	if(!da.ustr && !da.dstr && !da.tstr){
	$('#tishi').css('display','block');
	}else if(da.tstr){
         $('#tishi').css('display','none');	
       }
	}else{
	if(!da.ustr && !da.dstr){
	$('#tishi').css('display','block');
	}else{
	$('#tishi').css('display','none');	
	}		
	}
			
			}
			});*/
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