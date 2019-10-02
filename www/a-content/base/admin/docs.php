<?php defined('_ARAKNY_') OR exit;

use Arakny\Models\DocsModel as M;

/**
 * Declare variables
 *
 * @var string $locale
 * @var array $list
 */
?>
<?= _adminSubHeader([ _adminIconButton('add'), _adminIconButton('delete') ]) ?>

<div class="ai-c max-w-900">

	<table class="ui compact striped table list">
		<thead>
		<tr>
			<th class="collapsing">
				<div class="ui master checkbox">
					<input type="checkbox" id=""><label for="">&nbsp;</label>
				</div>
			</th>
			<th><?= _g('l_d_name') ?></th>
			<th><?= _g('l_d_title') ?></th>
			<th class="collapsing"><?= _g('task') ?></th>
		</tr>
		</thead>
		<tbody>
		<?php $i=0; foreach ($list as $item): $i++; ?>
		<tr data-id="<?= $item[M::d_id] ?>">
			<td>
				<div class="ui child checkbox">
					<input type="checkbox" name="cb" id="" value="<?= $item[M::d_id] ?>"><label for="">&nbsp;</label>
				</div>
			</td>
			<td class="data-name"><?= _e($item[M::d_name]) ?></td>
			<td class="data-title"><?= _e($item[M::d_title]) ?></td>
			<td class="right aligned">
				<div class="ui compact basic icon buttons">
					<a class="ui button" href="<?= $item['url_edit'] ?>"><i class="edit icon"></i></a>
					<a class="ui button btn-delete" data-href="<?= $item['url_delete'] ?>"><i class="delete icon"></i></a>
				</div>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div class="ui fluid selection dropdown">
		<input type="hidden" name="docs" value="5">
		<i class="dropdown icon"></i>
		<div class="default text">선택해주세요</div>
		<div class="menu">
			<div class="item" data-value="1">피자</div>
			<div class="item" data-value="2">치킨</div>
			<div class="item" data-value="3">곱창</div>
			<div class="item" data-value="4">소불고기</div>
			<div class="item" data-value="5">회</div>
		</div>
	</div>
</div>

<script>
	(function() {
		// 마스터/하위 체크박스 관련 처리
		var elList = document.querySelector('table.list'), $list = $(elList);
		var $masterCheckbox = $list.find('.master.checkbox');
		var $childCheckbox = $list.find('.child.checkbox');
		$masterCheckbox.checkbox({
			onChecked: function () { $childCheckbox.checkbox('check'); },
			onUnchecked: function () { $childCheckbox.checkbox('uncheck'); }
		});
		$childCheckbox.checkbox({
			onChange: function () {
				var allChecked = true, allUnchecked = true;

				$childCheckbox.each(function () {
					if ($(this).checkbox('is checked')) allUnchecked = false;
					else allChecked = false;
				});

				if (allChecked) $masterCheckbox.checkbox('set checked');
				else if (allUnchecked) $masterCheckbox.checkbox('set unchecked');
				else $masterCheckbox.checkbox('set indeterminate');
			}
		});

		function delete_click() {
			var $this = $(this);
			var $tr = $this.closest('tr');
			var $data_name = $tr.find('.data-name');

			swalConfirm(_t('_.Delete'), _t('Docs.Q.Delete', [ $data_name.text() ]), null, function (result) {
				// 삭제 - AJAX
				ajaxPost($this.data('href'), null, null,
					function (data) {
						$tr.remove();
					});
			});
		}
		$list.find('.btn-delete').click(delete_click);

		// 서브헤더 - 버튼 클릭 이벤트
		var $subHeader = $('.sub-header');
		$subHeader.find('.btn-add').on('click', function () {
			location.href = 'docs/write';
		});
		$subHeader.find('.btn-delete').on('click', function () {
			// 모두 체크가 되지 않았다면, 패스
			var allUnchecked = true;
			$childCheckbox.each(function () {
				if ($(this).checkbox('is checked')) allUnchecked = false;
			});
			if (allUnchecked) return;

			// 하나라도 체크된 것이 있다면, 삭제를 진행할지 여부를 물어보고 진행
			swalConfirm(_t('_.Delete'), _t('Docs.Q.DeleteDocs'), null, function (result) {
				$childCheckbox.each(function () {
					var $this = $(this);
					var $tr = $this.closest('tr');

					if ($this.checkbox('is checked')) {

						// 삭제 - AJAX
						ajaxPost($tr.find('.btn-delete').data('href'), null, null,
							function (data) {
								$tr.remove();
							});
					}
				});
			});

		});




		$('.dropdown').dropdown({});

	})();
</script>