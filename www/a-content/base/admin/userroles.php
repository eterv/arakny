<?php defined('_ARAKNY_') OR exit;

use Arakny\Models\UserRolesModel as Model;

/**
 * Declares variables
 *
 * @var string $locale
 * @var array $list
 * @var string $page_title
 */

?>
<?= _adminSubHeader([ _adminIconButton('add'), _adminIconButton('save') ]) ?>

<style>
	.sortable { margin-top: 5px; padding: 15px 0; border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5; overflow-y: auto; }
	.sortable li { display: flex; margin-top: 4px; background: #f5f5f5; border: 1px solid #ddd;
		transition-duration: 300ms; }
	.sortable li:first-child { margin-top: 0; border-top-left-radius: 7px; border-top-right-radius: 7px; }
	.sortable li:last-child { border-bottom-left-radius: 7px; border-bottom-right-radius: 7px; }
	.sortable li:hover { border-color: #999; }
	.sortable li.ghost { background: #ff8; opacity: 0.4; }
	.sortable li .handle { width: 40px; padding-top: 6px; text-align: center; cursor: move; transition-duration: 300ms; }
	.sortable li .handle:hover { color: #07a; background: rgba(0, 120, 170, 0.1); }
	.sortable li .text { flex: auto; padding: 7px 5px; }
	.sortable li .command { width: 32px; padding-top: 6px; text-align: center; cursor: pointer; color: #777; }
	.sortable li .command:hover { color: #07a; }
	.sortable li i { margin: 0 !important; }

	.sortable .ignore { background: #ddd; border-color: #bbb; }
	.sortable .ignore:hover { border-color: #888; }
	.sortable .ignore .handle { visibility: hidden; }
	.sortable .permanent .delete { color: #ccc; cursor: auto; }
	.sortable .permanent .delete:hover { color: #ccc; }
</style>

<div class="ai-c max-w-900">
	<div class="ai bold text"><?= _pageTitle() ?></div>
	<ul id="userroles-list" class="sortable">
		<?php foreach ($list as $item):
			if ($item[Model::ur_id] == 1 || $item[Model::ur_id] == 2) {
				$item['ignore'] = 'ignore';
			}
			if ($item[Model::ur_id] <= 3) {
				$item['permanent'] = 'permanent';
			}
			?>
		<li class="<?= $item['ignore'] ?? '' ?> <?= $item['permanent'] ?? '' ?>" data-id="<?= $item[Model::ur_id] ?>">
			<div class="handle"><i class="large grip lines outline icon"></i></div>
			<div class="text"><?= $item['text'] ?></div>
			<div class="edit command"><i class="cog icon"></i></div>
			<div class="delete command"><i class="trash icon"></i></div>
		</li>
		<?php endforeach; ?>
	</ul>

	<div id="mo-write" class="ui tiny modal">
		<i class="close icon"></i>
		<div class="header"></div>
		<div class="scrolling content">
			<form class="ui form">
				<?= _fieldHidden('ur_id') ?>
				<div class="two fields">
					<?= _adminFieldText('ur_name', null, [ 'required', 'maxlength' => 64 ]) ?>
					<?= _adminFieldText('ur_text', null, [ 'help' => '', 'required', 'maxlength' => 64, 'helpClass' => 'text-right' ]) ?>
				</div>
			</form>
		</div>
		<div class="actions">
			<div class="ui cancel button fw-medium"><?= _g('cancel') ?></div>
			<div class="ui ok positive button fw-medium"><?= _g('ok') ?></div>
		</div>
	</div>
</div>

<script>
	$(function() {
		//
		//	Sortable List
		//
		var list_selector = '.sortable';
		var listitem_selector = 'li';
		var list = document.querySelector(list_selector);
		var $list = $(list_selector);
		var sortable = new Sortable(list, {
			animation: 150,
			ghostClass: 'ghost',
			handle: '.handle',
			filter: '.ignore',
			swapThreshold: 0.75,
			onMove: function (evt, originalEvent) {
				if ($(evt.related).hasClass('ignore')) {
					return false;
				}
			}
		});

		function edit_click() {
			var id = this.parentNode.getAttribute('data-id');
			modal.edit(id);
		}
		$list.find('.edit').on('click', edit_click);

		function delete_click() {
			var id = this.parentNode.getAttribute('data-id');
			var text = this.parentNode.querySelector('.text').innerText;
			modal.delete(id, {
				text: text
			});
		}
		$list.find('.delete').on('click', delete_click);


		var f_name = byId('field_ur_name');
		var f_text = byId('field_ur_text');

		$(f_text).on('input', onInputDelayFunc(function() {
			var next = this.parentNode.nextSibling;
			if (this.value.indexOf('::') === 0 && this.value.length > 2) {
				// AJAX Process
				ajaxGet('<?= _url('admin/l10n/translate/') ?>' + encodeURIComponent(this.value.substr(2)), null,
					function (data) {
						next.innerText = data.value;
					});
			} else {
				next.innerText = this.value;
			}
		}));

		// Write Modal (추가/수정/삭제 모달 레이어)
		const modal = new WriteModal({
			title: '<?= _pageTitle() ?>',

			urlAdd: '<?= BASEURL ?>admin/userroles/add',
			urlEdit: '<?= BASEURL ?>admin/userroles/edit/{id}',
			urlDelete: '<?= BASEURL ?>admin/userroles/delete/{id}',
			urlGetData: '<?= BASEURL ?>admin/userroles/fromid/{id}',

			canDelete: function (id) {
				if (id <= 3) {
					toastWarning_t('M.CannotDelete');
					return false;
				}
				return true;
			},

			openEdit: function (id, data) {
				f_text.parentNode.nextSibling.innerText = data.row.text;

				f_name.readOnly = (id <= 2);
				f_text.readOnly = f_name.readOnly;
			},

			beforeSubmit: function (id) {
				// 슈퍼관리자, 관리자는 변경 불가
				if (id == 1 || id == 2) {
					return true;
				}
			},

			addSuccess: function (id, data) {
				const $item = $('<li data-id="' + id + '"></li>');
				$item.html(
					'<div class="handle"><i class="large grip lines outline icon"></i></div>' +
					'<div class="text">' + data.row.text + '</div>' +
					'<div class="edit command"><i class="cog icon"></i></div>' +
					'<div class="delete command"><i class="trash icon"></i></div>'
				);
				$list.append($item);

				$item.find('.edit').on('click', edit_click);
				$item.find('.delete').on('click', delete_click);
			},

			editSuccess: function (id, data) {
				$list.find('li[data-id=' + id + '] .text').text(data.text);
			},

			deleteSuccess: function (id, data) {
				const item = list.querySelector('li[data-id="' + id + '"]');
				list.removeChild(item);
			},

			reset: function (form) {
				f_text.parentNode.nextSibling.innerText = '';
			}
		});

		// 추가
		$('.btn-add').on('click', function () {
			modal.add();
		});

		// 확인 (저장)
		$('.btn-save').on('click', function () {
			const listdata = JSON.stringify(sortable.toArray());

			// 저장 - AJAX
			ajaxPost('<?= adminUrl('userroles/save') ?>', { list: listdata }, null,
				function (data) {
					toastSuccess_t('Userroles.OnSaveSuccess');
				});

		});

	});
</script>