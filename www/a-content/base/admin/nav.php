<?php defined('_ARAKNY_') OR exit;

use Arakny\Controllers\Admin\Nav as N;

/**
 * Declares variables
 *
 * @var string $locale
 * @var array $list
 */

$listDocs = getPreparedListForSelect('docs');
$listBoards = getPreparedListForSelect('boards');

$menu_target_list = [
	[ 'text' => _g('none'), 'value' => '', ],
	[ 'text' => '_blank', 'value' => '_blank', ],
	[ 'text' => '_self', 'value' => '_self', ],
	[ 'text' => '_parent', 'value' => '_parent', ],
	[ 'text' => '_top', 'value' => '_top', ],
];

?>
<?= _adminSubHeader([ _adminIconButton('add'), _adminIconButton('save') ]) ?>

<style>
	.nested-sortable { display: flex; flex-direction: column; min-height: 20px; margin-top: 7px; padding: 5px; border: 1px dashed #e5e5e5; }
	.nested-sortable.root { margin-top: 0; padding: 0; border: 0; }
	.nested-sortable .nested-sortable .nested-sortable .nested-sortable { display: none; }
	.nested-sortable .nested-sortable .nested-sortable .add.command { color: #ccc; cursor: auto; }
	.nested-sortable .nested-sortable .nested-sortable .add.command:hover { background: transparent; }
	.nested-sortable li { position: relative; display: block; margin-top: 7px; padding: .5rem 10px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 3px;
		transition-duration: 300ms; }
	.nested-sortable li:first-child { margin-top: 0; }
	.nested-sortable li:hover { border-color: #999; }
	.nested-sortable li.ghost { background: #ff8; opacity: 0.4; }
	.nested-sortable li .menu-item { display: flex; }
	.nested-sortable .handle { cursor: move; }
	.nested-sortable .label { flex: auto; padding-left: 5px; }
	.nested-sortable li .commands { border-radius: 1rem; padding: 0 0.5rem; height: auto; background: transparent; color: #777; }
	.nested-sortable li .commands.menu-on { background: #ddd; }
	.list-empty { padding: 70px 10px; border: 1px dashed #e5e5e5; text-align: center; }
</style>

<div class="ai-c max-w-900">
	<ul id="menu-list" class="nested-sortable root">
		<!-- 3 Level 하위 메뉴까지 허용 -->
		<?php foreach ($list as $item): ?>
		<li data-id="<?= $item[N::m_id] ?>"
			data-label="<?= _ea($item[N::m_label]) ?>"
			data-linktype="<?= $item[N::m_linktype] ?>"
			data-link="<?= $item[N::m_link] ?? '' ?>"
			data-target="<?= $item[N::m_target] ?? '' ?>">
			<div class="menu-item">
				<div class="handle label"><?= _e($item[N::m_label]) ?></div>
				<div class="ai-b dropdown commands" data-not-selectable>
					<i class="fas fa-ellipsis-v"></i>
					<nav class="menu s-icon right">
						<div class="item add command"><i class="fas fa-plus"></i></div>
						<div class="item edit command"><i class="fas fa-cog"></i></div>
						<div class="item delete command"><i class="fas fa-trash"></i></div>
					</nav>
				</div>
			</div>
			<ul class="nested-sortable">
				<?php foreach ($item[N::m_items] as $item2): ?>
				<li data-id="<?= $item2[N::m_id] ?>"
					data-label="<?= _ea($item2[N::m_label]) ?>"
					data-linktype="<?= $item2[N::m_linktype] ?>"
					data-link="<?= $item2[N::m_link] ?? '' ?>"
					data-target="<?= $item2[N::m_target] ?? '' ?>">
					<div class="menu-item">
						<div class="handle label"><?= _e($item2[N::m_label]) ?></div>
						<div class="ai-b dropdown commands" data-not-selectable>
							<i class="fas fa-ellipsis-v"></i>
							<nav class="menu s-icon right">
								<div class="item add command"><i class="fas fa-plus"></i></div>
								<div class="item edit command"><i class="fas fa-cog"></i></div>
								<div class="item delete command"><i class="fas fa-trash"></i></div>
							</nav>
						</div>
					</div>
					<ul class="nested-sortable">
						<?php foreach ($item2[N::m_items] as $item3): ?>
						<li data-id="<?= $item3[N::m_id] ?>"
							data-label="<?= _ea($item3[N::m_label]) ?>"
							data-linktype="<?= $item3[N::m_linktype] ?>"
							data-link="<?= $item3[N::m_link] ?? '' ?>"
							data-target="<?= $item3[N::m_target] ?? '' ?>">
							<div class="menu-item">
								<div class="handle label"><?= _e($item3[N::m_label]) ?></div>
								<div class="ai-b dropdown commands" data-not-selectable>
									<i class="fas fa-ellipsis-v"></i>
									<nav class="menu s-icon right">
										<div class="item add command"><i class="fas fa-plus"></i></div>
										<div class="item edit command"><i class="fas fa-cog"></i></div>
										<div class="item delete command"><i class="fas fa-trash"></i></div>
									</nav>
								</div>
							</div>
							<ul class="nested-sortable"></ul>
						</li>
						<?php endforeach; ?>
					</ul>
				</li>
				<?php endforeach; ?>
			</ul>
		</li>
		<?php endforeach; ?>
	</ul>
	<div class="list-empty hidden">(<?= _g('none') ?>)</div>

	<div id="mo-write" class="ui tiny modal">
		<i class="close icon"></i>
		<div class="header"></div>
		<div class="content">
			<form class="grid fields">
				<?= _fieldHidden(N::m_parent) ?>
				<?= _fieldHidden(N::m_id) ?>
				<?= _adminFieldText(N::m_label) ?>
				<?= _adminFieldSelect(N::m_linktype, null, 'menu_linktype') ?>
				<div class="field field-m_link">
					<label for="field_m_link"><?= _g('l_m_link') ?></label>
					<input type="hidden" id="field_m_link_0">
					<input type="hidden" id="field_m_link_t">
					<div id="field_m_link_d" class="control dropdown">
						<input type="hidden">
						<nav class="menu">
							<?php foreach ($listDocs as $item): ?>
							<div class="item" data-value="<?= $item['value'] ?>" data-title="<?= _ea($item['title']) ?>"><?= _e($item['text']) ?></div>
							<?php endforeach; ?>
						</nav>
					</div>
					<div id="field_m_link_b" class="control dropdown">
						<input type="hidden">
						<nav class="menu">
							<?php foreach ($listBoards as $item): ?>
							<div class="item" data-value="<?= $item['value'] ?>"><?= _e($item['text']) ?></div>
							<?php endforeach; ?>
						</nav>
					</div>
					<div class="control input">
						<input type="text" id="field_m_link_u" value="" maxlength="256"
							   placeholder="<?= _g('l_m_linktype_u') ?>">
					</div>
				</div>
				<?= _adminFieldSelect(N::m_target, null, $menu_target_list, [ 'search' ]) ?>
			</form>
		</div>
		<div class="actions">
			<div class="ui cancel button">취소</div>
			<div class="ui ok positive button fw-light">확인</div>
		</div>
	</div>
</div>

<script>
	(function() {
		var $mo_info = $('#mo-info');
		$mo_info.modal({  });

		// 지원 가능한 최대 깊이
		var maxDeepLevel = 3;
		var list_selector = '.nested-sortable';
		var listitem_selector = 'li';
		var list = document.querySelector(list_selector + '.root');
		var lists = document.querySelectorAll(list_selector);
		var sortable;
		var $list_root = $(list_selector + '.root');
		lists.forEach(function (el, i) {
			var temp_sortable = new Sortable(lists[i], {
				animation: 150,
				ghostClass: 'ghost',
				group: 'nested',
				handle: '.handle',
				swapThreshold: 0.25,
				fallbackOnBody: false,
				dragoverBubble: true,
				emptyInsertThreshold: 5,
				onMove: function (evt, originalEvent) {
					const $this = $(evt.dragged),
						$related = $(evt.related);

					// 최대 깊이, 현재 깊이, 현재 드래그 객체기준 최대 상대 깊이, 드롭존 대상 객체 레벨
					let lvl_deepest = 0, lvl_current, lvl_relative, lvl_dest;

					lvl_current = $this.parents(list_selector).length;
					if (lvl_deepest < lvl_current) lvl_deepest = lvl_current;

					$this.find(listitem_selector).each(function () {
						const n = $(this).parents(list_selector).length;
						if (lvl_deepest < n) lvl_deepest = n;
					});

					lvl_relative = lvl_deepest - lvl_current + 1;

					lvl_dest = $related.parents(listitem_selector).length + 1;
					return (lvl_dest + lvl_relative - 1 <= maxDeepLevel);
				}
			});

			if (i === 0) {
				sortable = temp_sortable;
			}
		});
		/* #ff9999 #d9ffd9 #99ff99 */
		
		function getLevel($el) {
			return $el.parents(list_selector).length;
		}

		function add_click() {
			const $el = $(this).closest(listitem_selector);

			f_parent.value = $el.data('id');

			modal.add();
		}
		$list_root.find('.add.command').on('click', add_click);

		function edit_click() {
			const $el = $(this).closest(listitem_selector),
				id = $el.data('id');

			modal.edit(id);
		}
		$list_root.find('.edit.command').click(edit_click);

		function delete_click() {
			const $el = $(this).closest(listitem_selector),
				id = $el.data('id'),
				label = $el.data('label');

			modal.delete(id, { text: label });
		}
		$list_root.find('.delete.command').click(delete_click);

		// 링크 종류 변경시 하위 링크 필드 동적 변경
		const f_id = byId('field_m_id'),
			f_parent = byId('field_m_parent'),
			f_label = byId('field_m_label'),
			$f_linktype = $('#field_m_linktype'),
			$f_link_wrap = $('.field-m_link'),
			$f_target_wrap = $('.field-m_target'),
			dd_linktype = ai.Dropdown.init('#field_m_linktype');

		let is_linktype_init = false;
		dd_linktype.on('change', function () {
			const v = dd_linktype.value;

			$f_link_wrap.find('input, select').removeAttr('name');
			$f_link_wrap.find('input[type=text], .control.dropdown').hide();

			if (v == '0' || v == 't') {
				$f_link_wrap.hide();
				$f_target_wrap.hide();
			} else {
				$f_link_wrap.show();
				$f_link_wrap.find('#field_m_link_' + v).show();
				$f_target_wrap.show();
			}

			if (v == 'u') {
				$f_link_wrap.find('#field_m_link_' + v).attr('name', 'm_link');
			} else {
				$f_link_wrap.find('#field_m_link_' + v + ' > input').attr('name', 'm_link');
			}

			is_linktype_init = true;
		});

		$f_linktype.on('click2', function () {
			if (this.value == 'd' && is_linktype_init) {
				const $option = $('#field_m_link_d').find('option:selected');
				if ($option.length > 0) {
					f_label.value = $option.data('title');
				}
			}
		});

		$('#field_m_link_d').on('click change', function () {
			const $option = $(this).find('option:selected');
			if ($option.length > 0) {
				f_label.value = $option.data('title');
			}
		});

		// Write Modal (추가/수정/삭제 모달 레이어)
		const modal = new WriteModal({
			title: '<?= _pageTitle() ?>',

			urlAdd: '<?= BASEURL ?>admin/nav/add',
			urlEdit: '<?= BASEURL ?>admin/nav/edit',
			urlDelete: '<?= BASEURL ?>admin/nav/delete',
			urlGetData: '<?= BASEURL ?>admin/nav/fromid/{id}',

			canAdd: function () {
				const parentid = f_parent.value;
				if (parentid > 0) {
					const $parent = $list_root.find(listitem_selector + '[data-id='+ parentid +']');
					if (getLevel($parent) >= 3) {
						toastWarning_t('M.CannotAdd');
						return false;
					}
				}
				return true;
			},

			openAdd: function (id, data) {
				dd_linktype.value = 0;
			},

			openEdit: function (id, data) {
				const $parent = $list_root.find(listitem_selector + '[data-id=' + id + ']').parents(listitem_selector);

				f_parent.value = ($parent.length > 0) ? $parent.first().data('id') : 0;
				//$f_linktype.change();

				$f_link_wrap.find('[name=m_link]').val(data.row['m_link']);
			},

			openDelete: function (id) {
				f_id.value = id;
			},

			addSuccess: function (id, data) {
				const $item = $('<li data-id="' + id + '"></li>');

				const parentid = data.parentid,
					$parent = (parentid > 0) ? $list_root.find(listitem_selector + '[data-id=' + parentid + '] > ' + list_selector).first() : $list_root;

				$item.attr('data-label', data.row.m_label);
				$item.attr('data-linktype', data.row.m_linktype);
				$item.attr('data-link', data.row.m_link);
				$item.attr('data-target', data.row.m_target);
				$item.html(
					'<div class="menu-item">' +
					'<div class="handle label"></div>' +
					'<div class="add command"><i class="add icon"></i></div>' +
					'<div class="edit command"><i class="cog icon"></i></div>' +
					'<div class="delete command"><i class="trash icon"></i></div>' +
					'</div>' +
					'<ul class="nested-sortable"></ul>'
				);

				$item.find('.label').text(data.row.m_label);

				$item.find('.add.command').on('click', add_click);
				$item.find('.edit.command').on('click', edit_click);
				$item.find('.delete.command').on('click', delete_click);

				$parent.append($item);
			},

			editSuccess: function (id, data) {
				const $item = $list_root.find('li[data-id=' + id + ']');

				$item.attr('data-label', data.row.m_label);
				$item.attr('data-linktype', data.row.m_linktype);
				$item.attr('data-link', data.row.m_link);
				$item.attr('data-target', data.row.m_target);

				$item.find('.label').first().text(data.row.m_label);
			},

			deleteSuccess: function (id, data) {
				const $item = $list_root.find(listitem_selector + '[data-id=' + id + ']');
				$item.remove();
			},
		});


		$('.btn-add').on('click', function () {
			f_parent.value = 0;

			modal.add();
		});

		$('.btn-save').on('click', function () {
			function getData($el, level) {
				let result = [];
				$el.each(function () {
					const $this = $(this);
					let parent = null;

					if ($this.parents(listitem_selector).length > 0) {
						parent = $this.parents(listitem_selector).first().data('id');
					}

					const data = {
						m_id: $this.data('id'),
						m_label: $this.data('label'),

						m_linktype: $this.data('linktype'),
						m_link: $this.data('link'),
						m_target: $this.data('target'),

						m_parent: parent,
						m_items: getData( $this.find(' > ' + list_selector + ' > ' + listitem_selector), level + 1 )
					};

					result.push(data);
				});
				return result;
			}
			const result = getData( $list_root.find(' > ' + listitem_selector), 1 );
			const json = JSON.stringify(result);

			const data = {
				menu: json
			};

			// AJAX Process
			ajaxPost('<?= adminUrl('nav/save') ?>', data, null,
				function (data) {
					toastSuccess_t('M.Saved');
				});
		});

		// 리스트가 없으면, 빈 공간을 표현하는 요소를 보여주고,
		// 리스트 데이터가 있으면, 그 빈 공간 요소를 감춘다.
		const elListEmpty = document.querySelector('.list-empty');
		const observer = new MutationObserver(function (mutationsList) {
			mutationsList.forEach(function (mutation) {
				if (mutation.type == 'childList') {
					if (list.querySelectorAll(listitem_selector).length > 0) {
						elListEmpty.classList.add('hidden');
						list.classList.remove('hidden');
					} else {
						elListEmpty.classList.remove('hidden');
						list.classList.add('hidden');
					}
				}
			});
		});
		observer.observe(list, { childList: true });

		if (list.querySelectorAll(listitem_selector).length === 0) {
			elListEmpty.classList.remove('hidden');
			list.classList.add('hidden');
		}

	})();
</script>