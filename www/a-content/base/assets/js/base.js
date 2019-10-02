/**
 * Arakny Base Theme Script
 */

if (!String.prototype.nl2br) {
	String.prototype.nl2br = function () {
		return this.replace(/\n/g, "<br>");
	};
}

if (!String.prototype.format) {
	String.prototype.format = function() {
		var args = arguments;
		return this.replace(/{(\d+)}/g, function(match, number) {
			return typeof args[number] != 'undefined' ? args[number] : match;
		});
	};
}
if (!String.format) {
	String.format = function (format) {
		var args = Array.prototype.slice.call(arguments, 1);
		return format.replace(/{(\d+)}/g, function(match, number) {
			return typeof args[number] != 'undefined' ? args[number] : match;
		});
	};
}

/**
 * 	Element.closest Polyfill (for IE9+)
 */
if (!Element.prototype.matches) {
	Element.prototype.matches = Element.prototype.msMatchesSelector ||
		Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
	Element.prototype.closest = function(s) {
		let el = this;
		do {
			if (el.matches(s)) return el;
			el = el.parentElement || el.parentNode;
		} while (el !== null && el.nodeType === 1);
		return null;
	};
}

if (window.NodeList && !NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}


function _url(uri, onlyPath) {
	if (typeof onlyPath === 'undefined') onlyPath = true;
	return onlyPath
		? BASEURL_ONLYPATH + uri
		: BASEURL + uri;
}

function byId(id) {
	return document.getElementById(id);
}

function formField(field_name, form) {
	if (_.isUndefined(form)) form = document;
	return form.querySelector('[name="' + field_name + '"]');
}

(function ($) {
	$.formField = function (field_name, form) {
		if (_.isUndefined(form)) form = $('body');
		return form.find('[name="' + field_name + '"]');
	};
})(jQuery);

// forEach method, could be shipped as part of an Object Literal/Module
var forEach = function (array, callback, scope) {
	for (var i = 0; i < array.length; i++) {
		callback.call(scope, array[i], i); // passes back stuff we need
	}
};

/**
 * Input Delay Debounce Callback
 */
var onInputDelayFunc = function (callback, interval) {
	if (typeof interval === 'undefined' || interval === null) interval = 350;
	return _.debounce(callback, interval);
};

/**
 * Fomantic UI - Toast
 */
function toastError(message, displayTime) {
	$('body').toast({
		class: 'error',
		message: message,
		displayTime: (_.isUndefined(displayTime) ? 2000 : displayTime),
		showProgress: 'bottom'
	});
}

function toastSuccess(message, displayTime) {
	$('body').toast({
		class: 'success',
		message: message,
		displayTime: (_.isUndefined(displayTime) ? 1500 : displayTime),
		showProgress: 'bottom'
	});
}

function toastWarning(message, displayTime) {
	$('body').toast({
		class: 'warning',
		message: message,
		displayTime: (_.isUndefined(displayTime) ? 2000 : displayTime),
		showProgress: 'bottom'
	});
}

/**
 * Fomantic UI - Form Validation - Extend & Default Fields Settings
 */

$.fn.form.settings.defaults = {
	s_name: { rules: 'empty|maxLength[100]' },
	s_desc: { rules: 'empty' },
	s_locale: { rules: 'empty' },
	s_admin_locale: { rules: 'empty' },
	s_admin_email: { rules: 'empty|email' },

	u_login: { rules: [ 'empty', 'minLength[2]', 'maxLength[100]', 'regExp[/^[a-zA-Z0-9@._-]{2,100}$/]' ] },
	u_pass: { rules: [ 'empty', 'minLength[5]', "regExp[/^[A-Za-z0-9`~!@#$%^&*()=+\\\\|{}[\\];:,<.>/?'&quot;_-]{5,100}$/]" ] },
	// [A-Za-z0-9`~!@#$%^&*()=+\\|{}[\];:,<.>/?'&quot;_-]{5,100}
	u_pass_check: { rules: [ 'empty', [ 'match[u_pass]', $.fn.form.settings.prompt.matchPassword ] ] },
	u_name: { rules: 'empty|minLength[2]|maxLength[100]' },
	u_email: { rules: 'empty|email' },
};
function initFormValidationSettings(fields) {
	if (typeof fields === 'undefined') fields = $.fn.form.settings.defaults;

	var rulearr, data;
	for (var prop in fields) {
		if (Array.isArray(fields[prop].rules)) {
			rulearr = fields[prop].rules;
		} else  {
			rulearr = fields[prop].rules.split('|');
		}

		fields[prop].rules = [];
		rulearr.forEach(function (value) {
			if (Array.isArray(value)) {
				data = { type: value[0] };
				if (value.length >= 2) data.prompt = value[1];

				fields[prop].rules.push(data);

			} else {
				fields[prop].rules.push({ type: value });
			}
		});
	}
	return fields;
}
$.fn.form.settings.defaults = initFormValidationSettings();


/**
 * 	SweetAlert Helper
 */

function swalAlert(title, text, type) {
	if (typeof type === 'undefined' || type === null) type = 'warning';

	Swal.fire({
		title: title,
		text: text,
		type: type,
	});
}
function swalError(title, text) {	swalAlert(title, text, 'error'); }
function swalSuccess(title, text) { swalAlert(title, text, 'success'); }

function swalConfirm(title, text, type, cbPositive, cbNegative, options) {
	if (typeof type === 'undefined' || type === null) type = 'warning';

	var defOptions = {
		title: title,
		text: text,
		type: type,
		showCancelButton: true,
		cancelButtonText: _t('_.Cancel'),
		confirmButtonText: _t('_.OK')
	};
	$.extend(defOptions, options);

	Swal.fire(defOptions).then(function (result) {
		if (result.value) {
			if (typeof cbPositive === 'function') cbPositive.call(this, result);
		} else {
			if (typeof cbNegative === 'function') cbNegative.call(this, result);
		}
	});
}

function swalConfirmYN(title, text, type, cbPositive, cbNegative, options) {
	var defOptions = {
		cancelButtonText: _t('_.No'),
		confirmButtonText: _t('_.Yes')
	};
	$.extend(defOptions, options);
	swalConfirm(title, text, type, cbPositive, cbNegative, defOptions);
}


/**
 *	CSRF Token
 */
function getCSRFKey() {
	return document.querySelector('meta[name="sec-name"]').getAttribute('content');
}
function getCSRFValue() {
	return document.querySelector('meta[name="sec-value"]').getAttribute('content');
}
function getCSRFData() {
	var sec_name = document.querySelector('meta[name="sec-name"]').getAttribute('content');
	var sec_value = document.querySelector('meta[name="sec-value"]').getAttribute('content');
	var data = {};
	data[sec_name] = sec_value;
	return data;
}
function getCSRFFormData(formData) {
	var sec_name = document.querySelector('meta[name="sec-name"]').getAttribute('content');
	var sec_value = document.querySelector('meta[name="sec-value"]').getAttribute('content');
	formData.append(sec_name, sec_value);
	return formData;
}


/**
 * 	jQuery AJAX Proxy Functions
 * 	(쉽게 AJAX 를 사용하도록 구현한 함수)
 */

function ajax(url, method, data, config, cb_done, cb_fail, debug) {
	if (typeof debug === 'undefined') debug = false;

	var opt = {
		url: url,
		method: method
	};

	// POST 메소드이면서 data 가 없거나, 객체 형식이라면 CSRF 필드를 추가한다.
	if (method.toLowerCase() === 'post') {
		if (typeof data === 'undefined' || data === null) {
			data = $.param(getCSRFData());

		} else if (data instanceof FormData) {
			data = getCSRFFormData(data);
			opt.contentType = false;
			opt.processData = false;

		} else if (data instanceof HTMLFormElement) {
			var k = getCSRFKey(), v = getCSRFValue();
			if (data.querySelector('input[name="'+ k +'"]') === null) {
				data.insertAdjacentHTML('beforeend', '<input type="hidden" name="' + k + '" value="' + v + '">');
			}
			data = $(data).serialize();

		} else if (typeof data === 'object' && data != null) {
			$.extend(true, data, getCSRFData());

		}
	}
	opt.data = data;

	if (typeof config === 'undefined' || config === null) config = {};
	$.extend(true, opt, config);

	try {
		var request = $.ajax(opt);
	} catch (e) {
	}

	return request.then(
		function (content, textStatus, jqXHR) {
			if (content.result === 'success') {
				if (typeof cb_done === 'function') {
					return cb_done.call(request, content.data, jqXHR, textStatus);
				}
			} else {
				return false;
			}
		},
		function (jqXHR, textStatus, errorThrown) {
			var result = false;
			var content;
			try {
				content = JSON.parse(jqXHR.responseText);
			} catch (e) {
				if (debug) console.log('Error', textStatus, errorThrown);
				return false;
			}

			if (content.result === 'failure') {
				if (typeof cb_fail === 'function') {
					result = cb_fail.call(request, content, jqXHR);
				} else {
					toastError(content.message);
				}

				if (debug) console.log('Error', content.message);

			} else if (typeof content.code !== 'undefined') {
				result = cb_fail.call(request, content, jqXHR);
			}

			return result;
		});
}
function ajaxGet(url, config, cb_done, cb_fail, debug) {
	return ajax(url, 'GET', undefined, config, cb_done, cb_fail, debug);
}
function ajaxPost(url, data, config, cb_done, cb_fail, debug) {
	return ajax(url, 'POST', data, config, cb_done, cb_fail, debug);
}


/**
 * 	Arakny L10n (JS ver)
 */

function _t(id, args) {
	var test;
	try {
		test = eval('L.' + id);
		if (typeof test === 'undefined') return id;
	} catch (e) {
		return id;
	}

	return test.replace(/{(\d+)}/g, function(match, number) {
		return typeof args[number] !== 'undefined' ? args[number] : match;
	});
}

function alert_t(id, args) {
	alert( _t(id, args) );
}

function toastError_t(id, args, displayTime) { toastError(_t(id, args), displayTime); }
function toastSuccess_t(id, args, displayTime) { toastSuccess(_t(id, args), displayTime); }
function toastWarning_t(id, args, displayTime) { toastWarning(_t(id, args), displayTime); }


/**
 * 	Parsley Helper
 */
/*
Parsley.addValidator('equaltoPassword', {
	validateString: function(value, attr) {
		return document.querySelector(attr).value === value;
	}
});

Parsley.addValidator('patternLogin', {
	validateString: function(value) {
		var regex = /^[a-zA-Z0-9@._-]*$/;
		return regex.test(value);
	}
});

Parsley.addValidator('unique', {
	validateString: function(value, attr, instance) {
		var url = _url(attr);
		var data = { field: instance.element.name, value: value };
		return ajaxPost(url, data, {},
			function (result) {
				if (! result.isUnique) throw('');
			},
			function (content) {
				throw(content.message);
			});
	}
});
*/

/**
 * 	TinyMCE Loader
 */

function tinymceLoader(options) {
	// Language 가져오기
	var lang = $('html').attr('lang').replace('-', '_');
	if (typeof lang === 'undefined') lang = 'en_US';

	// pagetype 가져오기
	if (typeof options.ai_page_type === 'undefined' || typeof options.ai_page_id === 'undefined') {
		console.log('tinymce setting error!'); return;
	}
	var pagetype = options.ai_page_type;
	var pageid = options.ai_page_id;
	var urlUpload = '/file/upload/' + pagetype;

	// AImageUploader 요소 생성
	var elImageUploader = document.createElement('div');

	// AFileExplorer 요소 생성
	var elFileExplorer = document.createElement('div');

	var opt = {
		height: 500,
		language: lang,
		plugins: [
			'advlist autosave code codesample emoticons image link lists media',
			'paste save searchreplace table',
			'arakny'
		],
		contextmenu: '',
		toolbar: 'fontsizeselect | bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | '
			+ 'link image media araknyimage',
		/*mobile: {
			theme: 'silver',
			plugins: 'image arakny',
			toolbar: 'link image araknyimage'
		},*/

		relative_urls: false,

		image_advtab: false,
		images_upload_url: urlUpload,
		images_upload_handler: function (blobinfo, success, failure) {
			var fd = new FormData();
			fd.append('pageid', pageid);
			fd.append('file', blobinfo.blob(), blobinfo.filename());

			// AJAX 업로드
			ajaxPost(urlUpload, fd, null,
				function (data) {
					var item = data.item;

					success(item.url);
				});
		},

		media_alt_source: false,
		media_poster: false,
		file_picker_types: 'image',
		file_picker_callback: function (cb, value, meta) {
			var aFileExplorer = new AFileExplorerDialog(elFileExplorer, {
				fileType: 'i',
				onApprove: function (selectedItems) {
					selectedItems.forEach(function (el) {
						cb(el.dataset.url, { title: el.dataset.origName });
					});
				}
			});
			aFileExplorer.show();
		},

		ai_image_uploader_selector: elImageUploader,
		ai_image_uploader_options: { },
	};

	$.extend(opt, options);

	tinymce.init(opt);
}