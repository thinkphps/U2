/**
 * Created by jack.wu on 14-4-11.
 */

//var jsonpHomeUrl = 'http://localhost/U2/index.php/Index';
//var tmplPath = 'http://localhost/U2/Home/Tpl/Public/';
//var baseurl='http://localhost/U2/';
var timer,loadid = 0;
(function($, window, document,undefined) {

    var TmallUniqloHome = function() {
        this.$weather = weather;

        this.provi = '';
    }

    //����Beautifier�ķ���
    TmallUniqloHome.prototype = {
        init: function() {
            var _this = this
            this.controlsEvent();
            this.cityOperator();

            var jsonpurl = baseurl +"index.php/Indexnew/getshopinfo?callback=callBackFunction.mapBindMarker";
            _this.$weather.jsonpFcuntion(jsonpurl);

            //������ʼ��
            _this.$weather.init({
                city : remote_ip_info.city || null,
                callback: function(city, temper, info){
                    var avg = callBackFunction.getavg(temper.high,temper.low);
                    _this.$weather.avg = avg;

                    var cookcity = _this.provi;
                    if(cookcity){
                        $.pron =  _this.provi;
                    }else{
                        $.pron = remote_ip_info.province;
                    }
                }
            });

        },

        sendcity :function(pro,city){
            var url = baseurl + "mini.php/Sendcity/sendpro?callback=callBackFunction.jsonpCallbackm&proname="+pro+"&cityname="+city;
            // ����script��ǩ������������
            var script = document.createElement('script');
            script.setAttribute('src', url);
            // ��script��ǩ����head����ʱ���ÿ�ʼ
            document.getElementsByTagName('head')[0].appendChild(script);
        },


        //l4�Ƽ�ģ��ͼ��
        getSuits :function(){
            var gender = $('#ulgender').find('.select').data('gender');
            this.showStyleMask(gender);
            //���ѡ�е���Ӥ�׶���ҵ�����ķ��ѡ�Ȼ����ʾӤ�׶����·�
            if( gender == 4){
                $('#ul_index-bar-place').find('.select').removeClass('select');
                $('.ch_all').addClass('select');
                $('.ch_all a').addClass('select');
                var url = jsonpHomeUrl +'/getgood?callback=callBackFunction.jsonpCallback3&tem='+this.$weather.avg+'&cid=0&sid=5&tid=0&pro='+$.pron;
                this.$weather.jsonpFcuntion(url);
                $('#suits-container').html('');
                $("#suits-container").hide();
                callBackFunction.setPageButtonDisplay(true);
            }
            else{
                var suitStyle = $('#ul_index-bar-place').find('.select').data('suitstyle');
                var jsonpurl = baseurl +'index.php/Indexnew/getConSuits2?callback=callBackFunction.callbackSuits&tem='+this.$weather.avg +  '&sid='+gender+'&fid='+suitStyle;
                this.$weather.jsonpFcuntion(jsonpurl);
            }
        },
        showStyleMask : function(gender){
            if( gender == 4){
                $('#style-mask').removeClass('children-style-mask');
                $('#style-mask').removeClass('male-style-mask');
                $('#style-mask').addClass('baby-style-mask').show();
                $('.style-children_mask').hide();
            }
            else if(gender == 3){

                $('#style-mask').removeClass('baby-style-mask');
                $('#style-mask').removeClass('male-style-mask');
                $('#style-mask').addClass('children-style-mask').show();
                $('.style-children_mask').show();
//                $('.baby-style-mask').hide();
//                $('.children-style-mask').show();
            }
            else if(gender == 2){
                $('#style-mask').removeClass('baby-style-mask');
                $('#style-mask').removeClass('children-style-mask');
                $('#style-mask').addClass('male-style-mask').show();
                $('.style-children_mask').hide();
            }
            else{
                $('#style-mask').removeClass('baby-style-mask');
                $('#style-mask').removeClass('children-style-mask');
                $('#style-mask').removeClass('male-style-mask');
                $('#style-mask').hide();
                $('.style-children_mask').hide();
            }
        },

        easytabs : function(menunr, active) {
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
        },
        controlsEvent : function(){
            var _this = this;
            $("#closemap").on("click",function(){
                $("#mapdiv").hide();
            });

            //���let's go��ť��ת����è��ҳ
            $('.youyigui_btn,.dr_logo').on('click',function(){
                window.open('http://a1761.oadz.com/link/C/1761/727/dbSAtIqGPkyXTaxXq7gPysYowUc_/p020/0/http://uniqlo.bigodata.com.cn/u2/');
            });

            //���ģ��ͼ��ת���������¼䲢������·������ղؼ���
            $('#suits-container').on('click','.imgSuits',function(){
                var suitid = $(this).data('suitid');
                var gender = $(this).data('gender');
                //jsonpHomeUrl
                window.open('http://a1761.oadz.com/link/C/1761/727/dbSAtIqGPkyXTaxXq7gPysYowUc_/p020/0/http://uniqlo.bigodata.com.cn/u2/?suitid='+ suitid + '&gender=' + gender);
            });

            $('#suits-container').on('click','.dressurl',function(){
                var dressurl = $(this).data('dressurl');
                var gender = $(this).data('gender');
                //jsonpHomeUrl
                window.open( dressurl );
            });

            // ��ҳ�����л�
            $('#ulweek').on('click', 'li', function(){
                var $that = $(this)
                $that.addClass('w_select').siblings('.w_select').removeClass('w_select');

                _this.$weather.init({
                    index : $that.index() + 1,
                    city:$('#nio-city').text(),
                    isMapChange :1,
                    callback: function(city, temper, info){
                        var avg = callBackFunction.getavg(temper.high,temper.low);
                        _this.$weather.avg = avg;
                        avg = avg?avg:0;
                        _this.$weather.occasion = _this.$weather.occasion?_this.$weather.occasion:0;
                        _this.$weather.sex = _this.$weather.sex?_this.$weather.sex:0;
                        _this.$weather.set = _this.$weather.set?_this.$weather.set:0;
                        _this.getSuits();
                        //��mimi������
                        _this.sendcity(city,info.city);
                    }
                });
            });

            //����Ա����ģ��ͼ
            $('#ulgender').on('click','li',function(){
                var $this = $(this);
//                alert($(this).data('gender'));
                $('#ulgender').find('.select').removeClass('select');
                $this.addClass('select');
                $this.find('a').addClass('select');
                $('#ul_index-bar-place .select').removeClass('select');
                $('.ch_all a').addClass('select');
                $('.product_inf').hide();
                _this.getSuits();

            });

            $('#ul_index-bar-place').on('click','li a',function(){
                var $this = $(this);
                $('#ul_index-bar-place').find('.select').removeClass('select');
                $this.addClass('select');
                _this.getSuits();
            });

            $('.home_arrow_left').on('click',function(){
                if( callBackFunction.CurrentPageSize > 6 ){
                    callBackFunction.CurrentPageSize -= 1;
                }
                $('#suits-container').moveprev();
            })

            $('.home_arrow_right').on('click',function(){

                //�����ǰ��¼<�ܼ�¼���������·�һ����¼
                if(callBackFunction.CurrentPageSize < callBackFunction.PageCount){

                    //�����ǰ��ǰ��¼==�Ѽ��ؼ�¼������ajaxȥ��̨ȡ��һҳ����
                    if(callBackFunction.CurrentPageSize=callBackFunction.CurrentLoadSize){
                        var gender = $('#ulgender').find('.select').data('gender');
                        var suitStyle = $('#ul_index-bar-place').find('.select').data('suitstyle');
                        var jsonpurl = baseurl +'index.php/Indexnew/getConSuits2?callback=callBackFunction.pageNextSuits&tem='
                            + _this.$weather.avg +  '&sid='+gender+'&fid='+suitStyle + '&page=' + callBackFunction.PageIndex;
                        _this.$weather.jsonpFcuntion(jsonpurl);
                    }else{
                        callBackFunction.CurrentPageSize += 1;
                        $('#suits-container').movenext();
                    }
                }

            })


            $('.weather').on('mouseenter',function(){
                if(loadid==0){
                    $('#suits-container').html('<div id="loadid" style="text-align:center; line-height=400px;padding-top:135px;"><img src="'+tmplPath+'/images/5-121204193R0-50.gif"></div>');
                }
                $("#div_main").show();
                $("#w_zk").hide();
                $("#w_sq").show();
                if(loadid==0){
                    _this.getSuits();
                    loadid  = 1;
                }
            });

        },
        bindProvinceOrCitys : function(){
            //�����ǰʡ����û��������ȥ���ݿ�ȡ����
            if($('#le1').html().length < 20){
                //��ȡʡ��
                var jsonpurl = baseurl +'index.php/Indexnew/getCityInfo?callback=callBackFunction.bindCity&id='+weather.cityCode;
                this.$weather.jsonpFcuntion(jsonpurl);
            }else if($('#spid').html().length < 10){
                $('#spid').html($('#le1').html());
                $('#spid').change();
                $('#spid').data('static',0);
            }
        },
        callGetShops : function(pid,cid){
            //���ص�ǰѡ�г��еĵ�����Ϣ
            var levelid = 0;
            switch(pid){
                case "1" :
                case "21" :
                case "42" :
                case "62" :
                    levelid = 1;
                    break
                default :
                    levelid = 0;
                    break;
            }
            var jsonpurl = baseurl +'index.php/Indexnew/getCityShop?callback=callBackFunction.bindShops&id='+cid+"&levelid=" + levelid;
            this.$weather.jsonpFcuntion(jsonpurl);
        },
        //ʡ�в���
        cityOperator : function(){
            var _this = this;

            $('#btn-city-close').on('click',this.hideCityDiv);

            //�����ʾ��ͼ
            $("#shopInfo,#a_shopinfo,#a_shopinfo2").on("click",function(){
                //����л��������Ѱ�ʡ����copy  le1��ʡ����Ϣ����ͼ��
                _this.bindProvinceOrCitys();
                $("#mapdiv").show();
                H.init();
            });

            //tips�������
            $('#scrollDiv').on('click','.preferential_2',function(){
                $("#mapdiv").show();
                stop_autochange();
                var list =   H.map.getOverlays();
                _this.bindProvinceOrCitys();
                $('#a_shopinfo').html($('#tipshopid').text());
                $('#shopInfo').hide();
                $('#a_shopinfo2').show();
                for(var i=1;i<list.length;i++){
                    if(list[i].title == $('#tipshopid').text() ){
                        setTimeout(function(){
                            H.setMarkerCenter(list[i]);
                        },100);
                        return;
                    }
                }
            });

            $('#btn-city-change').on('click',function(){
                _this.bindProvinceOrCitys();
                $('#div-citys').show();
            });

            $('#le1').on('change',function(){
                var pvalue = $('#le1 option:selected').val();
                var url = baseurl+"index.php/Indexnew/getcity?callback=callBackFunction.jsonpGetcity&pid="+pvalue;
                _this.$weather.jsonpFcuntion(url);
            });
            //���̸ı��λ����ǰ��������λ��
            $("#ddlShop").on("change",function(){
                var list =   H.map.getOverlays();

                var strHtml = '<span id="tipshopid" data-shopid="'+$('#ddlShop').val()+'">'+$('#ddlShop option:selected').text()+'</span><br>' + $('#ddlShop option:selected').data('opentime');
                $('#a_shopinfo').html($('#ddlShop option:selected').text());
                $('#shopInfo').hide();
                $('#a_shopinfo2').show();
                //tips
                callBackFunction.tipsfunction(strHtml);
                for(var i=1;i<list.length;i++){
                    if(list[i].title == $('#ddlShop option:selected').text() ){
                        H.setMarkerCenter(list[i]);
                        return;
                    }
                }
            });

            $('#btn-change').on('click',function(){
                var $that = $(this),
                    province = $('#le1 option:selected').text()
                city = $('#le2 option:selected').text()
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
                    weather.tipcity = city;
                    _this.$weather.init({'city' : city, 'province': province,
                        callback: function(city, temper, info){
                            var avg = callBackFunction.getavg(temper.high,temper.low);
                            _this.$weather.avg = avg;
                            avg = avg?avg:0;
                            _this.$weather.occasion = _this.$weather.occasion?_this.$weather.occasion:0;
                            _this.$weather.sex = _this.$weather.sex?_this.$weather.sex:0;
                            _this.$weather.set = _this.$weather.set?_this.$weather.set:0;
                            _this.getSuits();
                            //��mimi������
                            _this.sendcity(city,info.city);
                        }
                    });
                    _this.hideCityDiv();
                    $('#li_day0').addClass('w_select').siblings('.w_select').removeClass('w_select');
                    //����ͼ���е�ʡ��Ҳѡ��
                    $('#spid').val($('#le1').val());
                    $('#spid').change();
                    $('#spid').data('static',0);
                } else alert('��ѡ����У�');
            });


            //��ͼ�����л�
            $('#spid').on('change',function(){
                $('#spid').data('static',1);
                var pid = $('#spid option:selected').val();
                var levelid = 0;
                switch(pid){
                    case "1" :
                    case "21" :
                    case "42" :
                    case "62" :
                        levelid = 1;
                        break
                    default :
                        levelid = 0;
                        break;
                }
                var url = baseurl+"index.php/Indexnew/getcity?callback=callBackFunction.jsonpBaiduCity&pid="+pid+"&levelid=" + levelid;
                _this.$weather.jsonpFcuntion(url);
            });

            $('#scid').on('click',function(){
                callBackFunction.ISMapOper = 0;
            }).on('change',function(){

                var pid = $('#spid').val(),cid = $('#scid').val();
                if(callBackFunction.ISMapOper == 1){
                    _this.callGetShops(pid,cid);
                }else{

                H.map.centerAndZoom($("#scid option:selected").text(), 11);
                var cityname = $('#scid option:selected').text();
                 _this.callGetShops(pid,cid);
                //�����л�����ģ���ʡ��ѡ����
                $('#le1').val(pid,cid);
                $('#le1').change();

                if(cityname[cityname.length-1]=='��'){
                    var cnm = cityname.replace('��','');
                }else{
                    var cnm = $('#spid option:selected').text();
                }
                weather.tipcity = cnm;

                _this.$weather.init({'city' : cnm,
                    callback: function(city, temper, info){
                        var avg = callBackFunction.getavg(temper.high,temper.low);
                        _this.$weather.avg = avg;
                        avg = avg?avg:0;
                        _this.$weather.occasion = _this.$weather.occasion?_this.$weather.occasion:0;
                        _this.$weather.sex = _this.$weather.sex?_this.$weather.sex:0;
                        _this.$weather.set = _this.$weather.set?_this.$weather.set:0;
                        _this.getSuits();
                        _this.sendcity(city,info.city);
                    }
                });
                }
            });

        },

        hideCityDiv : function(){
            $('#div-citys').hide();
        }

    }//prototype

    var tmHome = new TmallUniqloHome();
    tmHome.init();

})(jQuery, window, document);


var callBackFunction = {
    ISMapOper : 0, //�Ƿ��ͼ�еĲ���
    PageSize : 10,//ÿҳ����
    PageIndex : 1,//��ǰҳ
    CurrentPageSize : 6,//��ʾ����
    PageCount : 30,//������
    CurrentLoadSize : 10,//��ǰ�Ѽ�������
    setPageButtonDisplay : function(isHide){
        if(isHide){
            $('.home_arrow_left,.home_arrow_right').hide();
        }
        else{
            $('.home_arrow_left,.home_arrow_right').show();
        }
    },
    callbackSuits : function(list){
        this.PageIndex = list.page;
        $("#div_index-bin,.index-suit").hide();
        this.CurrentPageSize = 6;
        this.CurrentLoadSize = 10;
        if(list.da == null){
            this.setPageButtonDisplay(true);
            $("#suits-container").hide();
            return;
        }
        var strHtml = "";
        var listlength = list.da.length;
        if( listlength > 6 ){
            if( listlength < this.PageSize ){
                for(var i = 0 ;i < listlength;i++){
                    strHtml += this.getCoverScrollItem(list.da[i]);
                }
            }
            else{
                for(var i = 0 ;i < this.PageSize;i++){
                    strHtml += this.getCoverScrollItem(list.da[i]);
                }
                this.PageCount = parseInt(list.count);
            }

            this.setPageButtonDisplay(false);
        }
        else{
            for(var i = 0 ;i < listlength;i++){
                strHtml += this.getCoverScrollItem(list.da[i]);
            }
            this.setPageButtonDisplay(true);
        }

        $('#suits-container').html(strHtml);
        $('#suits-container').coverscroll({items:'.item',minfactor:15,  'step':{ // compressed items on the side are steps
            'begin':0,//first shown step
            'limit':6, // how many steps should be shown on each side
            'width':8, // how wide is the visible section of the step in pixels
            'scale':true // scale down steps
        }});
        $("#suits-container").show();
    },
    //��ҳ��ȡL4ģ��ͼ
    pageNextSuits : function(list){
//        if( code == )

        var listlength = list.da.length;
        this.CurrentLoadSize += listlength;
        this.PageIndex = list.page;
        var strHtml = '';
        for(var i = 0 ;i < listlength;i++){
            strHtml += this.getCoverScrollItem(list.da[i]);
        }
        $("#suits-container").append(strHtml);
        $('#suits-container').movenext(this.CurrentPageSize);
        this.CurrentPageSize += 1;
    },
    jsonpGetcity : function(data){
        var str = '<option value="0">��ѡ��</option>';
        $.each(data.clist,function(pin,pv){
            str+="<option value='"+pv.region_id+"'>"+pv.local_name+"</option>";
        });
        $('#le2').html(str);
        var pid = $('#le1').val();
        switch (pid){
            case "1" :
            case "21" :
            case "42" :
            case "62" :
                $('#le2').val(data.clist[0].region_id);
                break
            default :

                if(callBackFunction.ISMapOper == 0){
                    if($('#scid').val() != '0'){
                        $('#le2').val($('#scid').val());
                    }else{
                        $('#le2').val(0);
                    }
                }
                else{
                    $('#le2').val(0);
                }

                break;
        }
    },
    jsonpCallbackm :function(data){

    },
    getCoverScrollItem : function(item){
        var strItem = '<div class="item">';
        strItem += '<img class="imgSuits" src="'+ item.suitImageUrl +'" data-gender="'+ item.suitGenderID+'"  data-suitid="'+ item.beubeuSuitID +'" />';
        strItem += '<div class="similarity">';
        var detail = item.detail;

        var numids = [];
        if(detail != null){
            for(var i =0;i<detail.length;i++){
                numids[i] = detail[i].num_iid;
                strItem += '<div class="circle">'
                strItem += '<a data-numid="'+detail[i].num_iid +'" href="'+ detail[i].detail_url +'&kid=11727_51912_165824_211542" target="_blank" title="'+detail[i].title +'">';
                strItem += '<img src="http://uniqlo.bigodata.com.cn/'+   detail[i].pic_url +'" ></a></div>';
            }
        }
        strItem +='</div>';
        strItem += '<div class="itemTitle">'+ this.getStyleByDescription(item.description)+'</div>';//<br><font style="color: #C0C0C0">'+ item.eglishName+'</font>
        strItem += '<div class="gotoroom none">';

        var url ='http://a1761.oadz.com/link/C/1761/727/dbSAtIqGPkyXTaxXq7gPysYowUc_/p020/0/http://uniqlo.bigodata.com.cn/u2/?suitid='+ item.beubeuSuitID + '&gender=' + item.suitGenderID ;
        strItem += '<a href="javascript:;" data-dressurl="'+ url + '" class="dressurl" target="_blank">ȥ�������¼��Դ�</a></div></div>';
        return strItem;
    },
    getStyleByDescription : function(description){
        var strHtml = '';
        if(description == '�ɰ�'){
            strHtml = '<div class="modelStyle1" ></div>';

        }else if(description == '�Ӽ�'){
            strHtml = '<div class="modelStyle2" ></div>';
        }else if(description == '��Ů'){
            strHtml = '<div class="modelStyle3" ></div>';
        }else if(description == 'ͨ��'){
            strHtml = '<div class="modelStyle4" ></div>';
        }else if(description == '����'){
            strHtml = '<div class="modelStyle5" ></div>';
        }else if(description == '����'){
            strHtml = '<div class="modelStyle6" ></div>';
        }else if(description == '����'){
            strHtml = '<div class="modelStyle7" ></div>';
        }else if(description == '��Լ'){
            strHtml = '<div class="modelStyle8" ></div>';
        }else if(description == 'Ӣ��'){
            strHtml = '<div class="modelStyle9" ></div>';
        }else if(description == '�˶�'){
            strHtml = '<div class="modelStyle10" ></div>';
        }else{
            strHtml = '<div class="modelStyle1" ></div>';
        }
        return strHtml;
    },
    getavg :function(high,low){
        var avg = Math.ceil((parseInt(low)+parseInt(high))/2);
        return avg;
    },
    tipsfunction : function(strContent){
        //tips
        stop_autochange();
        for (i = 1; i <=3; i++) {
            $('#tablink' + i).removeClass('current');
            $('.preferential_' + i).css('display','none');
        }
        $('#tablink2').addClass('current');
        $('.preferential_2').css('display','block');
        $('#shopid').html(strContent);
    },
    //��������Ϣ��ӵ���ͼ��
    mapBindMarker : function (data){
        H.initData(data);
        H.IsAddMarker = 1;
    },
    jsonpCallback3 : function(da){
        if(da.flag1=='p'){
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
            $('#tishi').css('display','none');
            $('#qtm').addClass('none');
        }
        $.uniqlo.kvSlider();
    },
    jsonpBaiduCity : function(data){
        var str = '<option value="0">��ѡ��</option>';
        $.each(data.clist,function(pin,pv){
            str+="<option value='"+pv.region_id+"'>"+pv.local_name+"</option>";
        });
        $('#scid').html(str);
        if($('#spid').data('static') == 1){
            $('#scid').val("0");
        }
        else{
            var pid = $('#le1').val();
            switch (pid){
                case "1" :
                case "21" :
                case "42" :
                case "62" :
                    $('#scid').val("0");
                    break
                default :
                    $('#scid').val($('#le2').val());
                    if(callBackFunction.ISMapOper == 1){
                        $('#scid').change();
                    }
                    break;
            }
        }
        $('#ddlShop').html('<option value="0">��ѡ��</option>');
    },
    bindCity : function(data){

        //��ʡ��
        var plist = data.plist;
        var strOptions = '<option value="0">��ѡ��</option>';
        for(var i = 0 ; i < plist.length;i++ ){
            strOptions += '<option value="'+ plist[i].region_id +'">'+ plist[i].local_name +'</option>';
        }
        $('#le1').html(strOptions);

        //�󶨵�ͼ���е�ʡ��
        $('#spid').html(strOptions);

        //�󶨳���
        var clist = data.clist;
        strOptions = '<option value="0">��ѡ��</option>';
        for(var i = 0 ; i < clist.length;i++ ){
            strOptions += '<option value="'+ clist[i].region_id +'">'+ clist[i].local_name +'</option>';
        }
        $('#le2').html(strOptions);

        //����ʡ��ѡ��ܼ
        $('#le1').val(data.nowcity.p_region_id);
        $('#spid').val(data.nowcity.p_region_id);
        $('#spid').change();
        $('#spid').data('static',0);
        //���ó���ѡ����
        $('#le2').val(data.nowcity.region_id);
    },
    bindShops : function(data){
        var strOption = '<option value="0">��ѡ��</option>';
        if(data != null){
            for(var i = 0; i < data.length;i++){
                strOption += '<option value="'+ data[i].id +'" data-opentime="'+ data[i].tradetime.replace(/\//g,'') +'">' + data[i].sname + '</option>';
            }
        }
        $('#ddlShop').html(strOption);
        if(callBackFunction.ISMapOper == 1){
            $('#ddlShop').val($('#tipshopid').data('shopid'));
        }
        else{
            $('#ddlShop').val(0);
        }
    },
    callGetCityIDByShopid:function(shopid){
        callBackFunction.ISMapOper = 1;

        switch($('#spid').val()){
            case "1" :
            case "21" :
            case "42" :
            case "62" :
                levelid = 1;
                break
            default :
                levelid = 0;
                break;
        }
        var jsonpurl = baseurl +'index.php/Indexnew/getShopId?callback=callBackFunction.setCityIDByShopid&id='+shopid+"&levelid="+levelid;
        weather.jsonpFcuntion(jsonpurl);
    },
    setCityIDByShopid:function(data){
        $('#scid').val(data.id);
        $('#scid').change();

    }
};


var tablink_idname = new Array("tablink");
var tabcontent_idname = new Array("preferential_");
var tabcount = new Array("3");
var loadtabs = new Array("1");
var autochangemenu = 1,counter = 0,slength;
var changespeed = 1;
var stoponhover = 0;
var menucount = loadtabs.length;
var a = 0;
var b = 1;

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
        //restart_autochange();
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