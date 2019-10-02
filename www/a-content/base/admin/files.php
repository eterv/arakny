<?php defined('_ARAKNY_') OR exit;
/**
 * Declare variables
 *
 * @var string $locale
 */

// @todo 파일 정리가 필요함.

// 버튼 - 선택 모드
$btnSelectmode = _adminIconButton('toggle btn-selectmode', _g('selectmode'), 'check double');

// 드롭다운 버튼 - 그룹
$ddbtnSort = _adminIconDropdownButton('sort', _g('sort'), 'sort alphabet down', [
	_adminDropdownItem('', '가나다순'),
	_adminDropdownItem('1', '가나다역순'),
	_adminDropdownItem('2', '최근날짜순'),
	_adminDropdownItem('3', '오래된날짜순'),
]);

// 드롭다운 버튼 - 그룹
$ddbtnMenu = _adminIconDropdownButton('menus primary', '', 'ellipsis vertical', [
	_adminDropdownItem('newfolder', '새 폴더'),
	_adminDropdownItem('upload', '파일 추가...'),
	_adminDropdownItem(null, null),
	_adminDropdownItem('delete', '삭제'),
	_adminDropdownItem(null, null),
	_adminDropdownItem('cmd1', '명령 1'),
	_adminDropdownItem('cmd2', '명령 2'),
]);
?>
<?= _adminSubHeader([ $btnSelectmode, $ddbtnSort, $ddbtnMenu ]) ?>

<style>
	/*.ai.filelist { max-height: 100px; overflow-y: auto; }*/
	.ai.c { height: 100%; }
	#filelist { height: 100%; }
</style>

<div class="ai-c">
	<!--
	<div class="ui labeled icon dropdown button dd-type">
		<i class="map marker icon"></i>
		<div class="default text">Select Friend</div>
		<div class="menu">
			<a class="item">하하1</a>
			<a class="item" data-value="">하하2</a>
		</div>
	</div>
	-->

	<div id="filelist">
		<!--
		<div class="list-wrap">
			<?php for ($i = 9; $i < 9; $i++): ?>
			<div class="item" data-id="<?= $i ?>1">
				<div class="item-wrap">
					<div class="ui checkbox"><input type="checkbox" class="chk" value="<?= $i ?>1"></div>
					<div class="image" style="background-image: url('../img1.jpg')"></div>
					<div class="name">fileName1.jpg</div>
				</div>
			</div>
			<?php endfor; ?>
		</div>
		-->
	</div>

	<div class="">

	</div>

</div>

<script>
	(function() {
		$('.ui.checkbox').checkbox({ });

		var filelist = new AFileList('#filelist', {
			maxFileSize: <?= getFileUploadMaxSize() ?>,
			urlFileList: '/file/list/f',
			urlUpload: '/file/upload/f'
		});

		$('.dropdown.sort').dropdown({
			onChange: function (value, text, $selectedItem) {

			}
		});

		$('.dropdown.menus').dropdown({
			action: 'hide',
			onChange: function (value, text, $selectedItem) {

			}
		});


		// 폼 - 유효성 검증
		var $form = $('#form');
		$form.form({
			keyboardShortcuts: false,
			on: 'change',
			inline: true,
			fields: {
				/*d_name: ['empty', 'maxLength[64]', 'regExp[/^[A-Za-z0-9_-]+$/]'],
				d_text: ['empty', 'maxLength[100]'],
				d_content_type: ['checked'],*/
				d_path: {
					identifier: 'd_path',
					depends: 'd_content_type',
					rules: [ { type: 'empty' }]
				}/*,
				d_auth_read: []*/
			},
			onSuccess: function (event, fields) {
				console.log(fields, event);
			}
		});
		$form.on('submit', function () {
			alert('전송!');
			return false;
		});

		// 상단 우측 메인 버튼

		$('.btn-cancel').on('click', function () {
			location.href = './';
		});

		$('.btn-ok').on('click', function () {
			var arr = [], value;

			$('.chk:checked').each(function () {
				arr.push(this.value);
			});

			value = arr.join(',');
			alert( value );

			filelist.selectedId = 21;
			//alert( filelist.$selectedItem );
		});

	})();

	$(function() {

	});
</script>