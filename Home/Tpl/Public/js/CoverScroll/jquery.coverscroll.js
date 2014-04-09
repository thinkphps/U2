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
	    	'limit':4, // how many steps should be shown on each side
	    	'width':8, // how wide is the visible section of the step in pixels
	    	'scale':true // scale down steps
	    },
	    'bendamount':2, // amount of "bending" of the CoverScroll (values 0.1 to 1 bend down, -0.1 to -1 bend up, 2 is straight no bending, 1.5 sligtly bends down)
	  	'movecallback':function(item){

        }, // callback function triggered after click on an item - parameter is the item's jQuery object
	  	'distributionbelowscale':0.95, // how apart are the items when number of items are below "scalethreshold" 
	    'msie':($('html').is('.ie6, .ie7, .ie8'))
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
        var mindex = imgs.length-1;

   		// draww all items on their appropriate places
        showItems(el, imgs, mindex,true);
        
        // add click events
        if(imgs.length <= opt.scalethreshold && opt.staticbelowthreshold){
        	imgs.each(function(index){
        		$(this).unbind('click.coverscroll');
	        	$(this).bind('click.coverscroll', function(){
	        		if($(this).hasClass(opt.selectedclass)){return true;}
	        		selectItem(el, this);
	        	});
                //dean
                $(this).unbind('mouseover');
                $(this).bind('mouseover', function(){
                    if($(this).hasClass(opt.selectedclass)){return true;}
                    selectItem(el, this);
                });
	        });
        }else{
        	imgs.each(function(index){
        		$(this).unbind('click.coverscroll');
	        	$(this).bind('click.coverscroll', function(){
	        		if($(this).hasClass(opt.selectedclass)){return true;}
	        		showItems(el, imgs, index);
	        	});
                //dean
                $(this).unbind('mouseover');
                $(this).bind('mouseover', function(){
                    if($(this).hasClass(opt.selectedclass)){return true;}
                    showItems(el, imgs, index);
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
            for(i=0;i<imgs.length;i++){
                var citem = $(imgs.get(i));
                citem.removeClass(opt.selectedclass);
                var css = {
                    'width':210,
                    'transform': 'matrix(1, 0, 0, 1, 0, 0) scale(1)'
                };
                if(gopt.msie){
                    isScrolling = true;
                    citem.animate(css, 500, function(){isScrolling=false});
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
      function countimglefts(el, imgs){
          var imglefts= new Array();
          var allimglen = 0;
          for(i=0;i<imgs.length;i++){
              var citem = $(imgs.get(i));
              if(i==imgs.length-1){allimglen = allimglen + citem.width()}
              else{allimglen = allimglen + citem.width() * 0.75}
          }
          var mleft =  (opt.minfactor===0||opt.minfactor > 0)?opt.minfactor:15;

          if((el.width()- allimglen)/2 > mleft){
              mleft = Math.round((el.width()- allimglen)/2);
          }
          console.log(mleft);
          imglefts.push(mleft)
          for(i=0;i<imgs.length-1;i++){
              var citem = $(imgs.get(i));
              mleft = Math.round(mleft + citem.width() * 0.75);
              mleft-= 9;
              imglefts.push(mleft);
              console.log(mleft);
          }

          return imglefts;
      }


      function showItems(el, imgs, mindex,isinit){
        if(isinit){
            var imglefts = countimglefts(el, imgs);
        }
        var minscale =  0.6;
        var angle = 45;
        var middle = $(imgs.get(mindex));
        // take care of the middle item
        var minfactor = (opt.minfactor===0||opt.minfactor > 0)?opt.minfactor:15; //dean
        var d = (el.height() > 250)?250:el.height();
       //            dean
        var css = {
//        	'width':d,
//        	'height':d,
//        	'left':Math.round(el.width()/2 - d/2)
        };
          //            dean
          if(isinit){
              css["left"] =  imglefts[mindex];
              css['top']=minfactor;
          }else{
              if(mindex== imgs.length-1 ){
                  css['width']=210;
              }else{
                  css['width']=210;
              }
          }
        css["transform"] =  'matrix(1, 0, 0, 1, 0, 0) scale(1)'


        if(opt.msie){
    		isScrolling = true;
    		middle.animate(css, 500, function(){isScrolling=false});
    	}else{
    		middle.css(css);
    	}
        middle.fadeIn(80);
        // getting the params
        var minfactor = (opt.minfactor===0||opt.minfactor > 0)?opt.minfactor:15;
        var distrib = opt.distribution?opt.distribution:2;

        var titleclass = opt.titleclass?opt.titleclass:'itemTitle';
        if(!opt.bendamount){opt.bendamount = 2;}

        // handling the title and highlight
        selectItem(el, middle, true);
        
        // left to middle items
        var cd = d, sc=0; sf=false;
        var showing = true;
        var cleft = Math.round(el.width()/2 - d/2);

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
                css["left"] =  imglefts[i];
                css['top']=minfactor;
            }else{
                css['width']=240;
            }
            //dean 如果需要选择两端平铺则改为mindex>0
            if(!isinit && mindex>0 && mindex<=imgs.length-1){
//                 if (i == 0){scale = 1;}else{scale = scale + (1-minscale)/mindex}
                if (mindex == 1){scale = minscale;}else{scale = scale + (1-minscale)/mindex}
                if(scale>1){scale = 1;}
                css["transform"] = 'perspective(1800px) rotateY('+ angle + 'deg) scale(' + scale +')'
            }else{
                css["transform"] =  'matrix(1, 0, 0, 1, 0, 0) scale(1)'
            }

        	if(opt.msie){
        		isScrolling = true;
        		citem.animate(css, 500, function(){isScrolling=false});
        	}else{
        		citem.css(css);
        	}
        	
        }
        
        //middle to right items
        var cd = d, sc=0; sf = false;
        var cleft = Math.round(el.width()/2 - d/2);
        var showing = true;
        var scale = minscale;
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
                css["left"] =  imglefts[i];
                css['top']=minfactor;
            }else {
                if(i== imgs.length-1 ){
                    css['width']=210;
                }else{
                    css['width']=210;
                }
            }
            //dean 如果需要选择两端平铺则改为mindex>0
            if(!isinit && mindex>=0 && mindex<imgs.length-1){
//                if (i == imgs.length-1){scale = 1;}else{scale = scale + (1-minscale)/(imgs.length-1-mindex);}
                if (mindex == imgs.length-2){scale = minscale;}else{scale = scale + (1-minscale)/(imgs.length-1-mindex);}
                if(scale>1){scale = 1;}
                css["transform"] = 'perspective(1800px) rotateY(-'+ angle + 'deg) scale('+ scale +')'
            }else{
                css["transform"] =  'matrix(1, 0, 0, 1, 0, 0) scale(1)'
            }

        	if(opt.msie){
        		isScrolling = true;
        		citem.animate(css, 500, function(){isScrolling=false});
        	}else{
        		citem.css(css);
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

})(jQuery);