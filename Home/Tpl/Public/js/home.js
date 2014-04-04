/**
 * Created by jack on 14-3-26.
 */
jQuery(function($){
    $.fn.lazyload = function(){
        return this.each(function(){
            var that = $(this),
                parent = that.parent(),
                index = parent.index(),
                len = parent.parent('ul').find('li').length;

            if(!this.src && (index < 5 || index >= len - 1)){
                this.src = that.data('original')
            }
        })
    }

    // 图片切换插件
    $.fn.nioSlider = function(options){
        return this.each(function(){
            var that = $(this)
            var ul = that.find('ul')
            var prev = that.siblings(options.prev)
            var next = that.siblings(options.next)
            options = $.extend({
                width : 180,
                speed : 600,
                min : 2,
                callback: $.noop
            }, options)

            options.callback.call(that)

            if(ul.find('li').length < options.min){
                return prev.add(next).hide()
            }
            next.show().click(function(){
                if(uniqlo.sliding) return
                uniqlo.sliding = true
                ul.animate({'left' : '-' + options.width}, options.speed, function(){
                    ul.children().first().remove().appendTo(ul.css('left', 0))
                    uniqlo.sliding = false
                    options.callback.call(that)
                })
            })
            prev.show().click(function(){
                if(uniqlo.sliding) return
                uniqlo.sliding = true
                ul.css('left', '-' + options.width + 'px')
                    .prepend(ul.children().last().remove())
                    .animate({'left' : 0}, options.speed,function(){
                        uniqlo.sliding = false
                        options.callback.call(that)
                    })
            })
        })
    }

    // jQuery easing plugin
    jQuery.easing['jswing'] = jQuery.easing['swing']

    jQuery.extend( jQuery.easing,
        {
            def: 'easeOutExpo',
            swing: function (x, t, b, c, d) {
                return jQuery.easing[jQuery.easing.def](x, t, b, c, d)
            },
            easeOutExpo: function (x, t, b, c, d) {
                return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b
            }
        })


    /* ===================== 内页业务开始 ===================== */

    var uniqlo = {                                           // uniqlo全局对象
        sliding : false,
        cabContainer : $('.mini-cab-container'),
        netContainer : $('.mini-net-container'),
        kvContainer : $('.mini-kv-container'),
        indexContainer: $('.index-slide-container'),
        body : $('html, body'),
        scrollTo : function(top){
            this.body.animate({'scrollTop' : top}, 300)
        },
        cabSlider : function(){
            return this.cabContainer.nioSlider({
                prev: '.mini-cab-prev',
                next: '.mini-cab-next'
            })
        },
        netSlider : function(){
            return this.netContainer.nioSlider({
                prev: '.mini-net-prev',
                next: '.mini-net-next',
                width: 218,
                min : 4
            })
        },
        kvSlider : function(){
            var kv = this.kvContainer.nioSlider({
                prev: '.mini-kv-prev',
                next: '.mini-kv-next',
                width: 200,
                min : 4,
                callback: function(){
                    this.find("img").lazyload();
                }
            });
            $('.mini-kv-prev,.mini-kv-next')[($.weather.sex||0) == 0?'hide':'show']()
            return kv;
        },
        indexSlider : function(){
            return this.indexContainer.nioSlider({
                prev: '.mini-kv-prev',
                next: '.mini-kv-next',
                width: 210
            })
        }
    }

    // cab slider
    uniqlo.cabSlider()

    // net slider
    uniqlo.netSlider()

    // bottom kv slider
    uniqlo.kvSlider().find('img').lazyload()

    // index slider
    uniqlo.indexSlider()

    // 导出全局对象供外部使用if need
    $.uniqlo = uniqlo

    /* ============ cabnet交互效果 ============= */

    !(function($){

        var cabnet = {                                         // 集中声明变量
            list      : [],                                      // 保存已添加的图片id
            hoverBox  : $('.mini-net-content'),                 // 图片悬浮框
            net       : $('.mini-net'),                       // 右侧net框
            netConfirm: $('.mini-net-confirm'),               // 删除确认框
            netSlide  : $('.mini-net-slide'),                 // 两个图片切换框
            netIsEmpty: true,                                    // net默认为空
            netChoose : $('a.mini-net-choose'),                  // 挑选衣服按钮
            cab       : $('div.mini-cab'),                       // 左侧的cab框
            cabSlide  : $('div.mini-cab-slide'),                 // cab-slide框
            cabTips   : $('div.mini-cab-tips'),                  // cab提示框
            cabBuy    : $('a.mini-cab-buy'),                     // cab购买按钮
            cabClear  : $('a.mini-cab-clear'),                   // cab清空按钮
            cabPrev   : $('a.mini-cab-prev'),                    // cab-prev按钮
            cabNext   : $('a.mini-cab-next'),                    // cab-next按钮
            cabChoose : $('form.mini-cab-choose'),               // cab-choose框
            buyIsShow : false,                                   // cab-choose默认隐藏
            cabEmpty  : $('a.mini-cab-empty'),                   // cab-empty框
            netLike   : $('a.mini-net-like'),                    // net-like按钮
            netHad    : $('a.mini-net-had')                     // net-had按钮


        }
        cabnet.netEmpty = cabnet.net.find('a.mini-net-empty')  // netSlide提示框

        var jsonpurl = baseurl +"index.php/Indexnew/getshopinfo?callback=mapBindMarker";
        jsonpFcuntion(jsonpurl);


        $("#w_sq").on("click",function(){
            $("#div_main").hide();
            $(this).hide();
            $("#w_zk").show();
        });

        $("#w_zk").on("click",function(){
            $("#div_main").show();
            $(this).hide();
            $("#w_sq").show();
        });

        /* == net交互 == */

        cabnet.net.on('add', function(e, id){                  // 注册netSlide的被添加自定义事件
            if(cabnet.netIsEmpty){
                cabnet.netEmpty.addClass('mini-net-scale')         // 隐藏net为空提示框
            }
            cabnet.list.push(id)                                 // 保存图片id
            cabnet.netIsEmpty = false

            uniqlo.netSlider()                                   // 运行net slider
        })

        cabnet.net.on('del', function(e, id){                  // 注册netSlide的被删除自定义事件
            var arr = cabnet.list
            for(var i = arr.length; i --;){
                if(arr[i] === id){
                    cabnet.list.splice(i, 1)
                }
            }

            if(!cabnet.list.length){
                cabnet.netEmpty.removeClass('mini-net-scale')
                cabnet.netIsEmpty = true
            }

            uniqlo.netSlider()                                   // 运行net slider
        })

        cabnet.netSlide.on('mouseover', 'img', function(e){    // netSlide图片悬浮效果

            if(uniqlo.sliding) return

            var position = $(this).position()
            var thisSlide = $(e.delegateTarget)
            var pos = thisSlide.data('pos')
            var top = pos === '#cab-top' ? 56 : 322
            var restSlide = thisSlide.siblings('div.mini-net-slide')

            netHoverCallback.call(this, pos, restSlide)          // 图片悬浮的callback里处理细节

            cabnet.hoverBox.css({                                // 显示图片悬浮框
                left: position.left + 26,
                top: position.top + top
            }).show()
        })

//        cabnet.hoverBox.on('mouseleave', function(){           // hoverBox鼠标离开后隐藏全部
//
//            cabnet.hoverBox.hide()
//            cabnet.netConfirm.hide()
//            cabnet.netLike.add(cabnet.netHad).removeClass('mini-net-checked')
//
//        }).on('click', function(){                             // 点击任意位置添加至搭配间
//
//            addCabCallback.call(this)                            // 添加至左侧的callback
//
//        }).on('click', 'a.mini-net-del', function(e){          // 点击删除按钮弹出confirm
//
//            cabnet.netConfirm.show()
//            e.stopPropagation()
//
//        })

        cabnet.netConfirm.on('click', false)                   // confirm点击任意位置不冒泡
            .on('click', 'a.mini-net-yes', function(e){            // confirm确认删除
                //kimi
                delgo(cabnet.hoverBox.data('id'))
                //kimi
                delNetCallback.call(this)                            // netSlide被删除的回调
                e.stopPropagation()

            }).on('click', 'a.mini-net-no', function(e){           // confirm取消删除

                cabnet.netConfirm.hide()
                e.stopPropagation()

            })

        cabnet.netLike.add(cabnet.netHad).on('click', function(e){

            $(this).toggleClass('mini-net-checked')              // '喜欢'与'已买入'的类名切换
            e.stopPropagation()

        })

        cabnet.netEmpty.add(cabnet.cabEmpty).add(cabnet.netChoose).on('click', function(){
            uniqlo.scrollTo(742)
        })

        /* == cab交互 == */

        cabnet.cab.ajax = cabnet.cab.sex = cabnet.cab.isEmpty = true // cab默认为空，默认读取后台
        cabnet.cab.pos = null                                  // 记录cab中添加的上衣/下衣
        cabnet.cab.on('add', function(e, pos, id){             // 注册cabSlide的被添加自定义事件
            var tag = cabnet.hoverBox.data('tag')
            var src = cabnet.hoverBox.data('src')

            cabnet.cabChoose.trigger('hidden')

            if(cabnet.cab.isEmpty){                              // 第一次点击net上衣/下衣图片
                cabnet.cab.isEmpty = false

                cabnet.cabTips.show()
                cabnet.cabEmpty.hide()
                cabnet.cabBuy.addClass('show')
                cabnet.cabClear.show()

                cabnet.cab.pos = pos                               // 记录pos
                cabAjaxCallback(tag, id, src, pos)                 // 第一次读取后台ajax

            } else {                                             // 第二次点击net上衣/下衣图片

                if(cabnet.cab.pos == pos && cabnet.cab.ajax){      // 如果点击同样的上衣/下衣

                    cabAjaxCallback(tag, id, src, pos)

                } else {

                    if(cabnet.cab.ajax){
                        cabnet.cabPrev.hide()
                        cabnet.cabNext.hide()
                    }
                    cabnet.cab.ajax = false

                }
            }
        }).on('click', function(){                             // 点击整个cab框关闭tips

            cabnet.cabTips.hide()

        })

        cabnet.cabBuy.on('click', function(){                  // 点击购买弹出form

            if(!cabnet.buyIsShow){

                cabnet.cabChoose.trigger('shown')

            } else{

                var url = cabnet.cabChoose.find('input:checked').val()

                if(url == 'undefined') return alert('您还没有选择这类衣服')
                window.open(url)

            }

        })

        cabnet.cabChoose.on('click', 'a', function(){

            cabnet.cabChoose.trigger('hidden')

        }).on('click','input', function(){

            var pos = this.getAttribute('pos')
            this.value = $(pos).find('img').first().attr('url')

        }).on('hidden', function(){

            cabnet.cabChoose.hide()

            cabnet.cabBuy.text('我要购买')

            cabnet.buyIsShow = false

        }).on('shown', function(){

            cabnet.cabChoose.show().find('input').first().trigger('click')

            cabnet.cabBuy.text('前往优衣库旗舰店')

            cabnet.buyIsShow = true

        })

        cabnet.cabSlide.on('click', function(){

            cabnet.cabChoose.trigger('hidden')

        })

        cabnet.cabClear.on('click', function(){                // 清空cab
            cabnet.cab.ajax = cabnet.cab.isEmpty = true          // 重置cab默认状态
            cabnet.cabSlide.find('ul').html('')
            cabnet.cabTips.hide()
            cabnet.cabBuy.removeClass('show')
            cabnet.cabClear.hide()
            cabnet.cabPrev.hide()
            cabnet.cabNext.hide()
            cabnet.cabChoose.hide()
            cabnet.cabEmpty.show()
        })

        /* == 内页kv交互 == */

        cabnet.kvSlide = $('.mini-kv-slide')                // kv-slide框
        cabnet.kvHover = $('.mini-kv-hover')                // kv-hover框
        cabnet.kvContent = $('.mini-kv-content'),
        cabnet.kvTimer = 0                                     // kv-hover框延迟
        cabnet.kvIsOpen = false                                // kv-hover状态
        cabnet.kvHover.price = cabnet.kvHover.find('strong')   // kv-hover价格
        cabnet.kvHover.rest = cabnet.kvHover.find('span')      // kv-hover余量
        cabnet.kvHover.title = cabnet.kvHover.find('a.ft14')   // kv-hover产品名

        cabnet.kvSlide.on('mousemove', 'img', function(e){     // kvSlide图片悬浮

            var isIndexPage = 'index';

            if(uniqlo.sliding) return

            var position = $(this).position()
            var thisSlide = $(e.delegateTarget)
            var pos = thisSlide.data('pos')
            var addBtn = cabnet.kvHover.find('a.mini-kv-add')
            var top = 160
            if(pos){
                top = pos == '#net-top' ? 53 : 250
                addBtn.text('收入衣柜')
            } else {
                addBtn.text('查看详情')
            }
            if(this.getAttribute('fg') == 116){
                addBtn.text('查看详情')
            }

            kvHoverCallback.call(this, pos, isIndexPage)         // 图片悬浮的callback里处理细节

            cabnet.kvTimer = setTimeout(function(){

                cabnet.kvHover.css({                               // 显示图片悬浮框
                    left: position.left + 55,
                    top: position.top + top
                }).show()
                cabnet.kvIsOpen = true

            }, 100)

        }).on('mouseout', function(){
            clearTimeout(cabnet.kvTimer)
        })

        cabnet.kvContent.on('mouseleave', function(){            // kvHover自动隐藏
            cabnet.kvHover.hide()
            cabnet.kvIsOpen = false
        }).on('click', 'a.mini-kv-add', function(){            // 点击添加衣柜
            var id = cabnet.kvHover.data('id')

            var target = this.getAttribute('target')
            if(target) return
        })


//        $(document).mousemove(function(e){
//            var div = $(e.target).closest('div')
//            if(cabnet.kvIsOpen && !div.is(cabnet.kvHover)){
//                cabnet.kvHover.hide()
//                cabnet.kvIsOpen = false
//            }
//        })

        /* == cab、net、kv系列回调函数 == */

        function addCabCallback(){                             // 添加至左侧的callback

            var id = cabnet.hoverBox.data('id')
            var pos = cabnet.hoverBox.data('pos')                // 添加对应cab的图片
            var url = ' url="' + cabnet.hoverBox.data('url') + '"'
            var src = ' src="' + cabnet.hoverBox.data('src') + '"'
            var ids = ' id="' + id + '"'
            var sex = cabnet.hoverBox.data('sex')

            if(cabnet.cab.isEmpty){
                cabnet.cab.sex = sex
            } else if(cabnet.cab.sex !== sex && cabnet.cab.pos !== pos){
                return alert('这件与您搭配间中的衣物性别不符哦')
            }

            $(pos).find('ul').html('<li><img ' + ids + src + url + ' /></li>')

            cabnet.cab.trigger('add', [pos, id])                 // 触发cab的被添加自定义事件
        }

        function netHoverCallback(pos, rest){                  // 图片悬浮的callback

            var url = this.getAttribute('url')
            var tag = this.getAttribute('tag')
            cabnet.hoverBox
                .find('h3').text(tag + '风格')
                .end().find('span').text(this.getAttribute('place') + '装')
                .end().find('img').attr('src', this.src)
                .end().find('a.mini-net-detail').attr('href', url)
                .end().find('strong').text(this.getAttribute('price'))

            cabnet.hoverBox.data({
                'pos': pos,                                        // 保存thisSlide映射到cab的id
                'rest': rest,                                      // 保存restSlide
                'id' : this.id,                                    // 保存图片id
                'src' : this.src,                                  // 保存图片src
                'tag' : tag,
                'sex' : this.getAttribute('sex'),
                'url' : url                                        // 保存图片url
            })
        }

        function cabAjaxCallback(tag, id, src, pos){           // 这里的ajaxCallback只是测试用
            var ul = cabnet.hoverBox.data('rest').find('ul').html() // 待删除
            $(pos).siblings('.mini-cab-slide').find('ul').html(ul)  // 待删除

            console.log(tag)                                     // 图片标签
            console.log(id)                                      // 图片id
            console.log(src)                                     // 图片src

            // !!读取数据后执行slider切换，必要
            uniqlo.cabSlider()
        }

        function kvHoverCallback(pos, isIndexPage){            // 参数pos保存了上衣/下衣类型

            var url = this.getAttribute('url')
            var price = this.getAttribute('price')

            cabnet.kvHover.price.text(price)
            cabnet.kvHover.rest.text(this.getAttribute('rest'))
            cabnet.kvHover.title.text(this.getAttribute('alt')).attr('href', url)

            cabnet.kvHover.data({                                // 保存图片src与id等信息
                'src' : this.getAttribute('src'),
                'tag' : this.getAttribute('tag'),
                'place' : this.getAttribute('place'),
                'price' : price,
                'url' : url,
                'id' : this.id,
                'sex' : this.getAttribute('sex'),
                'pos' : pos
            })

            var btn = cabnet.kvHover.find('a.mini-kv-add')

            if(!pos || this.getAttribute('fg') == 116){
                btn.attr({'href': url, 'target': '_blank'})
            } else if(isIndexPage){
                btn.attr('href', this.getAttribute('miniUrl'))
            } else {
                btn.attr({'href': 'javascript:;', 'target': null})
            }
        }

        function addNetCallback(id){
            var pos = cabnet.kvHover.data('pos')
            var src = ' src="' + cabnet.kvHover.data('src') + '"'
            var sex = ' sex="' + cabnet.kvHover.data('sex') + '"'
            var tag = ' tag="' + cabnet.kvHover.data('tag') + '"'
            var url = ' url="' + cabnet.kvHover.data('url') + '"'
            var place = ' place="' + cabnet.kvHover.data('place') + '"'
            var price = ' price="' + cabnet.kvHover.data('price') + '"'
            var ids = ' id="' + id + '"'
            var img = '<img' + src + sex + tag + url + place + price + ids + ' />'
            $(pos).find('ul').prepend('<li>' + img + '</li>')     // netSlide添加图片
            cabnet.net.trigger('add', id)                         // 触发netSlide的被添加自定义事件
        }

        function delNetCallback(){
            var id = cabnet.hoverBox.data('id')
            cabnet.hoverBox.fadeOut()
            cabnet.netSlide.find('#' + id).parent('li').fadeOut('normal', function(){
                $(this).remove()
                cabnet.net.trigger('del', id)                       // 触发netSlide的被删除自定义事件
            })
        }
    }($))

    /* ========= index-bin && mini-cate ========= */

    !(function($){

        var index = {                                                // 首页变量
            box: $('#div_main'),
            bin : $('#div_index-bin'),
            btn : $('a.index-btn'),
            binIsOpen: false,
            wea : $('div.index-wea'),
            weaArea:$('#divArea'),
            bar : $('div.index-bar'),
            p0 : $('li.index-p-0'),
            gender: $('#ulgender'),
            suit: $('#div_mini-gender'),
            tips: $('div.mini-gender-tips'),
            place: $('#ul_index-bar-place'),
            week: $('#ulweek'),
            singleSlide: $('div.index-single'),
            suitSlide: $('.index-suit'),
            suitIsOpen: false,
            babyMask: $('div.mini-place-mask'),
            babyUl: $('div.mini-baby-ul'),

            closeBin: function(){
                index.bin.add(index.bar).animate({'top': '-507px'}, 600, function(){
                    index.binIsOpen = false
                })
                index.box.animate({'height' : 0}, 600)

                index.bin.trigger('binClose')
            },
            openBin: function(){
                index.bin.add(index.bar).animate({'top': 0}, 600, function(){
                    index.binIsOpen = true
                })
                index.box.animate({'height': 507}, 600)
                index.bin.trigger('binOpen')
            },
            togClass: function(ele, cls){
                ele.addClass(cls).siblings('.' + cls).removeClass(cls)
            }
        }

//        index.bin.prepend('<p class="index-final-tip">注：衣物实际库存及价格请参照优衣库官方网站</p>')
        index.genderLi = index.gender.find('li')
        index.all = index.genderLi.eq(0)

        index.bin.on('binOpen', function(){                       // 下拉框展开时一切归位
            index.togClass(index.p0, 'select')

            index.togClass(index.all, 'select')

            index.suit.removeClass('select')

            if(index.suitIsOpen){
                index.suitSlide.trigger('suitClose')
            }

            index.btn.css({
                'background-position': '0 0'
            })

            index.babyMask.hide()

        })
        index.bin.on('binClose', function(){                      // 下拉框收缩

            index.btn.css({
                'background-position': '0 -18px'
            })

        })

        index.suitSlide.on('suitOpen', function(){                // 显示套装
            //kimi
            $.weather.set = 1;//是否选中套装标记
            //kimi
            index.suitSlide.show().prev().hide()
            index.suitIsOpen = true
            getgoods($.weather.occasion,$.weather.sex,$.weather.set)
        }).on('suitClose', function(){
            //kimi
            $.weather.set = 0;
            //kimi
            index.suitSlide.hide().prev().show()
            index.suitIsOpen = false
            getgoods($.weather.occasion,$.weather.sex,$.weather.set)
        })
        //kimi
        index.place.on('click', 'li', function(){                 // 首页场合切换
            if($(this).attr('id') != 0 && ($.weather.sex||0) == 0){
                index.gender.children().removeClass('select')
                $('.mini-gender-women').parent().addClass('select');
                $.weather.sex = $('.mini-gender-women').attr('id')
            }
            if(index.suitIsOpen) {
                index.suit.removeClass('select')

                index.suitSlide.trigger('suitClose')
            }
            var cid = $(this).attr('id');
            $.weather.set = 0;
            if(cid==0){
                $.weather.occasion = 0;//场合
            }else{
                $.weather.occasion = cid;//场合
            }
            var that = $(this)
            index.togClass(that, 'select')
            that.is('li.ch_jujia') && index.tips.trigger('shown')
            getgoods(cid,$.weather.sex,$.weather.set)

        })

        //kimi

        $('a.index-btn').on('click', function(){
            if(index.binIsOpen){
                index.closeBin()
                $.weather.occasion = 0
                $.weather.set = 0
                $.weather.sex = 0
                getgoods(0,0,0);
            } else {
                index.openBin()
            }
        })

        index.tips.on('click', function(){
            index.tips.fadeOut()
        }).on('shown', function(){
            index.tips.show()
            setTimeout(function(){
                index.tips.trigger('click')
            }, 3000)
        })

        index.weaArea.on('mouseenter',function(){
            if(!index.binIsOpen){
                index.openBin();
            }
        });

        $.uniqlo.index = index

        var cate = {
            cate : $('div.mini-cate'),
            ps   : $('div.mini-cate-ps'),
            place: $('div.mini-cate-place'),
            placeUl: $('ul.mini-place-ul'),
            placeAll: $('a.mini-p-0'),
            style: $('div.mini-cate-style'),
            styleUl: $('ul.mini-style-ul'),
            styleAll: $('a.mini-s-0'),
            design:$('div.mini-design'),
            designAll: $('a.mini-design-all'),
            designMore:$('a.mini-design-more'),

            placeArr: {
                'All': {
                    '商务': 1, '旅游': 2, '运动': 3, '居家': 4
                },
                'WOMEN': {
                    '商务': 1, '旅游': 2, '运动': 3, '居家': 4, '逛街': 5, '约会': 6
                },
                'MEN': {
                    '商务': 1, '旅游': 2, '运动': 3, '居家': 4, '逛街': 5, '约会': 6
                },
                'KIDS BABY': {
                    '上学': 1, '旅游': 2, '运动': 3, '居家': 4, '逛街': 5, '玩乐': 6
                }
            },
            styleArr:{
                'All': {
                    '休闲' : 6, '复古' : 7, '英伦' : 8, '学院' : 9
                },
                'WOMEN': {
                    '休闲' : 6, '可爱' : 1, '淑女' : 2, '森女' : 3, '酷': 4, '成熟' : 5, '复古' : 7, '英伦' : 8, '学院' : 9, '中性' : 10
                },
                'MEN': {
                    '休闲' : 6, '潮' : 11, '斯文' : 12, '自然' : 13, '酷' : 14, '成熟' : 15, '复古' : 17, '英伦' : 18, '学院' : 19, '中性' : 20
                },
                'KIDS BABY': {
                    '休闲' : 6, '可爱' : 21, '淑女' : 22, '潮' : 23, '酷' : 24, '复古' : 26, '英伦' : 27, '学院' : 28
                }
            }
        }

        cate.designMore.on('click', function(){                   // 更多款式切换
            $(this).toggleClass('mini-design-less')
                .parent().prev().toggleClass('mini-design-auto')
        })

        cate.designAll.on('click', function(){                    // 所有款式
            cate.design.find('.mini-design-checked').removeClass('mini-design-checked')
            cate.designAll.addClass('mini-design-checked')
            getRandPro()
        })

        cate.design.on('click', 'li a', function(){               // 其他任何款式
            cate.designAll.removeClass('mini-design-checked')
            $(this).toggleClass('mini-design-checked')
            getRandPro()
        })

        cate.ps.on('click', 'li', function(){                     // 穿衣场合和穿衣风格
            if(index.suitIsOpen) {
                index.suit.removeClass('select')

                index.suitSlide.trigger('suitClose')
            }

            var that = $(this)
            index.togClass(that, 'mini-cate-checked')
            that.parent().prev().removeClass('mini-cate-checked')
            if(that.is('li.mini-p-4')){
                index.tips.trigger('shown')
            }
            getRandPro()

        }).on('click', 'a.mini-cate-more', function(e){           // 下拉三角显示更多

            $(this).hide().closest('ul').css('height', 'auto').find('a.mini-cate-less').show()
            e.stopPropagation()

        }).on('click', 'a.mini-cate-less', function(e){           // 收起三角

            cate.ps.trigger('cateUlHide', this)
            e.stopPropagation()

        }).on('click', 'a.mini-p-0', function(){                  // 场合全部按钮
            if(index.suitIsOpen) {
                index.suit.removeClass('select')

                index.suitSlide.trigger('suitClose')
            }

            $(this).addClass('mini-cate-checked')
            cate.place.find('li.mini-cate-checked').removeClass('mini-cate-checked')
            getRandPro()

        }).on('click', 'a.mini-s-0', function(){                  // 风格全部按钮
            if(index.suitIsOpen) {
                index.suit.removeClass('select')

                index.suitSlide.trigger('suitClose')
            }

            $(this).addClass('mini-cate-checked')
            cate.style.find('li.mini-cate-checked').removeClass('mini-cate-checked')
            getRandPro()

        }).on('cateUlHide', function(e, ele){                     // 收起design的UL

            $(ele).hide().closest('ul').css('height', 77).find('a.mini-cate-more').show()

        })

        index.gender.on('click', 'a', function(){                  // 内页性别切换
            //kimi
            var sid = $(this).attr('id');
            if(sid==0){
                $.weather.sex = 0;

                if(index.suitIsOpen) {
                    index.suit.removeClass('select')
                    index.suitSlide.trigger('suitClose')
                }

            }else{
                $.weather.sex = sid;//性别
            }
            //kimi
            var that = $(this)
            var parent = that.parent()
            index.togClass(parent, 'select')

            index.gender.trigger('genderChange', [that.text(), that.data('key')])
            cate.designAll.trigger('click')
//            var kids = that.parents('div.index-bar').find('li.ch_shangxue')

            if(that.data('page')){                                   // 首页的kids
                $("#__15").show();
                $("#3_9").hide();
//                kids.show().prev().hide()
            } else {
                $("#3_9").show();
                $("#__15").hide();
//                kids.hide().prev().show()
            }
            //kimi
            getgoods($.weather.occasion,sid,$.weather.set);
            //kimi
        }).on('genderChange', function(e, key, keys){
            var place = cate.placeArr[key]
            var style = cate.styleArr[key]

            if(keys == 'baby'){
                index.babyMask.show()
            } else {
                index.babyMask.hide()
            }

            setUl(place, 'place', 'p')
            setUl(style, 'style', 's')

            cate.ps.find('a.mini-cate-less').each(function(){
                cate.ps.trigger('cateUlHide', this)
            })

            // reset index-ps
            if(!index.suitIsOpen){
                cate.place.add(cate.style).find('li.mini-cate-checked').removeClass('mini-cate-checked')
                cate.placeAll.add(cate.styleAll).addClass('mini-cate-checked')

                // index.place.find('li').first().trigger('click')
            }

            var indexP1IsChecked = index.place.find('li.ch_shangwu:visible').is('.select')

            if(index.suitIsOpen){
                if(indexP1IsChecked){
                    var indexP1 = index.place.find('li.ch_shangwu').addClass('select')

                    if($('a.mini-gender-kids').parent('li').hasClass('select'))
                        $.weather.occasion = indexP1.get(1).id
                    else
                        $.weather.occasion = indexP1.get(0).id
                }
            } else {
                index.place.find('li').first().trigger('click')
            }

        })

        index.suit.on('click', 'a', function(){

            index.tips.trigger('click')
            index.suit.toggleClass('select')

            if(index.suitIsOpen){
                index.suitSlide.trigger('suitClose')
                // resetGender()
            } else {
                if(!($.weather.sex||0)){
                    index.gender.children().removeClass('select')
                    $('.mini-gender-women').parent().addClass('select');
                    $.weather.sex = $('.mini-gender-women').attr('id')
                }
                // if($.weather.occasion||0){
                //  $.weather.occasion = index.place.children().removeClass('index-p-checked').first().addClass('index-p-checked').attr('id');
                // }

                index.suitSlide.trigger('suitOpen')
                // resetGender()
                // onSuitOpen()
            }
        })

        index.netEmpty = index.bin.find('a.mini-net-empty')       // 搜索結果为空红框
        index.netEmpty.on('click', resetGender)

        function resetGender(){
            index.gender.find('a').first().trigger('click')
        }

        // function onSuitOpen(){
        //   index.togClass(cate.placeUl.find('li.mini-p-4'), 'mini-cate-checked')
        //   index.togClass(cate.styleUl.find('li.mini-s-6'), 'mini-cate-checked')
        //   cate.placeAll.add(cate.styleAll).removeClass('mini-cate-checked')

        //   index.togClass(index.place.find('li.index-p-4'), 'index-p-checked')
        // }

        function setUl(item, str, c){
            if(!item) return
            var html = ''
            for(var i in item){
                html += '<li class="mini-' + c + '-' + item[i] + '">' + i + '</li>'
            }
            cate[str + 'Ul'].html(html)
            var li = cate[str + 'Ul'].find('li')
            if(li.length > 4){
                li.eq(3).append('<a href="javascript:;" class="mini-cate-more mini-btn">more</a>')
                li.last().append('<a href="javascript:;" class="mini-cate-less mini-btn">less</a>')
            }

        }

        function getRandPro(){                                    // for demo
            if(index.suitIsOpen){
                setImg(index.suitSlide.find('img'))
            } else {
                index.singleSlide.find('div.mini-kv-slide').each(function(){
                    setImg($(this).find('img'))
                })
            }
        }

        function setImg(img){                                     // for demo
            var temp
            for(var i = 0, len = img.length; i < len; i ++){
                temp = img.eq(i).attr('src')
                img.eq(i).attr('src', img.eq(i + 1).attr('src'))
                img.eq(i + 1).attr('src', temp)
            }
        }

    }($))

    /* =============== mini-aside =============== */

    !(function($){
        $('.mini-aside').on('click', 'li', function(){
            var target = $(this).data('nav')
            uniqlo.scrollTo( target === 'top' ? 0 : $('.' + target).offset().top)
        })
    }($))

    /* ============ login && logout ============ */

    !(function($){
        var login = $('.mini-login')
        var mask = login.prev()
        $('.mini-login-btn').click(function(){
            mask.add(login).hide()
        })

        $('.mini-logout').click(function(){
            mask.add(login).show()
        })
    }($))

    /* ============ mini-bot-select ============ */

    !(function($){
        var bot = {
            choose : $('#mini-bot-choose'),
            form1 : $('#mini-bot-form1'),
            form2 : $('#mini-bot-form2'),
            input : $('input.mini-bot-input'),
            inputBin : $('div.mini-bot-sel')
        }

        bot.choose.on('click', function(){
            bot.form1.toggle()
        })

        bot.form1.on('click', 'a.mini-bot-close', function(){
            bot.form1.hide()
        }).submit(false)

        bot.form2.on('click', 'input.mini-bot-input', function(){
            bot.inputBin.toggle()
        }).on('click', 'a', function(){
            bot.inputBin.hide()
            bot.input.val(this.innerText)
        }).on('mouseleave', function(){
            bot.inputBin.hide()
        })
    }($))

})