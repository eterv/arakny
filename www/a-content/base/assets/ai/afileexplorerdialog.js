/**
 * Arakny File Explorer Dialog
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

function AFileExplorerDialog(selector, options)
{
	var obj = this;

	// 옵션값 통합
	this.options = {
		// 데이터 통신 URL 주소
		urlFileDelete: '/file/delete',
		urlFileList: '/file/list/f',
		urlUpload: '/file/upload/f',

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

		reset: null,

		onApprove: null,

		fileType: '',
		maxFileSize: 2 * 1024 * 1024,
		multiSelectMode: false,

		// 선택자

		checkboxSelector: '.chk',

		// 언어
		language: 'ko'
	};
	$.extend(this.options, options);

	var ele = this.ele = (typeof selector === 'string') ? document.querySelector(selector) : selector;
	var $ele = $(ele);
	//var items = ele.querySelectorAll('.item');

	var selectedItems;

	this.show = function (cbApprove) {
		selectedItems = [];
		$ele.modal('show');
	};

	this.getSelectedItems = function () {
		return selectedItems;
	};

	var _createElement = function () {
		ele.classList.add('ai');
		ele.classList.add('file-explorer-d');
		ele.classList.add('ui');
		ele.classList.add('tiny');
		ele.classList.add('modal');

		ele.innerHTML = '<div class="header">File Explorer</div>' +
			'<div class="content"></div>' +
			'<div class="actions"><div class="ui cancel button">취소</div><div class="ui ok positive button fw-light">확인</div></div>';

		var elContent = ele.querySelector('.content');
		var elAFileList = document.createElement('div');
		elAFileList.style.height = '300px';
		elAFileList.style.maxHeight = '100vh';
		elContent.appendChild(elAFileList);

		var aFileList = new AFileList(elAFileList, {
			canChangeFiletypeFilter: false,
			canChangeMultiSelectMode: false,
			fileTypeFilter: obj.options.fileType,
			viewMode: true
		});
		aFileList.multiSelectMode = obj.options.multiSelectMode;

		$(ele).modal({
			allowMultiple: true,
			closable: false,
			autofocus: false,
			restoreFocus: false,
			onDeny: function () {

			},
			onApprove: function () {
				selectedItems = aFileList.getSelectedItems();
				if (typeof obj.options.onApprove === 'function') {
					obj.options.onApprove.call(obj, selectedItems);
				}
			}
		});

	};

	_createElement();
}