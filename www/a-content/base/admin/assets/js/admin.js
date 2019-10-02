/**
 * Arakny Admin Base Script
 */

/* -------------------------------------------------------------------------------- */
/* 		Write Modal
/* -------------------------------------------------------------------------------- */

var WriteModalMode = Object.freeze({ 'Add': 1, 'Edit': 2, 'Delete': 3 });

function WriteModal(options) {
	var obj = this;

	// 옵션값 통합
	this.options = {
		// 모달 창 선택자
		selector: '#mo-write',

		// 모달 레이어 제목
		title: '',

		// 데이터 통신 URL 주소
		urlAdd: '',
		urlEdit: '',
		urlDelete: '',
		urlGetData: '',

		// 이벤트

		canAdd: true,
		canEdit: true,
		canDelete: true,

		beforeSubmit: null,
		beforeSubmitInAddMode: null,
		beforeSubmitInEditMode: null,
		beforeSubmitInDeleteMode: null,

		addSuccess: null,
		editSuccess: null,
		deleteSuccess: null,

		openAdd: null,
		openEdit: null,
		openDelete: null,

		reset: null,
	};
	$.extend(this.options, options);

	var elModal = document.querySelector(obj.options.selector);
	var $modal = this.$modal = $(elModal);
	var $header = $modal.find('.header');
	var form = this.form = elModal.querySelector('form');

	var _mode = 0;
	var _id = 0;
	var result = false;

	$modal.modal({
		keyboardShortcuts: true,

		// 확인 (OK)
		onApprove: function () {
			if (typeof obj.options.beforeSubmit === 'function') {
				result = obj.options.beforeSubmit.call(obj, _id);
				if (result) return true;
			}

			switch (_mode) {
				case WriteModalMode.Add:		// 추가
					if (typeof obj.options.beforeSubmitInAddMode === 'function') {
						result = obj.options.beforeSubmitInAddMode.call(obj);
						if (result) return true;
					}

					// 추가 - AJAX
					ajaxPost(_getUrl(obj.options.urlAdd, _id), form, null,
						function (data) {
							var nid = (typeof data.id === 'undefined') ? 0 : data.id;
							if (typeof obj.options.addSuccess === 'function') {
								obj.options.addSuccess.call(obj, nid, data);
							}
							obj.close();
						});

					break;

				case WriteModalMode.Edit:		// 수정
					if (typeof obj.options.beforeSubmitInEditMode === 'function') {
						result = obj.options.beforeSubmitInEditMode.call(obj, _id);
						if (result) return true;
					}

					// 수정 - AJAX
					ajaxPost(_getUrl(obj.options.urlEdit, _id), form, null,
						function (data) {
							if (typeof obj.options.editSuccess === 'function') {
								obj.options.editSuccess.call(obj, _id, data);
							}
							obj.close();
						});

					break;

				case WriteModalMode.Delete:		// 삭제
					if (typeof obj.options.beforeSubmitInDeleteMode === 'function') {
						result = obj.options.beforeSubmitInDeleteMode.call(obj, _id);
						if (result) return true;
					}

					// 삭제 - AJAX
					ajaxPost(_getUrl(obj.options.urlDelete, _id), form, null,
						function (data) {
							if (typeof obj.options.editSuccess === 'function') {
								obj.options.editSuccess.call(obj, _id, data);
							}
							obj.close();
						});

					break;

				default:
					break;
			}
			return false;
		},
		
		// 모달 레이어가 닫힐 때의 이벤트
		onHide: function () {
			// 모달 데이터 초기화
			setTimeout(_reset, 200);
		}
	});

	/**
	 * 추가 모달 레이어 열기
	 */
	this.add = function () {
		if (_.isBoolean(obj.options.canAdd) && ! obj.options.canAdd) return false;
		else if (_.isFunction(obj.options.canAdd) && ! obj.options.canAdd.call(obj)) return false;

		_mode = WriteModalMode.Add;
		_setTitle();
		//_id = id;		// 메뉴의 경우 부모의 값을 전달해야 하기 때문에 필요할 수 있다.

		if (typeof obj.options.openAdd === 'function') {
			obj.options.openAdd.call(obj);
		}

		$modal.modal('show');
	};

	/**
	 * 수정 모달 레이어 열기
	 */
	this.edit = function (id) {
		if (_.isBoolean(obj.options.canEdit) && ! obj.options.canEdit) return false;
		else if (_.isFunction(obj.options.canEdit) && ! obj.options.canEdit.call(obj, id)) return false;

		_mode = WriteModalMode.Edit;
		_setTitle();
		_id = id;

		ajaxGet(_getUrl(obj.options.urlGetData, id), null,
			function (data) {

				var selector = form.querySelectorAll('input[type=hidden], input[type=text], select');
				forEach(selector, function (ele, i) {
					if (typeof data.row[ele.name] !== 'undefined') {
						ele.value = data.row[ele.name];
					}

					if (ele.parentNode.classList.contains('dropdown')) {
						ai.Dropdown.init(ele.parentNode).refresh();
					}
				});

				if (typeof obj.options.openEdit === 'function') {
					obj.options.openEdit.call(obj, id, data);
				}
			});

		$modal.modal('show');
	};

	/**
	 * 삭제 모달 레이어 열기
	 */
	this.delete = function (id, opt) {
		if (_.isBoolean(obj.options.canDelete) && ! obj.options.canDelete) return false;
		else if (_.isFunction(obj.options.canDelete) && ! obj.options.canDelete.call(obj, id)) return false;

		_mode = WriteModalMode.Delete;
		_id = id;

		if (typeof obj.options.openDelete === 'function') {
			obj.options.openDelete.call(obj, id);
		}

		var def_opt = {
			text: ''
		};
		$.extend(def_opt, opt);

		swalConfirm(_t('_.Delete'), _t('Q.Delete', [def_opt.text]), null, function (result) {
			// 삭제 - AJAX
			ajaxPost(_getUrl(obj.options.urlDelete, _id), form, null,
				function (data) {
					if (typeof obj.options.deleteSuccess === 'function') {
						obj.options.deleteSuccess.call(obj, _id, data);
					}
					obj.close();
				});
		});
	};

	/**
	 * 모달 레이어 닫기
	 */
	this.close = function () {
		$modal.modal('hide');
	};

	/* -------------------------------------------------------------------------------- */
	/* 		Private Methods
	/* -------------------------------------------------------------------------------- */

	/**
	 * id 와 같은 템플릿을 합병하여 URL 주소를 가져온다.
	 */
	var _getUrl = function (url, nid) {
		return url.replace('{id}', nid);
	};

	/**
	 * 모달 제목을 설정한다.
	 */
	var _setTitle = function () {
		switch (_mode) {
			case WriteModalMode.Add:
				$header.text(obj.options.title + ' - ' + _t('_.Add'));
				break;
			case WriteModalMode.Edit:
				$header.text(obj.options.title + ' - ' + _t('_.Edit'));
				break;
			case WriteModalMode.Delete:
				$header.text(obj.options.title + ' - ' + _t('_.Delete'));
				break;
			default:
				break;
		}
	};

	/**
	 * 모달 데이터를 초기화한다.
	 */
	var _reset = function () {
		_mode = 0;
		_id = 0;
		form.reset();

		if (typeof obj.options.reset === 'function') {
			obj.options.reset.call(obj, form);
		}
	}

}