/**
 * Created by jack on 14-8-7.
 */
baseurl='http://localhost/u2/';
var timer;
jsonpHomeUrl = 'http://localhost/u2/index.php/Index';
tmplPath = 'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/';
var list = [

    'http://localhost/u2/Home/Tpl/Public/js/citys.js',
    'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js',
    'http://localhost/u2/Home/Tpl/Public/js/nio-weather-new.js',
    'http://api.map.baidu.com/api?v=2.0&ak=2TT2TBbLxVUyRGpvY4Bq2gCN',
    'http://localhost/u2/Home/Tpl/Public/js/Map/H.min.js',
    'http://localhost/u2/Home/Tpl/Public/js/home.js',
    'http://localhost/u2/Home/Tpl/Public/js/CoverScroll/jquery.coverscroll.js',
    'http://localhost/u2/Home/Tpl/Public/js/main.js'
]

var arr = [];
for(var i = 0, ln = list.length; i < ln; i++){
    arr.push('<');
    arr.push('script src="'+ list[i] + '"' );
    arr.push('>');
    arr.push('</');
    arr.push('script');
    arr.push('>');
}
if(arr.length > 0){
    document.write(arr.join('') );
}
