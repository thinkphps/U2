/**
 * Created by jack on 14-8-7.
 */
baseurl='http://uniqlo.bigodata.com.cn/u2/';
var timer;
jsonpHomeUrl = 'http://uniqlo.bigodata.com.cn/u2/index.php/Index';
tmplPath = 'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/';
var list = [

    'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/jsmao/citys.js',
    'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js',
    'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/jsmao/nio-weather-new.js',
    'http://api.map.baidu.com/api?v=2.0&ak=2TT2TBbLxVUyRGpvY4Bq2gCN',
    'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/jsmao/Map/H.min.js',
    'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/jsmao/home.js',
    'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/jsmao/CoverScroll/jquery.coverscroll.js',
    'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/jsmao/main.js'
]

var arr = [];
arr.push('<link href="http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/css/dressing_room.css" rel="stylesheet" type="text/css" />')
arr.push('<script type="text/javascript" src="http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/jsmao/jquery.js"></script>')
arr.push('<script type="text/javascript">$BIGO = $.noConflict(true);</script>')
for(var i = 0, ln = list.length; i < ln; i++){
    arr.push('<script src="'+ list[i] + '">' );
    arr.push('</script>');
}
if(arr.length > 0){
    document.write(arr.join('') );
}
