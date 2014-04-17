(function($){
    // global options
    var gopt = {};
    var methods = {
        'init':function(options){
            // These are the default options
            var opt = {
                'minfactor':20, // how much is the next item smaller than previous in pixels
                'distribution':1.5, // how apart are the items (items become separated when this value is below 1)
                'scalethreshold':0, // after how many items to start scaling
                'staticbelowthreshold':false, // if true when number of items is below "scalethreshold" - don't animate
                'titleclass':'itemTitle', // class name of the element containing the item title
                'selectedclass':'selectedItem', // class name of the selected item
                'scrollactive':true, // scroll functionality switch
                'step':{ // compressed items on the side are steps
                    'begin':0,//first shown step
                    'limit':6, // how many steps should be shown on each side
                    'width':8, // how wide is the visible section of the step in pixels
                    'scale':true // scale down steps
                },
                'bendamount':2, // amount of "bending" of the CoverScroll (values 0.1 to 1 bend down, -0.1 to -1 bend up, 2 is straight no bending, 1.5 sligtly bends down)
                'movecallback':function(item){

                }, // callback function triggered after click on an item - parameter is the item's jQuery object
                'distributionbelowscale':0.95, // how apart are the items when number of items are below "scalethreshold"
                'msie':true
            };

            gopt = opt;
            var isScrolling = false;
            // Options are extended with user specified options
            if (options){$.extend(opt, options);}
            // main loop for selected elements
            return this.each(function(){
                var el = $(this);
                if(opt.items){
                    var imgs = el.find(opt.items);
                }else{
                    var imgs = el.find('img');
                }

                // if below scale threshold - don't scale
                if(imgs.length <= opt.scalethreshold){
                    opt.minfactor=0;
                    opt.distribution=opt.distributionbelowscale;
                }

                // default styles
                el.css({
                    'position':'relative'
                });
                imgs.css({
                    'position':'absolute',
                    '-webkit-transition': 'all 0.5s ease-in-out',
                    '-moz-transition': 'all 0.5s ease-in-out',
                    '-o-transition': 'all 0.5s ease-in-out',
                    '-ms-transition': 'all 0.5s ease-in-out',
                    'transition': 'all 0.5s ease-in-out'
                });
                // getting the index of middle item
//        var mindex = Math.ceil(imgs.length/2-1);
//        dean
//        var mindex = imgs.length-1;
                var mbegin =  (opt.step.begin===0||opt.step.begin > 0)?opt.step.begin:0;
                var mend =  (opt.step.limit > 0)? (mbegin + opt.step.limit-1):0;
                var mindex = (mend> 0)? mend:imgs.length-1;

                // draww all items on their appropriate places
                showItems(el, imgs, mindex,true);

                // add click events
                if(imgs.length <= opt.scalethreshold && opt.staticbelowthreshold){
                    imgs.each(function(index){
                        $(this).unbind('click.coverscroll');
                        $(this).bind('click.coverscroll', function(){
                            if($(this).hasClass(opt.selectedclass)){return true;}
                            selectItem(el, this);
//                    $(this).find(".gotoroom").show();
                            $(this).addClass(opt.selectedclass);
                            $(this).find(".gotoroom").show();
                        });
                        //dean
                        $(this).unbind('mouseover');
                        $(this).on('mouseover', function(){
                            if($(this).hasClass(opt.selectedclass)){return true;}
                            selectItem(el, this);
//                    setTimeout(function(){
//                        $(this).addClass(opt.selectedclass);
//                        $(this).find(".gotoroom").show();
//
//                    }, 100);

//                    $(this).find(".gotoroom").show();
                            $(this).addClass(opt.selectedclass);
                            $(this).find(".gotoroom").show();
                        });
                    });
                }else{
                    imgs.each(function(index){

                        $(this).unbind('click.coverscroll');
                        $(this).bind('click.coverscroll', function(){
                            if($(this).hasClass(opt.selectedclass)){return true;}
                            showItems(el, imgs, index);
//                    $(this).find(".gotoroom").show();
                            $(this).addClass(opt.selectedclass);
                            $(this).find(".gotoroom").show();
                        });
                        //dean
                        $(this).unbind('mouseover');

                        $(this).bind('mouseover', function(){
                            if($(this).hasClass(opt.selectedclass)){return true;}
                            showItems(el, imgs, index);
                            $(this).addClass(opt.selectedclass);
                            $(this).find(".gotoroom").show();
//                    setTimeout(function(){
//                        $(this).addClass(opt.selectedclass);
//                        $(this).find(".gotoroom").show();
//
//                    }, 100);

                        });

                    });
                }

                // add scroll event
                if(!opt.scrollactive){return true;}
                el.unbind('wheel');
                el.on('mouseleave',function(){

                    if(gopt.items){
                        var imgs = $(this).find(gopt.items);
                    }else{
                        var imgs = $(this).find('img');
                    }

                    var mbegin =  (gopt.step.begin===0||gopt.step.begin > 0)?gopt.step.begin:0;
                    var mend =  (gopt.step.limit > 0)? (mbegin + gopt.step.limit):0;
                    var imglefts = countimglefts(el, imgs);
                    for(i=mbegin;i<mend;i++){
                        var citem = $(imgs.get(i));
                        citem.removeClass(opt.selectedclass);
                        citem.find(".gotoroom").hide();
                        var css = {
                            'width':234,
                            'transform': 'matrix(1, 0, 0, 1, 0, 0) scale(1)',
                            'left':imglefts[i-mbegin]
                        };
                        if(gopt.msie){
                            isScrolling = true;
                            citem.animate(css, 100, function(){isScrolling=false});
                        }else{
                            citem.css(css);
                        }
                    }

                    // take care of z-index
                    setTimeout(function(){
                        var zi = 100;
                        imgs.each(function(ind){

                            zi = zi + ind;
                            $(this).css('z-index',zi);
                        });
                    },100);
                })
                    .on('wheel', function(evt){
                        if(!isScrolling){
                            var orgEvent = evt.originalEvent, delta, deltaY, deltaY;

                            // Old school scrollwheel delta
                            if (orgEvent['detail']) { deltaY = orgEvent.detail * -1; }
                            if (orgEvent['wheelDelta']) { deltaY = orgEvent.wheelDelta; }
                            if (orgEvent['wheelDeltaY']) { deltaY = orgEvent.wheelDeltaY; }
                            if (orgEvent['wheelDeltaX']) { deltaX = orgEvent.wheelDeltaX * -1; }

                            // Firefox < 17 horizontal scrolling related to DOMMouseScroll event
                            if (orgEvent['axis'] && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
                                deltaX = deltaY * -1;
                                deltaY = 0;
                            }

                            // Set delta to be deltaY or deltaX if deltaY is 0 for backwards compatabilitiy
                            delta = deltaY === 0 ? deltaX : deltaY;

                            // New school wheel delta (wheel event)
                            if (orgEvent['deltaY']) {
                                deltaY = orgEvent.deltaY * -1;
                                delta = deltaY;
                            }
                            if (orgEvent['deltaX']) {
                                deltaX = orgEvent.deltaX;
                                if ( deltaY === 0 ) { delta = deltaX * -1; }
                            }
                            // is it up or down
                            if(delta > 0){
                                el.find('.'+opt.selectedclass+':eq(0)').next().trigger('click');
                            }else{
                                el.find('.'+opt.selectedclass+':eq(0)').prev().trigger('click');
                            }
                        }
                        evt.preventDefault();
                        evt.stopPropagation();
                        return false;
                    });

            });

            //dean
            function checkbrowser(){

                var Sys = {};
                var ua = navigator.userAgent.toLowerCase();
                var s;
                (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
                    (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
                        (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
                            (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
                                (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;
                return Sys;
            }

            function countimglefts(el, imgs){
                var imglefts= new Array();
                var allimglen = 0;
                var mbegin =  (gopt.step.begin===0||gopt.step.begin > 0)?gopt.step.begin:0;
                var mend =  (gopt.step.limit > 0)? (mbegin + gopt.step.limit):0;
                var mleft =  (gopt.minfactor===0||gopt.minfactor > 0)?gopt.minfactor:5;
                for(i=mbegin;i<mend;i++){
//              var citem = $(imgs.get(i));
                    if(i==mend-1){allimglen = allimglen + 149}
                    else{allimglen = allimglen + 149 * 0.94}
                }
//          if((el.width()- allimglen)/2 > mleft){
//              mleft = Math.round((el.width()- allimglen)/2);
//          }

                imglefts.push(mleft)
                for(i=mbegin;i<mend-1;i++){
//              var citem = $(imgs.get(i));
                    mleft = Math.round(mleft + 149 * 0.94);
                    mleft -= 3;
                    imglefts.push(mleft);
                }

                return imglefts;
            }


            function showItems(el, imgs, mindex,isinit){
                imgs.find(".gotoroom").hide();
                imgs.removeClass(opt.selectedclass);
//        if(isinit){
                var imglefts = countimglefts(el, imgs);
//        }
                var minscale =  0.6;
                var angle = 45;
                var middle = $(imgs.get(mindex));
                // take care of the middle item
                var minfactor = (opt.minfactor===0||opt.minfactor > 0)?opt.minfactor:5; //dean
                var mbegin =  (gopt.step.begin===0||gopt.step.begin > 0)?gopt.step.begin:0;
                var mend =  (gopt.step.limit > 0)? (mbegin + gopt.step.limit):0;
                var d = (el.height() > 250)?250:el.height();
                //            dean
                var css = {
//        	'width':d,
//        	'height':d,
//        	'left':Math.round(el.width()/2 - d/2)
                };
                //            dean
                if(isinit){
                    css["left"] =  imglefts[mindex-mbegin];
                    css['top']=minfactor;
                    css['width']=234;
                }else{
                    if(mindex== mend-1 ){
                        css['width']=234;
                    }else{
                        css['width']=234;
                    }
                    css["left"] =  imglefts[mindex-mbegin];
                }
                css["transform"] =  'matrix(1, 0, 0, 1, 0, 0) scale(1)'


                if(opt.msie && isinit){
                    isScrolling = true;
                    middle.animate(css, 100, function(){isScrolling=false});
                }else{
                    middle.css(css);
                }
                middle.fadeIn(80);
                // getting the params
                var minfactor = (opt.minfactor===0||opt.minfactor > 0)?opt.minfactor:5;
                var distrib = opt.distribution?opt.distribution:2;

                var titleclass = opt.titleclass?opt.titleclass:'itemTitle';
                if(!opt.bendamount){opt.bendamount = 2;}

                // handling the title and highlight
//        selectItem(el, middle, true);

                // left to middle items
                var cd = d, sc=0; sf=false;
                var showing = true;
                var cleft = Math.round(el.width()/2 - d/2);

                var imgwithlist = new Array();
                var scale = minscale;
                for(i=mindex-1;i>=0;i--){
                    var citem = $(imgs.get(i));
                    cd = cd - minfactor;
                    if(!sf){
                        cleft = Math.round(cleft - cd/distrib + minfactor); // diff
                    }else{
                        cd = opt.step.scale?cd:cd + minfactor;
                        cleft = Math.round(cleft - opt.step.width);
                        sc++;
                    }
                    //            dean
//        	if(cleft >= 0 && showing && sc <= opt.step.limit){
//        		citem.show();
//        	}else if(!sf){
//        		cleft = Math.round(cleft + (cd/distrib) - minfactor - opt.step.width);
//        		sf = true; sc++;
//        		citem.show();
//        	}else{
//        		citem.hide();
//        		showing = false;
//        	}
                    var css = {
                        //            dean
//        		'width':cd,
//        		'height':cd,
//	        	'top':Math.round(el.height()/opt.bendamount - cd/opt.bendamount),
//	        	'left':cleft,
//                'top':minfactor
                    };
                    //            dean
                    if(isinit){

                        if(i<mbegin){
                            css["left"] =  -250;
//                            citem.hide();
                        }else{
                            css["left"] =  imglefts[i-mbegin];
//                            citem.show();
                        }
//                css["left"] =  imglefts[i];
                        css['top']=minfactor;
                    }else{
                        css['width']=240

//                css['width']=250-(mindex-1-i)*3;
//                alert(i*5)
//                css["left"] =  imglefts[i]-i*12;
                    }
                    //dean 如果需要选择两端平铺则改为mindex>0
                    if(!isinit && mindex>0 && mindex<=imgs.length-1){
//                 if (i == 0){scale = 1;}else{scale = scale + (1-minscale)/mindex}
                        if (mindex == 1){scale = minscale;}else{scale = scale + (1-minscale)/(mindex-mbegin)}
                        if(scale>1){scale = 1;}
                        css["transform"] = 'perspective(1800px) rotateY('+ angle + 'deg) scale(' + scale +')'

                        imgwithlist.push(240 * scale);

                    }else{
                        css["transform"] =  'matrix(1, 0, 0, 1, 0, 0) scale(1)'
                    }

                    if(opt.msie && isinit){
                        isScrolling = true;
                        citem.animate(css, 100, function(){isScrolling=false});
                    }else{
                        citem.css(css);
                    }

                }
                if(!isinit){
                    var mleft = minfactor;
                    for(i=mbegin;i<=mindex-1;i++){
                        var citem = $(imgs.get(i));
                        if(i==mbegin){
                            citem.css('left',mleft);
                        }else{
                            mleft = Math.round(mleft + imgwithlist[mindex-1-i] * 0.70);
                            citem.css('left',mleft);
                        }
                    }
                }


                //middle to right items
                var cd = d, sc=0; sf = false;
                var cleft = Math.round(el.width()/2 - d/2);
                var showing = true;
                var scale = minscale;
                imgwithlist = new Array();
                for(i=mindex+1;i<imgs.length;i++){
                    var citem = $(imgs.get(i));
                    cd = cd - minfactor;
                    if(!sf){
                        cleft = Math.round(cleft + cd/distrib); // diff
                    }else{
                        cd = opt.step.scale?cd:cd + minfactor;
                        cleft = Math.round(cleft + opt.step.width + (opt.step.scale?minfactor:0));
                        sc++;
                    }
                    //            dean
//        	if((cleft + cd) < el.width() && showing && sc <= opt.step.limit){ // diff
//        		citem.show();
//        	}else if(!sf){
//        		sf = true; sc++;
//        		cleft = Math.round((cleft - cd/distrib) + opt.step.width + minfactor);
//        		citem.show();
//        	}else{
//        		citem.hide();
//        		showing = false;
//        	}
                    var css = {
                        //            dean
//        		'width':cd,
//        		'height':cd,
//	        	'top':Math.round(el.height()/opt.bendamount - cd/opt.bendamount),
//              'left':cleft,
//                'top':minfactor
                    };

                    //            dean
                    if(isinit){
                        if((i-mbegin)>imglefts.length-1){
                            css["left"] = el.width();
//                            citem.hide();
                        }else{
                            css["left"] =  imglefts[i-mbegin];
//                            citem.show();
                        }
//                css["left"] =  imglefts[i];
                        css['top']=minfactor;
                    }else {
                        if(i== imgs.length-1 ){
                            css['width']=240;
                        }else{
                            css['width']=240
//                    css['width']=234;
//                    css['width']=290-(i-mindex-1)*3;
//                alert(i*5)
//                    css["left"] =  imglefts[i]+(imgs.length-i)*3;
                        }
                    }
                    //dean 如果需要选择两端平铺则改为mindex>0
                    if(!isinit && mindex>=0 && mindex<imgs.length-1){
//                if (i == imgs.length-1){scale = 1;}else{scale = scale + (1-minscale)/(imgs.length-1-mindex);}
                        if (mindex == mend-2){scale = minscale;}else{scale = scale + (1-minscale)/(mend-1-mindex);}
                        if(scale>1){scale = 1;}
                        css["transform"] = 'perspective(1800px) rotateY(-'+ angle + 'deg) scale('+ scale +')'
                        imgwithlist.push(240 * scale);
                    }else{
                        css["transform"] =  'matrix(1, 0, 0, 1, 0, 0) scale(1)'
                    }

                    if(opt.msie && isinit){
                        isScrolling = true;
                        citem.animate(css, 100, function(){isScrolling=false});
                    }else{
                        citem.css(css);
                    }
                }
                if(!isinit){
                    var mleft = imglefts[mindex+1-mbegin]
                    for(i=mindex+1;i<mend;i++){
                        var citem = $(imgs.get(i));
                        if(i==mindex+1){
                            citem.css('left',mleft);
                        }else{
                            mleft = Math.round(mleft + imgwithlist[i-mindex-2] * 0.70);
                            citem.css('left',mleft);
                        }
                    }
                }



                // take care of z-index
                setTimeout(function(){
                    var zi = 100;
                    imgs.each(function(ind){
//            dean
//	        	if(ind<=mindex){
//	        		zi = zi + ind;
//	        	}else{
//	        		zi = zi - ind;
//	        	}
                        if(mindex>0){zi = zi + ind;}else{zi = zi - ind;}
                        $(this).css('z-index',zi);
                    });
//            dean
                    if(mindex>0){
                        imgs.each(function(ind){
                            if(ind==mindex){
                                $(this).css('z-index',zi+1);
                            }
                        });
                    }
                },100);
                // end of showItems()
            };


            function selectItem(el, elem, center){

                elem = $(elem);
                var imgs;
                // all items collection
                if(opt.items){
                    imgs = el.find(opt.items);
                }else{
                    imgs = el.find('img');
                }
                //dean
                // handling the title
//      	var d = (el.outerHeight() > 250)?250:el.outerHeight();
//		var title = false;
//		if(title = elem.attr('title')){
//			el.find('.'+opt.titleclass).remove();
//			if(center){
//				var le = Math.round(el.width()/2 - d/2);
//			}else{
//				var le = parseInt(elem.css('left'));
//			}
//			$('<div style="position:absolute;text-align:center;top:'+d+'px;left:'+(le - d/2)+'px;width:'+(d*2)+'px" class="'+opt.titleclass+'">'+title+'</div>').appendTo(el);
//			setTimeout(function(){opt.movecallback.call(this, elem)}, 600);
//		}else if(title = elem.find('.'+opt.titleclass+':eq(0)')){
//			el.find('.'+opt.titleclass).hide();
//			title.css({
//        		'position':'absolute',
//        		'width':(d*2),
//        		'text-align':'center',
//        		'top':d,
//        		'left':Math.round(0-(d/2))
//        	});
//        	//title.find('img').css({'width':'100%', 'height':'100%'});
//        	title.show();
//        	setTimeout(function(){opt.movecallback.call(this, elem)}, 500);
//		}

                // selecting the item
                setTimeout(function(){
                    imgs.removeClass(opt.selectedclass);
                    elem.addClass(opt.selectedclass);

                }, 100);

            };

        },
        // select next
        'next':function(callback){
            //var opt = {};
            //if (options){$.extend(opt, options);}
            return this.each(function(){
                var el = $(this);
                el.find('.'+gopt.selectedclass+':eq(0)').next().trigger('click');
            });
        },
        // select previous
        'prev':function(){
            return this.each(function(){
                var el = $(this);
                el.find('.'+gopt.selectedclass+':eq(0)').prev().trigger('click');
            });
        },
        //dean
        'movenext':function(imgs){

            if(gopt.step.begin + gopt.step.limit == imgs.length){
                return;
            }
            //hide this first
            var lcitem = $(imgs.get(gopt.step.begin));
            var css = {
                'width':234,
                'transform': 'matrix(1, 0, 0, 1, 0, 0) scale(1)',
                'left': -250
            };
            lcitem.css(css);

            //show one more
            gopt.step.begin = gopt.step.begin +1;
            var mbegin =  (gopt.step.begin===0||gopt.step.begin > 0)?gopt.step.begin:0;
            var mend =  (gopt.step.limit > 0)? (mbegin + gopt.step.limit):0;
            var mleft =  (gopt.minfactor===0||gopt.minfactor > 0)?gopt.minfactor:5;

            for(i=mbegin;i<mend;i++){
                var citem = $(imgs.get(i));
                mleft = (i==mbegin)?mleft: Math.round(mleft + 149 * 0.94)-3;

                if(i==mend-1){
//                    citem.show();
                }
                var css = {
                    'width':234,
                    'transform': 'matrix(1, 0, 0, 1, 0, 0) scale(1)',
                    'left': mleft
                };

                citem.css(css);
            }

            // take care of z-index
            setTimeout(function(){
                var zi = 100;
                imgs.each(function(ind){

                    zi = zi + ind;
                    $(this).css('z-index',zi);
                });
            },100);

        },
        //dean
        'moveprev':function(imgs,w){
            //show this first

            if(gopt.step.begin== 0){
                return;
            }

            var mbegin =  (gopt.step.begin===0||gopt.step.begin > 0)?gopt.step.begin:0;
            var mend =  (gopt.step.limit > 0)? (mbegin + gopt.step.limit-1):0;

            var hcitem = $(imgs.get(mend));
            var css = {
                'width':234,
                'transform': 'matrix(1, 0, 0, 1, 0, 0) scale(1)',
                'left': w+10
            };
            hcitem.css(css);
            //show one more
            gopt.step.begin = gopt.step.begin -1;
            var mbegin =  (gopt.step.begin===0||gopt.step.begin > 0)?gopt.step.begin:0;
            var mend =  (gopt.step.limit > 0)? (mbegin + gopt.step.limit):0;
            var mleft =  (gopt.minfactor===0||gopt.minfactor > 0)?gopt.minfactor:5;
            for(i=mbegin;i<mend;i++){
                var citem = $(imgs.get(i));
                mleft = (i==mbegin)?mleft: Math.round(mleft + 149 * 0.94)-3;

                if(i==mbegin){
//                    citem.show();
                }
                var css = {
                    'width':234,
                    'transform': 'matrix(1, 0, 0, 1, 0, 0) scale(1)',
                    'left': mleft
                };

                citem.css(css);
            }

            // take care of z-index
            setTimeout(function(){
                var zi = 100;
                imgs.each(function(ind){

                    zi = zi + ind;
                    $(this).css('z-index',zi);
                });
            },100);
        }
    };
    // generic jQuery plugin skeleton
    $.fn.coverscroll = function(method){
        if (methods[method]){
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        }else if(typeof method === 'object' || ! method ) {
            return methods.init.apply(this, arguments);
        }else{
            $.error( 'Method ' +  method + ' does not exist on this plugin' );
        }
    };

    //dean
    $.fn.movenext = function(){

        if(gopt.items){
            var imgs = $(this).find(gopt.items);
        }else{
            var imgs = $(this).find('img');
        }
        return methods.movenext(imgs)
    };
    //dean
    $.fn.moveprev = function(){

        if(gopt.items){
            var imgs = $(this).find(gopt.items);
        }else{
            var imgs = $(this).find('img');
        }
        return methods.moveprev(imgs,$(this).width())

    };
})(jQuery);