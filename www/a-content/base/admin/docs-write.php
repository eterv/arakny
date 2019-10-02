<?php defined('_ARAKNY_') OR exit;
/**
 * Declare variables
 *
 * @var string $locale
 * @var string $mode
 * @var int $id
 * @var int $tempid
 * @var array $data
 * @var string $urlList
 * @var string $urlSubmit
 */

addScriptInHead('tinymce', baseThemeUrl('assets/js/tinymce/tinymce.min.js'));

$etcOptions = [
	_adminFieldBoolToggle('d_use_header_footer', $data, [ 'default' => 1 ]),
	_adminFieldBoolToggle('d_is_wide', $data, [ 'default' => 0 ]),
];
?>
<?= _adminSubHeader([ _adminIconButton('back'), _adminIconButton('save') ]) ?>

<div class="ai w900 c">
	id, name, title, content_type, content, path, is_wide, auth_read, hit, u_id/dt - c/u<br>
	<?= _parseUrl(uploadsUrl(), PHP_URL_PATH) ?><br>

	<form class="ui form" id="form" method="post">
		<?= _fieldHidden('mode', $mode) ?>
		<?= _fieldHidden('pageid', $tempid) ?>
		<?= _fieldHidden('d_id', $data) ?>

		<div class="two fields">
			<?= _adminFieldText('d_name', $data, [ 'required', ]) ?>
			<?= _adminFieldText('d_title', $data, [ 'required', 'maxlength' => 100 ]) ?>
		</div>
		<?= _adminFieldRadioGroup('d_content_type', $data, [0, 1]) ?>
		<?= _adminFieldTextarea('d_content', $data, [ 'hidden' => ($data['d_content_type'] == 0 ? false : true) ]) ?>
		<?= _adminFieldText('d_path', $data, [ 'maxlength' => 150, 'hidden' => ($data['d_content_type'] == 1 ? false : true),
				'helptext' => '<div class="ui message">URL 절대 경로 : &nbsp;<span id="d_path_absurl"></span></div>' ]) ?>

		<?= _adminFieldSelect('d_auth_read', $data, 'userroles_notadmin', [ 'clearable', 'multiple' ]) ?>

		<?= _adminFieldGroup('d_etc_options', null, $etcOptions) ?>

	</form>

</div>

<script>
	(function() {
		tinymceLoader({
			selector: '#field_d_content',
			ai_page_type: 'd',
			ai_page_id: <?= $tempid ?>,
			save_onsavecallback: function () {
				$form.form('validate form');
			}
		});

		// 폼 - 유효성 검증
		var elForm = byId('form');
		var $form = $(elForm);
		$form.form({
			keyboardShortcuts: false,
			on: 'change',
			inline: true,
			fields: {
				d_name: {
					identifier: 'd_name',
					rules: [
						{ type: 'empty' },
						{ type: 'maxLength[64]' },
						{ type: 'regExp[/^[A-Za-z0-9-]+$/]' }
					]
				},
				d_title: {
					identifier: 'd_title',
					rules: [
						{ type: 'empty' },
						{ type: 'maxLength[100]' }
					]
				},
				d_content_type: {
					identifier: 'd_content_type',
					rules: [ { type: 'checked' } ]
				},
				d_path: {
					identifier: 'd_path',
					depends: 'field_d_content_type_1',
					rules: [ { type: 'empty' }]
				}/*,
				d_auth_read: []*/
			},
			onSuccess: function (event, fields) {
				var url = '<?= $urlSubmit ?>';
				var mode = byId('field_mode').value;

				tinymce.get('field_d_content').save();

				// AJAX 문서 변경 요청
				ajaxPost(url, elForm, null,
					function (data) {
						if (mode === 'a') {		// 추가 모드
							toastSuccess_t('Docs.OnSaveSuccess');
							location.replace('write/' + data.id);

						} else {				// 수정 모드
							if (data.id == byId('field_d_id').value) {
								toastSuccess_t('Docs.OnSaveSuccess');
							}
						}
					});

			}
		});


		// 내용 종류에 따라 DHTML 에디터 혹은 문서 경로 필드를 보여준다.
		var $d_content_type = $.formField('d_content_type');
		$d_content_type.on('change', function () {
			var value = $d_content_type.filter(':checked').val();
			if (value == 0) {
				$('.field-d_content').show();
				$('.field-d_path').hide();
			} else if (value == 1) {
				$('.field-d_content').hide();
				$('.field-d_path').show();
			}
		}).trigger('change');

		var $d_path = $.formField('d_path');
		$d_path.on('input', function () {
			$('#d_path_absurl').text('<?= _parseUrl(contentUrl('docs/'), PHP_URL_PATH) ?>' + this.value);
		}).trigger('input');


		// 상단 우측 메인 버튼

		$('.btn-back').on('click', function () {
			location.href = '<?= $urlList ?>';
		});

		$('.btn-save').on('click', function () {
			$form.form('validate form');
		});

	})();
</script>