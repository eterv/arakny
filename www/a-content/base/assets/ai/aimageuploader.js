/**
 * Arakny Image Uploader
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

function AImageUploader(selector, options)
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


		onApprove: null,

		maxFileSize: 2 * 1024 * 1024,

		pageId: 0
	};
	$.extend(this.options, options);

	var ele = this.ele = (typeof selector === 'string') ? document.querySelector(selector) : selector;
	var $ele = $(ele);
	var elImageList;
	var items = [];

	var elDimmer, elLoader;

	var countUploadFiles = 0;

	this.show = function () {
		$ele.modal('show');
	};

	var _createElement = function () {
		ele.classList.add('ai');
		ele.classList.add('image-uploader');
		ele.classList.add('ui');
		ele.classList.add('tiny');
		ele.classList.add('modal');

		ele.innerHTML = '<div class="header">이미지 첨부하기</div>' +
			'<div class="content"></div>' +
			'<div class="actions"><div class="ui cancel button">취소</div><div class="ui positive button fw-light btn-ok">확인</div></div>';

		var elContent = ele.querySelector('.content');
		elContent.innerHTML = '<div class="ui compact icon buttons"><button class="ui primary button btn-device" title="Browse file on Device"><i class="desktop icon"></i></button>' +
			'<button class="ui button btn-file-explorer" title="Browse files on File Explorer"><i class="browser icon"></i></button></div>' +
			'<div class="imagelist-wrap"><div class="imagelist"></div></div>' +
			'<div class="desc">드래그하여 순서를 바꿀 수 있습니다.<br>이미지는 한번에 최대 10개까지 첨부가 가능합니다.</div>';

		elImageList = elContent.querySelector('.imagelist');

		new Sortable(elImageList, {
			animation: 150,
			ghostClass: 'ghost-item',
			onUpdate: function () {
				items = elImageList.querySelectorAll('.item');
			}
		});

		var elAFileExplorerDialog = document.createElement('div');

		// 버튼 이벤트
		elContent.querySelector('.btn-device').addEventListener('click', function () {
			var eFile = document.createElement('input');
			eFile.type = 'file';
			eFile.accept = 'image/*';
			eFile.multiple = true;

			eFile.addEventListener('change', function () {
				//countUploadFiles = this.files.length;
				//if (! dimmer.classList.contains('active')) dimmer.classList.add('active');

				for (var i = 0; i < this.files.length; i++) {
					if (this.files[i].size > obj.options.maxFileSize) {
						alert('[' + this.files[i].name + '] file is too big. You can\'t upload this file.');
						continue;
					}

					(function (file) {
						var reader = new FileReader();
						reader.addEventListener('load', function () {
							_addItem(reader.result, file.name);
						});
						reader.readAsDataURL(file);

					})(this.files[i]);
				}
			});

			eFile.click();
		});

		elContent.querySelector('.btn-file-explorer').addEventListener('click', function () {
			var aFileExplorer = new AFileExplorerDialog(elAFileExplorerDialog, {
				fileType: 'i',
				multiSelectMode: true,
				onApprove: function (selectedItems) {
					selectedItems.forEach(function (el) {
						_addItem(el.dataset.url, el.dataset.origName);
					});
				}
			});
			aFileExplorer.show();
		});

		ele.querySelector('.btn-ok').addEventListener('click', function (e) {
			//e.stopPropagation();
		});

		$ele.modal({
			allowMultiple: true,
			closable: false,
			restoreFocus: false,
			onDeny: function () { },
			onApprove: function () {
				countUploadFiles = items.length;
				if (! elDimmer.classList.contains('active')) elDimmer.classList.add('active');

				items.forEach(function (el) {
					_uploadImage(el);
				});

				return false;
			}
		});

		// dimmer, loader 요소
		elDimmer = document.createElement('div');
		elDimmer.className = 'ui inverted dimmer';
		elContent.appendChild(elDimmer);

		elLoader = document.createElement('div');
		elLoader.className = 'ui text loader';
		elLoader.innerText = _t('M.Uploading');
		elDimmer.appendChild(elLoader);
	};

	var _addItem = function (url, origName) {
		var elItem = document.createElement('div');
		elItem.className = 'item';
		elItem.innerHTML = '<div class="item-wrap"><div class="image"></div><div class="delete"><i class="close icon"></i></div></div>';

		elItem.dataset.url = url;
		elItem.dataset.origName = origName;

		var elImage = elItem.querySelector('.image');
		elImage.style.backgroundImage = 'url("' + url + '")';

		var elDelete = elItem.querySelector('.delete');
		elDelete.addEventListener('click', function () {
			elImageList.removeChild(elItem);
		});

		elImageList.appendChild(elItem);
		items = elImageList.querySelectorAll('.item');
	};

	// 파일 업로드
	var _uploadImage = function (el) {
		// Data URI 가 아니라면, 업로드 불필요.
		if (! /^data:/i.test(el.dataset.url)) {
			if (countUploadFiles > 0) countUploadFiles--;
			if (countUploadFiles === 0) {	// 마지막 업로드 끝나면?
				elDimmer.classList.remove('active');
				_callApproveCallback();
			}

			return true;
		}

		var url = obj.options.urlUpload;
		var fd = new FormData();

		// 게시판, 문서에서 이미지를 업로드 하려면, 반드시 페이지 id 필요
		fd.append('pageid', obj.options.pageId);

		var blobResult = _dataUriToBlobData(el.dataset.url);
		if (! /^image/i.test(blobResult.mime)) {
			toastError_t('ImageUploader.NotImage', [ el.dataset.origName ]);
			return false;
		}

		var file = new File(blobResult.blob, el.dataset.origName, { type: blobResult.mime });
		fd.append('file', file);

		// AJAX 업로드
		ajaxPost(url, fd, null,
			function (data) {
				var item = data.item;

				if (item.type === 'i') {
					el.dataset.url = item.url;
				} else {
					toastError_t('ImageUploader.NotImage', [ el.dataset.origName ]);

					elImageList.removeChild(el);
					items = elImageList.querySelectorAll('.item');

					// 추가로, 파일 삭제 부분을 넣어야 할지 말아야 할지 고민중...
				}

				if (countUploadFiles > 0) countUploadFiles--;
				if (countUploadFiles === 0) {	// 마지막 업로드 끝나면?
					elDimmer.classList.remove('active');
					_callApproveCallback();
				}
			});
	};

	var _dataUriToBlobData = function (dataUri) {
		// convert base64 to raw binary data held in a string
		// doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
		var byteString = atob(dataUri.split(',')[1]);

		// separate out the mime component
		var mimeString = dataUri.split(',')[0].split(':')[1].split(';')[0];

		// write the bytes of the string to an ArrayBuffer
		var ab = new ArrayBuffer(byteString.length);
		var ia = new Uint8Array(ab);
		for (var i = 0; i < byteString.length; i++) {
			ia[i] = byteString.charCodeAt(i);
		}

		// write the ArrayBuffer to a blob, and you're done
		return {
			blob: [ab],
			mime: mimeString
		};
	};

	// 결과물을 만들어 성공 콜백을 호출하고 모달 창을 닫는다.
	var _callApproveCallback = function () {
		var code = '';
		var list = [];

		items.forEach(function (el) {
			code += '<img src="' + el.dataset.url + '" alt="">\n';
			list.push({
				url: el.dataset.url
			});
		});

		if (typeof obj.options.onApprove === 'function') {
			obj.options.onApprove.call(obj, code, list);
		}

		$ele.modal('hide');
	};

	_createElement();
}