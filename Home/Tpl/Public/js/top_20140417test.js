/**
 * UNIQLO L1 Scripts
 *
 * @author	katsuma@team-lab.com
 * @version	1.0
 * @require	jquery 1.6
 * @require	jquery easing 1.3
 */

/*
 * uniqlo utils
 */
$.extend({
    debug : (location.href.indexOf("file:///")==0),
    log: function(msg){if($.debug && window.console) console.log(msg);}
});

/*
 * image rollover plugin
 */
(function($){
    $.fn["rollover"] = function(options){
        var settings = $.extend({
            "suffix" : "_o"
        }, options);

        var _imgs = [];

        this.each(function(){
            var dsrc  = $(this).attr('src');
            var ftype = dsrc.substring(dsrc.lastIndexOf('.'), dsrc.length);
            var hsrc  = (dsrc.indexOf(settings.suffix+ftype)>0) ? dsrc:dsrc.replace(ftype, settings.suffix+ftype);
            // preload
            _imgs.push(new Image());
            _imgs[_imgs.length-1].src = hsrc;

            $(this)
                .attr({"dsrc": dsrc, "hsrc": hsrc})
                .mouseover(function(){$(this).attr("src", $(this).attr("hsrc"));})
                .mouseout( function(){$(this).attr("src", $(this).attr("dsrc"));});
        });
        return this;
    };
})(jQuery);

/*
 * image rollover toggle plugin
 * @require image rollover plugin
 */
(function($){
    $.fn["rolloverToggle"] = function(options){
        var settings = $.extend({
            "current": true,
            "suffix" : "_o"
        }, options);

        this.each(function(){
            if(settings.current){
                // default -> hover
                $(this).attr({
                    "dsrc": $(this).attr("hsrc"),
                    "src" : $(this).attr("hsrc")
                });
            }else{
                // hover -> default
                /*$(this).attr({
                 "dsrc": $(this).attr("hsrc").replace(settings.suffix,""),
                 "src" : $(this).attr("hsrc").replace(settings.suffix,"")
                 });*///4/17修改
            }
        });
        return this;
    };
})(jQuery);

/*
 * image loader with callback plugin
 */
(function($){
    $.fn["imageLoader"] = function(callback){
        var _length = $(this).length;
        var _loaded = function(){
            _length--;
            if(_length==0) callback.apply();
        };
        this.each(function(){
            $("<img>")
                .attr("src", $(this).attr("src"))
                .load(_loaded)
                .error(_loaded);
        });
        return this;
    };
})(jQuery);

/*
 * uniqlo header plugin
 * @require image rollover plugin
 */
(function($){
    $.fn["header"] = function(options){
        // settings
        var settings = $.extend({
            "imgClass" : ".imgover",
            "mainID"   : ".id_navHeader",
            "subClass" : "navCategory",
            "cartID"   : ".id_gnav_cart_trigger",
            "searchID" : ".id_searchFocus",
            "duration" : 200,
            "animate"  : false
        }, options);

        // main
        this.each(function(){
            // ヘッダ第二階層で遷移しないのはクリックころす
            $(this).find("a").focus(function(){
                $(this).blur();
            });
            $(this).find("ul.navCategory>li>a").click(function(){
                if($(this).attr("href")=="#") return false;
            });

            var _timer_header_toggle;
            $(".id_navHeader").hover(
                function(){
                    clearTimeout(_timer_header_toggle);
                    $(".id_header").stop().animate({height:100}, 400, "easeOutQuint", function(){
                        $(".id_header").css({"overflow":"visible"});
                    });
                },
                function(){
                });
            $(this).find(">.navArea").hover(
                function(){
                },
                function(){
                    _timer_header_toggle = setTimeout(function(){
                        $(".id_header").stop().delay(100).animate({height:50}, 400, "easeOutQuint", function(){
                            $(".id_header").css({"overflow":"hidden"});
                        });
                    }, 500);
                });


            // 第二階層の遅延クローズ用カレント表示保持
            $(".id_navHeader>li").mouseover(function(){
                $(".id_navHeader>li").removeClass("current");
                $(this).addClass("current");
            });


            // 検索フォーム
            $(this)
                .find(settings.searchID)
                .focus(function(){
                    $(this).parent().parent().addClass("inputFocus");
                })
                .blur(function(){
                    $(this).parent().parent().removeClass("inputFocus");
                });

            // カンパニー判定
            if($("body.id_company").length>0){
                $(".id_header_company")
                    .parent()
                    .addClass("focus")
                    .end()
                    .find(">img")
                    .rolloverToggle()
                    .end()
                    .siblings("ul")
                    .removeClass("navCategoryHidden");
            }


            // ３階層ドロップダウン
            $(settings.mainID).find("li").hover(
                function(){
                    if(!settings.animate && $(this).parent().hasClass( settings.mainID.replace('.', '') ) ){
                        $(this).find(">ul").show();
                    }else{
                        $(this).find(">ul:not(:animated)").slideDown(settings.duration);
                    }
                },
                function(){
                    // カレントカテゴリ第二階層は閉じない
                    //if($(this).hasClass("focus") && $(this).find(">ul").hasClass(settings.subClass)) return;
                    if($(this).find(">ul").hasClass(settings.subClass)) return;

                    if(!settings.animate && $(this).parent().hasClass( settings.mainID.replace('.', '') ) ){
                        $(this)
                            .find(".sub")
                            .hide()
                            .end()
                            .find(">ul")
                            .hide();
                    }else{
                        $(this).find(">ul").slideUp(settings.duration, function(){
                            // アニメーションのタイミング次第で第三階層が取り残されるので強制的に閉じる
                            $(this).find(".sub").hide();
                        });
                    }
                });


            // ショッピングカート
            if(settings.animate){
                $(settings.cartID).parent().hover(
                    function(){
                        $(this).find(">ul:not(:animated)").slideDown(settings.duration);
                    },
                    function(){
                        $(this).find(">ul").slideUp(settings.duration);
                    });
            }else{
                $(settings.cartID).parent().hover(
                    function(){
                        $(this).find(">ul").show();
                    },
                    function(){
                        $(this).find(">ul").hide();
                    });
            }


            // ロールオーバー変更
            $(this).find(settings.imgClass)
                .unbind("mouseover")
                .unbind("mouseout")
                .parent()
                .parent()
                .hover(
                function(){
                    var $img = $(this).find(">a>img");
                    $img.attr("src", $img.attr("hsrc"));
                },
                function(){
                    var $img = $(this).find(">a>img");
                    $img.attr("src", $img.attr("dsrc"));
                });
        });
        return this;
    };
})(jQuery);


/*
 * L1 banner plugin
 * @require image rollover toggle plugin
 * @require image loader with callback plugin
 */
(function($){
    $.fn["l1Banner"] = function(options){
        // settings
        var settings = $.extend({
            "animate"  : false,
            "interval" : 4000
        }, options);

        // アニメーション設定（false指定でie7,8はアニメーションOFF）
        if(!settings.animate && $.support.opacity){
            settings.animate = true;
        }

        // china width 950 fix
        var _content_width = $(".id_content").width();
        var _content_height_min = $(".id_content_banner_top .content_banner_inner").height();
        var _content_height_max = $(".id_content_banner_top").height();

        var _content_location_permalinks = ["#!men", "#!women", "#!kids", "#!baby", "#!company"];
        var _content_banner_category;
        var _content_banner_current = 0;
        // カテゴリフィルタの固定リンクハッシュをヘッダのAタグから抽出
        var _content_location_permalinks = [];
        $("#navHeader>li>a").each(function(){
            _content_location_permalinks.push("#"+$(this).attr("href").split("#")[1]);
        });

        var _timer_content_banner_loading;
        var _timer_content_banner_rotation;
        var _timer_content_banner_toggle;

        // カテゴリ選択初期化
        var _initialize = function(callback){
            // location.hash判定
            var c, l = location.hash.replace("#!","").toLowerCase();
            if($.inArray(location.hash, _content_location_permalinks)!=-1){
                _content_banner_category = ".id_content_banner_"+l;
                c = ".category_"+l;
                /*$(".id_header_"+l)
                 .addClass("selected")
                 .find(">img")
                 .rolloverToggle();*///4/17修改
            }else{
                _content_banner_category = ".id_content_banner_top";
                c = "";
                $(".id_top").addClass("selected");
                $(".id_content_banner_loading").hide();
            }

            // カレントバナー切替
            $(".id_content_banner>div")
                .filter(":not(.id_content_banner_loading)")
                .hide()
                .end()
                .filter(_content_banner_category)
                .show();

            // カレントコンテンツ切替
            if(c!=""){
                if(!settings.animate){
                    $(".id_content_blocks>.content_block_list>li:not("+c+")").hide();
                }else{
                    $(".id_content_blocks>.content_block_list>li:not("+c+")").slideUp(600, "easeOutQuint");
                }
            }

            // バナーが無限ループできるよう先頭要素のクローンを末尾に追加
            $(".id_content_banner>div").each(function(){
                var $imgs = $(this).find(".content_banner_item");
                if($imgs.length<=1) return;
                $imgs.eq(0).clone(true).appendTo($(this).find(".content_banner_inner"));
                $(this).find(".content_banner_inner").css("width", _content_width*($imgs.length+1));
            });

            // カレントバナー画像の読み込み完了したらローテーション開始
            if($.support.opacity){
                $(_content_banner_category+" img").imageLoader(function(){
                    $(".id_content_banner_loading").delay(400).fadeOut(400);
                    _timer_content_banner_rotation = setTimeout(_rotateBanner, settings.interval);
                    callback.apply();
                });
                // IE7,8は画像読み込み待ちしない
            }else{
                $(".id_content_banner_loading").delay(400).fadeOut(400);
                _timer_content_banner_rotation = setTimeout(_rotateBanner, settings.interval);
                callback.apply();
            }
        };

        // 自動ローテーション
        var _rotateBanner = function(){
            _timer_content_banner_rotation = null;
            _content_banner_current++;
            var $target = $(_content_banner_category + " .content_banner_inner");
            var l = $(_content_banner_category+" .content_banner_item").length-1;
            $target.stop().animate({left:-_content_width*_content_banner_current}, 750, "easeInOutQuart", function(){
                var c = parseInt($(this).css('left').replace('px')) / -_content_width;
                // 末尾までローテーションしたらコールバックで先頭に戻す
                if(c == l) $target.css({'left':0});
                _rotateStart();
            });
            if(_content_banner_current==l) _content_banner_current = 0;

            // サムネイル部のカレント表示を更新
            if(!settings.animate){
                $(_content_banner_category+">.content_banner_thumbs .content_banner_thumb")
                    .eq(_content_banner_current)
                    .find(".content_banner_thumb_current>img")
                    .css({top:0})
                    .end()
                    .siblings()
                    .find(".content_banner_thumb_current>img")
                    .css({top:38});
            }else{
                $(_content_banner_category+">.content_banner_thumbs .content_banner_thumb")
                    .eq(_content_banner_current)
                    .find(".content_banner_thumb_current>img")
                    .stop()
                    .delay(300)
                    .animate({top:0}, 350, "easeOutQuint")
                    .end()
                    .siblings()
                    .find(".content_banner_thumb_current>img")
                    .stop()
                    .delay(300)
                    .animate({top:38}, 350, "easeOutQuint");
            }
        };

        // バナーカテゴリ切替
        var _switchBanner = function(cat){
            $.log("category current: " + cat);
            _rotateStop();

            // サムネイルエリアを閉じる
            //$("#content_banner").stop().animate({height:470}, 400, "easeOutQuart");
            // サムネイルエリアをフェードアウト
            $(".content_banner_thumbs").stop().animate({opacity:100}, 300);

            // ローディング
            clearTimeout(_timer_content_banner_loading);
            _timer_content_banner_loading = setTimeout(function(){
                $(".id_content_banner_loading")
                    .stop()
                    .fadeOut(200, _rotateStart);
            }, 500);

            $(".id_content_banner_loading")
                .stop()
                .css({opacity:($(".id_content_banner_loading").css("opacity")==1)?0:$(".id_content_banner_loading").css("opacity")})
                .show()
                .animate({opacity:1}, 250, function(){
                    $(_content_banner_category).hide();
                    _content_banner_current = 0;
                    _content_banner_category = ".id_content_banner_" + cat;

                    // 表示する前に初期化
                    $(_content_banner_category+" .content_banner_inner")
                        .stop(true, true)
                        .css({'left':0})
                        .parent()
                        .parent()
                        .find(">.content_banner_thumbs")
                        .find(">.content_banner_thumb:not(.first_thumb) .content_banner_thumb_current>img")
                        .stop(true, true)
                        .css({top:38})
                        .end()
                        .find(">.content_banner_thumb.first_thumb .content_banner_thumb_current>img")
                        .stop(true, true)
                        .css({top:0})
                        .closest(_content_banner_category)
                        .show();
                });
        };

        // ローテーションタイマー開始
        var _rotateStart = function(){
            if($(_content_banner_category+" .content_banner_item").length<=1) return;
            if(_timer_content_banner_rotation == null){
                $.log("banner: timer start");
                _timer_content_banner_rotation = setTimeout(_rotateBanner, settings.interval);
            }
        };

        // ローテーションタイマー停止
        var _rotateStop = function(){
            $.log("banner: timer stop");
            clearTimeout(_timer_content_banner_rotation);
            _timer_content_banner_rotation = null;
        };

        // ウィンドウのフォーカス判定（非アクティブ時はローテーション停止）
        $(window)
            .focus(_rotateStart)
            .blur (_rotateStop);

        // main
        _initialize(function(){

            // ヘッダでのカテゴリフィルタ
            var speed_  = 600;
            var easing_ = "easeOutQuint";

            // top
            $(".id_top")
                .click(function(){
                    // カレント判定
                    if($(this).hasClass("selected")) return false;
                    $(".id_navHeader>li>a").removeClass("selected");
                    $(this).addClass("selected");

                    // ヘッダ切替
                    $(".id_navHeader>li>a>img").rolloverToggle({current:false});

                    // バナー切替
                    _switchBanner("top");

                    // コンテンツ切替
                    if(!settings.animate){
                        $(".id_content_blocks>.content_block_list>li")
                            .show();
                    }else{
                        $(".id_content_blocks>.content_block_list>li")
                            .stop(true, true)
                            .slideDown(speed_, easing_);
                    }
                    return false;
                });

            // category
            $(".id_navHeader>li>a")
                .click(function(){
                    var sid = '';

                    // カレント判定
                    if($(this).hasClass("selected")) return false;
                    $(".id_top, .id_navHeader>li>a").removeClass("selected");
                    $(this).addClass("selected");

                    sid= $.trim( $(this).attr("class").replace('selected', '') );

                    // ヘッダ切替
                    $(".id_navHeader>li>a:not(."+  sid +")>img").rolloverToggle({current:false}); //TODO
                    $(this).find(">img").rolloverToggle();

                    // バナー切替
                    var cat = sid.replace("id_header_","");
                    _switchBanner(cat);

                    // コンテンツ切替
                    var c = ".category_" + cat;
                    if(!settings.animate){
                        $(".id_content_blocks>.content_block_list>li")
                            .filter(":not("+c+")")
                            .hide()
                            .end()
                            .filter(c)
                            .show();
                    }else{
                        $(".id_content_blocks>.content_block_list>li")
                            .filter(":not("+c+")")
                            .stop(true, true)
                            .slideUp(speed_, easing_)
                            .end()
                            .filter(c)
                            .stop(true, true)
                            .slideDown(speed_, easing_);
                    }
                    //return false;
                });

            // バナーエリアへのマウスオーバーでサムネイル開閉
            /*$(".id_content_banner")
             .mouseover(function(){
             _rotateStop();
             clearTimeout(_timer_content_banner_toggle);
             $(this)
             .stop()
             .animate({height:_content_height_max}, 600, "easeOutQuart");
             })
             .mouseout(function(){
             _rotateStart();
             _timer_content_banner_toggle = setTimeout(function(){
             $(".id_content_banner")
             .stop()
             .animate({height:_content_height_min}, 400, "easeOutQuart");
             }, 400);
             });*/

            // サムネイルにマウスオーバーでバナーのカレントを移動
            $(".id_content_banner>div").each(function(){
                $(this).find(".content_banner_thumbs a")
                    .click(function(){
                        _rotateStop();
                        //setTimeout(function(){_rotateStart();}, 500); // zero-it(stop rotation then start with a delay)

                        if(!settings.animate){
                            $(this)
                                .find(".content_banner_thumb_current>img")
                                .css({top:0})
                                .end()
                                .parent()
                                .siblings()
                                .find("a>.content_banner_thumb_current>img")
                                .css({top:38});
                        }else{
                            $(this)
                                .find(".content_banner_thumb_current>img")
                                .stop()
                                .animate({top:0}, 150, "easeOutQuart")
                                .end()
                                .parent()
                                .siblings()
                                .find("a>.content_banner_thumb_current>img")
                                .stop()
                                .animate({top:38}, 100, "easeOutQuart");
                        }

                    })
                    .each(function(i, elem){
                        $(this)
                            .click(function(){return false;})
                            .click(function(){
                                $(_content_banner_category+" .content_banner_inner")
                                    .stop()
                                    .animate({left:(-_content_width*i)}, 750, "easeOutQuart");
                                _content_banner_current = i;
                                $.log("banner current: "+i.toString());
                            });
                    });
            });
            $(".content_banner_item").hover(function() {_rotateStop();}, function() {_rotateStart();});
            $(".content_banner_thumb").hover(function() {_rotateStop();}, function() {_rotateStart();});// zero-it(start/stop rotation when hover out/in)

            // バナー左右の次へ／前へ
            $(".id_content_banner_nav_prev")
                .mouseover(function(){
                    $(".id_content_banner").trigger("mouseover");
                })
                .mouseout(function(){
                    $(".id_content_banner").trigger("mouseout");
                })
                .click(function(){
                    var c = $(_content_banner_category+">.content_banner_thumbs a").size();
                    if(_content_banner_current === 0) _content_banner_current = c;

                    $(_content_banner_category+">.content_banner_thumbs a:eq(" + (_content_banner_current-1).toString() +")")
                        .trigger("click"); // zero-it(change from mouseover to click)
                });
            $(".id_content_banner_nav_next")
                .mouseover(function(){
                    $(".id_content_banner").trigger("mouseover");
                })
                .mouseout(function(){
                    $(".id_content_banner").trigger("mouseout");
                })
                .click(function(){
                    var c = $(_content_banner_category+">.content_banner_thumbs a").size();
                    if(_content_banner_current >= c - 1) _content_banner_current = -1;

                    $(_content_banner_category+">.content_banner_thumbs a:eq(" + (_content_banner_current+1).toString() +")")
                        .trigger("click"); // zero-it(change from mouseover to click)
                });
        });
        return this;
    };
})(jQuery);

/*
 * L1 animated rollover plugin
 */
(function($){
    $.fn["animatedRollover"] = function(options){
        // settings
        var settings = $.extend({
            duration:350
        }, options);

        // main
        this.each(function(){
            $(this).hover(
                function(){
                    $(this)
                        .find(".overlay")
                        .stop()
                        .css({left:-690})
                        .animate({left:-230}, settings.duration, "easeOutQuart");
                },
                function(){
                    $(this)
                        .find(".overlay")
                        .stop()
                        .animate({left:230}, settings.duration*0.6, "easeInCubic");
                }
            );
        });
        return this;
    };
})(jQuery);

/*
 * L1 uniqlo logo allocation plugin
 */
(function($){

    $.fn["logoAllocation"] = function(options){
        // settings



        var settings = $.extend({
            // settings no logo
            "cofMin"  : 400,
            "cofMax"  : 600,
            "retryMax":10
        }, options);

        var _content_logo_allocations = [];
        var _content_logo_element = $("<li />")
            .addClass("logo_block")
            .addClass("contentH10")
            .css({display:"none"});

        // main
        this.each(function(i, elem){

            // 4-6ブロックごとにロゴ1つ
            var cof = Math.floor(Math.random()*(settings.cofMax-settings.cofMin+1))+settings.cofMin;
            var len = $(this).find(">li").length;
            var cnt = Math.floor(len/cof);

            for(var j=0; j<cnt; j++){
                var k, h, r = settings.retryMax;

                while(true){
                    k = cof*j+j+1;
                    k += Math.floor(Math.random()*cof)+1;
                    // インデックスをグリッド番号に翻訳
                    h = 0;
                    $(this)
                        .find(">li")
                        .each(function(i,elem){
                            h+= ($(this).hasClass("contentH05"))?0:
                                ($(this).hasClass("contentH10"))?1:2;
                            if(k==i+1){
                                h++;
                                return false;
                            }
                        });
                    // 横並びにならないようにカブリ総当り判定
                    if($.inArray(h, _content_logo_allocations)!=-1){
                        $.log("かぶったー: "+i.toString()+"-"+j.toString());
                        r--;
                        if(r>0) continue;
                    }
                    break;
                }
                if(r>0){
                    _content_logo_allocations.push(h);
                    $(this)
                        .find(">li:nth-child("+k.toString()+")")
                        //.after("<li class='logo_block contentH10'></li>");
                        .after(_content_logo_element.clone());
                }
            }
        });

        if($(".id_top").hasClass("selected")){
            // ie fix
            if(!$.support.opacity){
                $(".id_content_blocks .logo_block").show();
            }else{
                $(".id_content_blocks .logo_block").slideDown(600, "easeOutQuint");
            }
        }
        $.log(_content_logo_allocations);
        return this;
    };
})(jQuery);

/*
 * L1 image crossfade plugin
 */
(function($){
    $.fn["crossFade"] = function(options){
        // settings
        var settings = $.extend({
            "selector" : ">a>img:odd",
            "duration" : 400,
            //"interval" : 1330
            "interval" : 2666
        }, options);

        // ie fix
        if(!$.support.opacity) settings.interval *= 1.5;

        // main
        var $target = $(this).filter(":visible").find(settings.selector);
        var _timer  = setInterval(function(){
            $target.stop(true, true).fadeToggle(settings.duration, "easeInOutQuart");
        },settings.interval);
        return this;
    };
})(jQuery);

/*
 * animated page top plugin
 */
(function($){
    $.fn["animatedPageTop"] = function(options){
        // settings
        var settings = $.extend({
        }, options);

        // main
        this.each(function(){
            $(this).click(function(){
                //$(window).scrollTop(0);
                $("html,body").animate({scrollTop:0}, "slow", "easeOutQuart");
                $(this).blur();
                return false;
            });
        });
        return this;
    };
})(jQuery);


(function( $ ){

    // initialize
    $(function(){
        $(".imgover").rollover();
        $(".id_header").header();
        $(".id_header.headerTop").l1Banner();
        // lazy evaluation
        var interval =  setInterval(function(){
            if($(".id_content_blocks>ul").length > 0){
                $(".id_content_blocks>ul").logoAllocation();
                $(".id_content_blocks>ul>li").animatedRollover();
                $(".id_content_blocks .contentCrossFade").crossFade();
                $(".id_goPageTop>a").animatedPageTop();
                clearInterval(interval);
            }
        }, 600);
    });

})( jQuery );