/**
 * Created with JetBrains WebStorm.
 * User: Mos
 * Date: 13-5-30
 * Time: 上午11:40
 * To change this template use File | Settings | File Templates.
 */
jQuery(function($){

var weather = {
	time : function(){
		var now = new Date(),
			year = now.getFullYear(),
			month = now.getMonth(),
			date = now.getDate(),
			day = now.getDay(),
			arr = ['周日','周一','周二','周三','周四','周五','周六'],
			second = new Date(year, month, date + 1),
			third  = new Date(year, month, date + 2),
			forth  = new Date(year, month, date + 3),
			fifth  = new Date(year, month, date + 4);

		return {
			first : {
				year  : year,
				month : month + 1,
				date  : this.format(date),
				day   : arr[day]
			},
			second : {
				year  : second.getFullYear(),
				month : second.getMonth() + 1,
				date  : this.format(second.getDate()),
				day   : arr[second.getDay()]
			},
			third : {
				year  : third.getFullYear(),
				month : third.getMonth() + 1,
				date  : this.format(third.getDate()),
				day   : arr[third.getDay()]
			},
			forth : {
				year  : forth.getFullYear(),
				month : forth.getMonth() + 1,
				date  : this.format(forth.getDate()),
				day   : arr[forth.getDay()]
			},
			fifth : {
				year  : fifth.getFullYear(),
				month : fifth.getMonth() + 1,
				date  : this.format(fifth.getDate()),
				day   : arr[fifth.getDay()]
			}
		};
	},
	format : function(n){
		return n; //(n < 10 ? '0' : '') + n;
	},
	ajax : function(code, $, city){
		var that = this,
			url = "http://query.yahooapis.com/v1/public/yql?callback=?",
			query = "select * from json where url='http://m.weather.com.cn/data/";
		$.getJSON(
			url,
			{
				q: query + code + ".html'",
				format: "json"
			},
			function (data, textStatus, jqXHR) {
				if(data && data.query && data.query.results){
					that.setText(data.query.results.weatherinfo, $, city);
				} else {
					alert('抱歉，地区数据加载失败，请刷新重试！')
					throw new Error('no such code: ' + code);
				}
			}
		);
	},
	setText : function(info, $, city){
		var time  = this.time();

		$('.nio-city').text(city).attr('title', city);
		$('.mini-date').text(time.first.month + '月' + time.first.date + '日');

		$('.nio-date1').text(time.first.year + '.' + time.first.month + '.' + time.first.date);
		$('.nio-date2').text(time.second.year + '.' + time.second.month + '.' + time.second.date);
		$('.nio-date3').text(time.third.year + '.' + time.third.month + '.' + time.third.date);
		$('.nio-date4').text(time.forth.year + '.' + time.forth.month + '.' + time.forth.date);
		$('.nio-date5').text(time.fifth.year + '.' + time.fifth.month + '.' + time.fifth.date);

		$('.nio-day1').text(time.first.day);
		$('.nio-day2').text(time.second.day);
		$('.nio-day3').text(time.third.day);
		$('.nio-day4').text(time.forth.day);
		$('.nio-day5').text(time.fifth.day);

		$('.nio-wea1').text(info.weather1);
		$('.nio-wea2').text(info.weather2);
		$('.nio-wea3').text(info.weather3);
		$('.nio-wea4').text(info.weather4);
		$('.nio-wea5').text(info.weather5);

		$('.nio-tem1').html(this.temp(info.temp1));
		$('.nio-tem2').html(this.temp(info.temp2));
		$('.nio-tem3').html(this.temp(info.temp3));
		$('.nio-tem4').html(this.temp(info.temp4));
		$('.nio-tem5').html(this.temp(info.temp5));

		$('.nio-img1').attr('class', 'nio-img1 nio-' + info.img1);
		$('.nio-img2').attr('class', 'nio-img2 nio-' + info.img3);
		$('.nio-img3').attr('class', 'nio-img3 nio-' + info.img5);
		$('.nio-img4').attr('class', 'nio-img4 nio-' + info.img7);
		$('.nio-img5').attr('class', 'nio-img5 nio-' + info.img9);
	},
	temp : function(str){
		str = str.split('~');
		return Math.max(parseInt(str[0], 10), parseInt(str[1], 10)) + '&deg;';
	},
	init : function($, city){
		if(city === remote_ip_info['city']) return;
		city = remote_ip_info['city'] = city || remote_ip_info['city'];
		for (var i = 0, len = citys.length; i < len; i ++) {
			if(citys[i].n === city) break;
		}
		if(citys[i]) this.ajax(citys[i].i, $, city);
		else alert('抱歉，该地区天气数据暂缺！');
	}
};
weather.init($);

if(typeof data != 'undefined'){
	var sel = new LinkageSelect({data : data});
	sel.bind('.linkageseclet .level_1');
	sel.bind('.linkageseclet .level_2');
}

var location = $('#nio-location');
$('.nio-list').on('click', '.nio-shift', function(){
	location.toggle();
});
location.submit(function(){
	var that = $(this),
		province = that.find('select').first().find('option:checked').text(),
		city = that.find('select').last().find('option:checked').text();
	if(city !== '城市'){
		if(province === '台湾省'){
			city = city;
		} else if( province === '香港' || province === '澳门'){
			city = province;
		} else {
			city = city.slice(0, (city.slice(-1) === '区' ? -2 : -1));
		}
		weather.init($, city);
		that.hide();
	} else alert('请选择城市！');
	return false;
}).find('.pull-right').click(function(){
	location.hide();
});

});