/**
 * jQuery�༶�����˵�
 * 
 * Author:	ZhouFan <happyeddie@gmail.com>
 * 
 */

function LinkageSelect(options) {
	
	var bindEls	= new Array();
	var items	= {};
	
	// Ĭ�ϲ���
	var settings = {
		data		: {},
		file		: null,
		root		: '0',
		ajax		: null,
		timeout		: 30,
		method		: 'post',
		field_name	: null,
		auto		: false
	}; 
	
	// �Զ������
	if(options) {  
		jQuery.extend(settings, options); 
	}
	
	items	= settings.data;
	
	/**
	 * ��Ԫ��
	 * @param {Object} element
	 * @param {Object} value
	 */
	function _bind(element , value) {
		
		// ���������key
		var key	= bindEls.length ?
			bindEls[bindEls.length - 1].key + ',' + bindEls[bindEls.length - 1].value :
			settings.root;
		
		// ���󶨵�Ԫ�ط�������
		bindEls.push({
			element	: element,
			key		: key,
			value	: value
		});
		
		var item_count	= 0;
		for (var i in items) {
			item_count++;
		}
		
		// ��������id
		for (var el_id in bindEls) {
			if (bindEls[el_id].element == element) {
				var self_id	= parseInt(el_id);
			}
		}
		
		for(var el_id in bindEls){
			// Ϊ����ǰ��Ķ�������onchange�¼���onchangeʱ���������
			if (el_id < self_id){
				bindEls[el_id].element.change(function() {
					_fill(element);
				})
			}
		}
		
		// Ϊ��һ����������onchange�¼�����ˢ�������б�
		if (self_id > 0) {
			bindEls[self_id-1].element.change(function() {
				_fill(element , bindEls[self_id].key);
			});
		}
		
		element.change(function() {
			var self_key			= bindEls[self_id-1]?bindEls[self_id].key + ',' + $(this).val():'0,' + $(this).val();
			if (typeof bindEls[self_id+1] != 'undefined') {
				bindEls[self_id+1].key	= self_key;
			}
			
			// �Զ�������ѡֵ
			if (settings.field_name) {
				$(settings.field_name).val($(this).val());
			}
			
			if (settings.auto) {
				if (typeof bindEls[self_id+1] == 'undefined') {
					_find(self_key , function(key , json) {
						if (json) {
							var el	= $('<select></select>');
							element.after(el);
							_bind(el , '');
							_fill(bindEls[self_id+1].element , key , json);
						}
					});
				}
			}
		})
		_fill(element , key , value);
		
	}
	
	/**
	 * ���option
	 * @param {Object} element
	 * @param {Object} key
	 * @param {Object} value
	 */
	function _fill(element , key , value) {
		
		element.empty();
		if(element.is('select.level_1'))
		element.append('<option value="">��ѡ��</option>');
		
		var json = _find(key , function() {
			_fill(element , key , value);
		});
		
		if (!json) {
			if (settings.auto)
				element.hide();
			return false;
		}
		element.show();
		var index	= 1;
		var selected_index	= 0;
		for(var opt_value in json) {
			var opt_title	= json[opt_value];
			var selected	= '';
			if (opt_value == value) {
				selected_index	= index;
				selected		= 'selected="selected"';
			}
			var option	= $('<option value="' + opt_value + '" ' + selected + '>' + opt_title + '</option>');
			element.append(option);
			index++;
		}
		
		if (element[0]) {
			//IE6
			setTimeout(function(){
				element[0].options[selected_index].selected = true;
			}, 0);
			// ��FFѡ��Ĭ����
			element[0].selectedIndex	= 0;
			element.attr('selectedIndex' , selected_index);
		}
		element.width(element.width());
	}
	
	/**
	 * ����Ԫ��
	 * @param {Object} key
	 */
	function _find(key , callback) {
		if (typeof key == 'undefined') {	// ��δ����key
			return null;
		} else if (key[key.length-1] == ',') {	// ��key��','��β���϶���ȡ����ֵ
			return null
		} else if(typeof(items[key]) == "undefined") {
			
			// ����itemsԪ�ظ���
			var item_count	= 0;
			for (var i in items) {
				item_count++;
				break;
			}
			
			if (settings.ajax) {
				$.getJSON(settings.ajax , {key:key} , function(json) {
					items[key] = json;
					callback(key , json);
				})
			} else if(settings.file && item_count == 0) {
				$.getJSON(settings.file , function(json) {
					items = json;
					callback(key , json);
				})
			}
		}
			
		return items[key];
	}
	
	/**
	 * ��ȡ����
	 * @param {Object} element
	 */
	function _getEl(element) {
		if (typeof element == 'string') {
			return $(element);
		} else {
			return element;
		}
	}
	
	
	return {
		
		// ��Ԫ��
		bind	: function(element , value) {
			if (typeof element != 'object')
				element	= _getEl(element);
			value	= value?value:'';
			
			_bind(element , value);
			
		}
	}
	
}