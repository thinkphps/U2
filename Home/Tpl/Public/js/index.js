jQuery(function($){

	var weather = $('.nio-weather'),
		alterClass = function(ele, clsName, siblings, add){
			ele[add ? add : 'addClass'](clsName).siblings(siblings).removeClass(clsName);
		},
		alterHide = function(ele){
			ele.show().siblings().hide();
		};
	
	jQuery.easing['jswing'] = jQuery.easing['swing'];

	jQuery.extend( jQuery.easing,
	{
		def: 'easeOutExpo',
		swing: function (x, t, b, c, d) {
			return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
		},
		easeOutExpo: function (x, t, b, c, d) {
			return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
		}
	});

	// index tabs
	;(function($){
		var cases = $('.nio-showcases'),
			tab = $('.nio-tabs li'),
			list = $('.nio-list'),
			index;
		
		// index tabs && mini tabs
		tab.on('click', function(){
			var that = $(this);
			index = that.index();
			//kimi
            var lav = that.attr('la').split('-');
			getgoods('',lav[0],lav[1]);
			//kimi
			if(that.hasClass('nio-active') && cases.height() > 0) return;
			alterClass(that, 'nio-active', '.nio-active');
			alterHide(list.children().eq(index));
			if(cases.length) slideCase();
		});

		function slideCase(){
			cases.animate({'height' : 464}, 'slow').find('.nio-showcase').stop(true).css({'top' : '-464px'}).addClass('case-curr').animate({'top' : 0},'slow');
			// cases.animate({'height' : 520}, 'slow').find('.case-curr').stop(true).css({'z-index' : 0,'top' : '-520px'}).removeClass('case-curr');
			// cases.find('.nio-showcase').eq(index).addClass('case-curr').css('z-index', 1).stop(true).animate({'top': 0}, 'slow');
		}

		// index weather-list
		list.on('mouseenter', '> li', function(){
			var that = $(this),
				index = that.index();
			tab.eq(index).trigger('click');
		});

		// index nio-close
		cases.on('click', '.nio-close', function(){
			$(this).parent().animate({'top' : '-464px'}, 'slow').removeClass('case-curr');
			cases.animate({'height' : 0}, 'slow');
		});

		// nio-box roundabout
		if($.fn.roundabout){
			var roundabout = $('.nio-box ul').roundabout({
				btnNext : '.nio-next',
				btnPrev : '.nio-prev'
			});
			for(var i = 1; i < 5; i ++){
				var arr = document.createElement('img');
				arr.src = "images/nioweather/index/nio-box" + i + ".jpg";
			}
		}

		// index showcase list
		$('.nio-list2').on('mouseover', 'li', function(){
			var that = $(this),
				parent = that.parents('.nio-showcase'), 
				aside = parent.find('.nio-aside'),
				num = parent.find('.aside-curr').index(),
				index = that.index(),
				li = aside.find('li');
				//kimi
			var lia = $(this).children().text();
			    $('#mwctype').val(lia);
			    //kimi
			alterClass(that, 'nio-curr2', '.nio-curr2');
			if(!num){
				alterHide(parent.find('.nio-ul').eq(index));
			} else {
				alterHide(parent.find('.nio-box').eq(index));
			}
		});

		// nio index aside
		$('.nio-aside').on('click', 'li', function(){
			var that = $(this),
				num = that.index(),
				parent = that.parents('.nio-showcase'),
				area = parent.find('.nio-area'),
				li = $('.nio-list2 li');
				//kimi点击标签获取数据开始
		   var  tagname = $(this).children().children('span').text();
                getgoods(tagname,'','');
				//kimikimi点击标签获取数据开始
			alterClass(that, 'aside-curr', '.aside-curr');
			if(!num){
				area.show().next().hide();
			} else {
				area.hide().next().show();
				parent.find('.nio-boxs').attr('class', 'nio-boxs nio-box' + num);
				roundabout.roundabout("animateToChild", num - 1); // take care!
			}
			parent.find('.nio-curr2').mouseover();
		});
	}($));

	// nioSlider
	;(function($){
		$.fn.nioSlider = function(options){
			return this.each(function(){
				var that = $(this),
					ul = that.find('ul');
				that.siblings('.nio-next').click(function(){
					if(ul.is(':animated')) return;
					ul.animate({'left' : '-' + options.width}, 'slow', function(){
						ul.children().first().remove().appendTo(ul.css('left', 0));
					});
				}).siblings('.nio-prev').click(function(){
					if(ul.is(':animated')) return;
					ul.css('left', '-' + options.width + 'px').prepend(ul.children().last().remove()).animate({'left' : 0}, 'slow');
				});
			});
		};

		$('.nio-area .nio-container').nioSlider({
			width : 212
		});
		
		$('.nio-cabi .nio-container').nioSlider({
			width : 186
		});
		$('.nio-net .nio-container').nioSlider({
			width : 160
		});
	})($);

	//.nio-mask
	;(function($){
		$('.nio-container').on('mouseenter', '.pr', function(){
			$(this).find('.nio-mask').stop(true).slideDown();
		}).on('mouseleave', '.pr', function(){
			$(this).find('.nio-mask').stop(true).slideUp();
		});
	})//($);

	// mini-aside
	;(function($){
		var aside = $('.mini-aside'),
			body = $('html, body'),
			win = $(window),
			num, scroll;
		if(!aside.length) return;
		$.each(aside.find('li'), function(i, li){
			li = $(li);
			var nav = li.data('nav');
			num = nav === 'top' ? 0 : $('.' + nav).offset().top;
			li.data('nav', num);
		});

		aside.on('click', 'li', function(){
			body.animate({'scrollTop' : $(this).data('nav')}, 300);
		});
		win.scroll(function(){
			clearTimeout(scroll);
			scroll = setTimeout(function(){
				num = win.scrollTop();
				aside.stop().animate({'top' : num}, 800);
			}, 100);
		});
	})($);

	// minisite
	;(function($){
		// toggle get
		$('.nio-cabinet').on('click', '.nio-get', function(){$(this).toggleClass('nio-got');});

		// mini-toggle
		$('.mini-toggle').on('click', 'li a', function(){
			$(this).toggleClass('mini-checked');
		}).on('click', '.nio-close', function(){
			$(this).toggleClass('nio-open').parent().find('ul').toggle();
		});

		// mini-mask color select
		var mask = $('.mini-mask');
		mask.on('click', ' > div > a', function(){
			alterClass($(this), 'mask-checked', '.mask-checked', 'toggleClass');
		}).on('click', '.pull-right', function(){
			$(this).find('div').toggle();
		}).on('click', 'a.pa', function(){
			mask.hide();
		});

		$('.mini-color').on('click', 'a', function(){
			this.parentNode.parentNode.getElementsByTagName('p')[0].innerHTML = this.innerHTML;
		});

		$('.nio-net').on('mouseover', 'a.nio-purchase', function(){
			mask.show();
		});
	}($));

	// tshirts & pants
	;(function($){
		var cabinet = {
			$ : function(id){
				return document.getElementById(id);
			},
			hide : function(){
				for(var i = arguments.length; i --;){
					arguments[i].style.display = 'none';
				}
			},
			show : function(){
				for(var i = arguments.length; i --;){
					arguments[i].style.display = 'block';
				}
			},
			getArr : function(that, dir){
				var index = that.parents('li').index(),
					minis = that.parents('ul').find(dir),
					arr = $.map(minis, function(mini){
						return '<li>' + mini.outerHTML + '</li>';
					}),
					temp = arr[0];
				arr[0] = arr[index];
				arr[index] = temp;

				this.show(recomand, purchase);
				return arr;
			}
		};
		// mini-top
		var tshirt = cabinet.$('tshirt'),
			tshirts = cabinet.$('tshirts'),
			pant = cabinet.$('pant'),
			pants = cabinet.$('pants'),
			slideTshirts = cabinet.$('slide-tshirts'),
			slidePants = cabinet.$('slide-pants'),
			recomand = cabinet.$('recomand'),
			purchase = cabinet.$('purchase');

		$('.nio-cabi').on('click', 'a.mini-top',function(){
			var that = $(this),
				arr = cabinet.getArr(that, '.mini-bot');

			tshirt.innerHTML = this.innerHTML;
			slidePants.innerHTML = arr.join('');
			cabinet.show(tshirt, pants);
			cabinet.hide(tshirts, pant);
		}).on('click', 'a.mini-bot', function(){
			var that = $(this),
				arr = cabinet.getArr(that, '.mini-top');

			pant.innerHTML = this.innerHTML;
			slideTshirts.innerHTML = arr.join('');
			cabinet.show(pant, tshirts);
			cabinet.hide(pants, tshirt);
		});

		// nio-clear
		$('.nio-clear').click(function(){
			cabinet.hide(tshirts,tshirt,pants,pant,recomand,purchase);
		});
	})($);

	// nio-refresh
	;(function($){

		var index = 0;
		// nio-refresh
		$('.nio-refresh').click(refresh);

		// nio-area2
		$('.nio-area2').on('click', 'a', function(){
			alterClass($(this).parent(), 'dd-checked', '.dd-checked', 'toggleClass');
			refresh();
		});

		$.random = function(min, max) {
			if (max == null) {
				max = min;
				min = 0;
			}
			return min + Math.floor(Math.random() * (max - min + 1));
		};
		$.shuffle = function(obj) {
			var rand,
				index = 0,
				shuffled = [];
			$.each(obj, function(i, value) {
				rand = $.random(index++);
				shuffled[index - 1] = shuffled[rand];
				shuffled[rand] = value;
			});
			return shuffled;
		};

		var uls = $('.nio-minisite .nio-ul'),
			str = 'images/nioweather/sku/sku';

		function refresh(){
			$.each(uls, function(i, ul){
				var arr = [1,2,3,4,5,6,7,8];
				$.each($(ul).find('img'),function(j, img){
					arr = $.shuffle(arr);
					img.src = str + (index + 1) + '/' + (i ? 'b' : 'a') + arr.pop() + '.png';
				});
			});
		}

		// mini showcase
		$('.nio-list2_tab').on('click', 'li', function(){
			var that = $(this);
			index = that.index();
			alterClass(that, 'nio-curr2_gray', '.nio-curr2_gray');
			alterHide($('.nio-ul2').eq(index));
		});
	}($));
});