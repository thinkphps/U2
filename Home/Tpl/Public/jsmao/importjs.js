/**
 * Created by jack on 14-8-7.
 */
baseurl='http://localhost:81/U2/';
var timer;
jsonpHomeUrl = 'http://localhost:81/U2/index.php/Indexnew';
tmplPath = 'http://localhost:81/U2/Home/Tpl/Public/';
var list = [

    'http://localhost:81/U2/Home/Tpl/Public/jsmao/citys.js',
    'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js',
    'http://localhost:81/U2/Home/Tpl/Public/jsmao/nio-weather-new.js',
    'http://localhost:81/U2/Home/Tpl/Public/jsmao/home.js',
    'http://localhost:81/U2/Home/Tpl/Public/jsmao/CoverScroll/jquery.coverscroll.js',
    'http://localhost:81/U2/Home/Tpl/Public/jsmao/main.js'
]

var arr = [];
arr.push('<link href="http://localhost:81/U2/Home/Tpl/Public/css/dressing_room.css" rel="stylesheet" type="text/css" />')
arr.push('<script type="text/javascript" src="http://localhost:81/U2/Home/Tpl/Public/jsmao/jquery.js"></script>')
arr.push('<script type="text/javascript">$BIGO = $.noConflict(true);</script>')
for(var i = 0, ln = list.length; i < ln; i++){
    arr.push('<script src="'+ list[i] + '">' );
    arr.push('</script>');
}
if(arr.length > 0){
    document.write(arr.join('') );
}
