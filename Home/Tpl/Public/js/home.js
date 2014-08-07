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

    // ͼƬ�л����
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


    /* ===================== ��ҳҵ��ʼ ===================== */

    var uniqlo = {                                           // uniqloȫ�ֶ���

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

    // ����ȫ�ֶ����ⲿʹ��if need
    $BIGO.uniqlo = uniqlo

    /* ============ cabnet����Ч�� ============= */

    !(function($BIGO){

        var cabnet = {                                         // ������������
            list      : [],                                      // ��������ӵ�ͼƬid
            hoverBox  : $BIGO('.mini-net-content'),                 // ͼƬ������
            net       : $BIGO('.mini-net'),                       // �Ҳ�net��
            netConfirm: $BIGO('.mini-net-confirm'),               // ɾ��ȷ�Ͽ�
            netSlide  : $BIGO('.mini-net-slide'),                 // ����ͼƬ�л���
            netIsEmpty: true,                                    // netĬ��Ϊ��
            netChoose : $BIGO('a.mini-net-choose'),                  // ��ѡ�·���ť
            cab       : $BIGO('div.mini-cab'),                       // ����cab��
            cabSlide  : $BIGO('div.mini-cab-slide'),                 // cab-slide��
            cabTips   : $BIGO('div.mini-cab-tips'),                  // cab��ʾ��
            cabBuy    : $BIGO('a.mini-cab-buy'),                     // cab����ť
            cabClear  : $BIGO('a.mini-cab-clear'),                   // cab��հ�ť
            cabPrev   : $BIGO('a.mini-cab-prev'),                    // cab-prev��ť
            cabNext   : $BIGO('a.mini-cab-next'),                    // cab-next��ť
            cabChoose : $BIGO('form.mini-cab-choose'),               // cab-choose��
            buyIsShow : false,                                   // cab-chooseĬ������
            cabEmpty  : $BIGO('a.mini-cab-empty'),                   // cab-empty��
            netLike   : $BIGO('a.mini-net-like'),                    // net-like��ť
            netHad    : $BIGO('a.mini-net-had')                     // net-had��ť
        }
        cabnet.netEmpty = cabnet.net.find('a.mini-net-empty')  // netSlide��ʾ��

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

        /* == net���� == */

        cabnet.net.on('add', function(e, id){                  // ע��netSlide�ı�����Զ����¼�
            if(cabnet.netIsEmpty){
                cabnet.netEmpty.addClass('mini-net-scale')         // ����netΪ����ʾ��
            }
            cabnet.list.push(id)                                 // ����ͼƬid
            cabnet.netIsEmpty = false

            uniqlo.netSlider()                                   // ����net slider
        })

        cabnet.net.on('del', function(e, id){                  // ע��netSlide�ı�ɾ���Զ����¼�
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

            uniqlo.netSlider()                                   // ����net slider
        })

        cabnet.netSlide.on('mouseover', 'img', function(e){    // netSlideͼƬ����Ч��

            if(uniqlo.sliding) return

            var position = $BIGO(this).position()
            var thisSlide = $BIGO(e.delegateTarget)
            var pos = thisSlide.data('pos')
            var top = pos === '#cab-top' ? 56 : 322
            var restSlide = thisSlide.siblings('div.mini-net-slide')

            netHoverCallback.call(this, pos, restSlide)          // ͼƬ������callback�ﴦ��ϸ��

            cabnet.hoverBox.css({                                // ��ʾͼƬ������
                left: position.left + 26,
                top: position.top + top
            }).show()
        })

//        cabnet.hoverBox.on('mouseleave', function(){           // hoverBox����뿪������ȫ��
//
//            cabnet.hoverBox.hide()
//            cabnet.netConfirm.hide()
//            cabnet.netLike.add(cabnet.netHad).removeClass('mini-net-checked')
//
//        }).on('click', function(){                             // �������λ������������
//
//            addCabCallback.call(this)                            // ���������callback
//
//        }).on('click', 'a.mini-net-del', function(e){          // ���ɾ����ť����confirm
//
//            cabnet.netConfirm.show()
//            e.stopPropagation()
//
//        })

        cabnet.netConfirm.on('click', false)                   // confirm�������λ�ò�ð��
            .on('click', 'a.mini-net-yes', function(e){            // confirmȷ��ɾ��
                //kimi
                delgo(cabnet.hoverBox.data('id'))
                //kimi
                delNetCallback.call(this)                            // netSlide��ɾ���Ļص�
                e.stopPropagation()

            }).on('click', 'a.mini-net-no', function(e){           // confirmȡ��ɾ��

                cabnet.netConfirm.hide()
                e.stopPropagation()

            })

        cabnet.netLike.add(cabnet.netHad).on('click', function(e){

            $BIGO(this).toggleClass('mini-net-checked')              // 'ϲ��'��'������'�������л�
            e.stopPropagation()

        })

        cabnet.netEmpty.add(cabnet.cabEmpty).add(cabnet.netChoose).on('click', function(){
            uniqlo.scrollTo(742)
        })

        /* == cab���� == */

        cabnet.cab.ajax = cabnet.cab.sex = cabnet.cab.isEmpty = true // cabĬ��Ϊ�գ�Ĭ�϶�ȡ��̨
        cabnet.cab.pos = null                                  // ��¼cab����ӵ�����/����
        cabnet.cab.on('add', function(e, pos, id){             // ע��cabSlide�ı�����Զ����¼�
            var tag = cabnet.hoverBox.data('tag')
            var src = cabnet.hoverBox.data('src')

            cabnet.cabChoose.trigger('hidden')

            if(cabnet.cab.isEmpty){                              // ��һ�ε��net����/����ͼƬ
                cabnet.cab.isEmpty = false

                cabnet.cabTips.show()
                cabnet.cabEmpty.hide()
                cabnet.cabBuy.addClass('show')
                cabnet.cabClear.show()

                cabnet.cab.pos = pos                               // ��¼pos
                cabAjaxCallback(tag, id, src, pos)                 // ��һ�ζ�ȡ��̨ajax

            } else {                                             // �ڶ��ε��net����/����ͼƬ

                if(cabnet.cab.pos == pos && cabnet.cab.ajax){      // ������ͬ��������/����

                    cabAjaxCallback(tag, id, src, pos)

                } else {

                    if(cabnet.cab.ajax){
                        cabnet.cabPrev.hide()
                        cabnet.cabNext.hide()
                    }
                    cabnet.cab.ajax = false

                }
            }
        }).on('click', function(){                             // �������cab��ر�tips

            cabnet.cabTips.hide()

        })

        cabnet.cabBuy.on('click', function(){                  // ������򵯳�form

            if(!cabnet.buyIsShow){

                cabnet.cabChoose.trigger('shown')

            } else{

                var url = cabnet.cabChoose.find('input:checked').val()

                if(url == 'undefined') return alert('����û��ѡ�������·�')
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

            cabnet.cabBuy.text('��Ҫ����')

            cabnet.buyIsShow = false

        }).on('shown', function(){

            cabnet.cabChoose.show().find('input').first().trigger('click')

            cabnet.cabBuy.text('ǰ�����¿��콢��')

            cabnet.buyIsShow = true

        })

        cabnet.cabSlide.on('click', function(){

            cabnet.cabChoose.trigger('hidden')

        })

        cabnet.cabClear.on('click', function(){                // ���cab
            cabnet.cab.ajax = cabnet.cab.isEmpty = true          // ����cabĬ��״̬
            cabnet.cabSlide.find('ul').html('')
            cabnet.cabTips.hide()
            cabnet.cabBuy.removeClass('show')
            cabnet.cabClear.hide()
            cabnet.cabPrev.hide()
            cabnet.cabNext.hide()
            cabnet.cabChoose.hide()
            cabnet.cabEmpty.show()
        })

        /* == ��ҳkv���� == */

        cabnet.kvSlide = $BIGO('.mini-kv-slide')                // kv-slide��
        cabnet.kvHover = $BIGO('.product_inf')                // kv-hover��
        cabnet.kvContent = $BIGO('.inf_con'),
        cabnet.kvTimer = 0                                     // kv-hover���ӳ�
        cabnet.kvIsOpen = false                                // kv-hover״̬
        cabnet.kvHover.price = cabnet.kvHover.find('strong')   // kv-hover�۸�
        cabnet.kvHover.rest = cabnet.kvHover.find('span')      // kv-hover����
        cabnet.kvHover.title = cabnet.kvHover.find('a.ft14')   // kv-hover��Ʒ��

        cabnet.kvSlide.on('mousemove', 'img', function(e){     // kvSlideͼƬ����

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

            kvHoverCallback.call(this, pos, isIndexPage)         // ͼƬ������callback�ﴦ��ϸ��

            cabnet.kvTimer = setTimeout(function(){

                cabnet.kvHover.css({                               // ��ʾͼƬ������
                    left: position.left + 55,
                    top:  top
                }).show()
                cabnet.kvIsOpen = true

            }, 100)

        }).on('mouseout', function(){
            clearTimeout(cabnet.kvTimer)
        })

        cabnet.kvContent.on('mouseleave', function(){            // kvHover�Զ�����
            cabnet.kvHover.hide()
            cabnet.kvIsOpen = false
        }).on('click', 'a.mini-kv-add', function(){            // �������¹�
            var id = cabnet.kvHover.data('id')

            var target = this.getAttribute('target')
            if(target) return
        })

        function hideKvhover(){

            cabnet.kvHover.hide()
            cabnet.kvIsOpen = false
        }

        $BIGO("#ulweek,.dr_main_con_sub_nav,.mini-kv-slide,.dr_header").on("mouseenter",hideKvhover);
        /* == cab��net��kvϵ�лص����� == */
        function addCabCallback(){                             // ���������callback
            var id = cabnet.hoverBox.data('id')
            var pos = cabnet.hoverBox.data('pos')                // ��Ӷ�Ӧcab��ͼƬ
            var url = ' url="' + cabnet.hoverBox.data('url') + '"'
            var src = ' src="' + cabnet.hoverBox.data('src') + '"'
            var ids = ' id="' + id + '"'
            var sex = cabnet.hoverBox.data('sex')
            if(cabnet.cab.isEmpty){
                cabnet.cab.sex = sex
            } else if(cabnet.cab.sex !== sex && cabnet.cab.pos !== pos){
                return alert('�������������е������Ա𲻷�Ŷ')
            }

            $BIGO(pos).find('ul').html('<li><img ' + ids + src + url + ' /></li>')

            cabnet.cab.trigger('add', [pos, id])                 // ����cab�ı�����Զ����¼�
        }

        function netHoverCallback(pos, rest){                  // ͼƬ������callback

            var url = this.getAttribute('url')
            var tag = this.getAttribute('tag')
            cabnet.hoverBox
                .find('h3').text(tag + '���')
                .end().find('span').text(this.getAttribute('place') + 'װ')
                .end().find('img').attr('src', this.src)
                .end().find('a.mini-net-detail').attr('href', url)
                .end().find('strong').text(this.getAttribute('price'))

            cabnet.hoverBox.data({
                'pos': pos,                                        // ����thisSlideӳ�䵽cab��id
                'rest': rest,                                      // ����restSlide
                'id' : this.id,                                    // ����ͼƬid
                'src' : this.src,                                  // ����ͼƬsrc
                'tag' : tag,
                'sex' : this.getAttribute('sex'),
                'url' : url                                        // ����ͼƬurl
            })
        }

        function cabAjaxCallback(tag, id, src, pos){           // �����ajaxCallbackֻ�ǲ�����
            var ul = cabnet.hoverBox.data('rest').find('ul').html() // ��ɾ��
            $BIGO(pos).siblings('.mini-cab-slide').find('ul').html(ul)  // ��ɾ��

            console.log(tag)                                     // ͼƬ��ǩ
            console.log(id)                                      // ͼƬid
            console.log(src)                                     // ͼƬsrc

            // !!��ȡ���ݺ�ִ��slider�л�����Ҫ
            uniqlo.cabSlider()
        }

        function kvHoverCallback(pos, isIndexPage){            // ����pos����������/��������

            var url = this.getAttribute('url')
            var price = this.getAttribute('price')
            //�۸�
            $BIGO('.price').html('<span>��</span>' + price);
            //����
            $BIGO('.inf_xx p').text(this.getAttribute('alt'));
            //�鿴��ϸ
            $BIGO('.inf_con a').attr('href',url);
            //ʣ����
            $BIGO('.stock span').text(this.getAttribute('rest'));
            cabnet.kvHover.data({                                // ����ͼƬsrc��id����Ϣ
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
            $BIGO(pos).find('ul').prepend('<li>' + img + '</li>')     // netSlide���ͼƬ
            cabnet.net.trigger('add', id)                         // ����netSlide�ı�����Զ����¼�
        }

        function delNetCallback(){
            var id = cabnet.hoverBox.data('id')
            cabnet.hoverBox.fadeOut()
            cabnet.netSlide.find('#' + id).parent('li').fadeOut('normal', function(){
                $BIGO(this).remove()
                cabnet.net.trigger('del', id)                       // ����netSlide�ı�ɾ���Զ����¼�
            })
        }
    }($BIGO))

    /* ========= index-bin && mini-cate ========= */

    !(function($BIGO){

        var index = {                                                // ��ҳ����
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

//        index.bin.prepend('<p class="index-final-tip">ע������ʵ�ʿ�漰�۸���������¿�ٷ���վ</p>')
        index.genderLi = index.gender.find('li')
        index.all = index.genderLi.eq(0)

        index.bin.on('binOpen', function(){                       // ������չ��ʱһ�й�λ
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
        index.bin.on('binClose', function(){                      // ����������

            index.btn.css({
                'background-position': '0 -18px'
            })

        })

        index.suitSlide.on('suitOpen', function(){                // ��ʾ��װ
            //kimi
            $BIGO.weather.set = 1;//�Ƿ�ѡ����װ���
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
                    '����': 1, '����': 2, '�˶�': 3, '�Ӽ�': 4
                },
                'WOMEN': {
                    '����': 1, '����': 2, '�˶�': 3, '�Ӽ�': 4, '���': 5, 'Լ��': 6
                },
                'MEN': {
                    '����': 1, '����': 2, '�˶�': 3, '�Ӽ�': 4, '���': 5, 'Լ��': 6
                },
                'KIDS BABY': {
                    '��ѧ': 1, '����': 2, '�˶�': 3, '�Ӽ�': 4, '���': 5, '����': 6
                }
            },
            styleArr:{
                'All': {
                    '����' : 6, '����' : 7, 'Ӣ��' : 8, 'ѧԺ' : 9
                },
                'WOMEN': {
                    '����' : 6, '�ɰ�' : 1, '��Ů' : 2, 'ɭŮ' : 3, '��': 4, '����' : 5, '����' : 7, 'Ӣ��' : 8, 'ѧԺ' : 9, '����' : 10
                },
                'MEN': {
                    '����' : 6, '��' : 11, '˹��' : 12, '��Ȼ' : 13, '��' : 14, '����' : 15, '����' : 17, 'Ӣ��' : 18, 'ѧԺ' : 19, '����' : 20
                },
                'KIDS BABY': {
                    '����' : 6, '�ɰ�' : 21, '��Ů' : 22, '��' : 23, '��' : 24, '����' : 26, 'Ӣ��' : 27, 'ѧԺ' : 28
                }
            }
        }

        cate.designMore.on('click', function(){                   // �����ʽ�л�
            $BIGO(this).toggleClass('mini-design-less')
                .parent().prev().toggleClass('mini-design-auto')
        })

        cate.designAll.on('click', function(){                    // ���п�ʽ
            cate.design.find('.mini-design-checked').removeClass('mini-design-checked')
            cate.designAll.addClass('mini-design-checked')
            getRandPro()
        })

        cate.design.on('click', 'li a', function(){               // �����κο�ʽ
            cate.designAll.removeClass('mini-design-checked')
            $BIGO(this).toggleClass('mini-design-checked')
            getRandPro()
        })

        cate.ps.on('click', 'li', function(){                     // ���³��Ϻʹ��·��
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

        }).on('click', 'a.mini-cate-more', function(e){           // ����������ʾ����

            $BIGO(this).hide().closest('ul').css('height', 'auto').find('a.mini-cate-less').show()
            e.stopPropagation()

        }).on('click', 'a.mini-cate-less', function(e){           // ��������

            cate.ps.trigger('cateUlHide', this)
            e.stopPropagation()

        }).on('click', 'a.mini-p-0', function(){                  // ����ȫ����ť
            if(index.suitIsOpen) {
                index.suit.removeClass('select')

                index.suitSlide.trigger('suitClose')
            }

            $BIGO(this).addClass('mini-cate-checked')
            cate.place.find('li.mini-cate-checked').removeClass('mini-cate-checked')
            getRandPro()

        }).on('click', 'a.mini-s-0', function(){                  // ���ȫ����ť
            if(index.suitIsOpen) {
                index.suit.removeClass('select')

                index.suitSlide.trigger('suitClose')
            }

            $BIGO(this).addClass('mini-cate-checked')
            cate.style.find('li.mini-cate-checked').removeClass('mini-cate-checked')
            getRandPro()

        }).on('cateUlHide', function(e, ele){                     // ����design��UL

            $BIGO(ele).hide().closest('ul').css('height', 77).find('a.mini-cate-more').show()

        })

        index.netEmpty = index.bin.find('a.mini-net-empty')       // ����?��Ϊ�պ��
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