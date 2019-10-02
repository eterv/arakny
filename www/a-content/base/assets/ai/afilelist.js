/**
 * Arakny File List
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

function AFileList(selector, options)
{
	var obj = this;

	// 옵션값 통합
	this.options = {
		// 모달 창 선택자
		selector: '.ai.filelist',

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

		fileTypeFilter: '',
		maxFileSize: 2 * 1024 * 1024,

		// 기능 선택
		canChangeFiletypeFilter: true,
		canChangeMultiSelectMode: true,
		viewMode: false,

		// 선택자

		checkboxSelector: '.chk',

		// 언어
		language: 'ko'
	};
	$.extend(this.options, options);

	var ele = this.ele = (typeof selector === 'string') ? document.querySelector(selector) : selector;
	var toolbar;
	var listbox, listWrap;
	var items = [];

	var isOrderNewest = true;
	var lastSelectedItem = null;
	var multiSelectMode = false;

	var countUploadFiles = 0;

	var fileTypeFilter = '';

	/* ----------------------------------------------------------------------
	 * 		이벤트 (Events)
	 * ---------------------------------------------------------------------- */

	var itemClickEvent = function (e) {
		e.stopPropagation();
		if (multiSelectMode) {
			this.classList.toggle('selected');

		} else {
			if (lastSelectedItem !== null) lastSelectedItem.classList.remove('selected');

			this.classList.add('selected');
			lastSelectedItem = this;
		}
	};

	ele.addEventListener('keyup', function (e) {
		if (multiSelectMode) {
			if (e.ctrlKey && e.keyCode == 65) {		// Ctrl + A
				obj.selectAll();
			}
		}
	});

	var onWindowResize = function () {
		var w = ele.clientWidth;
		if (w < 480) {
			_.forEach(items, function(item) {
				item.style.width = (100 / 3) + '%';
			});
		} else if (w < 768) {
			_.forEach(items, function(item) {
				item.style.width = (100 / 4) + '%';
			});
		} else if (w < 1024) {
			_.forEach(items, function(item) {
				item.style.width = (100 / 4) + '%';
			});
		} else if (w < 1200) {
			_.forEach(items, function(item) {
				item.style.width = (100 / 5) + '%';
			});
		} else {
			_.forEach(items, function(item) {
				item.style.width = (100 / 8) + '%';
			});
		}
	};
	window.addEventListener('resize', _.debounce(onWindowResize, 250));
	onWindowResize();

	// 선택 박스

	var selBox = document.createElement('div');
	selBox.classList.add('sel-box');

	ele.appendChild(selBox);

	var selBox_startX = 0;
	var selBox_startY = 0;
	var dragMode = false;

	var mc = new Hammer(ele, { domEvents: true });
	mc.get('pan').set({ threshold: 0 });

	mc.on('panstart', function (e) {
		if (!multiSelectMode) return;

		var rectEle = ele.getBoundingClientRect();
		var eleX = window.pageXOffset + rectEle.left;
		var eleY = window.pageYOffset + rectEle.top;

		selBox_startX = e.center.x - eleX;
		selBox_startY = e.center.y - eleY;

		//console.log(eleY, e.center.y, selBox_startY, rectEle.y);
		//console.log(selBox_startY, rectEle.y, eleY);

		selBox.style.left = selBox_startX + 'px';
		selBox.style.top = selBox_startY + 'px';
		selBox.style.width = '0';
		selBox.style.height = '0';
		selBox.style.display = 'block';
		selBox.style.opacity = '0';

		var rectSB = selBox.getBoundingClientRect();
		var canDrag = true;
		_.forEach(items, function (item) {
			if (!canDrag) return;
			var rectItem = item.getBoundingClientRect();

			// 교차 지점 찾기 알고리즘 적용
			var x_overlap = Math.max(0, Math.min(rectSB.right, rectItem.right) - Math.max(rectSB.left, rectItem.left));
			var y_overlap = Math.max(0, Math.min(rectSB.bottom, rectItem.bottom) - Math.max(rectSB.top, rectItem.top));
			if (x_overlap > 0 && y_overlap > 0) {
				canDrag = false;
			}
		});
		if (!canDrag) {
			selBox.style.display = 'none';
			return;
		}

		selBox.style.display = 'block';
		selBox.style.opacity = '1';

		dragMode = true;
	});
	mc.on('panmove', function (e) {
		if (!dragMode) return false;

		//console.log(selBox_startX, selBox_startY, e.deltaX, e.deltaY);

		if (e.deltaX < 0) {
			selBox.style.left = (selBox_startX + e.deltaX) + 'px';
			selBox.style.width = Math.abs(e.deltaX) + 'px';
		} else {
			selBox.style.left = selBox_startX + 'px';
			selBox.style.width = e.deltaX + 'px';
		}

		if (e.deltaY < 0) {
			selBox.style.top = (selBox_startY + e.deltaY) + 'px';
			selBox.style.height = Math.abs(e.deltaY) + 'px';
		} else {
			selBox.style.top = selBox_startY + 'px';
			selBox.style.height = e.deltaY + 'px';
		}
	});
	mc.on('panend', function (e) {
		if (!dragMode) return;

		var rectSB = selBox.getBoundingClientRect();

		items.forEach(function (item) {
			var rectItem = item.getBoundingClientRect();

			// 교차 지점 찾기 알고리즘 적용
			var x_overlap = Math.max(0, Math.min(rectSB.right, rectItem.right) - Math.max(rectSB.left, rectItem.left));
			var y_overlap = Math.max(0, Math.min(rectSB.bottom, rectItem.bottom) - Math.max(rectSB.top, rectItem.top));
			if (x_overlap > 0 && y_overlap > 0) {
				item.classList.add('selected');
			}
		});

		selBox.style.display = 'none';
		dragMode = false;
	});


	/* ----------------------------------------------------------------------
	 * 		메소드 (Methods)
	 * ---------------------------------------------------------------------- */

	this.selectAll = function() {
		items.forEach(function (el) {
			el.classList.add('selected');
		});
	};

	this.add = function() { _addItem(123, '../img1.jpg', 'FileName1.jpg'); };

	// 아이템 삭제
	this.delete = function () {
		this.getSelectedItems().forEach(function (el) {
			_deleteFile(el);
			_deleteFile(el);
		});
	};

	// 새 폴더 생성
	this.newFolder = function () {
		alert('미완성');
	};

	// 업로드
	this.upload = function(callback) {
		// 작업중...

		/**
		 * 자동으로 file input 을 생성하고  업로드를 한다.
		 */

		var eFile = document.createElement('input');
		eFile.type = 'file';
		//eFile.accept = 'image/*';
		eFile.multiple = true;
		eFile.addEventListener('change', function () {
			countUploadFiles = this.files.length;
			if (! dimmer.classList.contains('active')) dimmer.classList.add('active');

			for (var file, i = 0; i < this.files.length; i++) {
				file = this.files[i];
				//alert(file.name + ' ' + file.type + ' ' + file.size);

				if (file.size > obj.options.maxFileSize) {
					alert('[' + file.name + '] file is too big. You can\'t upload this file.');
					continue;
				}

				_uploadFile(file);
			}
		});
		eFile.click();
	};

	// 선택된 아이템을 반환한다.
	this.getSelectedItem = function () {
		return listbox.querySelector('.list-item.selected');
	};

	// 선택된 아이템을 반환한다.
	this.getSelectedItems = function () {
		return listbox.querySelectorAll('.list-item.selected');
	};

	/* ----------------------------------------------------------------------
	 * 		속성 (Properties)
	 * ---------------------------------------------------------------------- */

	Object.defineProperty(this, 'fileTypeFilter', {
		get: function () { return fileTypeFilter },
		set: function (value) {
			fileTypeFilter = value;
			switch (fileTypeFilter) {
				case 'av':
					items.forEach(function (el) {
						if (el.dataset.type === 'a' || el.dataset.type === 'v') {
							el.style.display = 'block';
						} else {
							el.style.display = 'none';
						}
					});
					break;
				case 'i':
					items.forEach(function (el) {
						if (el.dataset.type === 'i') {
							el.style.display = 'block';
						} else {
							el.style.display = 'none';
						}
					});
					break;
				default:
					items.forEach(function (el) {
						el.style.display = 'block';
					});
			}

			toolbar.querySelectorAll('.filetype.button .menu .item').forEach(function (el) {
				if (el.dataset.value === fileTypeFilter) {
					el.classList.add('active');
					el.classList.add('selected');
				} else {
					el.classList.remove('active');
					el.classList.remove('selected');
				}
			});
		}
	});

	Object.defineProperty(this, 'multiSelectMode', {
		get: function() { return multiSelectMode; },
		set: function(value) {
			multiSelectMode = value;
			if (multiSelectMode) {
				ele.classList.add('multi-select-mode');
			} else {
				ele.classList.remove('multi-select-mode');
				items.forEach(function (el) {
					el.classList.remove('selected');
				});
			}
		}
	});


	/* ----------------------------------------------------------------------
	 * 		내부 메소드 (Private Methods)
	 * ---------------------------------------------------------------------- */

	var _createItem = function (itemData) {
		var item = document.createElement('div');
		item.classList.add('list-item');
		item.addEventListener('click', itemClickEvent);

		var itemWrap = document.createElement('div');
		itemWrap.className = 'list-item-wrap';
		item.appendChild(itemWrap);

		var divImage = document.createElement('div');
		divImage.className = 'image';
		itemWrap.appendChild(divImage);

		var divName = document.createElement('div');
		divName.className = 'name';
		divName.innerText = itemData.filename;
		itemWrap.appendChild(divName);

		return item;
	};

	// 아이템을 하나 추가한다.
	var _addItem = function (itemData) {
		var item = _createItem(itemData);

		item.dataset.id = itemData.id;
		item.dataset.url = itemData.url;
		item.dataset.origName = itemData.origname;
		item.dataset.type = itemData.type;

		// 순서가 새날짜순 정렬이면, 앞에 추가하고, 아니면 뒤에 추가
		if (isOrderNewest) {
			if (listWrap.firstChild) {
				listWrap.insertBefore(item, listWrap.firstChild);
			} else {
				listWrap.appendChild(item);
			}
		} else {
			listWrap.appendChild(item);
		}

		items = listbox.querySelectorAll('.list-item');
		onWindowResize();

		return item;
	};

	// 오디오 파일 추가
	var _addAudioFile = function (itemData) {
		var item = _addItem(itemData);

		var divImage = item.querySelector('.image');

		var fa = document.createElement('i');
		fa.className = 'volume icon';
		divImage.appendChild(fa);
	};

	// 일반 파일 추가
	var _addGeneralFile = function (itemData) {
		var item = _addItem(itemData);

		var divImage = item.querySelector('.image');

		var fa = document.createElement('i');
		fa.className = 'file icon';
		divImage.appendChild(fa);
	};

	// 비디오 파일 추가
	var _addVideoFile = function (itemData) {
		var item = _addItem(itemData);

		var divImage = item.querySelector('.image');

		var fa = document.createElement('i');
		fa.className = 'video icon';
		divImage.appendChild(fa);
	};

	// 이미지 파일 추가
	var _addImageFile = function (itemData) {
		var item = _addItem(itemData);

		var divImage = item.querySelector('.image');
		divImage.style.backgroundImage = "url(" + itemData.urlThumb + ")";
	};

	// 파일 삭제
	var _deleteFile = function (item) {
		var url = obj.options.urlFileDelete;
		var fd = new FormData();
		fd.append('id', item.dataset.id);
		fd.append('url', item.dataset.url);

		// AJAX 업로드
		ajaxPost(url, fd, null,
			function (data) {
				listWrap.removeChild(item);
			});
	};

	// 파일 업로드
	var _uploadFile = function (file) {
		var url = obj.options.urlUpload;
		var fd = new FormData();
		fd.append('file', file);

		// AJAX 업로드
		ajaxPost(url, fd, null,
			function (data) {
				var item = data.item;

				switch (item.type) {
					case 'a': _addAudioFile(item); break;
					case 'i': _addImageFile(item); break;
					case 'v': _addVideoFile(item); break;
					default: _addGeneralFile(item);
				}

				if (countUploadFiles > 0) countUploadFiles--;
				if (countUploadFiles === 0) dimmer.classList.remove('active');
			});
	};

	/* ----------------------------------------------------------------------
	 * 		내부 메소드 (Private Methods) - Fomantic UI Helper
	 * ---------------------------------------------------------------------- */

	var _createTbIcon = function (className, title, icon, menuitems, dropdown_options, evtClick) {
		if (typeof className === 'undefined') className = '';

		// 버튼 만들기
		var tbIconButton = document.createElement('div');
		tbIconButton.className = 'ui top left pointing dropdown button ' + className;
		tbIconButton.title = title;
		tbIconButton.insertAdjacentHTML('beforeend', '<i class="' + icon + ' icon"></i>');

		var tbIconButtonMenu = document.createElement('div');
		tbIconButtonMenu.className = 'menu';
		tbIconButton.appendChild(tbIconButtonMenu);

		if (typeof menuitems !== 'undefined' && Array.isArray(menuitems)) {
			for (var i = 0; i < menuitems.length; i++) {
				tbIconButtonMenu.appendChild(menuitems[i]);
			}

			if (typeof dropdown_options === 'undefined') dropdown_options = {};
			$(tbIconButton).dropdown(dropdown_options);
		}

		if (typeof evtClick === 'function') {
			tbIconButton.addEventListener('click', evtClick);
		}

		return tbIconButton;
	};

	var _createTbDropdownItem = function (value, content, cbClick) {
		var tbDropdownItem = document.createElement('div');

		if (typeof value === 'undefined') {
			tbDropdownItem.className = 'divider';
		} else {
			tbDropdownItem.className = 'item';
			tbDropdownItem.dataset.value = value;
			tbDropdownItem.innerText = content;
			tbDropdownItem.addEventListener('click', cbClick);
		}

		return tbDropdownItem;
	};




	ele.classList.add('ai');
	ele.classList.add('filelist');
	ele.style.position = 'relative';

	// toolbar 요소
	toolbar = document.createElement('div');
	toolbar.className = 'toolbar';
	ele.appendChild(toolbar);

	var tbIconButtons = document.createElement('div');
	tbIconButtons.className = 'ui compact icon buttons';
	toolbar.appendChild(tbIconButtons);

	// 버튼 만들기

	if (obj.options.canChangeFiletypeFilter) {
		tbIconButtons.appendChild(_createTbIcon('toggle', '선택모드', 'check double', undefined, undefined, function () {
			this.classList.toggle('active');
			obj.multiSelectMode = this.classList.contains('active');
		}));
	}

	if (obj.options.canChangeFiletypeFilter) {
		tbIconButtons.appendChild(_createTbIcon('filetype', '파일형식', 'filter', [
			_createTbDropdownItem('', '전체보기'),
			_createTbDropdownItem('i', '이미지'),
			_createTbDropdownItem('av', '오디오/비디오')
		], {
			selectOnKeydown: false,
			onChange: function (value, text, $selectedItem) {
				obj.fileTypeFilter = value;
			}
		}));
	}

	if (! obj.options.viewMode) {
		tbIconButtons.appendChild(_createTbIcon('primary', '', 'ellipsis vertical', [
			_createTbDropdownItem('upload', '파일 추가...', function () {
				obj.upload();
			}),
			_createTbDropdownItem(),
			_createTbDropdownItem('delete', '삭제', function () {
				obj.delete();
			})
		], {
			action: 'hide',
			onChange: function (value, text, $selectedItem) {

			}
		}));
	}

	// 툴바에 자식요소가 전혀 없으면 - 툴바 감추기
	if (tbIconButtons.childNodes.length === 0) {
		toolbar.style.display = 'none';
	}


	// listbox 요소
	listbox = document.createElement('div');
	listbox.className = 'listbox';
	ele.appendChild(listbox);

	// list-wrap 요소
	listWrap = document.createElement('div');
	listWrap.className = 'list-wrap';
	listbox.appendChild(listWrap);

	// dimmer, loader 요소
	var dimmer = document.createElement('div');
	dimmer.className = 'ui inverted dimmer';
	ele.appendChild(dimmer);

	var loader = document.createElement('div');
	loader.className = 'ui text loader';
	loader.innerText = _t('M.Uploading');
	dimmer.appendChild(loader);



	// Init - 초기화
	var _init = function () {
		var url = obj.options.urlFileList;

		// AJAX 요청
		ajaxPost(url, null, null,
			function (data) {
				if (data.list.length > 0) {
					var tempIsOrderNewest = isOrderNewest;
					isOrderNewest = false;

					for (var item, i = 0; i < data.list.length; i++) {
						item = data.list[i];

						switch (item.type) {
							case 'a': _addAudioFile(item); break;
							case 'i': _addImageFile(item); break;
							case 'v': _addVideoFile(item); break;
							default: _addGeneralFile(item);
						}
					}

					isOrderNewest = tempIsOrderNewest;

					obj.fileTypeFilter = obj.options.fileTypeFilter;
				}
			});
	};
	_init();

	// 임시... 테스트
	//obj.multiSelectMode = true;

}