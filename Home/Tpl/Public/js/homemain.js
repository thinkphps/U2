/**
 * Created by jack on 14-4-4.
 */
var jsonpHomeUrl = 'http://uniqlo.bigodata.com.cn/u2/index.php/Index';
var getgood = 'http://uniqlo.bigodata.com.cn/u2/index.php/Index/ajaxgood';
var imgpath = 'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public';
var goodurl = 'http://uniqlo.bigodata.com.cn/u2/index.php/Index/getgood';
var sendurl = '{$uniurl}';
var provi = '';
var tmplPath = 'http://uniqlo.bigodata.com.cn/u2/Home/Tpl/Public/';
$(function(){



    var jsonpurl = baseurl +"index.php/Indexnew/getshopinfo?callback=mapBindMarker";
    jsonpFcuntion(jsonpurl);

    $('#suits-container').on('click','.imgSuits',function(){
        var similarity = $(this).parent().find(".similarity");
        var numids = [];
        var list = similarity.find("a");
        for(var i = 0;i<list.length;i++){
            numids[i] = $(list[i]).data("numid");
        }
        //jsonpHomeUrl
        window.open( "http://uniqlo.bigodata.com.cn/u1_5/mini.php/Index/index/num/"+ numids.join());
    });


    $('.youyigui_btn').attr('href','http://uniqlo.bigodata.com.cn/u1_5/mini.php/IndexNew/index.html');
    //$('.preferential_1').remove();
    //$('#tablink1').remove();
    window.imgpath = imgpath;
    $.weather.init({
        'subindex':1,
        imgpath : imgpath,
        city : remote_ip_info.city || null,
        callback: function(city, temper, info){
            var avg = getavg(temper.high,temper.low);
            $.weather.avg = avg;
            $.weather.getgurl = goodurl;
            var cookcity;
            cookcity = provi;
            if(cookcity){
                $.pron = provi;
            }else{
                $.pron = remote_ip_info.province;
            }

            getSuits();
            $("#div_index-bin").hide();
//            var jsonpurl = jsonpHomeUrl+"/ajaxgood?callback=jsonpCallback&tem="+avg+"&pro="+$.pron;
//            jsonpFcuntion(jsonpurl);
        }
    });

    $.uniqlo.index.week.on('click', 'li', function(){                  // ��ҳ�����л�
        var that = $(this)
        $.uniqlo.index.togClass(that, 'w_select')
        $.weather.init({
            'subindex':1,
            'shopid':$.weather.shopid,
            index : that.index() + 1,
            city:$('#nio-city').text(),
            imgpath : window.imgpath,
            callback: function(city, temper, info){
                var avg = getavg(temper.high,temper.low);
                $.weather.avg = avg;
                if($.weather.sex.toString() == "0" ||($.weather.occasion.toString() == "0" && $.weather.sex == undefined )&& $.weather.set.toString() != "1"){
                    getSuits();
                    $("#div_index-bin").hide();
                }else{
                    var jsonpurl =jsonpHomeUrl +"/getgood?callback=jsonpCallback2&tem="+avg+"&cid="+$.weather.occasion+'&sid='+$.weather.sex+'&tid='+$.weather.set+'&pro='+$.pron;;
                    jsonpFcuntion(jsonpurl);
                    $('#suits-container').html('');
                    $("#suits-container").hide();
                }
            }
        })
    })
})

//��������Ϣ��ӵ���ͼ��
function mapBindMarker(data){
    H.initData(data);
}

function weatherJsonpCallback(data){
    //kimi�ж��Ƿ����µ꿪��
    if(data.newstorre){
        $('.preferential_1').remove();
        $('#tablink1').remove();
       $('#scrollDiv').prepend('<li class="preferential_1" style="display:none;"><i></i><a>'+data.newstorre+'</a></li>');
       $('.preferential_side_bar').prepend("<li class=\"current\" id=\"tablink1\" onclick=\"easytabs('1', '1');\" onfocus=\"easytabs('1','1');\" onclick=\"return false;\"></li>");
    }else{
        $('.preferential_1').remove();
        $('#tablink1').remove();
    }
    stop_autochange();
    var lilength = $('#scrollDiv').children().length;
    slength = 3-lilength+1;
    counter = 3-lilength;
    loadtabs[0] = 3-lilength+1;
    if(data.shopid<=0){
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
  }else{
        for (i = 1; i <=3; i++) {
            $('#tablink' + i).removeClass('current');
            $('.preferential_' + i).css('display','none');
        }
        $('#tablink2').addClass('current');
        $('.preferential_2').css('display','block');
    }
    $.weather.setText(data,$.weather.currentOption);
}
//jsonp�ύ����
function jsonpFcuntion(url){
    var JSONP=document.createElement("script");
    JSONP.type="text/javascript";
    JSONP.src=url;
    document.getElementsByTagName("head")[0].appendChild(JSONP);
}
function tipsfunction(v){
    //tips
    stop_autochange();
        for (i = 1; i <=3; i++) {
            $('#tablink' + i).removeClass('current');
            $('.preferential_' + i).css('display','none');
        }
        $('#tablink2').addClass('current');
        $('.preferential_2').css('display','block');
    $('#shopid').html(v);

}
function jsonpCallback(da){
    if(da.ustr){
        $('#upc').html(da.ustr);
    }else{
        $('#upc').html('');
    }
    if(da.dstr){
        $('#downc').html(da.dstr);
    }else{
        $('#downc').html('');
    }
    $.uniqlo.kvSlider();
    if(!da.ustr && !da.dstr){
        $('#tishi').show();
    }else{
        $('#tishi').hide();
    }
}
function getgoods(cid,sid,tid){

    if(  sid == "0" || ( cid == "0" && sid == undefined) && tid != "1"){
        getSuits();
        $("#div_index-bin").hide();
    }
    else{
        var JSONP=document.createElement("script");
        JSONP.type="text/javascript";
        JSONP.src=jsonpHomeUrl +"/getgood?callback=jsonpCallback3&tem="+$.weather.avg+"&cid="+cid+'&sid='+sid+'&tid='+tid+'&pro='+$.pron;
        document.getElementsByTagName("head")[0].appendChild(JSONP);
        $('#suits-container').html('');
        $("#suits-container").hide();
    }
}

function jsonpCallback2(da){
    if(da.flag1=='p'){
        if($.weather.set==1){
            if(da.ustr){
                $('#upc').html(da.ustr);
            }
            if(da.dstr){
                $('#downc').html(da.dstr);
            }
        }else{
            //�����Ӥ�׶�������
            if(da.sid==4){
                if(da.fl==1){
                    $('#upc').html(da.ustr);
                    $('#downc').html(da.dstr);
                }else{
                    $('#taoz').html(da.ustr);
                }
            }else{
                $('#upc').html(da.ustr);
                $('#downc').html(da.dstr);
            }
        }
    }
    if(da.flag=='t'){
        $('#taoz').html(da.tstr);
    }else{
        if($.weather.set==1){
            $('#taoz').html('');
        }
    }
    if(da.fl==1){
        if(da.sid==4){
            $('.index-single').removeClass('none');
            $('.index-suit').addClass('none');
            $('.index-suit').css('display','none');
            $('.index-single').css('display','block');
        }
        $('#tishi').css('display','none');
        $('#qtm').removeClass('none');
    }else{
        if($.weather.set==1){
            $('.index-single').addClass('none');
            $('.index-suit').removeClass('none');
            $('.index-suit').css('display','block');
            $('.index-single').css('display','none');
        }else{
            if(da.sid==4){
                $('.index-single').addClass('none');
                $('.index-suit').removeClass('none');
                $('.index-suit').css('display','block');
                $('.index-single').css('display','none');
            }else{
                $('.index-single').removeClass('none');
                $('.index-suit').addClass('none');
                $('.index-suit').css('display','none');
                $('.index-single').css('display','block');
            }
        }
        $('#tishi').css('display','none');
        $('#qtm').addClass('none');
    }
    $.uniqlo.kvSlider();
    if($.weather.set==1){
        if(!da.ustr && !da.dstr && !da.tstr){
            $('#tishi').css('display','block');
        }else if(da.tstr){
            $('#tishi').css('display','none');
        }
    }
}
function jsonpCallback3(da){
    if(da.flag1=='p'){
        if($.weather.set==1){
            if(da.ustr){
                $('#upc').html(da.ustr);
            }
            if(da.dstr){
                $('#downc').html(da.dstr);
            }
        }else{
            //�����Ӥ�׶�������
            if(da.sid==4){
                if(da.fl==1){
                    $('#upc').html(da.ustr);
                    $('#downc').html(da.dstr);
                }else{
                    $('#taoz').html(da.ustr);
                }
            }else{
                $('#upc').html(da.ustr);
                $('#downc').html(da.dstr);
            }
        }
    }
    if(da.flag=='t'){
        $('#taoz').html(da.tstr);
    }else{
        if($.weather.set==1){
            $('#taoz').html('');
        }
    }
    if(da.fl==1){
        if(da.sid==4){
            $('.index-single').removeClass('none');
            $('.index-suit').addClass('none');
            $('.index-suit').css('display','none');
            $('.index-single').css('display','block');
        }
        $('#tishi').css('display','none');
        $('#qtm').removeClass('none');
    }else{
        if($.weather.set==1){
            $('.index-single').addClass('none');
            $('.index-suit').removeClass('none');
            $('.index-suit').css('display','block');
            $('.index-single').css('display','none');
        }else{
            if(da.sid==4){
                $('.index-single').addClass('none');
                $('.index-suit').removeClass('none');
                $('.index-suit').css('display','block');
                $('.index-single').css('display','none');
            }else{
                $('.index-single').removeClass('none');
                $('.index-suit').addClass('none');
                $('.index-suit').css('display','none');
                $('.index-single').css('display','block');
            }
        }
        $('#tishi').css('display','none');
        $('#qtm').addClass('none');
    }
    $.uniqlo.kvSlider();
    if($.weather.set==1){
        if(!da.ustr && !da.dstr && !da.tstr){
            $('#tishi').css('display','block');
        }else if((!da.ustr || !da.dstr) && !da.tstr){
            $('#tishi').css('display','block');
        }else if(da.tstr){
            $('#tishi').css('display','none');
        }
    }
}
function getavg(high,low){
    var avg = Math.ceil((parseInt(low)+parseInt(high))/2);
    return avg;
}
function sendcity(pro,city){
    var url = "http://uniqlo.bigodata.com.cn/u1_5/mini.php/Sendcity/sendpro?callback=jsonpCallbackm&proname="+pro+"&cityname="+city;
    // ����script��ǩ������������
    var script = document.createElement('script');
    script.setAttribute('src', url);
    // ��script��ǩ����head����ʱ���ÿ�ʼ
    document.getElementsByTagName('head')[0].appendChild(script);
}

function jsonpCallback4(da){
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
    $.uniqlo.kvSlider();
}
$('#le1').on('change',function(){
    var pvalue = $('#le1 option:selected').val();
    var url = baseurl+"index.php/Indexnew/getcity?callback=jsonpGetcity&pid="+pvalue;
    jsonpFcuntion(url);
});

function jsonpGetcity(data){
    var str = '<option value="0">��ѡ��</option>';
    $.each(data.clist,function(pin,pv){
        if(pv.sel==1){
            var sel2 = "selected='selected'";
        }
        str+="<option value='"+pv.region_id+"' "+sel2+">"+pv.local_name+"</option>";
    });
    $('#le2').html(str);
}

//��ͼ�����л�
$('#spid').on('change',function(){
    var pid = $('#spid option:selected').val();
    var url = baseurl+"index.php/Indexnew/getcity?callback=jsonpBaiduCity&pid="+pid+"&baiduid=1";
    jsonpFcuntion(url);
});

function jsonpBaiduCity(data){
    var str = '<option value="0">��ѡ��</option>';
    $.each(data.clist,function(pin,pv){
        str+="<option value='"+pv.region_id+"'>"+pv.local_name+"</option>";
    });
    $('#scid').html(str);
    $('#ddlShop').html('<option value="0">��ѡ��</option>');
}

$('#scid').on('change',function(){
    H.map.centerAndZoom($("#scid option:selected").text(), 11);
    var pid = $('#spid option:selected').val(),cid = $('#scid option:selected').val();
    var cityname = $('#scid option:selected').text();
    if(cityname[cityname.length-1]=='��'){
        var cnm = cityname.replace('��','');
    }else{
        var cnm = $('#spid option:selected').text();
    }

    $.weather.init({'city' : cnm,imgpath : window.imgpath,'subid':'1','baiduerjiid':cid,
        callback: function(city, temper, info){
            var avg = getavg(temper.high,temper.low);
            $.weather.avg = avg;
            avg = avg?avg:0;
            $.weather.occasion = $.weather.occasion?$.weather.occasion:0;
            $.weather.sex = $.weather.sex?$.weather.sex:0;
            $.weather.set = $.weather.set?$.weather.set:0;
            if($.weather.sex.toString() == "0" ||($.weather.occasion.toString() == "0" && $.weather.sex == undefined )&& $.weather.set.toString() != "1"){
                getSuits();
                $("#div_index-bin").hide();
            }
            else{
                var jsonpurl ="http://uniqlo.bigodata.com.cn/u2/index.php/Index/getgood?callback=jsonpCallback4&tem="+avg+"&pro="+city+'&cid='+$.weather.occasion+'&sid='+$.weather.sex+'&tid='+$.weather.set;
                jsonpFcuntion(jsonpurl);
                $('#suits-container').html('');
                $("#suits-container").hide();
            }
            sendcity(city,info.city);
        }
    });
});
function jsonpBaiduCity2(data){
    $('#ddlShop').html('<option value="0">��ѡ��</option>');
    if(data.shopid=='undefined'){
        $('#a_shopinfo').html('�����������¿��ŵ�');
    }
    var str = '<option value="0">��ѡ��</option>';
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
            $('#emptymsg').html('�������������¿��ŵ꣬��ѡ���������е��ŵ�');
        }
    }
}
var tablink_idname = new Array("tablink");
var tabcontent_idname = new Array("preferential_");
var tabcount = new Array("3");
var loadtabs = new Array("1");
var autochangemenu = 1,counter = 0,slength;
var changespeed = 1;
var stoponhover = 0;

function easytabs(menunr, active) {
    if (menunr == autochangemenu) {
        currenttab = active;
    }
    if ((menunr == autochangemenu) && (stoponhover == 1)) {
        stop_autochange()
    } else if ((menunr == autochangemenu) && (stoponhover == 0)) {
        counter = 0;
    }
    menunr = menunr - 1;
    for (i = 1; i <= tabcount[menunr]; i++) {
        $('#'+tablink_idname[menunr] + i).removeClass('current');
        $('.'+tabcontent_idname[menunr] + i).css('display','none');
    }

    if($('#'+tablink_idname[menunr] + active)){
    $('#'+tablink_idname[menunr] + active).addClass('current');
    $('.'+tabcontent_idname[menunr] + active).css('display','block');
       }
}

var totaltabs = tabcount[autochangemenu - 1];
var currenttab = loadtabs[autochangemenu - 1];

function start_autochange() {
    counter = counter + 1;
    timer = setTimeout("start_autochange()", 3000);
    if (counter == changespeed + 1) {
        currenttab++;
        if (currenttab > totaltabs) {
            currenttab = slength;
        }
        easytabs(autochangemenu, currenttab);
        restart_autochange();
    }
}

function restart_autochange() {
    clearTimeout(timer);
    counter = 0;
    start_autochange();
}
function stop_autochange() {
    clearTimeout(timer);
    counter = 0;
}

var menucount = loadtabs.length;
var a = 0;
var b = 1;


//l4�Ƽ�ģ��ͼ��
function getSuits(){

    var jsonpurl = baseurl +"index.php/Indexnew/getSuits?callback=callbackSuits&tem="+$.weather.avg;
    jsonpFcuntion(jsonpurl);
}


function callbackSuits(list){
    $("#div_index-bin,.index-suit").hide();

    var strHtml = "";
    var listlength = list.length;
    if(listlength > 6){
        listlength = 6
    }

    for(var i = 0 ;i < listlength;i++){
        strHtml += getCoverScrollItem(list[i]);
    }
    $('#suits-container').html(strHtml);
    $("#suits-container").show();

    $('#suits-container').coverscroll({items:'.item',minfactor:5});

}

function getCoverScrollItem(item){
    var strItem = '<div class="item">';
    strItem += '<img class="imgSuits" src="'+ item.suitImageUrl +'" />';
    strItem += '<div class="similarity">';
    var detail = item.detail;
    var numids = [];
    for(var i =0;i<detail.length;i++){
        numids[i] = detail[i].num_iid;
        strItem += '<div class="circle">'
        strItem += '<a data-numid="'+detail[i].num_iid +'" href="'+ detail[i].detail_url +'" target="_blank">';
        strItem += '<img src="http://uniqlo.bigodata.com.cn/'+   detail[i].pic_url +'" ></a></div>';
    }
    strItem +='</div>';
    strItem += '<div class="itemTitle">'+item.description+'<br><font style="color: #C0C0C0">'+ item.eglishName+'</font></div>';
    strItem += '<div class="gotoroom none">';
    strItem += '<a href="http://uniqlo.bigodata.com.cn/u1_5/mini.php/Index/index/num/'+ numids.join() +'" target="_blank">ȥ�������¼��Դ�</a></div></div>';
    return strItem;
}

function jsonpCallbackm(data){

}
