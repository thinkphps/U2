/**
 * Created by jack on 14-3-26.
 */
$BIGO(function($BIGO){
    $BIGO.fn.lazyload = function(){
        return this.each(function(){
            var that = $BIGO(this),
                parent = that.parent(),
                index = parent.index(),
                len = parent.parent('ul').find('li').length;

            if(!this.src && (index < 5 || index >= len - 1)){
                this.src = that.data('original')
            }
        })
    }

    // 图片切换插件
    $BIGO.fn.nioSlider = function(options){
        return this.each(function(){
            var that = $BIGO(this)
            var ul = that.find('ul')
            var prev = that.siblings(options.prev)
            var next = that.siblings(options.next)
            options = $BIGO.extend({
                width : 180,
                speed : 600,
                min : 2,
                callback: $BIGO.noop
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

    // $BIGO easing plugin
    $BIGO.easing['jswing'] = $BIGO.easing['swing']

    $BIGO.extend( $BIGO.easing,
        {
            def: 'easeOutExpo',
            swing: function (x, t, b, c, d) {
                return $BIGO.easing[$BIGO.easing.def](x, t, b, c, d)
            },
            easeOutExpo: function (x, t, b, c, d) {
                return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b
            }
        })


    /* ===================== 内页业务开始 ===================== */

    var uniqlo = {                                           // uniqlo全局对象

        sliding : false,
        cabContainer : $BIGO('.mini-cab-container'),
        netContainer : $BIGO('.mini-net-container'),
        kvContainer : $BIGO('.mini-kv-container'),
        indexContainer: $BIGO('.index-slide-container'),
        body : $BIGO('html, body'),
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
//            $BIGO('.mini-kv-prev,.mini-kv-next')[($BIGO.weather.sex||0) == 0?'hide':'show']()
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
    $BIGO.uniqlo = uniqlo

    /* ============ cabnet交互效果 ============= */

    !(function($BIGO){

        var cabnet = {                                         // 集中声明变量
            list      : [],                                      // 保存已添加的图片id
            hoverBox  : $BIGO('.mini-net-content'),                 // 图片悬浮框
            net       : $BIGO('.mini-net'),                       // 右侧net框
            netConfirm: $BIGO('.mini-net-confirm'),               // 删除确认框
            netSlide  : $BIGO('.mini-net-slide'),                 // 两个图片切换框
            netIsEmpty: true,                                    // net默认为空
            netChoose : $BIGO('a.mini-net-choose'),                  // 挑选衣服按钮
            cab       : $BIGO('div.mini-cab'),                       // 左侧的cab框
            cabSlide  : $BIGO('div.mini-cab-slide'),                 // cab-slide框
            cabTips   : $BIGO('div.mini-cab-tips'),                  // cab提示框
            cabBuy    : $BIGO('a.mini-cab-buy'),                     // cab购买按钮
            cabClear  : $BIGO('a.mini-cab-clear'),                   // cab清空按钮
            cabPrev   : $BIGO('a.mini-cab-prev'),                    // cab-prev按钮
            cabNext   : $BIGO('a.mini-cab-next'),                    // cab-next按钮
            cabChoose : $BIGO('form.mini-cab-choose'),               // cab-choose框
            buyIsShow : false,                                   // cab-choose默认隐藏
            cabEmpty  : $BIGO('a.mini-cab-empty'),                   // cab-empty框
            netLike   : $BIGO('a.mini-net-like'),                    // net-like按钮
            netHad    : $BIGO('a.mini-net-had')                     // net-had按钮
        }
        cabnet.netEmpty = cabnet.net.find('a.mini-net-empty')  // netSlide提示框

        $BIGO("#w_sq").on("click",function(){
            $BIGO("#div_main").hide();
            $BIGO(this).hide();
            $BIGO("#w_zk").show();
        });

        $BIGO("#w_zk").on("click",function(){
            $BIGO("#div_main").show();
            $BIGO(this).hide();
            $BIGO("#w_sq").show();
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

            var position = $BIGO(this).position()
            var thisSlide = $BIGO(e.delegateTarget)
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

            $BIGO(this).toggleClass('mini-net-checked')              // '喜欢'与'已买入'的类名切换
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
            this.value = $BIGO(pos).find('img').first().attr('url')

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

        cabnet.kvSlide = $BIGO('.mini-kv-slide')                // kv-slide框
        cabnet.kvHover = $BIGO('.product_inf')                // kv-hover框
        cabnet.kvContent = $BIGO('.inf_con'),
        cabnet.kvTimer = 0                                     // kv-hover框延迟
        cabnet.kvIsOpen = false                                // kv-hover状态
        cabnet.kvHover.price = cabnet.kvHover.find('strong')   // kv-hover价格
        cabnet.kvHover.rest = cabnet.kvHover.find('span')      // kv-hover余量
        cabnet.kvHover.title = cabnet.kvHover.find('a.ft14')   // kv-hover产品名

        cabnet.kvSlide.on('mousemove', 'img', function(e){     // kvSlide图片悬浮

            var isIndexPage = 'index';

            if(uniqlo.sliding) return

            var position = $BIGO(this).position()
            var thisSlide = $BIGO(e.delegateTarget)
            var pos = thisSlide.data('pos')
            var addBtn = cabnet.kvHover.find('a.mini-kv-add')
            var top = 186;
//            if(pos){
//                top = pos == '#net-top' ? 83 : 280
//            }

            kvHoverCallback.call(this, pos, isIndexPage)         // 图片悬浮的callback里处理细节

            cabnet.kvTimer = setTimeout(function(){

                cabnet.kvHover.css({                               // 显示图片悬浮框
                    left: position.left + 101,
                    top:  top
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

        function hideKvhover(){

            cabnet.kvHover.hide()
            cabnet.kvIsOpen = false
        }

        $BIGO("#ulweek,.dr_main_con_sub_nav,.mini-kv-slide,.dr_header").on("mouseenter",hideKvhover);
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

            $BIGO(pos).find('ul').html('<li><img ' + ids + src + url + ' /></li>')

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
            $BIGO(pos).siblings('.mini-cab-slide').find('ul').html(ul)  // 待删除

            console.log(tag)                                     // 图片标签
            console.log(id)                                      // 图片id
            console.log(src)                                     // 图片src

            // !!读取数据后执行slider切换，必要
            uniqlo.cabSlider()
        }

        function kvHoverCallback(pos, isIndexPage){            // 参数pos保存了上衣/下衣类型

            var url = this.getAttribute('url')
            var price = this.getAttribute('price')
            //价格
            $BIGO('.price').html('<span>￥</span>' + price);
            //标题
            $BIGO('.inf_xx p').text(this.getAttribute('alt'));
            //查看详细
            $BIGO('.inf_con a').attr('href',url);
            //剩余库存
            $BIGO('.stock span').text(this.getAttribute('rest'));
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
            $BIGO(pos).find('ul').prepend('<li>' + img + '</li>')     // netSlide添加图片
            cabnet.net.trigger('add', id)                         // 触发netSlide的被添加自定义事件
        }

        function delNetCallback(){
            var id = cabnet.hoverBox.data('id')
            cabnet.hoverBox.fadeOut()
            cabnet.netSlide.find('#' + id).parent('li').fadeOut('normal', function(){
                $BIGO(this).remove()
                cabnet.net.trigger('del', id)                       // 触发netSlide的被删除自定义事件
            })
        }
    }($BIGO))

    /* ========= index-bin && mini-cate ========= */

    !(function($BIGO){

        var index = {                                                // 首页变量
            box: $BIGO('#div_main'),
            bin : $BIGO('#div_index-bin'),
            btn : $BIGO('a.index-btn'),
            binIsOpen: false,
            wea : $BIGO('div.index-wea'),
            weaArea:$BIGO('#divArea'),
            bar : $BIGO('div.index-bar'),
            p0 : $BIGO('li.index-p-0'),
            gender: $BIGO('#ulgender'),
            suit: $BIGO('#div_mini-gender'),
            tips: $BIGO('div.mini-gender-tips'),
            place: $BIGO('#ul_index-bar-place'),
            week: $BIGO('#ulweek'),
            singleSlide: $BIGO('div.index-single'),
            suitSlide: $BIGO('.index-suit'),
            suitIsOpen: false,
            babyMask: $BIGO('div.mini-place-mask'),
            babyUl: $BIGO('div.mini-baby-ul'),

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
            $BIGO.weather.set = 1;//是否选中套装标记
            //kimi
            index.suitSlide.show().prev().hide()
            index.suitIsOpen = true
            getgoods($BIGO.weather.occasion,$BIGO.weather.sex,$BIGO.weather.set)
        }).on('suitClose', function(){
            //kimi
            $BIGO.weather.set = 0;
            //kimi
            index.suitSlide.hide().prev().show()
            index.suitIsOpen = false
            getgoods($BIGO.weather.occasion,$BIGO.weather.sex,$BIGO.weather.set)
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

        $BIGO.uniqlo.index = index

        var cate = {
            cate : $BIGO('div.mini-cate'),
            ps   : $BIGO('div.mini-cate-ps'),
            place: $BIGO('div.mini-cate-place'),
            placeUl: $BIGO('ul.mini-place-ul'),
            placeAll: $BIGO('a.mini-p-0'),
            style: $BIGO('div.mini-cate-style'),
            styleUl: $BIGO('ul.mini-style-ul'),
            styleAll: $BIGO('a.mini-s-0'),
            design:$BIGO('div.mini-design'),
            designAll: $BIGO('a.mini-design-all'),
            designMore:$BIGO('a.mini-design-more'),

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
            $BIGO(this).toggleClass('mini-design-less')
                .parent().prev().toggleClass('mini-design-auto')
        })

        cate.designAll.on('click', function(){                    // 所有款式
            cate.design.find('.mini-design-checked').removeClass('mini-design-checked')
            cate.designAll.addClass('mini-design-checked')
            getRandPro()
        })

        cate.design.on('click', 'li a', function(){               // 其他任何款式
            cate.designAll.removeClass('mini-design-checked')
            $BIGO(this).toggleClass('mini-design-checked')
            getRandPro()
        })

        cate.ps.on('click', 'li', function(){                     // 穿衣场合和穿衣风格
            if(index.suitIsOpen) {
                index.suit.removeClass('select')

                index.suitSlide.trigger('suitClose')
            }

            var that = $BIGO(this)
            index.togClass(that, 'mini-cate-checked')
            that.parent().prev().removeClass('mini-cate-checked')
            if(that.is('li.mini-p-4')){
                index.tips.trigger('shown')
            }
            getRandPro()

        }).on('click', 'a.mini-cate-more', function(e){           // 下拉三角显示更多

            $BIGO(this).hide().closest('ul').css('height', 'auto').find('a.mini-cate-less').show()
            e.stopPropagation()

        }).on('click', 'a.mini-cate-less', function(e){           // 收起三角

            cate.ps.trigger('cateUlHide', this)
            e.stopPropagation()

        }).on('click', 'a.mini-p-0', function(){                  // 场合全部按钮
            if(index.suitIsOpen) {
                index.suit.removeClass('select')

                index.suitSlide.trigger('suitClose')
            }

            $BIGO(this).addClass('mini-cate-checked')
            cate.place.find('li.mini-cate-checked').removeClass('mini-cate-checked')
            getRandPro()

        }).on('click', 'a.mini-s-0', function(){                  // 风格全部按钮
            if(index.suitIsOpen) {
                index.suit.removeClass('select')

                index.suitSlide.trigger('suitClose')
            }

            $BIGO(this).addClass('mini-cate-checked')
            cate.style.find('li.mini-cate-checked').removeClass('mini-cate-checked')
            getRandPro()

        }).on('cateUlHide', function(e, ele){                     // 收起design的UL

            $BIGO(ele).hide().closest('ul').css('height', 77).find('a.mini-cate-more').show()

        })

        index.netEmpty = index.bin.find('a.mini-net-empty')       // 搜索?果为空红框
        index.netEmpty.on('click', resetGender)

        function resetGender(){
            index.gender.find('a').first().trigger('click')
        }
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
                    setImg($BIGO(this).find('img'))
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

    }($BIGO))

    /* =============== mini-aside =============== */

    !(function($BIGO){
        $BIGO('.mini-aside').on('click', 'li', function(){
            var target = $BIGO(this).data('nav')
            uniqlo.scrollTo( target === 'top' ? 0 : $BIGO('.' + target).offset().top)
        })
    }($BIGO))

    /* ============ login && logout ============ */

    !(function($BIGO){
        var login = $BIGO('.mini-login')
        var mask = login.prev()
        $BIGO('.mini-login-btn').click(function(){
            mask.add(login).hide()
        })

        $BIGO('.mini-logout').click(function(){
            mask.add(login).show()
        })
    }($BIGO))

    /* ============ mini-bot-select ============ */

    !(function($BIGO){
        var bot = {
            choose : $BIGO('#mini-bot-choose'),
            form1 : $BIGO('#mini-bot-form1'),
            form2 : $BIGO('#mini-bot-form2'),
            input : $BIGO('input.mini-bot-input'),
            inputBin : $BIGO('div.mini-bot-sel')
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
    }($BIGO))

})