/*
 * jQuery validation plug-in 1.7
 */
;(function(jQuery) {
jQuery.extend(jQuery.fn, {
	validate: function( options ) {
		if (!this.length) {
			options && options.debug && window.console && console.warn( "nothing selected, can't validate, returning nothing" );
			return;
		}
		var validator = jQuery.data(this[0], 'validator');
		if ( validator ) {
			return validator;
		}
		validator = new jQuery.validator( options, this[0] );
		jQuery.data(this[0], 'validator', validator); 
		if ( validator.settings.onsubmit ) {
			this.find("input, button").filter(".cancel").click(function() {
				validator.cancelSubmit = true;
			});
			if (validator.settings.submitHandler) {
				this.find("input, button").filter(":submit").click(function() {
					validator.submitButton = this;
				});
			}
			this.submit( function( event ) {
				if ( validator.settings.debug )
					event.preventDefault();
				function handle() {
					if ( validator.settings.submitHandler ) {
						if (validator.submitButton) {
							var hidden = jQuery("<input type='hidden'/>").attr("name", validator.submitButton.name).val(validator.submitButton.value).appendTo(validator.currentForm);
						}
						validator.settings.submitHandler.call( validator, validator.currentForm );
						if (validator.submitButton) {
							hidden.remove();
						}
						return false;
					}
					return true;
				}
				if ( validator.cancelSubmit ) {
					validator.cancelSubmit = false;
					return handle();
				}
				if ( validator.form() ) {
					if ( validator.pendingRequest ) {
						validator.formSubmitted = true;
						return false;
					}
					return handle();
				} else {
					validator.focusInvalid();
					return false;
				}
			});
		}
		return validator;
	},
	valid: function() {
        if ( jQuery(this[0]).is('form')) {
            return this.validate().form();
        } else {
            var valid = true;
            var validator = jQuery(this[0].form).validate();
            this.each(function() {
				valid &= validator.element(this);
            });
            return valid;
        }
    },
	removeAttrs: function(attributes) {
		var result = {},
			jQueryelement = this;
		jQuery.each(attributes.split(/\s/), function(index, value) {
			result[value] = jQueryelement.attr(value);
			jQueryelement.removeAttr(value);
		});
		return result;
	},
	rules: function(command, argument) {
		var element = this[0];
		if (command) {
			var settings = jQuery.data(element.form, 'validator').settings;
			var staticRules = settings.rules;
			var existingRules = jQuery.validator.staticRules(element);
			switch(command) {
			case "add":
				jQuery.extend(existingRules, jQuery.validator.normalizeRule(argument));
				staticRules[element.name] = existingRules;
				if (argument.messages)
					settings.messages[element.name] = jQuery.extend( settings.messages[element.name], argument.messages );
				break;
			case "remove":
				if (!argument) {
					delete staticRules[element.name];
					return existingRules;
				}
				var filtered = {};
				jQuery.each(argument.split(/\s/), function(index, method) {
					filtered[method] = existingRules[method];
					delete existingRules[method];
				});
				return filtered;
			}
		}
		var data = jQuery.validator.normalizeRules(
		jQuery.extend(
			{},
			jQuery.validator.metadataRules(element),
			jQuery.validator.classRules(element),
			jQuery.validator.attributeRules(element),
			jQuery.validator.staticRules(element)
		), element);
		if (data.required) {
			var param = data.required;
			delete data.required;
			data = jQuery.extend({required: param}, data);
		}
		return data;
	}
});
jQuery.extend(jQuery.expr[":"], {
	blank: function(a) {return !jQuery.trim("" + a.value);},
	filled: function(a) {return !!jQuery.trim("" + a.value);},
	unchecked: function(a) {return !a.checked;}
});
jQuery.validator = function( options, form ) {
	this.settings = jQuery.extend( true, {}, jQuery.validator.defaults, options );
	this.currentForm = form;
	this.init();
};
jQuery.validator.format = function(source, params) {
	if ( arguments.length == 1 ) 
		return function() {
			var args = jQuery.makeArray(arguments);
			args.unshift(source);
			return jQuery.validator.format.apply( this, args );
		};
	if ( arguments.length > 2 && params.constructor != Array  ) {
		params = jQuery.makeArray(arguments).slice(1);
	}
	if ( params.constructor != Array ) {
		params = [ params ];
	}
	jQuery.each(params, function(i, n) {
		source = source.replace(new RegExp("\\{" + i + "\\}", "g"), n);
	});
	return source;
};
jQuery.extend(jQuery.validator, {
	defaults: {
		messages: {},
		groups: {},
		rules: {},
		errorClass: "error",
		validClass: "valid",
		errorElement: "label",
		focusInvalid: true,
		errorContainer: jQuery( [] ),
		errorLabelContainer: jQuery( [] ),
		onsubmit: true,
		ignore: [],
		ignoreTitle: false,
		onfocusin: function(element) {
			this.lastActive = element;
			if ( this.settings.focusCleanup && !this.blockFocusCleanup ) {
				this.settings.unhighlight && this.settings.unhighlight.call( this, element, this.settings.errorClass, this.settings.validClass );
				this.errorsFor(element).hide();
			}
		},
		onfocusout: function(element) {
			if ( !this.checkable(element) && (element.name in this.submitted || !this.optional(element)) ) {
				this.element(element);
			}
		},
		onkeyup: function(element) {
			if ( element.name in this.submitted || element == this.lastElement ) {
				this.element(element);
			}
		},
		onclick: function(element) {
			if ( element.name in this.submitted )
				this.element(element);
			else if (element.parentNode.name in this.submitted)
				this.element(element.parentNode);
		},
		highlight: function( element, errorClass, validClass ) {
			jQuery(element).addClass(errorClass).removeClass(validClass);
		},
		unhighlight: function( element, errorClass, validClass ) {
			jQuery(element).removeClass(errorClass).addClass(validClass);
		}
	},
	setDefaults: function(settings) {
		jQuery.extend( jQuery.validator.defaults, settings );
	},
	messages: {
		 required: "必选字段",
		 string:"包含非法字符",
		remote: "请修正该字段",
		email: "请输入正确格式的电子邮件",
		url: "请输入合法的网址",
		date: "请输入合法的日期",
		dateISO: "请输入合法的日期 (ISO).",
		number: "请输入合法的数字",
		numberDE: "Bitte geben Sie eine Nummer ein.",
		digits: "只能输入整数",
		creditcard: "请输入合法的信用卡号",
		equalTo: "请再次输入相同的值",
		accept: "请输入拥有合法后缀名的字符串",
		maxlength: jQuery.validator.format("请输入一个长度最多是 {0} 的字符串"),
		minlength: jQuery.validator.format("请输入一个长度最少是 {0} 的字符串"),
		rangelength: jQuery.validator.format("请输入一个长度介于 {0} 和 {1} 之间的字符串"),
		range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"),
		max: jQuery.validator.format("请输入一个最大为 {0} 的值"),
		min: jQuery.validator.format("请输入一个最小为 {0} 的值")
	},
	autoCreateRanges: false,
	prototype: {
		init: function() {
			this.labelContainer = jQuery(this.settings.errorLabelContainer);
			this.errorContext = this.labelContainer.length && this.labelContainer || jQuery(this.currentForm);
			this.containers = jQuery(this.settings.errorContainer).add( this.settings.errorLabelContainer );
			this.submitted = {};
			this.valueCache = {};
			this.pendingRequest = 0;
			this.pending = {};
			this.invalid = {};
			this.reset();
			var groups = (this.groups = {});
			jQuery.each(this.settings.groups, function(key, value) {
				jQuery.each(value.split(/\s/), function(index, name) {
					groups[name] = key;
				});
			});
			var rules = this.settings.rules;
			jQuery.each(rules, function(key, value) {
				rules[key] = jQuery.validator.normalizeRule(value);
			});
			function delegate(event) {
				var validator = jQuery.data(this[0].form, "validator"),
					eventType = "on" + event.type.replace(/^validate/, "");
				validator.settings[eventType] && validator.settings[eventType].call(validator, this[0] );
			}
			jQuery(this.currentForm)
				.validateDelegate(":text, :password, :file, select, textarea", "focusin focusout keyup", delegate)
				.validateDelegate(":radio, :checkbox, select, option", "click", delegate);

			if (this.settings.invalidHandler)
				jQuery(this.currentForm).bind("invalid-form.validate", this.settings.invalidHandler);
		},
		form: function() {
			this.checkForm();
			jQuery.extend(this.submitted, this.errorMap);
			this.invalid = jQuery.extend({}, this.errorMap);
			if (!this.valid())
				jQuery(this.currentForm).triggerHandler("invalid-form", [this]);
			this.showErrors();
			return this.valid();
		},
		checkForm: function() {
			this.prepareForm();
			for ( var i = 0, elements = (this.currentElements = this.elements()); elements[i]; i++ ) {
				this.check( elements[i] );
			}
			return this.valid(); 
		},
		element: function( element ) {
			element = this.clean( element );
			this.lastElement = element;
			this.prepareElement( element );
			this.currentElements = jQuery(element);
			var result = this.check( element );
			if ( result ) {
				delete this.invalid[element.name];
			} else {
				this.invalid[element.name] = true;
			}
			if ( !this.numberOfInvalids() ) {
				this.toHide = this.toHide.add( this.containers );
			}
			this.showErrors();
			return result;
		},
		showErrors: function(errors) {
			if(errors) {
				jQuery.extend( this.errorMap, errors );
				this.errorList = [];
				for ( var name in errors ) {
					this.errorList.push({
						message: errors[name],
						element: this.findByName(name)[0]
					});
				}
				this.successList = jQuery.grep( this.successList, function(element) {
					return !(element.name in errors);
				});
			}
			this.settings.showErrors
				? this.settings.showErrors.call( this, this.errorMap, this.errorList )
				: this.defaultShowErrors();
		},
		resetForm: function() {
			if ( jQuery.fn.resetForm )
				jQuery( this.currentForm ).resetForm();
			this.submitted = {};
			this.prepareForm();
			this.hideErrors();
			this.elements().removeClass( this.settings.errorClass );
		},
		numberOfInvalids: function() {
			return this.objectLength(this.invalid);
		},
		objectLength: function( obj ) {
			var count = 0;
			for ( var i in obj )
				count++;
			return count;
		},
		hideErrors: function() {
			this.addWrapper( this.toHide ).hide();
		},
		valid: function() {
			return this.size() == 0;
		},
		size: function() {
			return this.errorList.length;
		},
		focusInvalid: function() {
			if( this.settings.focusInvalid ) {
				try {
					jQuery(this.findLastActive() || this.errorList.length && this.errorList[0].element || [])
					.filter(":visible")
					.focus()
					.trigger("focusin");
				} catch(e) {
				}
			}
		},
		findLastActive: function() {
			var lastActive = this.lastActive;
			return lastActive && jQuery.grep(this.errorList, function(n) {
				return n.element.name == lastActive.name;
			}).length == 1 && lastActive;
		},
		elements: function() {
			var validator = this,
				rulesCache = {};
			return jQuery([]).add(this.currentForm.elements)
			.filter(":input")
			.not(":submit, :reset, :image, [disabled]")
			.not( this.settings.ignore )
			.filter(function() {
				!this.name && validator.settings.debug && window.console && console.error( "%o has no name assigned", this);
				if ( this.name in rulesCache || !validator.objectLength(jQuery(this).rules()) )
					return false;
				rulesCache[this.name] = true;
				return true;
			});
		},
		clean: function( selector ) {
			return jQuery( selector )[0];
		},
		errors: function() {
			return jQuery( this.settings.errorElement + "." + this.settings.errorClass, this.errorContext );
		},
		reset: function() {
			this.successList = [];
			this.errorList = [];
			this.errorMap = {};
			this.toShow = jQuery([]);
			this.toHide = jQuery([]);
			this.currentElements = jQuery([]);
		},
		prepareForm: function() {
			this.reset();
			this.toHide = this.errors().add( this.containers );
		},
		prepareElement: function( element ) {
			this.reset();
			this.toHide = this.errorsFor(element);
		},
		check: function( element ) {
			element = this.clean( element );
			if (this.checkable(element)) {
				element = this.findByName( element.name )[0];
			}
			var rules = jQuery(element).rules();
			var dependencyMismatch = false;
			for( method in rules ) {
				var rule = { method: method, parameters: rules[method] };
				try {
 					var result = jQuery.validator.methods[method].call( this, element.value.replace(/\r/g, ""), element, rule.parameters );
					if ( result == "dependency-mismatch" ) {
						dependencyMismatch = true;
						continue;
					}
					dependencyMismatch = false;
					if ( result == "pending" ) {
						this.toHide = this.toHide.not( this.errorsFor(element) );
						return;
					}
					if( !result ) {
						this.formatAndAdd( element, rule );
						return false;
					}
				} catch(e) {
					this.settings.debug && window.console && console.log("exception occured when checking element " + element.id
						 + ", check the '" + rule.method + "' method", e);
					throw e;
				}}
			if (dependencyMismatch)
				return;
			if ( this.objectLength(rules) )
				this.successList.push(element);
			return true;
		},
		customMetaMessage: function(element, method) {
			if (!jQuery.metadata)
				return;
			var meta = this.settings.meta
				? jQuery(element).metadata()[this.settings.meta]
				: jQuery(element).metadata();
			return meta && meta.messages && meta.messages[method];
		},
		customMessage: function( name, method ) {
			var m = this.settings.messages[name];
			return m && (m.constructor == String
				? m
				: m[method]);
		},
		findDefined: function() {
			for(var i = 0; i < arguments.length; i++) {
				if (arguments[i] !== undefined)
					return arguments[i];
			}
			return undefined;
		},
		defaultMessage: function( element, method) {
			return this.findDefined(
				this.customMessage( element.name, method ),
				this.customMetaMessage( element, method ),
				// title is never undefined, so handle empty string as undefined
				!this.settings.ignoreTitle && element.title || undefined,
				jQuery.validator.messages[method],
				"<strong>Warning: No message defined for " + element.name + "</strong>"
			);
		},
		formatAndAdd: function( element, rule ) {
			var message = this.defaultMessage( element, rule.method ),
				theregex = /\jQuery?\{(\d+)\}/g;
			if ( typeof message == "function" ) {
				message = message.call(this, rule.parameters, element);
			} else if (theregex.test(message)) {
				message = jQuery.format(message.replace(theregex, '{jQuery1}'), rule.parameters);
			}			
			this.errorList.push({
				message: message,
				element: element
			});
			this.errorMap[element.name] = message;
			this.submitted[element.name] = message;
		},
		addWrapper: function(toToggle) {
			if ( this.settings.wrapper )
				toToggle = toToggle.add( toToggle.parent( this.settings.wrapper ) );
			return toToggle;
		},
		defaultShowErrors: function() {
			for ( var i = 0; this.errorList[i]; i++ ) {
				var error = this.errorList[i];
				this.settings.highlight && this.settings.highlight.call( this, error.element, this.settings.errorClass, this.settings.validClass );
				this.showLabel( error.element, error.message );
			}
			if( this.errorList.length ) {
				this.toShow = this.toShow.add( this.containers );
			}
			if (this.settings.success) {
				for ( var i = 0; this.successList[i]; i++ ) {
					this.showLabel( this.successList[i] );
				}
			}
			if (this.settings.unhighlight) {
				for ( var i = 0, elements = this.validElements(); elements[i]; i++ ) {
					this.settings.unhighlight.call( this, elements[i], this.settings.errorClass, this.settings.validClass );
				}
			}
			this.toHide = this.toHide.not( this.toShow );
			this.hideErrors();
			this.addWrapper( this.toShow ).show();
		},
		validElements: function() {
			return this.currentElements.not(this.invalidElements());
		},
		
		invalidElements: function() {
			return jQuery(this.errorList).map(function() {
				return this.element;
			});
		},
		showLabel: function(element, message) {
			var label = this.errorsFor( element );
			if ( label.length ) {
				label.removeClass().addClass( this.settings.errorClass );
				label.attr("generated") && label.html(message);
			} else {
				label = jQuery("<" + this.settings.errorElement + "/>")
					.attr({"for":  this.idOrName(element), generated: true})
					.addClass(this.settings.errorClass)
					.html(message || "");
				if ( this.settings.wrapper ) {
					label = label.hide().show().wrap("<" + this.settings.wrapper + "/>").parent();
				}
				if ( !this.labelContainer.append(label).length )
					this.settings.errorPlacement
						? this.settings.errorPlacement(label, jQuery(element) )
						: label.insertAfter(element);
			}
			if ( !message && this.settings.success ) {
				label.text("");
				typeof this.settings.success == "string"
					? label.addClass( this.settings.success )
					: this.settings.success( label );
			}
			this.toShow = this.toShow.add(label);
		},
		errorsFor: function(element) {
			var name = this.idOrName(element);
    		return this.errors().filter(function() {
				return jQuery(this).attr('for') == name;
			});
		},
		idOrName: function(element) {
			return this.groups[element.name] || (this.checkable(element) ? element.name : element.id || element.name);
		},
		checkable: function( element ) {
			return /radio|checkbox/i.test(element.type);
		},
		findByName: function( name ) {
			var form = this.currentForm;
			return jQuery(document.getElementsByName(name)).map(function(index, element) {
				return element.form == form && element.name == name && element  || null;
			});
		},
		getLength: function(value, element) {
			switch( element.nodeName.toLowerCase() ) {
			case 'select':
				return jQuery("option:selected", element).length;
			case 'input':
				if( this.checkable( element) )
					return this.findByName(element.name).filter(':checked').length;
			}
			return value.length;
		},
		depend: function(param, element) {
			return this.dependTypes[typeof param]
				? this.dependTypes[typeof param](param, element)
				: true;
		},
		dependTypes: {
			"boolean": function(param, element) {
				return param;
			},
			"string": function(param, element) {
				return !!jQuery(param, element.form).length;
			},
			"function": function(param, element) {
				return param(element);
			}
		},
		optional: function(element) {
			return !jQuery.validator.methods.required.call(this, jQuery.trim(element.value), element) && "dependency-mismatch";
		},
		startRequest: function(element) {
			if (!this.pending[element.name]) {
				this.pendingRequest++;
				this.pending[element.name] = true;
			}
		},
		stopRequest: function(element, valid) {
			this.pendingRequest--;
			if (this.pendingRequest < 0)
				this.pendingRequest = 0;
			delete this.pending[element.name];
			if ( valid && this.pendingRequest == 0 && this.formSubmitted && this.form() ) {
				jQuery(this.currentForm).submit();
				this.formSubmitted = false;
			} else if (!valid && this.pendingRequest == 0 && this.formSubmitted) {
				jQuery(this.currentForm).triggerHandler("invalid-form", [this]);
				this.formSubmitted = false;
			}
		},
		previousValue: function(element) {
			return jQuery.data(element, "previousValue") || jQuery.data(element, "previousValue", {
				old: null,
				valid: true,
				message: this.defaultMessage( element, "remote" )
			});
		}
	},
	classRuleSettings: {
		required: {required: true},
		email: {email: true},
		url: {url: true},
		date: {date: true},
		dateISO: {dateISO: true},
		dateDE: {dateDE: true},
		number: {number: true},
		numberDE: {numberDE: true},
		digits: {digits: true},
		creditcard: {creditcard: true}
	},
	addClassRules: function(className, rules) {
		className.constructor == String ?
			this.classRuleSettings[className] = rules :
			jQuery.extend(this.classRuleSettings, className);
	},
	classRules: function(element) {
		var rules = {};
		var classes = jQuery(element).attr('class');
		classes && jQuery.each(classes.split(' '), function() {
			if (this in jQuery.validator.classRuleSettings) {
				jQuery.extend(rules, jQuery.validator.classRuleSettings[this]);
			}
		});
		return rules;
	},
	attributeRules: function(element) {
		var rules = {};
		var jQueryelement = jQuery(element);
		for (method in jQuery.validator.methods) {
			var value = jQueryelement.attr(method);
			if (value) {
				rules[method] = value;
			}
		}
		if (rules.maxlength && /-1|2147483647|524288/.test(rules.maxlength)) {
			delete rules.maxlength;
		}
		return rules;
	},
	metadataRules: function(element) {
		if (!jQuery.metadata) return {};
		var meta = jQuery.data(element.form, 'validator').settings.meta;
		return meta ?
			jQuery(element).metadata()[meta] :
			jQuery(element).metadata();
	},
	staticRules: function(element) {
		var rules = {};
		var validator = jQuery.data(element.form, 'validator');
		if (validator.settings.rules) {
			rules = jQuery.validator.normalizeRule(validator.settings.rules[element.name]) || {};
		}
		return rules;
	},
	normalizeRules: function(rules, element) {
		jQuery.each(rules, function(prop, val) {
			if (val === false) {
				delete rules[prop];
				return;
			}
			if (val.param || val.depends) {
				var keepRule = true;
				switch (typeof val.depends) {
					case "string":
						keepRule = !!jQuery(val.depends, element.form).length;
						break;
					case "function":
						keepRule = val.depends.call(element, element);
						break;
				}
				if (keepRule) {
					rules[prop] = val.param !== undefined ? val.param : true;
				} else {
					delete rules[prop];
				}
			}
		});
		jQuery.each(rules, function(rule, parameter) {
			rules[rule] = jQuery.isFunction(parameter) ? parameter(element) : parameter;
		});
		jQuery.each(['minlength', 'maxlength', 'min', 'max'], function() {
			if (rules[this]) {
				rules[this] = Number(rules[this]);
			}
		});
		jQuery.each(['rangelength', 'range'], function() {
			if (rules[this]) {
				rules[this] = [Number(rules[this][0]), Number(rules[this][1])];
			}
		});
		if (jQuery.validator.autoCreateRanges) {
			if (rules.min && rules.max) {
				rules.range = [rules.min, rules.max];
				delete rules.min;
				delete rules.max;
			}
			if (rules.minlength && rules.maxlength) {
				rules.rangelength = [rules.minlength, rules.maxlength];
				delete rules.minlength;
				delete rules.maxlength;
			}
		}
		if (rules.messages) {
			delete rules.messages;
		}
		return rules;
	},
	normalizeRule: function(data) {
		if( typeof data == "string" ) {
			var transformed = {};
			jQuery.each(data.split(/\s/), function() {
				transformed[this] = true;
			});
			data = transformed;
		}
		return data;
	},
	addMethod: function(name, method, message) {
		jQuery.validator.methods[name] = method;
		jQuery.validator.messages[name] = message != undefined ? message : jQuery.validator.messages[name];
		if (method.length < 3) {
			jQuery.validator.addClassRules(name, jQuery.validator.normalizeRule(name));
		}
	},
	methods: {
		required: function(value, element, param) {
			if ( !this.depend(param, element) )
				return "dependency-mismatch";
			switch( element.nodeName.toLowerCase() ) {
			case 'select':
				var val = jQuery(element).val();
				return val && val.length > 0;
			case 'input':
				if ( this.checkable(element) )
					return this.getLength(value, element) > 0;
			default:
				return jQuery.trim(value).length > 0;
			}
		},
		remote: function(value, element, param) {
			if ( this.optional(element) )
				return "dependency-mismatch";
			var previous = this.previousValue(element);
			if (!this.settings.messages[element.name] )
				this.settings.messages[element.name] = {};
			previous.originalMessage = this.settings.messages[element.name].remote;
			this.settings.messages[element.name].remote = previous.message;
			param = typeof param == "string" && {url:param} || param; 
			if ( previous.old !== value ) {
				previous.old = value;
				var validator = this;
				this.startRequest(element);
				var data = {};
				data[element.name] = value;
				jQuery.ajax(jQuery.extend(true, {
					url: param.url,
					mode: "abort",
					port: "validate" + element.name,
					dataType: "json",
					data: param.data || data,
					success: function(response) {
						if ( response ) {
							var submitted = validator.formSubmitted;
							validator.prepareElement(element);
							validator.formSubmitted = submitted;
							validator.showErrors();
						} else {
							var errors = {};
 							 errors[element.name] =  response || validator.defaultMessage( element, "remote" );
 							errors[element.name] = response.Msg;  
                            validator.showErrors(errors);  

						}
						  previous.message = response.Msg; //previous.valid = response;  
					      previous.valid = response.Result;  
						  if(response.Result){
							  validator.successList.push(element);	
						   }else{
							   var ts = new Object;
							    ts.message=response.Msg;
							  ts.element=element;
							  validator.errorList.push(ts);  
						   }
 					      validator.stopRequest(element, response.Result);  
					}
				}, param));
				return "pending";
			} else if( this.pending[element.name] ) {
				return "pending";
			}
			return previous.valid;
		},
		minlength: function(value, element, param) {
			return this.optional(element) || this.getLength(jQuery.trim(value), element) >= param;
		},
		maxlength: function(value, element, param) {
			return this.optional(element) || this.getLength(jQuery.trim(value), element) <= param;
		},
		rangelength: function(value, element, param) {
			var length = this.getLength(jQuery.trim(value), element);
			return this.optional(element) || ( length >= param[0] && length <= param[1] );
		},
		min: function( value, element, param ) {
			return this.optional(element) || value >= param;
		},
		max: function( value, element, param ) {
			return this.optional(element) || value <= param;
		},
		range: function( value, element, param ) {
			return this.optional(element) || ( value >= param[0] && value <= param[1] );
		},
		email: function(value, element) {
			return this.optional(element) || /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test($.trim(value));
			//return this.optional(element) || /^((([a-z]|\d|[!#\$%&'\*\+\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test($.trim(value));
		},
		url: function(value, element) {
			return this.optional(element) || /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
		},
		date: function(value, element) {
			return this.optional(element) || !/Invalid|NaN/.test(new Date(value));
		},
		dateISO: function(value, element) {
			return this.optional(element) || /^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(value);
		},
		number: function(value, element) {
			return this.optional(element) || /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(value);
		},
		digits: function(value, element) {
			return this.optional(element) || /^\d+$/.test(value);
		},
		creditcard: function(value, element) {
			if ( this.optional(element) )
				return "dependency-mismatch";
			if (/[^0-9-]+/.test(value))
				return false;
			var nCheck = 0,
				nDigit = 0,
				bEven = false;
			value = value.replace(/\D/g, "");
			for (var n = value.length - 1; n >= 0; n--) {
				var cDigit = value.charAt(n);
				var nDigit = parseInt(cDigit, 10);
				if (bEven) {
					if ((nDigit *= 2) > 9)
						nDigit -= 9;
				}
				nCheck += nDigit;
				bEven = !bEven;
			}
			return (nCheck % 10) == 0;
		},
		accept: function(value, element, param) {
			param = typeof param == "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
			return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i")); 
		},
		equalTo: function(value, element, param) {
			var target = jQuery(param).unbind(".validate-equalTo").bind("blur.validate-equalTo", function() {
				jQuery(element).valid();
			});
			return value == target.val();
		}
	}
});
jQuery.format = jQuery.validator.format;
})(jQuery);
;(function(jQuery) {
	var ajax = jQuery.ajax;
	var pendingRequests = {};
	jQuery.ajax = function(settings) {
		settings = jQuery.extend(settings, jQuery.extend({}, jQuery.ajaxSettings, settings));
		var port = settings.port;
		if (settings.mode == "abort") {
			if ( pendingRequests[port] ) {
				pendingRequests[port].abort();
			}
			return (pendingRequests[port] = ajax.apply(this, arguments));
		}
		return ajax.apply(this, arguments);
	};
})(jQuery);
;(function(jQuery) {
	if (!jQuery.event.special.focusin && !jQuery.event.special.focusout && document.addEventListener) {
		jQuery.each({
			focus: 'focusin',
			blur: 'focusout'	
		}, function( original, fix ){
			jQuery.event.special[fix] = {
				setup:function() {
					this.addEventListener( original, handler, true );
				},
				teardown:function() {
					this.removeEventListener( original, handler, true );
				},
				handler: function(e) {
					arguments[0] = jQuery.event.fix(e);
					arguments[0].type = fix;
					return jQuery.event.handle.apply(this, arguments);
				}
			};
			function handler(e) {
				e = jQuery.event.fix(e);
				e.type = fix;
				return jQuery.event.handle.call(this, e);
			}
		});
	};
	jQuery.extend(jQuery.fn, {
		validateDelegate: function(delegate, type, handler) {
			return this.bind(type, function(event) {
				var target = jQuery(event.target);
				if (target.is(delegate)) {
					return handler.apply(target, arguments);
				}
			});
		}
	});
})(jQuery);
jQuery.validator.addMethod("byteRangeLength", function(value, element, param) {    
  var length = value.length;    
  for(var i = 0; i < value.length; i++){    
   if(value.charCodeAt(i) > 127){    
    length++;    
   }    
  }    
  return this.optional(element) || ( length >= param[0] && length <= param[1] );    
}, "请确保输入的值在3-15个字节之间(一个中文字算2个字节)");    
jQuery.validator.addMethod("isIdCardNo", function(value, element) {    
  return this.optional(element) || isIdCardNo(value);    
}, "请正确输入您的身份证号码");    
jQuery.validator.addMethod("isUserName", function(value, element) {    
  return this.optional(element) || /^[\u0391-\uFFE5\w]+$/.test(value);    
}, "用户名只能包括中文字、英文字母、数字和下划线");    
jQuery.validator.addMethod("isMobile", function(value, element) {    
  var length = value.length;    
  return this.optional(element) || (length == 11 && /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/.test(value));    
}, "请正确填写您的手机号码");    
jQuery.validator.addMethod("isPhone", function(value, element) {    
  var tel = /^(\d{3,4}-?)?\d{7,9}$/g;    
  return this.optional(element) || (tel.test(value));    
}, "请正确填写您的电话号码");    
jQuery.validator.addMethod("isZipCode", function(value, element) {    
  var tel = /^[0-9]{6}$/;    
  return this.optional(element) || (tel.test(value));    
}, "请正确填写您的邮政编码");    
jQuery.validator.addMethod("tbpp", function(value, element) {    
  var g=true;
  var chack_tb = /^[Tt][Bb]_/;
  var chack_pp = /^[Pp][Pp]_/;
  if(chack_tb.test(value) || chack_pp.test(value)){
		 g=false
 	}
   return this.optional(element) || g;    
}, "错误的用户输入");   
jQuery.validator.addMethod("MobileOrPhone", function(value, element) {
	if(jQuery("#phone").val()=='' && jQuery("#userFixedPhone").val()==''){   
		tg= false;   
	}else{   
		tg= true;   
	}   
	return tg;
},  "固定电话和手机必须填写一个");
jQuery.validator.addMethod("TelAll", function(value, element) {
//	if(jQuery("#userFixedPhone").val()!=''){ 
//		if(jQuery("#userFixedPhone").val()==''){
//			tg=false;
//		}else{
//			tg=true; 
//		}
//	}else{   
//		tg=true;   
//	}   
//	return tg;
	var tel = /^(([0\+]\d{2,3}-)?(0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$/g;    
	return this.optional(element) || (tel.test(value));    
},  "固定电话必须填写完全");
 jQuery.validator.addMethod("string", function(value, element) {   
        return this.optional(element) ||  /^[^\|"'<>\[\]\{\}\$\*\.]*$/.test(value); 
     }, "不允许包含特殊符号!");   
function GetRemoteInfo(postUrl, data) { 
      var remote = {  
         type: "POST",  
 	     async: false,  
         url: postUrl,  
         dataType: "",  
         data: data, 
         dataFilter: function(data) {
    	  	var result = new Object();  
    	  	data = eval('(' + data + ')');
            result.Result = data['status']['statusCode'];  
            result.Msg = data['status']['status'];; 
               if (result.Result == "-1") {  
                result.Result = false;  
                return result;  
            }  
            else {  
                result.Result = result.Result == "1" ? true : false;  
                return result;  
            }
        }  
    };  
   return remote;  
 } 
 function DIYMethod(){   
    jQuery.validator.addMethod("stringMinLength", function(value, element, param) {   
        var length = value.length;   
         for ( var i = 0; i < value.length; i++) {   
             if (value.charCodeAt(i) > 127) {   
                 length++;   
            }   
         }   
         return this.optional(element) || (length >= param);   
    }, jQuery.validator.format("长度不能小于{0}!"));   
     jQuery.validator.addMethod("stringMaxLength", function(value, element, param) {   
         var length = value.length;   
        for ( var i = 0; i < value.length; i++) {   
           if (value.charCodeAt(i) > 127) {   
                 length++;   
             }   
         }   
         return this.optional(element) || (length <= param);   
     }, jQuery.validator.format("长度不能大于{0}!"));   
     jQuery.validator.addMethod("string", function(value, element) {   
        return this.optional(element) || /^[\u0391-\uFFE5\w]+$/.test(value);   
     }, "不允许包含特殊符号!");   
    jQuery.validator.addMethod("byteRangeLength", function(value, element, param) {   
        var length = value.length;   
         for(var i = 0; i < value.length; i++){   
             if(value.charCodeAt(i) > 127){   
                length++;   
            }   
         }   
        return this.optional(element) || ( length >= param[0] && length <= param[1] );   
    }, "请确保输入的值在3-15个字节之间(一个中文字算2个字节)");   
     jQuery.validator.addMethod("stringCH", function(value, element) {   
        var length = value.length;   
        for(var i = 0; i < value.length; i++){   
            if(value.charCodeAt(i) > 127){   
                 length++;   
            }   
         }   
         return this.optional(element) || /[^u4E00-u9FA5]/g.test(value);   
     }, "只能输入汉字,一个汉字占两具字节");   
     jQuery.validator.addMethod("stringEN", function(value, element) {   
        var length = value.length;   
        for(var i = 0; i < value.length; i++){   
            if(value.charCodeAt(i) > 127){   
                 length++;   
           }   
         }   
        return this.optional(element) || /^[A-Za-z]+$/g.test(value);   
     }, "只能输入字母");   
 }  